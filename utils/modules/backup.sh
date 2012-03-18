#!/bin/bash


DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR
BASE=`php getvar.php basedir`
NAME=`echo $BASE |sed -e 's/^http:\/\///g' -e 's/\/$//g' -e 's/\//_/g'`
cd $DIR/../..
BACKUPDIR=$HOME/lodspeakr_backup
if [[ ! -d $BACKUPDIR ]]; then
  echo "WARNING: No $BACKUPDIR dir. Creating it." >&2
  mkdir $BACKUPDIR
fi

if [[ ! -d $BACKUPDIR ]]; then
  echo "ERROR: Couldn't create $BACKUPDIR. Operation aborted" >&2
  exit 1
fi

tmpFile=$NAME"-backup-"`date +%Y%m%d%H%M%S`.tar.gz

tar -czf $tmpFile settings.inc.php components

mv $tmpFile $BACKUPDIR/
echo "New backup $tmpFile created"

