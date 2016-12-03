#!/usr/bin/env bash

set -x
for i in `seq 700 1000`;
do
  ./python/src/ro/vivi/alegeriparlamentare2016/html2text.py \
    http://www.alegeriparlamentare2016.ro/candidati/candidat_detail/id:${i} > /tmp/candidat${i}.txt
done



