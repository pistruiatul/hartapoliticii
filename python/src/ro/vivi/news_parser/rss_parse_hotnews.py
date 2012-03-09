# -*- coding: utf-8 -*-

'''
Created on Oct 12, 2009

Parse the RSS feed from the source, gets the links from it and then fetches the
pages with the news.

The code:
  - gets the rss feed from the source. That contains their most recent 100 news.
  - gets out the links and fetches the news content.
  - saves the meta information plus the real content in a file for each day.

This script should run at least once every 5 days because it only gets and
outputs the news for these days so I don't have to check any incremental
addition, I can safely rewrite the past 5 days of news.

@author: vivi
'''
from crawler import get_news_html
from crawler import is_cache_hit, cache_hits
from crawler import replace_circ_diacritics
from crawler import replace_html_comments

import os
import urllib

from datetime import datetime
from dateutil import parser
from xml.etree.ElementTree import parse
from BeautifulSoup import BeautifulSoup
from HTMLParser import HTMLParseError

source = 'python/src/ro/vivi/news_parser/hotnews'
source_xml = 'http://www.hotnews.ro/rss/politic'

NUM_DAYS_REWRITE = 20


def get_news_text_from_html(data):
  """ Given a string of data, locate the content.

  Arguments:
    data: A string with the entire html page.
  Returns:
    A string with just the content text.
  """
  # From the data, get just the content. I don't quite understand why this
  # didn't work with a regular expression.
  data = replace_circ_diacritics(data)
  data = replace_html_comments(data)

  try:
    soup = BeautifulSoup(data)
  except HTMLParseError:
    return 'error'

  tag = soup.find('div', {'id': 'articleContent'})
  if tag is None:
    return "error: article not found"

  script = tag.findNext('script', { 'type': 'text/javascript'})
  if script is not None:
    script.extract()

  #tag.findNext('div', {'class': 'tool_back'}).extract()
  #links = tag.findNext('div', {'class': 'links'})
  #if links is not None:
  #  links.extract()

  return str(tag)


# ===========================
# The main method for now, do not rely on it.
if not os.path.exists(source):
  os.mkdir(source)

now = datetime.now()
open_files = []

print "\n\n================================="
print "\n====================== !! STEP 1 =="

rss = parse(urllib.urlopen(source_xml))
#<item>
#  <title><![CDATA[bla bla bla]]></title>
#  <comments>http://www.catavencuile_pd_l-10406.html#comentarii</comments>
#  <link>http://www.catavencu.ro/fiica_lui_glorile_pd_l-10406.html</link>
#  <description><![CDATA[bla bla bla]]></description>
#  <pubDate>Sun, 04 Oct 2009 17:49:00 +0300</pubDate>
#</item>
for item in rss.findall('channel/item'):
  title = item.findtext('title').encode('UTF-8')
  link = item.findtext('link').encode('UTF-8')

  datestr = item.findtext('pubDate').encode('UTF-8').replace(' +0300', '').replace(' GMT', '')
  d = parser.parse(datestr)

  content = get_news_text_from_html(get_news_html(source, link))

  if content is None:
    print ' ! ' + source + ' error for ' + link + ', main tags nowhere'
    continue
  elif content.startswith('error'):
    print " ! parsing: " + content + " " + link
    continue

  fname = source + '/daily_%04d%02d%02d.txt' % (d.year, d.month, d.day)

  timedelta = now - d
  if timedelta.days < NUM_DAYS_REWRITE:
    if fname in open_files:
      f = open(fname, 'a')
    else:
      f = open(fname, 'w')
      f.write("<all>\n")
      open_files.append(fname)
    # 16 17 18 09 2009
    news_time = '%02d %02d %02d %02d %04d' % \
        (d.hour, d.minute, d.day, d.month, d.year)

    f.write("<item>\n")
    f.write(" <news_link>" + link + "</news_link>\n")
    f.write(" <news_title>" + urllib.quote(title) + "</news_title>\n")
    f.write(" <news_place></news_place>\n")
    f.write(" <news_time>" + news_time + "</news_time>\n")
    f.write(" <news_content>" + urllib.quote(content) + "</news_content>\n")

    if not is_cache_hit(source, link):
      f.write(" <news_status>fresh</news_status>")
    else:
      f.write(" <news_status>stale</news_status>")

    f.write("</item>\n")
    f.close()

for fname in open_files:
  f = open(fname, 'a')
  f.write("</all>")
  f.close()
  print "-- updated " + fname

print " + cache hits for news items: " + str(len(cache_hits))
