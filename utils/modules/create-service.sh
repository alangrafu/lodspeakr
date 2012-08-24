#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='services'
cd $DIR
#Check models
mainDir=$DIR/../../components/$initToken/$1/

if [ -e "$mainDir" ]
then
  echo "ERROR: This service $1 already exists." >&2
  exit 1
else
  mkdir -p $mainDir
fi

cp -rf ../defaults/service/* $mainDir/

echo $initToken.$1 created/modified successfully! >&2
