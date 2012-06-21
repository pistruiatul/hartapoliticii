#!/bin/bash -e

set -x

# Export the current directory as a python path so we can include modules
# from various python scripts as "ro.vivi.module_name"
PYTHONPATH="`pwd`/python/src"
export PYTHONPATH

mkdir -p `pwd`/tmp/comisii

python python/src/ro/vivi/cdep_comisii/get_data.py `pwd`/tmp/comisii

