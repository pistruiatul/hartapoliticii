'''
Created on Sep 27, 2009

From all the images of people, make tiny images.

@author: vivi
'''

import os
import sys
import Image
import ImageOps


def resize_image_fit(img, target_width, target_height, dir):
  img = ImageOps.fit(img, (target_width, target_height), Image.ANTIALIAS)

  if not os.path.exists(dir + fname):
    print " + generated new image", dir + fname
    img.save(dir + fname)



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

  resize_image_fit(img, target_width, target_height, 'www/images/people_tiny/')
  resize_image_fit(img, 100, 100, 'www/images/people_medium/')