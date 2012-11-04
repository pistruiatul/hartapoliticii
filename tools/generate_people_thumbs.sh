#!/bin/bash

# Keeps in sync the people directory from vivi.ro with the
# local one.

read -s -p "Enter Password: " PASS

lftp -u hartapoliticii,$PASS www.hartapoliticii.ro <<EOF
mirror -v -e images/people www/images/people
quit 0
EOF

python python/src/ro/vivi/thumb_generator/make_tiny_images.py

lftp -u hartapoliticii,$PASS hartapoliticii.ro <<EOF
mirror -v -R --ignore-time www/images/people_tiny images/people_tiny
quit 0
EOF

