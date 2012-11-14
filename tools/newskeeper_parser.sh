#!/bin/bash -e

# Export the current directory as a python path so we can include modules
# from various python scripts as "ro.vivi.module_name"
PYBIN='/usr/bin/python'

WORKING_DIR=./

PYTHONPATH="$WORKING_DIR/python/src"
export PYTHONPATH

cd $WORKING_DIR


mkdir -p python/src/ro/vivi/news_parser/newskeeper

# TODO: Move the directory where we save cached file somewhere outside the
# repository.
start=`date +%s`
$PYBIN python/src/ro/vivi/news_parser/json_parse_newskeeper.py
$PYBIN python/src/ro/vivi/news_parser/entity_extractor.py \
    python/src/ro/vivi/news_parser/newskeeper

end=`date +%s`
t1=`expr $end - $start`

echo -e "newskeeper:\t$t1 sec\n"
