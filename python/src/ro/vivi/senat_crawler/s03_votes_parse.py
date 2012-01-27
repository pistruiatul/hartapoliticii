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
import re
import sys

def get_data(fname):
  f = codecs.open(fname, "r", "utf-8")
  data = f.read()
  f.close()
  return data


def get_vote_time(data):
  """ Returns the time that the vote occured """
  time = re.findall('<br />((\d+)-(\d+)-(\d+) (\d+):(\d+))</span>', data)[0][0]
  return time


def get_vote_type(data):
  """ Gets the type of vote as described on the page. This is not very
  consistent but we do know for a fact that final votes will contain the
  substring 'vot final'.
  """
  regexp = 'DESCRIERE_LUNGALabel"><a (?:[^>]*)>(?:(?:[^<]*)\|)?([^<]*)</a>'
  if not re.search(regexp, data):
    return None
  return re.findall(regexp, data)[0].strip()


def get_law_title(data):
  """ Returns the title of a law from a law file. The title will be in something
  like this:
  <P class="tTitlu" >
   ...
  </P>
  NOTE: The law file is different from the vote file.
  """
  # AccordionPane3_content_grdTitlu_ctl02_Label1
  # AccordionPane2_content_grdLista_ctl02_Label2
  return re.findall(
      '<span id="AccordionPane3_content_grdTitlu_ctl02_Label1">([^<]*)</span>', data)[0].strip()

def get_votes_from_file(data):
  """ Given a file with a vote crawled from the parliament website, fetch the
  list of people and what they voted.

  A line with a voting detail looks like this:
    <td><font color="#284775">Dobra</font></td>
    <td><font color="#284775">Nicolae</font></td>
    <td><font color="#284775">(1) PD-L</font></td>
    <td><font color="#284775">X</font></td>
    <td><font color="#284775">&nbsp;</font></td>
    <td><font color="#284775">&nbsp;</font></td>
    <td><font color="#284775">&nbsp;</font></td>
  """
  # Stupid UTF, I should figure out how to write this regular expression.
  all_present = re.findall('Prezen(?:[^:]*): (\d+);', data)
  votes = re.findall(7 * '<td><font color="#(?:\d+)">([^<]*)</font></td>', data)

  if len(votes) != int(all_present[0]):
    # TODO(vivi): throw some error here, for when this will run automatically
    # so I can see it easily.
    print 'ERROR! My count of votes doesn\'t match theirs'

  result = []
  for vote in votes:
    name = vote[0] + ' ' + vote[1]

    none = '&nbsp;'
    if vote[3] != none: v = 'DA'
    elif vote[4] != none: v = 'NU'
    elif vote[5] != none: v = 'Ab'
    else: v = '-'

    party = vote[2]
    if party == none:
      party = ''
    else:
      party = vote[2]

    result.append([name, party, v])

  return result


# ===========================
# The main method for now, do not rely on it.

print '== Step 3 in the pipeline, vote_* files -> votes_agg.txt'

if len(sys.argv) <= 2:
  print "Usage: python s03_votes_parse.py input_dir output_file"
  sys.exit(1)

input_dir = sys.argv[1]
output_file = sys.argv[2]

f = open(input_dir + '/pages/vote_META.txt')
lines = f.read().split('\n')
f.close()

out = codecs.open(output_file, 'w', 'utf-8')

for line in lines:
  if line == '':
    break
  # Get the line with the law, the vote and the filename
  (law_link, law_file, vote_link, vote_file) = line.split(' ')
  # Get the name of the law?
  vote_id = re.findall('http://www\.senat\.ro/VoturiPlenDetaliu\.aspx\?'
                       'AppID=(.*)', vote_link)[0]

  data = get_data(vote_file)

  votes = get_votes_from_file(data)
  time = get_vote_time(data)
  type = get_vote_type(data)

  print " + vote file: ", vote_file, time

  # If we could not find a type, just return None.
  if type is None:
    print " -- skipped, type returned None"
    continue

  data = get_data(law_file)
  law_title = get_law_title(data)

  out.write('vote_link:' + vote_link + "\n")
  out.write('vote_time:' + time + "\n")
  out.write('vote_type:' + type + "\n")
  out.write('law_link:' + law_link + "\n")
  out.write('law_desc:' + law_title + "\n")
  for vote in votes:
    # [name, party, vote]
    out.write('v_name:' + vote[0] + "\n")
    out.write('v_party:' + vote[1] + "\n")
    out.write('v_vote:' + vote[2] + "\n")
  out.write("vote_end: ---------------- # end vote\n")

print '-- done.'
out.close()
