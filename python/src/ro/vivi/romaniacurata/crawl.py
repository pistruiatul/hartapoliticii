# coding=utf8

'''
Created on November 1st, 2012

Go through the definitions of electoral colleges from becparlamentare2012.ro
and extract the definition for each of the colleges.

@author: vivi
'''

import urllib
import string
import re
import sys

from datetime import datetime
from dateutil import parser

from bs4 import BeautifulSoup
from HTMLParser import HTMLParseError
from xml.etree.ElementTree import parse

from ro.vivi.hplib import *

import codecs

accepted_categories = [
  6,  # Colaborarea cu fosta Securitate
  7,  # Funcțiile deținute în aparatul comunist
  9,  # Traseismul, migrația de la un partid la altul
  10, # Nepotismul, gradul de rudenie al candidatului cu alți politicieni
      # din același partid
  11, # Averea candidaților
  12, # Afacerile și contractele cu statul
  13, # Datoriile firmelor candidaților către stat
  14, # Conflictele de interese și incompatibilitate
  15, # Condamnări și arestări
  16, # Calitatea de sponsori ai partidelor politice
  17, # Atitudini rasiste și discriminatoare
]

def main():
  file = codecs.open(sys.argv[1], "w", "utf-8")

  for i in range(1, 9999):
    url = "http://verificaintegritatea.romaniacurata.ro/?p=%s" % i
    data = get_page(url, "/work/tmp/romaniacurata", "UTF-8", True)

    try:
      soup = BeautifulSoup(data, "lxml")
    except HTMLParseError:
      print url, "-- Failed to parse HTML data"
      continue

    name = soup.find('h1', {'class': 'entry-title entry-title-single'})
    if name is None:
      print url, "-- no name found!"
      continue

    category = soup.find('span', {'class': 'cat-links'})
    category_name = ""

    if category is None:
      print url, "--", "no category found"
      continue

    if category:
      link = category.find('a')
      skip = True

      for cat_id in accepted_categories:
        if link['href'].endswith("cat=%s" % cat_id):
          skip = False

      if skip:
        print url, "--", link['href'], link.contents, "Not an accepted category"
        continue

      category_name = link.contents

    # Now let's find the content.
    entry_content = soup.find('div', {'class': 'entry-content'})
    paragraphs = entry_content.next.findNextSiblings('p')

    # find where the content starts.
    start_index = 0
    for index, p in enumerate(paragraphs):
      if p.text.startswith("Alian"):
        start_index = index + 1

    if not start_index:
      # print url, "--", "We could not find the index where the content starts!"
      # print "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
      sys.exit()

    content = []
    for p in paragraphs[start_index:]:
      if p.text.strip() != "":
        content.append(p.text.strip())

    context = []
    for p in paragraphs[0:start_index]:
      if p.text.strip() != "":
        context.append(p.text.strip().replace("\n", " "))

    file.write("name=   %s\n" % name.text)
    file.write("context=%s\n" % ", ".join(context))
    file.write("source= %s\n" % url)
    file.write("cat=    %s\n" % category_name[0])
    file.write("\n".join(content) + "\n")

    print url, name.text
  file.close()

main()