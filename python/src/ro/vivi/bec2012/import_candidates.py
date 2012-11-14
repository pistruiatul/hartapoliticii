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

from datetime import datetime
from dateutil import parser

from bs4 import BeautifulSoup
from HTMLParser import HTMLParseError
from xml.etree.ElementTree import parse

from ro.vivi.hplib import *


COUNTY_LIST = [
  "ALBA1",
  "ARAD",
  "ARGES",
  "BACAU",
  "BIHOR",
  "BISTRITA NASAUD",
  "BOTOSANI",
  "BRASOV",
  "BRAILA",
  "BUZAU",
  "CARAS SEVERIN",
  "CALARASI",
  "CLUJ",
  "CONSTANTA",
  "COVASNA",
  "DAMBOVITA",
  "DOLJ",
  "GALATI",
  "GIURGIU",
  "GORJ",
  "HARGHITA",
  "HUNEDOARA",
  "IALOMITA",
  "IASI",
  "ILFOV",
  "MARAMURES",
  "MEHEDINTI",
  "MURES",
  "NEAMT",
  "OLT",
  "PRAHOVA",
  "SATU MARE",
  "SALAJ",
  "SIBIU",
  "SUCEAVA",
  "TELEORMAN",
  "TIMIS",
  "TULCEA",
  "VASLUI",
  "VALCEA",
  "VRANCEA",
  "BUCURESTI",
  "STRAINATATE"
]

# Given the URL from a becparlamentare2012.ro college description, extract the
# content of the description from the page.
#
# It's quite a shame that these pages were made in Microsoft Word or whatever.
#
def get_candidates_list(party, county, suffix, room):
  url = "http://www.becparlamentare2012.ro/" + party + "%20" + county + suffix

  # And now, catch the exceptions that don't respect the rules and parse
  # these ones too.
  if county == "VRANCEA" and party == "USL" and room == "D":
    url = "http://www.becparlamentare2012.ro/" + party + "%20VRANCEACD.html"

  if county == "CARAS SEVERIN" and party == "UDMR" and room == "S":
    url = "http://www.becparlamentare2012.ro/" + party + "%20CARAS-SEVERIN-S.html"

  if county == "CALARASI" and party == "PRM" and room == "D":
    url = "http://www.becparlamentare2012.ro/" + party + "%20CALARSI-CD.html"

  if county == "CLUJ" and party == "ARD" and room == "D":
    url = "http://www.becparlamentare2012.ro/" + party + "%20BCLUJ-CD.html"

  if county == "IALOMITA" and party == "USL" and room == "S":
    url = "http://www.becparlamentare2012.ro/" + party + "%20IAOMITA-S.html"

  if county == "IALOMITA" and party == "UDMR" and room == "S":
    url = "http://www.becparlamentare2012.ro/" + party + "%20IALOITA-S.html"

  if county == "IALOMITA" and party == "PP" and room == "S":
    url = "http://www.becparlamentare2012.ro/" + party + "%20IAOMITA-S.html"

  data = urllib.urlopen(url)
  if data.getcode() == 404:
    print "---------------------------------------------------"
    print "-- Nothing found at %s " % url
    return None

  try:
    soup = BeautifulSoup(data, "lxml")
  except HTMLParseError:
    return None

  content = soup.findAll('p', {'class': 'MsoNormal'})
  if content is None:
    return None

  result = ''
  college_number = 0

  for p in content:
    text = p.text.strip().replace("\n", " ")
    text = re.sub("([ ]+)", " ", text)
    text = re.sub("\\.", "", text)

    if text == "":
      continue

    if re.match("[0-9]+", text):
      college_number = string.atoi(text)

    if 'Termeni' in text or "CAMERA DEPUTA" in text or "Nr crt" in text or \
        'Nr' in text or 'crt' in text or \
        'Colegiul uninominal' in text or 'Numele ' in text or \
        "SENAT" in text or re.match("[0-9]+", text):
      continue

    text.replace("'", "\'")

    if county == "CARAS-SEVERIN":
      county = "CARAS SEVERIN"

    if county == "CALARSI":
      county = "CALARASI"

    if county == "BCLUJ":
      county = "CLUJ"

    print ('{"county": "%s",'
           ' "party": "%s",'
           ' "room": "%s",'
           ' "college": "%s",'
           ' "name": "%s", '
           ' "source": "%s"},') % (
      county, party, room, college_number, text, url)

  return result


# Returns the list of parties present on a page dedicated to the county
# like http://www.becparlamentare2012.ro/BRAILA.html
def get_parties_from_county_page(url, county):
  data = urllib.urlopen(url)
  if data.getcode() == 404:
    return None

  if county == "ALBA1":
    county = "ALBA"

  if county == "BRASOV":
    county = "Brasov"

  if county == "CLUJ":
    county = "Cluj"

  if county == "COVASNA":
    county = "Covasna"

  if county == "ILFOV":
    county = "Ilfov"

  if county == "TIMIS":
    return ["USL", "ARD", "PPDD", "PRM", "UDMR", "EMNP-PPMT",
            "Candidat independent"]

  content = "".join(data.readlines())

  regexp = ("CD si S ([-A-Za-z ]+) " + county + ".ht").replace(" ", "%20")
  results = re.findall(regexp, content, re.MULTILINE)

  #print ", ".join(results)
  return results


def go_through_counties():
  for county in COUNTY_LIST:
    print "\n"


    if county == "HUNEDOARA":
      url = "http://www.becparlamentare2012.ro/%s.html" % "HUNEADOARA"
    else:
      url = "http://www.becparlamentare2012.ro/%s.html" % county

    parties = get_parties_from_county_page(url, county)

    if county == "ALBA1":
      county = "ALBA"

    if parties is None:
      continue

    for party in parties:
      get_candidates_list(party, county, "-CD.html", "D")
      get_candidates_list(party, county, "-S.html", "S")


go_through_counties()