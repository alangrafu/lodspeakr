#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='types'

cd $DIR
componentName=${1/\:/__}
#Check models
mainDir=$DIR/../../components/$initToken/$componentName/

if [ -e "$mainDir" ]
then
  echo "ERROR: This type $componentName already exists." >&2
  exit 1
else
  mkdir -p $mainDir
fi

cp -rf ../defaults/type/* $mainDir/

echo $componentName created successfully! >&2
