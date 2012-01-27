#!/bin/bash

# Keeps in sync the people directory from vivi.ro with the
# local one.

cd /Users/Shared/vivi/eclipse/workspace/pistruiatul-senat/src/ro/vivi/pistruiatul

read -s -p "Enter Password: " PASS

lftp -u vivi,$PASS vivi.ro <<EOF
mirror -v -e politica/images/people people
quit 0
EOF

/usr/bin/python2.5 make_tiny_images.py

lftp -u vivi,$PASS vivi.ro <<EOF
mirror -v -R --ignore-time people_tiny politica/images/people_tiny
quit 0
EOF

cp -f people/* /usr/local/www/politica/images/people
cp -f people_tiny/* /usr/local/www/politica/images/people_tiny
