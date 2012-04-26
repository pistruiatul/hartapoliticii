'''
Created on Sep 27, 2009

From all the images of people, make tiny images.

@author: vivi
'''

import os
import sys
import Image

if not os.path.exists(os.getcwd() + '/www/images/people/'):
  print "Please run this from the repository root"
  sys.exit(1)

# Get the last five daily news files.
files = os.listdir(os.getcwd() + '/www/images/people/')

target_height = 30
target_width = 22

for fname in files:
  print 'resizing people/' + fname
  try:
    img = Image.open('www/images/people/' + fname)
  except IOError:
    continue

  width, height = img.size

  new_width = width * target_height / height
  new_height = target_height

  extra = (new_width - target_width) / 2

  img = img.resize((new_width, new_height), Image.ANTIALIAS)
  img = img.crop((extra, 0, new_width - extra, new_height))

  if not os.path.exists('www/images/people_tiny/' + fname):
    print " + generated new image", 'www/images/people_tiny/' + fname
    img.save('www/images/people_tiny/' + fname)
