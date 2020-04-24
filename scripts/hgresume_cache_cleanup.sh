#!/bin/bash

# this file should be placed in /usr/local/bin/hgresume_cache_cleanup.sh
#
# crontab
# 10 1 * * 0 /usr/local/bin/hgresume_cache_cleanup.sh > /tmp/hgresume_cleanup.cronlog 2>&1

WORKDIR="/var/cache/hgresume"
EXTENSIONS="bundle metadata async_run"
FILES_TO_REMOVE="/tmp/cache_files_to_delete.txt"

> $FILES_TO_REMOVE
cd $WORKDIR

for EXT in $EXTENSIONS; do
    /usr/bin/find $WORKDIR -name "*$EXT" -mtime +20 -exec ls -l {} \; >> $FILES_TO_REMOVE
    /usr/bin/find $WORKDIR -name "*$EXT" -mtime +20 -exec rm {} \;
done
