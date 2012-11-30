#!/bin/bash -e

# Export the current directory as a python path so we can include modules
# from various python scripts as "ro.vivi.module_name"
PYBIN='/usr/bin/python'

PYTHONPATH="`pwd`/python/src"
export PYTHONPATH

$PYBIN python/src/ro/vivi/romaniacurata/crawl.py www/hp-scripts/romania_curata_integritate.txt
