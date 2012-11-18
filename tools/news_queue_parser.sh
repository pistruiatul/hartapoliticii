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
$PYBIN python/src/ro/vivi/news_parser/parse_news_queue.py
$PYBIN python/src/ro/vivi/news_parser/entity_extractor.py \
    python/src/ro/vivi/news_parser/news_queue

end=`date +%s`
t5=`expr $end - $start`

echo -e "news_queue:\t$t1 sec"
