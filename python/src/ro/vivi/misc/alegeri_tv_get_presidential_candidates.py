'''
Created on Sep 27, 2009

Parses this page http://www.alegeri.tv/alegeri-prezidentiale-2009 to get the
list of candidates to the presidential elections in 2009.

@author: vivi
'''

import re
import urllib


def get_candidates_from_page(data):
  """ Given the page from alegeri.tv with the candidates to be president,
  extract their names and return them.
  """
  parties = re.findall('<h2 class="nume_partid">([^<]*)</h2>', data)
  names = re.findall('<h2 class="nume" (:?[^>]*)>'
                     '<a href="alegeri-prezidentiale-2009/(:?[\w-]*)" >'
                     '<b>([^<]*)</b></a></h2>', data)

  res = []

  for i in range(0, len(parties)):
    print '"' + str(i+1) + '","' + names[i][2] + '","0","' + parties[i] + '","0"'
    res.append(names[i][2] + ", " + parties[i])

  return res

#=========== this is the main part

f = urllib.urlopen('http://www.alegeri.tv/alegeri-prezidentiale-2009')
data = f.read()

names = get_candidates_from_page(data)
print names

# Add these names in a table.