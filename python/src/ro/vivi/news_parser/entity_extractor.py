# -*- coding: utf-8 -*-

'''
Created on Sep 24, 2009

Parse the RSS feed from mediafax, gets the links from it and then fetches the
pages with the news.

The code:
  - gets the rss feed from mediafax. That contains about the past 10 days.
  - gets out the links and fetches the news content.
  - saves the meta information plus the real content in a file for each day.

This script should run at least once every 5 days because it only gets and
outputs the news for these days so I don't have to check any incremental
addition, I can safely rewrite the past 5 days of news.

@author: vivi
'''

import re
import os
import urllib
import time
import sys
from datetime import datetime
from xml.etree.ElementTree import parse

from text_utils import strip_tags_and_new_lines
from ro.vivi.hplib import *

NUMBER_OF_DAYS_TO_PARSE = 3

# This has caused a problem when I used this script with the default, assuming
# it's mediafax, but instead it was hotnews. Never use this script without a
# parameter, unless it's for development purposes.
SOURCE = 'src/ro/vivi/pistruiatul/mediafax'
if len(sys.argv) > 1:
  SOURCE = sys.argv[1]

arr = SOURCE.split("/")
SOURCE_NAME = arr[len(arr) - 1]

def is_not_a_daily(file):
  return not file.find('daily_')


# A list of common capitalized words (that are associated with the name of a
# person)
common_capitalized_words = [
  "Deputatul", "Senatorul", "Europarlamentarul", "Preşedintele", "Premierul",
  "Primarul", "Domnişoara", "Domnul", "Agriculturii", "Finanţelor", "Călăraşi",
  "Interne", "Ministerul", "Educaţiei", "Palatul", "Consiliului", "Naţional",
  "Camera", "Deputaţilor", "Parlamentului", "Drepturilor", "Omului", "Târgu",
  "Jiu", "Coloana", "Infinitului", "Băncii", "Centrale", "HotNews",
  "Departamentul", "Bucuresti", "Preşedinţie"
]


def get_names_from_text(data):
  """ Given a text of data, attempts to find all the potential names. We define
  as potential names continuous groups of capitalized words, not separated by
  punctuation.
  """
  data = strip_tags_and_new_lines(data)
  # Transforms all the punctuation into dots so I can catch them as being
  # between capitalized names.
  data = re.sub("[!*#='|.,;\"\\(\\):\\?}{]", " . ", data)

  # Delete the 'read also' links.
  if data.find('Citeşte şi') > 0:
    data = data[0 : data.find('Citeşte şi')]

  words = data.split(" ")

  names = []
  name = []
  for word in words:
    # when you meet a separator, delete the name
    if re.search("[.,;\]\[]", word) or \
       (re.search("^[0-9a-zşșî/(\\-]", word) and
        not word.startswith("Ş") and
        not word.startswith("Ș") and
        not word.startswith("Ț")) or \
       re.search("[A-ZŢ]{2,}", word) or \
       word == "" or \
       word in common_capitalized_words or \
       len(word) <= 2:
      if len(name) > 1:
        names.append(name)
      name = []
    else:
      name.append(word)

  return names


# A global cache for articles.
link_to_article_id_hash = {}

# A global hash where I keep track of whether I've already added this
# article:person association. We need this so that we only tag a person in a
# specific article once.
tagged_people_hash = {}


def add_article_to_db(id, time, place, link, title, photo, source):
  """ Given all the information about an article plus the person associated with
  it, insert this association in the database. The script naively calls the api
  to add it entirely, assuming that the API will insert the article if it does
  not already exist.

  TODO(vivi): Optimize this, separate article resolving and (id, article)
  association, or get some sort of cache for article id's.
  """

  data = {
    'id': id,
    'time': time,
    'place': place,
    'link': link,
    'title': title,
    'source': source,
    'photo': photo
  }

  if link in link_to_article_id_hash:
    data['idarticle'] = link_to_article_id_hash[link]

    # also check if somehow I haven't added this already.
    key = str(link_to_article_id_hash[link]) + ":" + str(id)
    if key in tagged_people_hash:
      return
    else:
      tagged_people_hash[key] = 'been here, done that'

  f1 = urllib.urlopen(BASE_URL + '/api/new_news_article.php?api_key=' + API_KEY,
                      urllib.urlencode(data))
  # Keep the local version up to date too.
  # urllib.urlopen('http:///new_news_article.php',
  #                urllib.urlencode(data))

  articleId = f1.read()
  link_to_article_id_hash[link] = articleId
  data['idarticle'] = articleId


# The list of words that, if they show up in a qualifier, they act as stop
# words that show us that it's not a good qualifier from the start.
qualifier_stop_words = ['arătând', 'făcând', 'plecând', 'invitat în studio',
                        'care afirma că']

qualifier_stop_starts = ['a anunţat', 'a anuntat', 'a răspuns', 'a raspuns',
                         'citat de', 'a vizitat', 'a declarat', 'a stabilit',
                         'a explicat', 'si ', 'și ', 'iar ', 'dar ',
                         'exceptând ', 'in discutie cu', 'în discuție cu',
                         'a precizat', 'potrivit ', 'alături de',
                         'i-a spus lui', 'citat de', 'intr-un interviu pentru',
                         'într-un interviu pentru', 'aflat la']

def could_be_qualifier(item):
  """ Given a qualifier for a person, eliminates false positives on some obvious
  rules, like length of the phrase or the presence of certain words that will
  never be qualifiers. More and more rules should be added here.
  """
  if len(item) > 80:
    return False

  for stop_word in qualifier_stop_starts:
    if item.startswith(stop_word):
      return False

  for stop_word in qualifier_stop_words:
    if item.find(stop_word) > -1:
      return False

  return True


def get_qualifiers(name, data):
  """ Given a name and a blob of text, find the qualifiers of that name in the
  text. Let's start simple and find this type:

    + Sentence: "Monica Macovei, fost ministru al Justitiei,..."
    - Extract: "first premier"
  """
  data = strip_tags_and_new_lines(data)

  post_qualifiers = re.findall(name + ', ([^,.]+)[.|,]', data)
  # TODO(vivi): Add the
  # - 'bula demnitarului' paranthesis here too.
  # - Sentences like "Ion Iliescu este bla bla".
  post_qualifiers = filter(could_be_qualifier, post_qualifiers)

  return post_qualifiers


def add_person_qualifier(link, idperson, name, qualifiers):
  if link not in link_to_article_id_hash:
    return
  idarticle = link_to_article_id_hash[link]
  for q in qualifiers:
    if q == '':
      continue
    data = {
      'idarticle': idarticle,
      'link': link,
      'idperson': idperson,
      'name': name,
      'q': q,
      'source': SOURCE_NAME
    }
    urllib.urlopen(
        BASE_URL + '/api/add_person_qualifier.php?api_key=' + API_KEY,
        urllib.urlencode(data))


# ===========================
# The main method for now, do not rely on it.
print "\n\n\n--!! STEP 2"

# Get the last five daily news files.
files = filter(is_not_a_daily, os.listdir(os.getcwd() + '/' + SOURCE + '/'))
files.sort()

for fname in files[-NUMBER_OF_DAYS_TO_PARSE : ]:
  print "--"
  print "-- ++ working on " + SOURCE + "/" + fname

  tree = parse(SOURCE + '/' + fname)
  for item in tree.findall('item'):
    link = item.findtext('news_link').encode('UTF-8')
    # news_title is at this point an UTF-8 encoded string, quoted.
    title = urllib.unquote(item.findtext('news_title').encode('UTF-8'))

    place = item.findtext('news_place').encode('UTF-8')

    photo = item.findtext('news_photo')
    # How on earth do I do photo = photo || '' in python?
    if not photo:
      photo = ''

    status = item.findtext('news_status')
    if status is None:
      status = 'stale'

    tstr = item.findtext('news_time').encode('UTF-8').split(' ')
    d = datetime(year=int(tstr[4]), month=int(tstr[3]), day=int(tstr[2]),
                 hour=int(tstr[0]), minute=int(tstr[1]))

    if status == 'fresh':
      print "-- " + link
      if photo:
        print "   " + photo

    news_content = urllib.unquote(item.findtext('news_content'))
    names = get_names_from_text(news_content)
    source = item.findtext('news_source')
    if not source:
      source = SOURCE_NAME

    for name in names:
      plain = ' '.join(name)
      try:
        id = get_person_id_for_name(plain)
      except ValueError:
        id = 0
        continue

      if id > 0:
        add_article_to_db(id, time.mktime(d.timetuple()), place, link, title,
                          photo, source)

      if link in link_to_article_id_hash:
        add_person_qualifier(link, id, plain,
                             get_qualifiers(plain, news_content))

      if status == 'fresh':
        print '+ ' + plain + ' (' + str(id) + ")"
