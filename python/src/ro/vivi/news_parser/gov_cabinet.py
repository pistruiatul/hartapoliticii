'''
Created on Sep 29, 2009

Parses the gov.ro and gets the prime minister's cabinet list.

@author: vivi
'''

import re
import urllib

def get_post_from_page(link):
  url = urllib.urlopen(link)
  data = url.read()

  return re.findall('<meta name="description" content="([^"]*)" />', data)[0]


def add_person_to_db(name, title, link):
  data = {
    'name': name,
    'title': title,
    'link': link,
  }

  #urllib.urlopen('http://www.hartapoliticii.ro/api/set_gov_ro_guy.php',
  #               urllib.urlencode(data))
  # Keep the local version up to date too.
  urllib.urlopen('http://localhost/politica/api/set_govro_guy.php',
                 urllib.urlencode(data))


#============= main section is here

url = urllib.urlopen('http://www.gov.ro/cabinet__c7l1p1.html')
data = url.read()

pattern = '<h5><a href="([^"]*)" title="([^"]*)" class="ministere">([^"]*)</a></h5>'

all = re.findall(pattern, data)
for match in all:
  name = match[1]
  link = 'http://www.gov.ro' + match[0]
  title = get_post_from_page(link)

  print name, title, link
  add_person_to_db(name, title, link)

print "Done: " + str(len(all))