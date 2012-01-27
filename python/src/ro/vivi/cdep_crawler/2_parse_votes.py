# -*- coding: UTF-8 -*-
'''
Created on Aug 28, 2009

STEP 3

From the files that have the laws that were voted on and the files that have the
details of each vote, parse them and simplify them, getting the list of people
and what they each voted on.

We should produce a file that contains the following

  75e2b7b3-bf0c-44c5-82b3-4beaae491b88
  02-02-2009 17:10
  L605/2008| - vot final
  http://webapp.senat.ro/sergiusenat.proiect.asp?cod=13520&pos=0&NR=L605&AN=2008
  Proiect de lege pentru ratificarea celei de-al doilea amendament
  Panturu Tudor
  PD-L
  DA
  Necula Marius-Gerard
  PD-L
  DA
  ...

@author: vivi
'''

import codecs
import hashlib
import os
import re
import sys
import urllib2
from urllib2 import URLError

def get_link_data(link):
  """Fetches the link and returns it if not cached. Also caches the link.

  NOTE: This for now is tailored for the cdep site because of the weird encoding
  that site is using, which for now is hardcoded.
  """
  if not os.path.exists(work_dir + '/cache'):
    os.mkdir(work_dir + '/cache')

  m = hashlib.md5()
  m.update(link)
  fname = work_dir + '/cache/' + m.hexdigest()

  if os.path.exists(fname):
    return get_file_data(fname)
  else:
    success = False
    while not success:
      try:
        # Grab the link, and store it in the file
        f = urllib2.urlopen(law_link, None, 20)
        encoding = f.headers['content-type'].split('charset=')[-1]
        data = unicode(f.read(), encoding)
        f.close()
        success = True
      except URLError:
        print "Timed out, retrying ", law_link
        success = False

    w = codecs.open(fname, 'w', 'utf-8')
    w.write(data)
    w.close()

    return data


def get_file_data(fname):
  """Returns the contents of a random file."""
  f = codecs.open(fname, 'r', 'utf-8')
  data = f.read()
  f.close()
  return data


def get_vote_time(data):
  """ Returns the time that the vote occured """
  date = re.compile('/pls/steno/evot\\.data\\?dat=(?:\d+)&cam=[20]&idl=1">'
                    '(\d+)\\.(\d+)\\.(\d+)</A> &gt; (\d+):(\d+)')
  m = date.findall(data)[0]
  # I want to return DD-MM-YYYY hh:mm, which is perfect, same order.
  return '%s-%s-%s %s:%s' % (m[0], m[1], m[2], m[3], m[4])


def get_vote_type(data):
  """ Gets the type of vote as described on the page.
  On the page it it looks something like this:
    Subiect vot:</td><td><b>
    Vot final
    - Proiectul de Hotărâre privind Bugetul Camerei Deputaţilor pe anul 2011
  """
  r = re.compile('Subiect vot:</td><td><b>\n([^\n]*)\n', re.MULTILINE)
  m = r.search(data)
  return m.group(1)


def get_vote_link(fname):
  """Given a filename, returns the link that this vote was found at.
  link = 'http://www.cdep.ro/pls/steno/evot.nominal?idv=%d&idl=1' % vote_id
  """
  print fname
  vote_id = re.search('vote_(\d+)\\.html', fname).group(1)
  return 'http://www.cdep.ro/pls/steno/evot.nominal?idv=%s&idl=1' % vote_id


def get_law_link(data):
  """Returns the link to the law that this vote was about.

  The url looks like this: /pls/proiecte/upl_pck.proiect?idp=10576

  """
  r = re.compile('/pls/proiecte/upl_pck\\.proiect\\?idp=(\d+)')
  m = r.search(data)
  if m is not None:
    return 'http://www.cdep.ro' + m.group(0)
  else:
    return "None"


def get_law_title(law_link):
  """ Tries to fetch the title of the law from the law's page.

  The title should be all on one line, like this:
  <td class="headline" width="100%">PL-x n...009<br>Proiect d...te</td>

  """
  if law_link == 'None':
    return 'None', 'None'

  data = get_link_data(law_link)

  r = re.compile(('<td class="headline" width="100%">'
                  '(?:[\w\\. -]+) (\d+)/(\d+)<br>([^<]*)</td>'), re.UNICODE)
  m = r.search(data)
  if m is None:
    return 'None', 'None'
  else:
    return m.group(1) + u'/' + m.group(2), m.group(3)


def get_votes_from_file(data):
  """ Given a file with a vote crawled from the parliament website, fetch the
  list of people and what they voted.

  A line with a voting detail looks like this:
  <tr valign="top">
  <td nowrap align="right">5.</td>
  <td><A HREF="/pls/parlam/structura.mp?idm=303&cam=2&leg=2008">Name</A></td>
  <td align="center">PD-L</td>
  <td align="center">
  DA
  </td>
  """
  # Stupid UTF, I should figure out how to write this regular expression.
  all_present = re.findall(
      'prezen(?:[^:]*):</td><td align="center"><b>(\d+)</td>', data)

  votes_reg = re.compile(
      ('<td nowrap align="right">(?:\d+).</td>\n'
       '<td><A HREF="/pls/parlam/structura\\.mp\\?'
           'idm=(\d+)&cam=2&leg=2008">([^<]+)</A></td>\n'
       '(?:<td align="center">deputat</td>\n)?'
       '<td align="center">([^<]+)</td>\n'
       '<td align="center">\n'
       '([^<]+)\n'
       '</td>'), re.MULTILINE)
  votes = votes_reg.findall(data)

  if len(votes) != int(all_present[0]):
    # TODO(vivi): throw some error here, for when this will run automatically
    # so I can see it easily.
    print 'ERROR! My count of votes doesn\'t match theirs'
    print 'ERROR: I counted ', len(votes), ', they say ', int(all_present[0])
    print votes

  result = []
  for vote in votes:
    name = vote[1]
    v = vote[3]  # DA NU Ab -
    if v[0] == 'A':
      v = 'Ab'
    party = vote[2]
    if party == '-':
      party = 'Independent'

    result.append([name, party, v])

  return result


# ===========================
# The main method for now, do not rely on it.

if len(sys.argv) <= 2:
  print "Usage: $ python 2_parse_votes.py work_dir output_file"
  sys.exit(1)

work_dir = sys.argv[1]
if not os.path.exists(work_dir):
  os.mkdir(work_dir)
outfile = sys.argv[2]

print '== Step 2 in the pipeline, vote_* files -> votes_agg.txt'

out = codecs.open(outfile, 'w', 'utf-8')

def vote_file(x): return x.startswith('vote_')
files = filter(vote_file, os.listdir(work_dir + '/pages'))

for fname in files:
  data = get_file_data(work_dir + '/pages/' + fname)
  if data == '':
    continue
  print "Working on:", fname

  votes = get_votes_from_file(data)
  print " + Votes: ", len(votes)

  time = get_vote_time(data)
  type = get_vote_type(data)
  print " + Time: ", time, type

  law_link = get_law_link(data)
  (law_num, law_title) = get_law_title(law_link)
  print " + Law:", law_link, '/', law_num, law_title

  out.write('vote_link:' + get_vote_link(fname) + "\n")
  out.write('vote_time:' + time + "\n")
  out.write('vote_type:' + type + "\n")
  out.write('law_link:' + law_link + "\n")
  out.write('law_num:' + law_num + "\n")
  out.write('law_desc:')
  out.write(law_title)
  out.write("\n")
  for vote in votes:
    # [name, party, vote]
    out.write('v_name:' + vote[0] + "\n")
    out.write('v_party:' + vote[1] + "\n")
    out.write('v_vote:' + vote[2] + "\n")
  out.write("vote_end: ---------------- # end vote\n")


print '-- done.'
out.close()
