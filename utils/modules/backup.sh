#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
BACKUPDIR=$HOME/lodspeakr_backup
cd $DIR/../..
if [[ ! -d $BACKUPDIR ]]; then
  echo "WARNING: No $BACKUPDIR dir. Creating it." >&2
  mkdir $BACKUPDIR
fi

if [[ ! -d $BACKUPDIR ]]; then
  echo "ERROR: Couldn't create $BACKUPDIR. Operation aborted" >&2
  exit 1
fi

tmpFile=lodspeakr_backup_`date +%Y%m%d%H%M%S`.tar.gz

tar -czf $tmpFile settings.inc.php models views

mv $tmpFile $BACKUPDIR/
echo "New backup $tmpFile created"

