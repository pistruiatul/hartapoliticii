#!/bin/bash

set -x

TEMP=/work/tmp

mysqldump -u root -proot -v --port=3306 \
  --add-drop-table \
  hartapoliticii_pistruiatul \
    $1 > $TEMP/temp_table_export.sql

set +x

read -s -p "Enter Password: " PASS

# And push them online.
mysql -C \
  --host=mysql.s1045.sureserver.com --port=3307 \
  -u vivi -p$PASS \
  hartapoliticii_pistruiatul \
  < $TEMP/temp_table_export.sql