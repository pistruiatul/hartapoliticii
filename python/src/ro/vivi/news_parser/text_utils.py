# -*- coding: utf-8 -*-

'''

Misc functions related to the text processing, like eliminating tags and such.

Created on Jan 08, 2010

@author: vivi
'''

import re


def strip_tags_and_new_lines(data):
  """ Given a blob of text, eliminates the html tags from it and the new
  line characters and returns the processed text.
  
  Arguments:
    data: The raw htmlized blob of text.
  Returns:
    The processed string, without tags and newlines.
  """
  # First step, eliminate all html tags.
  data = re.sub("<([^>]*)>", " ", data)
  # Eliminate all the useless separators
  data = re.sub("[\n\t\r/]", " ", data)

  return data


def strip_punctuation(data):
  """ Given a blob of text, eliminates the punctuation from it.
  
  Arguments:
    data: The raw htmlized blob of text.
  Returns:
    The processed string, without tags and newlines.
  """
  # Eliminate all the useless separators
  data = re.sub("[':;.,\"()?!]", " ", data)
  data = re.sub("-", "", data)
  data = re.sub("([ ]+)", " ", data)
  return data

  
def lower(data):
  # Eliminate all the useless separators
  data = data.lower()
  data = re.sub("Î", "î", data)
  data = re.sub("Ț", "ț", data)
  data = re.sub("Ă", "ă", data)
  data = re.sub("Â", "â", data)
  data = re.sub("Ș", "ș", data)
  data = re.sub("Ş", "ș", data)
  return data


def strip_diacritics(data):
  # Eliminate all the useless separators
  data = data.lower()
  data = re.sub("î", "i", data)
  data = re.sub("ț", "t", data)
  data = re.sub("ţ", "t", data)
  data = re.sub("ă", "a", data)
  data = re.sub("â", "a", data)
  data = re.sub("ș", "s", data)
  data = re.sub("ş", "s", data)
  return data
