#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Created on April 14, 2012

Comisii & comisii from cdep.ro

@author: cornel
"""

import re
import datetime
import sys
import codecs
import time

import hashlib
import json
import os
import urllib2

from urllib2 import URLError
from BeautifulSoup import BeautifulSoup
from sets import Set
#from dateutil.relativedelta import relativedelta

from ro.vivi.hplib import *

# page start 
# http://www.cdep.ro/pls/parlam/structura.co

#for each comisii
  # membrii - done
  # TODO - detect page changes
  # http://www.cdep.ro/pls/parlam/structura.co?idc=<ID>

  # sedinte
  # http://www.cdep.ro/co/sedinte.lista?tip=<ID>&an=2012 # 2011, 2010, 2009

  # documente
  # http://www.cdep.ro/pls/proiecte/upl_com.lista?idc=<ID>&an=2012

def get_comisii(tmp_dir):
  link = 'http://www.cdep.ro/pls/parlam/structura.co'
  comisii_page = get_page(link, tmp_dir)
  reg_comisii = re.compile('HREF="/pls/parlam/structura\.co\?idc=(\d+)">(.*?)<\/')

  return Set(reg_comisii.findall(comisii_page))

# get former members
#   tb - table
def extract_former_members(tb):
  members = []
  header_skipped = False
  has_functie = False
  has_inout = False

  for row in tb.findAll('tr'):
    cells = row.findAll('td')
    # 2nd row when we have both in and out time
    if len(cells) == 2:
      has_inout = True
      continue
    if not header_skipped:
      header_skipped = True
      has_functie = True if cells[1].string == u'Funcţia' else False
      continue
    timein = '0'

    # there are two possible tables on these pages
    if has_functie:
      nume = cells[2].find('b').string
      partid = cells[3].find('a').string
      timeout = cells[4].string
      functia = cells[1].string.replace('&nbsp;', '')
    else:
      nume = cells[1].find('b').string
      partid = cells[2].find('a').string
      functia = ''
      if has_inout:
        timein = '0' if cells[3].string == '&nbsp;' else cells[3].string
        timeout = cells[4].string
      else:
        timeout = cells[3].string

    members.append( (nume, functia, partid, timein, timeout) )
  return members

# extracts data from the html page (it also retrives it)
#
def get_comisie_details(id, tmp_dir):
  try:
    id = int(id)
  except ValueError:
    print " + No details for id %s" % id

  members = []
  link = 'http://www.cdep.ro/pls/parlam/structura.co?idc=%d' % id
  print " + %s" % link
  comisie_page = get_page(link, tmp_dir)
  soup = BeautifulSoup(comisie_page)
  tbs = soup.findAll('table', attrs={"class" : "tip01"})

  prev_functia = ''
  header_skipped = False
  for row in tbs.pop(0).findAll('tr'):
    if not header_skipped:
      header_skipped = True
      continue
    cells = row.findAll('td')
    functia = cells[1].string.replace('&nbsp;', '')
    if functia != '':
      prev_functia = functia
    else:
      functia = prev_functia
    
    if cells[2].find('b') != None:
      nume = cells[2].find('b').string.replace('&nbsp;', '')
    partid = cells[3].contents[1].string

    if len(cells) > 4 and cells[4].string != None:
      timein = cells[4].string.replace('&nbsp;', '')
    else:
      timein = ''

    #time.mktime(datetime.date.today().timetuple())
    members.append( (nume, functia, partid, timein, None))
    #print "  nume: ", nume
    #print "  functia: ", functia

  for t in tbs:
    new_members = extract_former_members(t)
    for m in new_members:
      members.append(m)
  
  return members


def main():
  """ Main function. """
  global TMP_DIR

  if len(sys.argv) <= 1:
    print "The first argument should be the output directory."
    TMP_DIR = "/tmp"
  else:
    TMP_DIR = sys.argv[1]

  print "tmp_dir: %s" % TMP_DIR

  comisii = get_comisii(TMP_DIR)
  print "Found %d comisii\n" % len(comisii)
  if len(comisii) > 0:
    for comisie in comisii:
      #if comisie[0] != '1': continue
      members = get_comisie_details(comisie[0], TMP_DIR)
      print "\t%d membri" % len(members)

main()

