# -*- coding: utf-8 -*-

'''
Created on Nov 12, 2012

Script that gets news items from newskeeper.ro in JSON format.
The data is then converter to XML for further processing.

note: get dict2xml from https://github.com/quandyfactory/dict2xml 

@author cornel

'''

import urllib, urllib2, os
import simplejson
import sys, time
import dict2xml
import re,codecs
from bs4 import BeautifulSoup

NO_CACHE = False

SOURCES = {
      'ZF' : 'ziarul financiar',
      'RL' : 'romania libera',
      'JURNALUL' : 'jurnalul national',
      'WS' : 'wall-street.ro',
      'GANDUL' : 'gandul',
      'ADEVARUL': 'adevarul'
    }

def extract_article_image(source, html):
  """
  Extracts image link from raw html
  """

  image = ""
  try:
    soup = BeautifulSoup(html, "lxml")
  except HTMLParseError:
    print "\terror: can't parse raw HTML"
    return image

  try:
    if source in ['RL', 'GANDUL', 'JURNALUL']:
      elem = soup.find('meta', {'property': 'og:image'})
      if elem:
        image = elem['content']
      else:
        # RL
        elem = soup.find('div', {'class': 'main_pic'})
        if elem:
          elem2 = elem.find('div', id='item_0').find('img')['src']
          if elem2:
            image = "http://www.romanialibera.ro" + elem2
        else: # GANDUL
          elem = soup.find('div', {'class': 'image'})
          if elem:
            image = elem.find('a', id='jsMainImage').find('img')['src']

    elif source in ['EVZ', 'CAPITAL']:
      image = soup.find('div', id='gallery').find('img')['src']
      domain = source.lower() + '.ro'
      if not domain in image:
        image = 'http://www.' + domain +'/' + image

    elif source == 'ADEVARUL':
      # we'll have to change this soon, as they seem to work on a new version of the website
      elem = soup.find('div', {'class': 'bb-wg-media'})
      if elem:
        image = elem.find('div', {'class':'mg'}).find('img')['src']
        # get a smaller image if pssible
        image = "http://www.adevarul.ro" + re.sub("/4\.jpg", "/2.jpg", image)

    elif source == 'ZF':
      elem = soup.find('div', id='content').find('div', id='mainVideo').find('div', {'class':'image'})
      if elem:
        image = re.sub("=\d{3}$", "=100", elem.find('img')['src'])
    else:
      image = ''
  except AttributeError, e: pass
  except TypeError, e: pass

  return image

#----------------------------------------------------------------
# main
#

WORK_DIR = 'python/src/ro/vivi/news_parser/newskeeper'
if len(sys.argv) > 1:
  WORK_DIR = sys.argv[1]

try:
  f = urllib2.urlopen('http://newskeeper.ro/hpexport/list')
  #f = file('/tmp/lista.json')
except IOError, e:
  print "Error: ", e
  sys.exit(1)

items = simplejson.load(f)

all_xml = ''

skipped = 0

for i in items:

  # we don't care about libertatea, right?!
  # we already get hotnews articles
  if i['newspaper'] in ['LIBERTATEA', 'HOTNEWS', 'MEDIAFAX'] :
    continue

  #if i['newspaper'] not in ['EVZ', 'CAPITAL'] :
  #  continue

  cache_file = "%s/nk_%s.json" % (WORK_DIR, i['md5'])
  if os.path.exists(cache_file):
    print 'skipping: ', i['nkUrl']
    skipped = skipped + 1
    if skipped >= 10:
      break
    else:
      continue

  try:
    nk_full_uri = 'http://newskeeper.ro%s' % i['nkUrl']
    print "  + ", nk_full_uri
    f = urllib2.urlopen(nk_full_uri)
  except urllib2.URLError, e:
    print " - skipping %s" % i['nkUrl']
    continue
  try:
    item = simplejson.load(f)
  except simplejson.decoder.JSONDecodeError:
    print " - ERR: can't get json"
    continue

  if item.has_key('optionalArticle') \
      and item['optionalArticle'] \
      and item['optionalArticle'].has_key('title'):
    news_title = item['optionalArticle']['title']
  else:
    continue

  if item['optionalArticle'].has_key('content'):
    content = item['optionalArticle']['content']
  else:
    continue

  if SOURCES.has_key(i['newspaper']):
    news_source = SOURCES[ i['newspaper'] ]
  else:
    news_source = i['newspaper'].lower()

  category = item['optionalArticle']['category'].lower()

  # Only look at articles that are in categories potentially related to Politics
  cat_ok_re = "politic|parlamentare|alegeri|justi|news"
  if re.search(cat_ok_re, category):
    print '     we got politics as a category [' + category + ']'

    # extract image
    image_url = extract_article_image(i['newspaper'], item['rawHtml'])

    new_item = {
          'news_source' : news_source,
          'news_link' : i['originalUrl'],
          'news_title': urllib.quote( news_title.encode("UTF-8") ),
          'news_time':   time.strftime("%H %M %d %m %Y", time.localtime(int(i['insertDate']/1000) )),
          'news_content': urllib.quote( content.encode("UTF-8") ),
          'news_status' : 'fresh',
          'news_place' : '',
          'news_photo' : image_url,
        }

    xml = dict2xml.dict2xml(new_item, root=False)
    xml = re.sub(' type="\w+"','', xml)
    all_xml += "<item>\n" + xml + "</item>\n\n"
  else:
    print '     ... skipped, category was [' + category.encode('UTF-8') + ']'

  # making sure we don't get this resource again
  f = open(cache_file, 'w')
  f.close

  # be nice to this server..
  time.sleep(0.5)


time.strftime("%Y%m%d-%H")
all_xml_fname = WORK_DIR + '/daily_%s.txt' % time.strftime("%Y%m%d-%H")
f = codecs.open(all_xml_fname, "w", "utf-8")
f.write("<all>\n" + all_xml + "\n</all>")
f.close

print "\ndone."

