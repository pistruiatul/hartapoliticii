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
import hashlib
import codecs
import time

from sets import Set

from urllib2 import URLError
from dateutil.relativedelta import relativedelta

# A few constants used as states at some point.
NONE = 0
START = 1
END = 2

# Where to save all the cache files and the resulting aggregate files. Should
# be specified as a parameter when running this script.
TMP_DIR = '/tmp'


def getFileData(fname):
  """Returns the contents of a random file."""
  f = codecs.open(fname, 'r', 'utf-8')
  data = f.read()
  f.close()
  return data


def removeHtmlTags(data):
  p = re.compile(r'<.*?>')
  return p.sub('', data)


def getPage(link):
  """ Fetches the page at the provided link. Checks whether this is indeed the
  page of a vote. If that is true it returns the page as a string, otherwise
  returns None.

  TODO: This is duplicated from cdep_crawler. We should just somehow move this
  into a common library.
  """

  # First, see if this is already cached.
  fname = TMP_DIR + '/cache/%s.html' % hashlib.md5(link).hexdigest()
  if os.path.exists(fname):
    return getFileData(fname)

  success = False
  while not success:
    try:
      f = urllib2.urlopen(link, None, 20)
      data = unicode(f.read(), 'ISO-8859-2')
      f.close()

      # Write this page into a cached file, with a more common charset.
      cache_file = codecs.open(fname, 'w', 'utf-8')
      cache_file.write(data)
      cache_file.close()

      return data
    except URLError:
      print "Timed out, retrying ", link
      success = False


def getListOfStenogramLinks(page):
  """ Given the contents of the web page with the list of stenograms, return
  the links to all the stenograms referenced on that page.
  """
  reg_steno = re.compile(
    'href="/pls/steno/steno\\.stenograma\\?ids=(\d*)&idm=(?:\d*)&idl=1">')

  return Set(reg_steno.findall(page))


def getDeclarations(page):
  """ Extracts the declarations from the stenogram page.
  """

  # Each declaration is on a single line, right after the comment signaling
  # the start of that declaration, and it looks like this:
  #
  #<!-- START=1702625,1665541,1787098 -->
  #<p align="justify"><B><A HRE....
  #<!-- END -->

  # first of all, split the text in lines.
  lines = page.split("\n")
  print '   = %s lines' % len(lines)

  state = NONE
  current_person = ''

  declarations = []

  # We go through each and every line because the stenogram is in chronological
  # order. Once we record a person, every following START comment that doesn't
  # have a person specified will belong to that person.
  for line in lines:
    if re.search('<!-- START=([,\d]*) -->', line):
      # If this is a start line, state becomes START and we move to the next
      # line.
      state = START
      continue

    if state == START:
      declaration_start = 0

      # We now have a line that's a declaration. First check if it has a person
      # in it.
      persons = re.findall(
          '<font color="#0000FF">(?:Domnul|Doamna) (.*)</font>', line)
      if len(persons) > 0:
        current_person = persons[0]
        declaration_start = line.find('</B>:') + 5

      declaration = line[declaration_start:]

      # And move the state back to NONE
      state = NONE

      # The introduction to the stenogram doesn't belong to anybody.
      if current_person != '':
        declarations.append([current_person, declaration])

  return declarations


def getMaxStenogramId():
  """ Returns the highest stenogram id found on the cdep site.
  """

  # Go through the list of days from today back in time.
  d = datetime.date.today()

  max_steno_id = 0
  while not max_steno_id:
    link = ('http://www.cdep.ro/pls/steno/steno.data?cam=2&dat=%s&idl=1' %
            d.strftime('%Y%m%d'))

    print ' + fetching %s' % link
    page = getPage(link)

    steno_numbers = getListOfStenogramLinks(page)
    if len(steno_numbers) > 0:
      max_steno_id = max(steno_numbers)

    d = d - relativedelta(d, days = 1)

  return max_steno_id


def getDateFromStenoPage(page):
  """ Given a stenogram page, find out the date
  """
  # The date on the page will look like this '&gt; 28-02-2012'
  d = re.findall('&gt; (\d\d)-(\d\d)-(\d\d\d\d)', page)
  if not len(d):
    return None

  return datetime.datetime(int(d[0][2]), int(d[0][1]), int(d[0][0]))


def main():
  """ Main function. """
  global TMP_DIR

  if len(sys.argv) <= 1:
    print "The first argument should be the output directory."
    sys.exit(1)
  else:
    TMP_DIR = sys.argv[1]
    print TMP_DIR

  max_steno_id = getMaxStenogramId()

  # Now that we have the range of stenogram numbers, let's crawl them and
  # extract useful info from them.
  out = codecs.open(TMP_DIR + '/declaratii_agg.txt', 'w', 'utf-8')

  for steno_id in range(1, int(max_steno_id)):
    link = 'http://www.cdep.ro/pls/steno/steno.stenograma?ids=%s' % steno_id

    steno_page = getPage(link)
    print ' + %s' % link

    date = getDateFromStenoPage(steno_page)
    if date is None:
      # It means this was an empty page that didn't even have a date.
      continue

    # Now that we've got the stenogram page, the hard part begins.
    declarations = getDeclarations(steno_page)
    print '   = %s declarations' % len(declarations)

    # Write these into a file.
    for declaration in declarations:
      # Write the link, person, time and declaration.
      out.write('link: %s\n' % link)
      out.write('time: %d\n' % time.mktime(date.timetuple()))
      out.write('person: %s\n' % declaration[0])
      out.write('declaration: %s\n' % declaration[1])

  out.close()


main()
