'''
Created on Aug 28, 2009

Goes over all the files under pages/day_*.html and
 - extracts the URL's for the pages with the laws into law_*.html
 - extracts the URL's for the pages with votes into vote_*.html

This code also produces a file that associates the two URL's together, the law
url and the vote URL so that the next step knows what to make of the two.

This step generates teh vote_META.txt, where on each line we have these 4 fields
  law_link law_file vote_link vote_file
separated by space.

This is the second step in the pipeline.

@author: vivi
'''

import os
import re
import md5
import urllib2
import sys

def get_votes_from_file(fname):
  """ Crawls all the pages with votes on them (the daily stuff produced at the
  first step in the pipeline) and produces a list of tuples with the links to
  the law and the link to the page with the details of the vote.
  """
  f = open(outdir + '/pages/' + fname)
  page = f.read()
  f.close()

  # Finds all the links to the law pages. These are the pages under the
  # "Denumire VOT" column, but that's wrongly named.
  # NOTE: This is the old regular expression and i'll keep it here, just
  # because I love the 'sergiusenat' format. :-)
  #
  #laws = re.findall('(sergiusenat\\.proiect\\.asp\\?cod=(\d*)&amp;pos=(\d*)'
  #    '&amp;NR=([\w\d]*)&amp;AN=(\d*))" target="_blank">([^<]*)</a>',
  #    page)

  # <a href="Legis/Lista.aspx?cod=13816&amp;pos=0&amp;NR=L661&amp;AN=2008"
  # target="_blank">L661/2008| - vot final</a>
  laws = re.findall('(Legis/Lista\\.aspx\\?cod=(\d*)&amp;pos=(\d*)&amp;'
      'NR=([\w\d]*)&amp;AN=(\d*))" target="_blank">([^<]*)</a>', page)

  votes = re.findall('(VoturiPlenDetaliu\\.aspx\\?AppID=([\w-]*))" '
      'target="_blank">([^<]*)<', page)

  if len(laws) > 0:
    print "Working on ", fname, ", laws: ", len(laws)

  results = []
  for index in range(len(laws)):
    law_link = 'http://www.senat.ro/' + laws[index][0].replace('&amp;', '&')
    vote_link = 'http://www.senat.ro/' + votes[index][0]
    results.append([law_link, vote_link])

  return results


def crawl_voting_and_laws_pages(votes):
  """ Given the tuples of law/vote page URL's, it goes ahead and crawls those
  pages and saves them on disk. It then appends the information in the META file
  """
  meta = open(outdir + '/pages/vote_META.txt', 'w')
  for vote in votes:
    (law_link, vote_link) = vote
    if len(re.findall('cod=(\d+)&', law_link)) > 0:
      law_fname = fetch_page(law_link)
      #print law_link, "  =  ", law_fname
      vote_fname = fetch_page(vote_link)
      #print vote_link, "  =  ", vote_fname
      # append_meta_info(law_link, vote_link)
      meta.write(' '.join([law_link, law_fname, vote_link, vote_fname]))
      meta.write('\n')
  meta.close()


def fetch_page(link):
  """ Fetches the page at the given link and stores it on disk. If the page is
  already on disk, we don't fetch it anymore.
  """
  fname = outdir + '/pages/vote_' + md5.new(link).hexdigest() + '.html'
  print 'Loading ', link, 'into', fname
  if os.path.exists(fname):
    # If the file exists already, just get it from disk.
    f = open(fname)
    data = f.read()
    f.close()
  else:
    # If the file is not on disk, read it from the URL and write it back.
    f = urllib2.urlopen(link)
    data = f.read()
    f.close()

    f = open(fname, "w")
    f.write(data)
    f.close()

  return fname


# ===========================
# The main method for now, do not rely on it.

if len(sys.argv) <= 1:
  print "The first argument should be the output directory."
  sys.exit(1)

outdir = sys.argv[1]
if not os.path.exists(outdir + '/pages'):
  os.mkdir(outdir + '/pages')

print '== Step 2 in the pipeline, day_* files -> vote_* files.'

def day_file(x): return x.startswith('day_')
files = filter(day_file, os.listdir(outdir + '/pages'))

# Get the tuples for links between
# - laws that have been voted on
#   http://webapp.senat.ro/sergiusenat.proiect.asp?cod=14053&pos=0&NR=L86&AN=2009
# - the actual voting page
#   http://www.senat.ro/VoturiPlenDetaliu.aspx?AppID=bb1aac71-854f-4090-a466-31d710cc91fa
votes = []
for fname in files:
  votes = votes + get_votes_from_file(fname)

crawl_voting_and_laws_pages(votes)
print "Done!"
