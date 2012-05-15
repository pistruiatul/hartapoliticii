#!/bin/bash -e

read -s -p "Enter Password: " PASS

# This script is meant to be run from the root of the repository and it pushes
# the changes from the local repository into production.
cd ./www

lftp -u hartapoliticii,$PASS www.hartapoliticii.ro <<EOF
mirror -v -R --ignore-time \
    -X '*.htaccess' \
    -X '*.htpasswd' \
    -X 'a/*' \
    -X 'templates_c/*' \
    -X 'images/people/*' \
    -X 'images/people_tiny/*' \
    -X '.git/*' \
    -X '*.DS_Store' \
    ./ /www/www
quit 0
EOF
