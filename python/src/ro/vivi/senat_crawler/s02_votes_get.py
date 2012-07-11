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

from xml.etree import ElementTree as parse

def get_votes_from_file(fname):
  """ Crawls all the pages with votes on them (the daily stuff produced at the
  first step in the pipeline) and produces a list of tuples with the links to
  the law and the link to the page with the details of the vote.
  """
  f = open(outdir + '/pages/' + fname)
  page = f.read()
  f.close()
  
  # This page has h4 html tags that are not closed. 
  # This causes the parser to fail. There is no need for h4 tags. Remove all
  strings_to_remove_list = ["<h4>", "</h4>", "&nbsp;", 
                            " xmlns=\"http://www.w3.org/1999/xhtml\""]
  for string_to_remove in strings_to_remove_list:
      page = page.replace(string_to_remove, "")
  
  html_page = parse.fromstring(page)
  
  # Get all table rows
  tr_tag_list = html_page.findall("body/form/div/table/tr")
  
  if len(tr_tag_list) > 0:
    print "Working on ", fname, ", laws: ", len(tr_tag_list)

  results = []
  # Work trough each row separately
  for tr_tag in tr_tag_list:
    # Get the table data from the current row.
    td_tag_list = tr_tag.findall("td")
    
    if td_tag_list:
      # Only the second and third columns are interesting: 
      # "Denumire"(law) and "Descriere"(vote)
      law_anchor_attributes = td_tag_list[1].find("font/a").attrib
      vote_anchor_attributes = td_tag_list[2].find("font/a").attrib
      
      law_link = ""
      vote_link = ""

      if "href" in law_anchor_attributes:
        law_link = 'http://www.senat.ro/' + law_anchor_attributes['href']\
          .replace('&amp;', '&')
      if "href" in vote_anchor_attributes:
        vote_link = 'http://www.senat.ro/' + vote_anchor_attributes['href']\
          .replace("./VoturiPlenDetaliu.aspx?AppID=", 
                   "VoturiPlenDetaliu.aspx?AppID=")
        
      if law_link and vote_link:
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
