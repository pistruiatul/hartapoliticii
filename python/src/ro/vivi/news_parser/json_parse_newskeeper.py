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

NO_CACHE = False

SOURCES = {
      'ZF' : 'ziarul financiar',
      'RL' : 'romania libera',
      'JURNALUL' : 'jurnalul national',
      'WS' : 'wall-street.ro',
      'GANDUL' : 'gandul',
      'ADEVARUL': 'adevarul'
    }

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
parsed = 0

for i in items:

  # we don't care about libertatea, right?!
  # we already get hotnews articles
  if i['newspaper'] in ['LIBERTATEA', 'HOTNEWS', 'MEDIAFAX'] :
    continue

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
  
  content = item['optionalArticle']['content']

  if SOURCES.has_key(i['newspaper']):
    news_source = SOURCES[ i['newspaper'] ]
  else:
    news_source = i['newspaper'].lower()
  
  new_item = {
        'news_source' : news_source,
        'news_link' : i['originalUrl'],
        'news_title': urllib.quote( news_title.encode("UTF-8") ),
        'news_time':   time.strftime("%H %M %d %m %Y", time.localtime(int(i['insertDate']/1000) )),
        'news_content': urllib.quote( content.encode("UTF-8") ),
        'news_status' : 'fresh',
        'news_place' : '',
      }
  xml = dict2xml.dict2xml(new_item)
  xml = re.sub(' type="\w+"','', xml.replace('<?xml version="1.0" encoding="UTF-8" ?>', ''))
  xml = xml.replace('root>', 'item>')
  all_xml += "<item>\n" + xml + "</item>\n\n"

  # making sure we don't get this resource again
  f = open(cache_file, 'w')
  f.close

  parsed += 1
  if parsed >= 50:
    break

  # be nice to this server..
  time.sleep(1)


time.strftime("%Y%m%d-%H")
all_xml_fname = WORK_DIR + '/daily_%s.txt' % time.strftime("%Y%m%d-%H")
f = codecs.open(all_xml_fname, "w", "utf-8")
f.write("<all>\n" + all_xml + "\n</all>")
f.close

print "\ndone."
