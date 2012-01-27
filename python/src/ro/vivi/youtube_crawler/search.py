# -*- coding: UTF-8 -*-
"""
Created on Nov 8, 2009

@author: vivi
"""

import os
import re
import sys
import urllib
import dateutil
import time

from dateutil import parser

import gdata.youtube
import gdata.youtube.service

FLAGS = {}

def ParseFlags(args, flags):
  for arg in args:
    (name, value) = arg.split('=')
    name = name.replace('--', '')
    FLAGS[name] = value


def GetPersonId(name):
  if name == "Rotaru Constantin":
    return 3408
  
  f = urllib.urlopen('http://hartapoliticii.ro/search.php?q=' + 
                     urllib.quote(name))
  id = f.read().split('\n')

  print id

  if len(id) == 2:
    return id[0]
  else:
    return -1
  

def AddEntryDetails(idperson, entry):
  data = {}

  data['idperson'] = idperson

  data['title'] = entry.media.title.text
  data['time'] = int(time.mktime(parser.parse(entry.published.text).timetuple()))
  data['content'] = entry.media.description.text
  data['watch_url'] = entry.media.player.url
  data['player_url'] = entry.GetSwfUrl()
  data['duration'] = entry.media.duration.seconds

  data['thumb'] = entry.media.thumbnail[0].url

  f = urllib.urlopen(
      'http://www.hartapoliticii.ro/api/generic_insert.php?table=yt_videos',
      urllib.urlencode(data))
  print f.read()


def AddVideoFeed(idperson, feed):
  for entry in feed.entry:
    AddEntryDetails(idperson, entry)


def SearchAndAdd(name):
  id = GetPersonId(name)
  
  yt_service = gdata.youtube.service.YouTubeService()
  query = gdata.youtube.service.YouTubeVideoQuery()
  query.vq = name
  query.orderby = 'published'
  query.racy = 'include'
  feed = yt_service.YouTubeQuery(query)
  
  AddVideoFeed(id, feed)


if __name__ == '__main__':
  ParseFlags(sys.argv[1:], FLAGS)

  if 'name' not in FLAGS:
    print 'Usage: search.py --name="Traian Băsescu"'
    sys.exit(1)
  
  SearchAndAdd(FLAGS['name'])
