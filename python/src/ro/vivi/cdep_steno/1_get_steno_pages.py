"""
Created on March 5, 2012

Downloads all the pages that have lists of stenograms or political declarations
from the cdep.ro site. These lists will cover both the senate and cdep because
all the stenograms are hosted on the cdep site for both.

@author: vivi
"""

import re
import datetime
import sys
import codecs
import time

from sets import Set
from dateutil.relativedelta import relativedelta

from ro.vivi.hplib import *

# A few constants used as states at some point.
NONE = 0
START = 1
END = 2

# Where to save all the cache files and the resulting aggregate files. Should
# be specified as a parameter when running this script.
TMP_DIR = '/tmp'


def remove_html_tags(data):
  p = re.compile(r'<.*?>')
  return p.sub('', data)



def get_list_of_stenogram_links(page):
  """ Given the contents of the web page with the list of stenograms, return
  the links to all the stenograms referenced on that page.
  """
  reg_steno = re.compile(
    'href="/pls/steno/steno\\.stenograma\\?ids=(\d*)&idm=(?:\d*)&idl=1">')

  return Set(reg_steno.findall(page))


def get_declarations(page):
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
          '<font color="#0000FF">'
          '(?:Domnul|Doamna) ([^:]*)(?: \([^\)]+\))?(?:[:])?(?:[ ]*)</font>',
          line)
      if len(persons) > 0:
        current_person = persons[0]
        declaration_start = line.find('</B>') + 4

      declaration = line[declaration_start:]

      # And move the state back to NONE
      state = NONE

      # The introduction to the stenogram doesn't belong to anybody.
      if current_person != '':
        declarations.append([current_person, declaration])

  return declarations


def get_max_stenogram_id():
  """ Returns the highest stenogram id found on the cdep site.
  """

  # Go through the list of days from today back in time.
  d = datetime.date.today()

  max_steno_id = 0
  while not max_steno_id:
    link = ('http://www.cdep.ro/pls/steno/steno.data?cam=2&dat=%s&idl=1' %
            d.strftime('%Y%m%d'))

    print ' + fetching %s' % link
    page = get_page(link)

    steno_numbers = get_list_of_stenogram_links(page)
    if len(steno_numbers) > 0:
      max_steno_id = max(steno_numbers)

    d = d - relativedelta(d, days = 1)

  return max_steno_id


def get_date_from_steno_page(page):
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

  max_steno_id = get_max_stenogram_id()

  for steno_id in range(1, int(max_steno_id)):
    if steno_id in [5603, 7073]:
      # For whatever reason, cdep is consistently returning a 404 for these
      # stenogram ids.
      continue

    link = 'http://www.cdep.ro/pls/steno/steno.stenograma?ids=%s' % steno_id

    steno_page = get_page(link, TMP_DIR)

    date = get_date_from_steno_page(steno_page)
    if date is None:
      # It means this was an empty page that didn't even have a date.
      continue

    # Now that we've got the stenogram page, the hard part begins.
    declarations = get_declarations(steno_page)
    print '   = %s declarations in %s' % (len(declarations), link)

    if not len(declarations):
      continue

    # Write each stenogram in a separate file so it's easier for us to parse
    # later on.
    out = codecs.open(TMP_DIR + '/stenos/steno_%05d.txt' % steno_id, 'w',
                      'utf-8')

    # The very first line of every file is the link itself so that when we
    # process it we can easily check if we've already processed this file.
    out.write(link + '\n')

    # Write these into a file.
    for declaration in declarations:
      # Write the link, person, time and declaration.
      out.write('time: %d\n' % time.mktime(date.timetuple()))
      out.write('person: %s\n' % declaration[0])
      out.write('declaration: %s\n' % declaration[1])

    out.close()


main()
