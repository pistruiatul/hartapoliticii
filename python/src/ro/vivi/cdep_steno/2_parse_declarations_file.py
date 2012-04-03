"""
Given a file with declarations, this script parses them and puts them in the
database.

The expected file format has each piece of information on an individual line,
in the following order:
  link:
  time:
  person:
  declaration:

This group of four lines just keeps repeating.
"""

import sys
import codecs

from ro.vivi.hplib import *

NUMBER_OF_STENOS_TO_PARSE = 20

# In this hash I will record the number declarations that are present at a
# particular link from one person. This way when a person has multiple
# declarations in that file I can uniquely identify each of them with a
# separate URL and avoid duplicating them when we run the script again.
# http://www.cde...rama?ids=5132#<idperson>:<declaration_number>
declaration_counts_hash = {}


def record_declaration(hash):
  """ Given a hash holding the person, time, link and text of a declaration,
  record this in the database or something.
  """
  global declaration_counts_hash

  # First, let's find the person id.
  person_id = get_person_id_for_name(hash['person'])
  if person_id <= 0:
    print " - %s: can't find" % hash['person']
    return

  # See what is the number of this declaration.
  key = '%s:%d' % (hash['link'], person_id)
  if key not in declaration_counts_hash:
    declaration_counts_hash[key] = 0
  declaration_counts_hash[key] += 1

  print " + %s: %s" % (hash['person'], person_id)
  data = {
    'idperson': person_id,
    'time': hash['time'],
    'source': '%s#%d:%d' %
              (hash['link'], person_id, declaration_counts_hash[key]),
    'declaration': hash['declaration']
  }
  urllib.urlopen(BASE_URL + '/api/declarations_new.php?api_key=' + API_KEY,
                 urllib.urlencode(data))


def is_steno_file(file_name):
  return file_name.find('stenos/steno_')


def steno_link_already_processed(link):
  """ Checks whether we already have all the declarations in here in the
  database on the server.
  """
  f = urllib.urlopen(BASE_URL + '/api/declarations_count.php?link=' +
                     urllib.quote(link) + '&api_key=' + API_KEY)
  count = int(f.read())
  return count > 0


def process_steno_file(file_name):
  input_file = codecs.open(file_name, 'r', 'utf-8')
  input = input_file.read()
  input_file.close()

  lines = input.split("\n")

  link = lines[0]
  print '== Processing %s, %s' % (file_name, link)

  # Figure out if we've already processed this file and it's contents are
  # already on the server.
  if steno_link_already_processed(link):
    print '   = already done'
    return

  hash = {
    'link': link
  }
  count = 0
  # The state is what we're expecting next.
  for line in lines[1:]:
    if line == "":
      continue

    # Split once in two around the separator.
    (tag, content) = line.encode('utf-8').split(': ', 1)
    hash[tag] = content

    if tag == 'declaration':
      # We have parsed the last four lines and they are all now in the hash,
      # now store them somewhere.
      record_declaration(hash)

    count += 1


def main():
  if len(sys.argv) <= 1:
    print "The first argument should be the file we're parsing."
    sys.exit(1)
  else:
    input_dir = sys.argv[1]
    print "Input directory: %s" % input_dir

  files = filter(is_steno_file, os.listdir(input_dir))
  files.sort()

  for file_name in files[-20:]:
    process_steno_file(input_dir + file_name)


main()
