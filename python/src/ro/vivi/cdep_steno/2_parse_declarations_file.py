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

LAST_PROCESSED_LINE_FILE = '/tmp/pdf.lpl'

# In this hash I will record the number declarations that are present at a
# particular link. This way when a person has multiple declarations in that
# file I can uniquely identify each of them with a separate URL.
# http://www.cdep.ro/pls/steno/steno.stenograma?ids=5132#<declaration_number>
declaration_counts_hash = {}


def record_declaration(hash, no_commit):
  """ Given a hash holding the person, time, link and text of a declaration,
  record this in the database or something.
  """
  global declaration_counts_hash

  # First, let's find the person id.
  person_id = get_person_id_for_name(hash['person'])
  if person_id <= 0:
    print " + can't find %s" % hash['person']
    return

  # See what is the number of this declaration.
  key = hash['link']
  if key not in declaration_counts_hash:
    declaration_counts_hash[key] = 0
  declaration_counts_hash[key] += 1

  print "%s %s" % (hash['person'], person_id)
  data = {
    'idperson': person_id,
    'time': hash['time'],
    'source': '%s#%d' % (hash['link'], declaration_counts_hash[key]),
    'declaration': hash['declaration']
  }
  if not no_commit:
    urllib.urlopen('http://zen.dev/api/new_declaration.php?api_key=' + API_KEY,
                    urllib.urlencode(data))


def write_last_processed_line(line_number):
  f = codecs.open(LAST_PROCESSED_LINE_FILE, 'w', 'utf-8')
  f.write('%d' % line_number)
  f.close()


def get_last_processed_line():
  if not os.path.exists(LAST_PROCESSED_LINE_FILE) or \
      os.path.getsize(LAST_PROCESSED_LINE_FILE) == 0:
    return 0

  f = codecs.open(LAST_PROCESSED_LINE_FILE, 'r', 'utf-8')
  line_number = int(f.read())
  f.close()
  return line_number


def main():
  if len(sys.argv) <= 1:
    print "The first argument should be the file we're parsing."
    sys.exit(1)
  else:
    input_file_name = sys.argv[1]
    print "Input file: %s" % input_file_name

  input_file = codecs.open(input_file_name, 'r', 'utf-8')
  input = input_file.read()
  input_file.close()

  lines = input.split("\n")

  last_processed_line = get_last_processed_line()

  hash = {}
  count = 0
  # The state is what we're expecting next.
  for line in lines:
    if line == "":
      continue

    # Split once in two around the separator.
    (tag, content) = line.encode('utf-8').split(': ', 1)
    hash[tag] = content

    if tag == 'declaration':
      print '%8d / %8d' % (count, len(lines))
      # We have parsed the last four lines and they are all now in the hash,
      # now store them somewhere.
      record_declaration(hash, count < last_processed_line)
      write_last_processed_line(count)

    count += 1


main()
