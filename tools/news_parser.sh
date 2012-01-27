#!/bin/bash -e

PYBIN='/usr/bin/python'

if [ $HOSTNAME = 'zen.local' ]; then
  cd /work/hartapoliticii/python
else
  cd /workspace/hartapoliticii/python
fi

start=`date +%s`
$PYBIN src/ro/vivi/pistruiatul/rss_parse_mediafax.py
$PYBIN src/ro/vivi/pistruiatul/entity_extractor.py \
    src/ro/vivi/pistruiatul/mediafax \
    secret/api_key

end=`date +%s`
t1=`expr $end - $start`

start=`date +%s`
$PYBIN src/ro/vivi/pistruiatul/rss_parse_hotnews.py
$PYBIN src/ro/vivi/pistruiatul/entity_extractor.py \
    src/ro/vivi/pistruiatul/hotnews \
    secret/api_key

end=`date +%s`
t5=`expr $end - $start`

echo -e "mediafax:\t$t1 sec\nhotnews:\t$t5 sec"
