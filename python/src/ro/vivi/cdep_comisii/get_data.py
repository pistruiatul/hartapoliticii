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

def get_meetings(com_id, year, tmp_dir):
  link = 'http://www.cdep.ro/co/sedinte.lista?tip=%s&an=%s' % (com_id, year) # 2011, 2010, 2009
  print " + %s" % link
  sedinte_page = get_page(link, tmp_dir)
  #reg_date = re.compile('<span class="title_gradient_1">(.*?)</span>')
  #ro_dates = reg_date.findall(html)

  meetings = []
  soup = BeautifulSoup(sedinte_page)
  div=soup.find('div', attrs={'id':'pageContent'})
  ps = div.findNextSiblings('p')
  # check if the <p> containing the table exists
  if len(ps) == 0:
    return meetings

  t=ps[0].find('table')
  tr = t.find('tr')
  reg_fix_date1 = re.compile("^\d+\.")
  while tr is not None and hasattr(tr, 'name') and getattr(tr, 'name') == 'tr':
    date = tr.text
    date = reg_fix_date1.sub('', date)
    docs = []

    # get the next row (which contains the attached docs)
    tr = tr.nextSibling.nextSibling
    tt = tr.find('table')
    for dtr in tt.findAll('tr'):
      cells = dtr.findAll('td')
      name = cells[0].text
      link = cells[1].find('a')['href']

      docs.append({'name': name, 'link': link})

    #print "\t %s -> %d doc(s)" % (date, len(docs))
    meetings.append((date, docs))
    
    # next row that has the date/time of the meeting
    tr = tr.nextSibling.nextSibling

  return meetings


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

def get_bin_file(link, tmp_dir):
  """ Fetches the binary file (pdf) at the provided link.  
  """

  _, file_ext = os.path.splitext(link)
  if ext == '': ext = '.dat'

  # First, see if this is already cached.
  fname = tmp_dir + '/bin_cache/%s%s' % hashlib.md5(link).hexdigest(), ext
  if os.path.exists(fname):
    return get_file_data(fname)

  success = False
  while not success:
    try:
      f = urllib2.urlopen(link, None, 20)
      data = f.read()
      f.close()

      # Save into a cached file.
      cache_file = open(fname, 'w')
      cache_file.write(data)
      cache_file.close()

      return data
    except URLError:
      print "Timed out, retrying ", link
      success = False

def main():
  """ Main function. """
  global TMP_DIR

  if len(sys.argv) <= 1:
    print "The first argument should be the output directory."
    TMP_DIR = "/tmp"
  else:
    TMP_DIR = sys.argv[1]

  print "tmp_dir: %s" % TMP_DIR

  if True:
    comisii = get_comisii(TMP_DIR)
    print "Found %d comisii\n" % len(comisii)
    if len(comisii) > 0:
      for comisie in comisii:
        com_id = comisie[0]
        #if com_id != '2': continue
        members = get_comisie_details(com_id, TMP_DIR)
        print "\t%d membri" % len(members)
        sedinte = get_meetings(com_id, 2011, TMP_DIR)
        print "\t %d ședințe" % len(sedinte)


main()

