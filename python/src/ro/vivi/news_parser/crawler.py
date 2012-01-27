# -*- coding: utf-8 -*-

'''

Misc functions related to the crawler. Used by all the rss_parse_* files for
some common crawling utilities.

Created on Oct 17, 2009

@author: vivi
'''

import md5
import os
import urllib

NO_CACHE = False
cache_hits = []


def is_cache_hit(source, link):
  fname = get_file_name_for_link(source, link)
  return fname in cache_hits


def get_file_name_for_link(source, link):
  """ Returns the file name based on the link.
  """
  m = md5.new()
  m.update(link)
  return source + '/news_' + m.hexdigest() + '.html'


def get_news_html(source, link):
  """ Fetch the page at the given URL and return the response.
  If the page was already fetched, just get it locally from the cache.

  Arguments:
    link: The url link from where to get the page.
  Returns:
    A string with just the content text.
  """
  fname = get_file_name_for_link(source, link)
  if not os.path.exists(fname) or NO_CACHE:
    # Check if somehow maybe I have this day id parse already.
    f = urllib.urlopen(link)
    data = f.read()
    f = open(fname, 'w')
    f.write(data)
    f.close()
  else:
    f = open(fname)
    data = f.read()
    f.close()
    cache_hits.append(fname)
  return data


def replace_html_comments(data):
  """ Replaces the html comments in the data and returns the string without them
  """
  start = 1
  end = 0
  res = ""

  while start > 0:
    new_start = data.find("<!--", start + 4)
    res = res + data[end : new_start]
    start = new_start
    end = data.find("-->", start) + len("-->")
  return res


def replace_circ_diacritics(data):
  return data\
      .replace('&icirc;', 'î')\
      .replace('&quot', '')\
      .replace('&Icirc;', 'Î')\
      .replace('&ldquo;', '"')\
      .replace('&bdquo;', '"')\
      .replace('&acirc;', 'â')\
      .replace('&Acirc;', 'Â')


def capitalize(str):
  res = []
  for part in str.split(' '):
    res.append(part.capitalize())
  return ' '.join(res)


def get_api_key(file):
  f = open(file, 'r')
  key = f.read()
  f.close()
  return key
