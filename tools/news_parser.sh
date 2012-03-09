#!/bin/bash -e

# Export the current directory as a python path so we can include modules
# from various python scripts as "ro.vivi.module_name"
PYTHONPATH="`pwd`/python/src"
export PYTHONPATH

PYBIN='/usr/bin/python'

if [ $HOSTNAME = 'zen.local' ]; then
  cd /work/hartapoliticii
else
  cd /workspace/hartapoliticii
fi

# TODO: Move the directory where we save cached file somewhere outside the
# repository.
start=`date +%s`
$PYBIN python/src/ro/vivi/news_parser/rss_parse_mediafax.py
$PYBIN python/src/ro/vivi/news_parser/entity_extractor.py \
    python/src/ro/vivi/news_parser/mediafax

end=`date +%s`
t1=`expr $end - $start`

start=`date +%s`
$PYBIN python/src/ro/vivi/news_parser/rss_parse_hotnews.py
$PYBIN python/src/ro/vivi/news_parser/entity_extractor.py \
    python/src/ro/vivi/news_parser/hotnews

end=`date +%s`
t5=`expr $end - $start`

echo -e "mediafax:\t$t1 sec\nhotnews:\t$t5 sec"
