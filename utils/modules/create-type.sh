#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='types'

cd $DIR

#Check models
mainDir=$DIR/../../components/$initToken/$1/

if [ -e "$mainDir" ]
then
  echo "ERROR: This type $1 already exists." >&2
  exit 1
else
  mkdir -p $mainDir
fi

cp -rf ../defaults/type/* $mainDir/

echo $1 created successfully! >&2
