#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR/../..
GIT=`which git`
if [ -z $GIT ];then
  echo "No git found. Aborting"
  exit 1
fi

echo Updating LODSPeaKr
$GIT pull -q
if [ "$?" != 0 ];then
  echo "Update couldn't finish properly. Stopping further actions"
  exit 0
fi

echo Updating GUI elements
cp -rf doc/examples/originalComponents/static/admin/* components/static/admin/
