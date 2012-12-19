#!/bin/bash

#Check git
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR/../..
GIT=`which git`
if [ -z $GIT ];then
  echo "No git found. Aborting"
  exit 1
fi

#Ask for backup
answer_backup=""
while [ "$answer_backup" != "y" -a "$answer_backup" != "n" ]; do
  echo -n "Do you want to create a backup of the current installation? [y/n]: "
  read -u 1 answer_backup
done

if [ "$answer_backup" = "y" ]; then
    echo "Performing backup"
    utils/lodspk.sh backup
fi

#Perform git pull
echo Updating LODSPeaKr
$GIT pull -q
if [ "$?" != 0 ];then
  echo "Update couldn't finish properly. Stopping further actions"
  exit 0
fi


#Update GUI
echo Updating GUI elements
cp -rf doc/examples/originalComponents/static/admin/* components/static/admin/
