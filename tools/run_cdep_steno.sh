#!/bin/bash -e

set -x

# Export the current directory as a python path so we can include modules
# from various python scripts as "ro.vivi.module_name"
PYTHONPATH="`pwd`/python/src"
export PYTHONPATH

python python/src/ro/vivi/cdep_steno/1_get_steno_pages.py /work/tmp/cdep_steno

python python/src/ro/vivi/cdep_steno/2_parse_declarations_file.py \
    /work/tmp/cdep_steno/declaratii_agg.txt
