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


def main():
  """ Main function. """
  if len(sys.argv) <= 1:
    print "The first argument should be the output directory."
    sys.exit(1)

  # Go through the list of days from today back in time and save the ones
  # that are lists.
  d = datetime.date.today()




main()
