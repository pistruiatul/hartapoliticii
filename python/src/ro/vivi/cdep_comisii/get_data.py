#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Comisii & comisii from cdep.ro

@author: cornel
"""

import re
import datetime, time
import sys
import codecs
import time

import hashlib
import json
import os, getopt
import urllib2, urllib

from urllib2 import URLError
from BeautifulSoup import BeautifulSoup
from sets import Set
#from dateutil.relativedelta import relativedelta

from ro.vivi.hplib import *

import MySQLdb

# page start 
# http://www.cdep.ro/pls/parlam/structura.co

#for each comisii
  # membrii - done
  # TODO - detect page changes
  # http://www.cdep.ro/pls/parlam/structura.co?idc=<ID>

  # sedinte
  # http://www.cdep.ro/co/sedinte.lista?tip=<ID>&an=2012 # 2011, 2010, 2009

  # documente/proiecte
  # http://www.cdep.ro/pls/proiecte/upl_com.lista?idc=<ID>&an=2012

def get_comisii(tmp_dir):
  link = 'http://www.cdep.ro/pls/parlam/structura.co'
  comisii_page = get_page(link, tmp_dir)
  reg_comisii = re.compile('HREF="/pls/parlam/structura\.co\?idc=(\d+)">(.*?)<\/')

  return list(Set(reg_comisii.findall(comisii_page)))

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
      anchors = cells[1].findAll('a')
      if len(anchors) == 0:
        continue

      if link is None:
        continue

      for a in anchors:
        link = a['href']
        docs.append({'name': name, 'link': link})

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
def get_commitee_details(id, tmp_dir):
  try:
    id = int(id)
  except ValueError:
    print " + No details for id %s" % id

  members = []
  link = 'http://www.cdep.ro/pls/parlam/structura.co?idc=%d' % id
  print " + %s" % link
  try:
    comisie_page = get_page(link, tmp_dir)
  except:
    print "  :) skipping for now"
    return members
  
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

  for t in tbs:
    new_members = extract_former_members(t)
    for m in new_members:
      members.append(m)
  
  return members

def get_bin_file(link, tmp_dir):
  """ Fetches the binary file (pdf) at the provided link.  
  """

  _, file_ext = os.path.splitext(link)
  if file_ext == '': file_ext = '.dat'

  # First, see if this is already cached.
  fname = tmp_dir + '/bin_cache/%s%s' % (hashlib.md5(link).hexdigest(), file_ext)
  if os.path.exists(fname):
    return fname

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

      return fname
    except:
      print "Timed out, retrying ", link
      success = False
      time.sleep(2)


def get_documents(meetings, year, tmp_dir):
  reg_pdf = re.compile('\.pdf$', re.I)
  for m in meetings:
    for d in m[1]:
      link = d['link']
      if reg_pdf.search(link, re.I) is None:
        continue

      ## fix damn ș`s
      #link = re.sub(u'\u0219', '&#537;', link)
      if re.match('/', link):
        link = 'http://www.cdep.ro' + re.sub(' ', '%20', link)
      else:
        link = 'http://www.cdep.ro/co/' + re.sub(' ', '%20', link)
      try:
        get_bin_file(link, tmp_dir)
      except:
        print ' :[ skiping file ', link


def get_db():
  db = MySQLdb.Connection(
              host = 'localhost', 
              db   = 'hartapoliticii_pistruiatul',
              user = 'cornel',
              passwd = 'a'
            )
  db.set_character_set('utf8')
  cursor = db.cursor()
  cursor.execute('SET NAMES utf8')
  cursor.execute('SET CHARACTER SET utf8')
  cursor.execute('SET character_set_connection=utf8')
  
  return cursor

def get_db_committees(db):
  sql = "SELECT idcom, name, link FROM committees ORDER BY idcom"
  db.execute(sql)
  return db.fetchall()

def store_committees(db, committees):
  sql = "INSERT INTO committees (idcom, name, link) " \
        + " VALUES(%s, %s, %s)"
 
  for c in committees:
    (idcom, name) = c
    link = 'http://www.cdep.ro/pls/parlam/structura.co?idc=%s' % idcom
    db.execute(sql, (idcom, name, link))

def usage():
  print """
    get_data.py -t tmddir [-y <YEAR=2012>] -a <committees|members|meetings>
  """

def main(argv):
  """ Main function. """
  global TMP_DIR
  global debug

  debug = False

  if len(sys.argv) <= 1:
    print "The first argument should be the output directory."
    TMP_DIR = "/tmp"
  else:
    TMP_DIR = sys.argv[1]

  if len(sys.argv) >= 2:
    debug = True

  for d in [TMP_DIR, "%s/%s" % (TMP_DIR, 'cache'),  "%s/%s" % (TMP_DIR, 'bin_cache')]:
    if (not os.path.exists(d)):
      os.mkdir(d)

  # year we're interested in
  year = '2012'

  committees = get_comisii(TMP_DIR)
  if debug:
    print "Found %d comisii\n" % len(committees)

  #print json.dumps(committees)

  for c in committees:
    idcom, name = c

    # get members of this commitee
    members = get_commitee_details(idcom, TMP_DIR)
    #print json.dumps(members)
    
    #for m in members:
    #  # name, funcție, party, in, out = m
    #  print m[0], m[1]

    # get meetings for the given year
    meetings = get_meetings(idcom, year, TMP_DIR)
    #print json.dumps(meetings)
    # meetings[0] = (<date,ro>, [<doc1>, doc2]), where dict x = {name: $name, link: $link}
    print "\t* %d meetings" % len(meetings)
    
    continue
    get_documents(meetings, year, TMP_DIR)



def main__(argv):
  """ Main function. """
  global TMP_DIR
  global _debug

  actions = ('committees', 'members', 'meetings')

  TMP_DIR = "/tmp"
  _debug = 0
  action = ''

  # year we're interested in
  year = '2012'

  try:                                
    opts, args = getopt.getopt(argv, "ht:y:a:d", ['help', 'tmpdir=', 'year=', 'action='])
  except getopt.GetoptError:
    usage()
    sys.exit(2)

  for opt, arg in opts:
    if opt in ("-h", "--help"):
      usage()
      sys.exit()
    elif opt in ("-t", "--tmpdir"):
      TMP_DIR = arg
    elif opt == '-d':
      _debug = 1
    elif opt in ('-y', '--year'):
      year = arg
    elif opt in ('-a', '--action'):
      action = arg

  if action not in actions:
    print "\n    Error: unkown action [%s]" % action
    usage()
    sys.exit(2)

  if _debug: print opts

  print "tmp_dir: %s" % TMP_DIR
  print "action: %s" % action
  print "year: %s" % year

  db = get_db()

  if action == 'committees':
    committees = get_comisii(TMP_DIR)
    print "Found %d comisii\n" % len(committees)
    store_committees(db, committees)

  comms = get_db_committees(db)

  if action == 'members':
    for c in comms:
      idcom, name, _ = c
      if _debug and idcom != '1': continue
      members = get_commitee_details(idcom, TMP_DIR)
      for m in members:
        # name, funcție, party, in, out = m
        print m[0], m[1]

      continue
      # get meetings for the given year
      meetings = get_meetings(com_id, year, TMP_DIR)
      # meetings[0] = (<date,ro>, [<doc1>, doc2]), where dict x = {name: $name, link: $link}
      print "\t* %d meetings" % len(meetings)
      get_documents(meetings, year, TMP_DIR)

if __name__ == "__main__":
  main(sys.argv[1:])

