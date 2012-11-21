#!/bin/bash -e

# Every half an hour re-score the links submitted by users.
curl http://hartapoliticii.ro/api/community_compute_scores.php\?api_key\=`cat secret/api_key`
