"""
Created on Jan 18, 2011

Downloads all the pages with the votes. For cdep, fortunately the votes are
indexed by ID, http://www.cdep.ro/pls/steno/evot.nominal?idv=4829&idl=1.

The link to the law that is under voting is on the page of the vote, so we'll
just crawl that in a second step of this pipeline.

@author: vivi
"""

import codecs
import datetime
from dateutil.relativedelta import relativedelta
import re
import os
import sys
import urllib2
from urllib2 import URLError

def getMostRecentVoteId():
  """By walking back in time from today, get the most recent vote id."""
  # http://www.cdep.ro/pls/steno/eVot.Data?dat=20101012&cam=2&idl=1
  d = datetime.date.today()
  vote_id = -1
  while vote_id is -1:
    # Fetch the page with the date.
    link = ('http://www.cdep.ro/pls/steno/eVot.Data?cam=2&idl=1&dat=' +
            d.strftime("%Y%m%d"))

    success = False
    while not success:
      try:
        f = urllib2.urlopen(link, None, 20)
        data = f.read()
        f.close()
        success = True
      except URLError:
        print "Timed out, retrying ", link
        success = False

    print "Fetching ", link

    # Find all the matches of votes, and get the max.
    votes = re.findall('pls/steno/evot\\.nominal\\?idv=([\d]+)&idl=1', data)
    if len(votes) > 0:
      # Found it! Now get the max.
      return max(votes)
    else:
      d = d - relativedelta(d, days = 1)


def getPageIfVote(link):
  """ Fetches the page at the provided link. Checks whether this is indeed the
  page of a vote. If that is true it returns the page as a string, otherwise
  returns None.
  """
  data = getPage(link)

  date_headline = '<span class="headline">([^,]+), ([^,]*), ([\w\d: ]+)</span>'
  if re.search(date_headline, data) is not None:
    return data

  return None


def getPage(link):
  """ Fetches the page at the provided link. Checks whether this is indeed the
  page of a vote. If that is true it returns the page as a string, otherwise
  returns None.
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


def process_vote(vote_id, outdir):
  link = 'http://www.cdep.ro/pls/steno/evot.nominal?idv=%d&idl=1' % vote_id
  fname = outdir + '/pages/vote_%d.html' % vote_id

  if os.path.exists(fname):
    print "Cache hit ", fname
    return

  data = getPageIfVote(link)
  f = codecs.open(fname, 'w', 'utf-8')
  if data is not None:
    f.write(data)
  else:
    f.write('')
  f.close()

  print "Wrote down ", link, os.path.getsize(fname)


# -------------- Main method ----------------

if len(sys.argv) <= 1:
  print "The first argument should be the output directory."
  sys.exit(1)

# Go through all the ids starting with 4829 (the first vote on February 4th 2009
# from this session. Stop when you don't get anything anymore.
vote_id = 4828
last_vote_id = int(getMostRecentVoteId())
print "Max vote id:", last_vote_id

outdir = sys.argv[1]
if not os.path.exists(outdir + '/pages'):
  os.mkdir(outdir + '/pages')

# If the vote id is specified just get this one, otherwise get all.
if len(sys.argv) > 2:
  VOTE_ID = int(sys.argv[2])
  os.remove(outdir + '/pages/vote_%d.html' % VOTE_ID)
  process_vote(VOTE_ID, outdir)

else:
  while vote_id < last_vote_id:
    vote_id += 1
    process_vote(vote_id, outdir)

print "Done!"


