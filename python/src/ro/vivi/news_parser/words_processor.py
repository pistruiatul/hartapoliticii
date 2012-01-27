# -*- coding: utf-8 -*-

'''
Created on Jan 08, 2010

Goes through the news content and attempts to count the words in there. First,
we just want a word frequency measurement so we know what to compare against
for spotting outliers.

@author: vivi
'''

import re
import os
import urllib
import time
import sys
from datetime import datetime
from xml.etree.ElementTree import parse

from text_utils import strip_tags_and_new_lines
from text_utils import strip_punctuation
from text_utils import lower
from text_utils import strip_diacritics

from operator import itemgetter

NUMBER_OF_DAYS_TO_PARSE = 10


# This has caused a problem when I used this script with the default, assuming
# it's mediafax, but instead it was hotnews. Never use this script without a
# parameter, unless it's for development purposes.
SOURCE = 'mediafax'
if len(sys.argv) > 1:
  SOURCE = sys.argv[1]


def is_not_a_daily(file):
  return not file.find('daily_')


# ===========================
# The main method for now, do not rely on it.
print "\n\n\n--!! STEP 2"

map = {}

# Get the last five daily news files.
files = filter(is_not_a_daily, os.listdir(os.getcwd() + '/' + SOURCE + '/'))
files.sort()

for fname in files[-NUMBER_OF_DAYS_TO_PARSE : ]:
  print "--"
  print "-- ++ working on " + SOURCE + "/" + fname

  tree = parse(SOURCE + '/' + fname)
  for item in tree.findall('item'):
    link = item.findtext('news_link').encode('UTF-8')
    # news_title is at this point an UTF-8 encoded string, quoted.
    title = urllib.unquote(item.findtext('news_title').encode('UTF-8'))

    tstr = item.findtext('news_time').encode('UTF-8').split(' ')
    d = datetime(year=int(tstr[4]), month=int(tstr[3]), day=int(tstr[2]),
                 hour=int(tstr[0]), minute=int(tstr[1]))

    news_content = urllib.unquote(item.findtext('news_content'));
    news_content = strip_tags_and_new_lines(news_content)
    news_content = strip_punctuation(news_content)
    news_content = lower(news_content)
    news_content = strip_diacritics(news_content)
    
    words = news_content.split(" ")

    for word in words:
      if not word:
        continue
      
      if not word in map:
        map[word] = 1
      else:
        map[word] = map[word] + 1
    
    
sorted_list = sorted(map.items(), key=itemgetter(1), reverse=True)

for word in sorted_list:
  print word[0], word[1]
