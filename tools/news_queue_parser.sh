#!/bin/bash -e

# Export the current directory as a python path so we can include modules
# from various python scripts as "ro.vivi.module_name"
PYBIN='/usr/bin/python'

if [ $HOSTNAME = 'zen.local' ]; then
  WORKING_DIR=/work/hartapoliticii
else
  WORKING_DIR=/workspace/hartapoliticii
fi

PYTHONPATH="$WORKING_DIR/python/src"
export PYTHONPATH

cd $WORKING_DIR

# TODO: Move the directory where we save cached file somewhere outside the
# repository.
start=`date +%s`
$PYBIN python/src/ro/vivi/news_parser/news_queue_parse.py
$PYBIN python/src/ro/vivi/news_parser/entity_extractor.py \
    python/src/ro/vivi/news_parser/news_queue

# Once we've detected all the people in the news articles and added them on
# the web, now go over the queue and clean up the articles that have not had
# any tags. This is a PY script only because we want it to read the API key
# and call a PHP that does this job.
curl http://hartapoliticii.ro/api/news_queue_purge.php\?api_key\=`cat secret/api_key`

end=`date +%s`
t5=`expr $end - $start`
