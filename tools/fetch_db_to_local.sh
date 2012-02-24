#!/bin/bash -e

read -s -p "Enter Password: " PASS

mysqldump --host=mysql5.s701.sureserver.com --port=3307 -u vivi -p$PASS \
  --add-drop-table \
  -v hartapoliticii_pistruiatul \
  > hartapoliticii_pistruiatul.sql

mysql -u root -proot hartapoliticii_pistruiatul \
  < hartapoliticii_pistruiatul.sql
