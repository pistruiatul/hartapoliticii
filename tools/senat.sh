#!/bin/bash -e
# -e: Exit immediately when one of these commands exits with an error.

set -x

TEMP="/work/tmp/senat"
if [ ! -d $TEMP ]; then
  mkdir $TEMP
fi

# Get the pages with the days.
#/usr/bin/python ./python/src/ro/vivi/senat_crawler/s01_days_get.py $TEMP

# From the files with the days, get the votes and the law pages.
#/usr/bin/python ./python/src/ro/vivi/senat_crawler/s02_votes_get.py $TEMP

# From all these files now generate the aggregate monster file.
#/usr/bin/python ./python/src/ro/vivi/senat_crawler/s03_votes_parse.py \
#  $TEMP $TEMP/senat_2008_agg.txt

# Transform the big file with all votes into a database, plus aggregate stuff.

# We store the binary in /tmp
CP="$TEMP"
CP="$CP:`pwd`/java/src"
CP="$CP:`pwd`/java/lib/mysql-connector-java-5.1.7-bin.jar"
CP="$CP:`pwd`/java/lib/HTTPClient.zip"

# Delete the java compilation.
rm -rf $TEMP/ro

# Compile the java parser.
javac -cp $CP -d $TEMP ./java/src/ro/vivi/pistruiatul/Main.java

# Run it against the cdep file.
java -cp $CP -Xmx1024m ro/vivi/pistruiatul/Main \
  senat_2008 $TEMP/senat_2008_agg.txt

# TODO(vivi): Add some sanity checks here to make sure that we're not writing
# something totally stupid.
# And now dump the tables we have just modified.
mysqldump -u root -proot -v --port=3306 \
  --add-drop-table \
  hartapoliticii_pistruiatul \
    senat_2008_belong \
    senat_2008_belong_agg \
    senat_2008_laws \
    senat_2008_votes \
    senat_2008_votes_agg \
    senat_2008_votes_details \
  > $TEMP/data.sql

set +x

read -p "Do you want to push this to prod now? [y/n] " -n 1
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    exit 1
fi

read -s -p "Enter Password: " PASS

# And push them online.
mysql -C \
  --host=mysql.s701.sureserver.com --port=3307 \
  -u vivi -p$PASS \
  hartapoliticii_pistruiatul \
  < $TEMP/data.sql

