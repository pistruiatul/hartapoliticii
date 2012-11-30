"""
A library with some common functions that all the scripts here could use.
For now I'll just dump a bunch of them in here, when it gets too out of hand
we can split it in multiple libraries?
"""

import codecs
import hashlib
import json
import os
import urllib
import urllib2

from urllib2 import URLError


BASE_URL = 'http://hartapoliticii.ro'


# A global cache for names. Once we resolved a name we can afterwards look it
# up here.
name_to_id_hash = {}


def get_api_key(file):
  f = open(file, 'r')
  key = f.read()
  f.close()
  return key


# Keep the API key in a global variable.
API_KEY = get_api_key('./secret/api_key')


def get_person_id_for_name(name):
  """ Given a name, attempts to resolve it to an id. If multiple id's are
  returned, -n is returned so that I can further investigate why this
  ambiguity exists.

  !!! IMPORTANT NOTE !!!!

  Dear python, fuck you very much for asking me to specifically encode a
  string to UTF-8 in order for your library to work.

  In other words, for this to work properly, 'name' needs to be a UTF-8 string,
  otherwise urllib.quote throws an error.

  So if you see a KeyError, you most likely have to call this method like this:
    get_person_id_for_name(name.encode('UTF-8'))
  """
  if name in name_to_id_hash:
    return name_to_id_hash[name]

  f = urllib.urlopen(BASE_URL + '/api/search.php?q=' +
                     urllib.quote(name) + '&api_key=' + API_KEY)
  people = json.loads(f.read())
  if len(people) == 1:
    name_to_id_hash[name] = int(people[0]['id'])
  else:
    name_to_id_hash[name] = -len(people)

  return name_to_id_hash[name]



def get_file_data(fname):
  """Returns the contents of a random file."""
  f = codecs.open(fname, 'r', 'utf-8')
  data = f.read()
  f.close()
  return data



def get_page(link, tmp_dir=None, encoding='ISO-8859-2', skip_404=False):
  """ Fetches the page at the provided link. Checks whether this is indeed the
  page of a vote. If that is true it returns the page as a string, otherwise
  returns None.

  TODO: This is duplicated from cdep_crawler. We should just somehow move this
  into a common library.
  """

  # First, see if this is already cached.
  fname = None

  if tmp_dir:
    fname = tmp_dir + '/cache/%s.html' % hashlib.md5(link).hexdigest()
    if os.path.exists(fname):
      return get_file_data(fname)

  success = False
  data = ""
  while not success:
    try:
      f = urllib2.urlopen(link, None, 20)
      data = unicode(f.read(), encoding)
      f.close()
      success = True

    except urllib2.URLError, e:
      if e.code == 404 and skip_404:
        success = True
      else:
        print "Timed out, retrying ", link
        success = False

  # Write this page into a cached file, with a more common charset.
  if fname:
    cache_file = codecs.open(fname, 'w', 'utf-8')
    cache_file.write(data)
    cache_file.close()

  return data