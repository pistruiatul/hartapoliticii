'''
Created on Sep 27, 2009

From all the images of people, make tiny images.

@author: vivi
'''

import os
import Image

# Get the last five daily news files.
files = os.listdir(os.getcwd() + '/people/')

target_height = 30
target_width = 22

for fname in files:
  print 'resizing people/' + fname
  img = Image.open('people/' + fname)

  width, height = img.size

  new_width = width * target_height / height
  new_height = target_height

  extra = (new_width - target_width) / 2

  img = img.resize((new_width, new_height), Image.ANTIALIAS)
  img = img.crop((extra, 0, new_width - extra, new_height))

  img.save('people_tiny/' + fname)
