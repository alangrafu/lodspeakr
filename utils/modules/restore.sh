#!/bin/bash


DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR
BASE=`php getvar.php basedir`
NAME=`echo $BASE |sed -e 's/^http:\/\///g' -e 's/\/$//g' -e 's/\//_/g'`
cd $DIR/../..
BACKUPDIR=$HOME/lodspeakr_backup
if [[ ! -d $BACKUPDIR ]]; then
  echo "ERROR: No $BACKUPDIR dir." >&2
  exit 1
fi

LIST=( `ls $BACKUPDIR/$NAME-backup* 2>/dev/null` )
CHOSEN=-1
if [ "${#LIST[@]}" -eq 0 ];then
  echo "No backups available";
  exit
fi
while [[ "$CHOSEN" -lt 0 || "$CHOSEN" -ge "${#LIST[@]}" ]] ;do
j=0
echo
echo "Choose from the following available backups"
  for i in ${LIST[@]}; do
    echo "["$j"]" `basename $i`
    let j=$j+1
  done
  echo -n "Select which backup to restore: "
  read -u 1 CHOSEN
done

echo
echo "*** ATTENTION ***"
echo "Are you sure you want to restore this backup? This may lead to overrite settings.inc.php, models/ and views/"
echo -n "(write 'yes' if you are sure): "
confirm=no
read confirm
if [[ "$confirm" !=  "yes" ]];then
  echo "Nothing done"
  exit 0
fi
RESTORE=`basename ${LIST[$CHOSEN]}`
echo "Restoring "$RESTORE"..."

cp ${LIST[$CHOSEN]} .
tar zxf $RESTORE
if [[ $? -eq 0 ]];then
  echo Restore successful
else
  echo A problem occurred in the restoring process
fi

