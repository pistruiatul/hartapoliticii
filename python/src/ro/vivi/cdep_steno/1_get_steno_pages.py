"""
Created on March 5, 2012

Downloads all the pages that have lists of stenograms or political declarations
from the cdep.ro site. These lists will cover both the senate and cdep because
all the stenograms are hosted on the cdep site for both.

@author: vivi
"""

import re
import os
import datetime
import sys
import urllib2

from urllib2 import URLError
from dateutil.relativedelta import relativedelta

def getPage(link):
  """ Fetches the page at the provided link. Checks whether this is indeed the
  page of a vote. If that is true it returns the page as a string, otherwise
  returns None.

  TODO: This is duplicated from cdep_crawler. We should just somehow move this
  into a common library.
  """
  success = False
  data = ""

  while not success:
    try:
      f = urllib2.urlopen(link, None, 20)
      data = unicode(f.read(), 'ISO-8859-2')
      f.close()
      success = True
    except URLError:
      print "Timed out, retrying ", link
      success = False
  return data


def getListOfStenogramLinks(page):
  """ Given the contents of the web page with the list of stenograms, return
  the links to all the stenograms referenced on that page.
  """
  reg_steno = re.compile(
    'href="(/pls/steno/steno\\.stenograma\\?ids=(?:\d*)&idm=(?:\d*)&idl=1)">')
  links = reg_steno.findall(page)

  return links


def main():
  """ Main function. """
  if len(sys.argv) <= 1:
    print "The first argument should be the output directory."
    sys.exit(1)

  # Go through the list of days from today back in time and save the ones
  # that are lists.
  d = datetime.date.today()

  # Should go back until 2008 or something, now just go back 500 steps
  steps = 20
  while steps > 0:
    link = ('http://www.cdep.ro/pls/steno/steno.data?cam=2&dat=%s&idl=1' %
            d.strftime('%Y%m%d'))

    print ' + fetching %s' % link
    page = getPage(link)

    stenograms = getListOfStenogramLinks(page)
    print '   - %s stenograms' % len(stenograms)
    for link in stenograms:
      steno_page = getPage('http://www.cdep.ro' + link)
      print '   : %s' % link



    d = d - relativedelta(d, days = 1)
    steps -= 1



main()
