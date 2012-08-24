#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='uris'

cd $DIR

#Check models
mainDir=$DIR/../../components/$initToken/$1

if [ -e "$mainDir" ]
then
  echo "ERROR: Component for this URI $1 already exists." >&2
else
  mkdir -p $mainDir
fi

#Create  file structure

cp -rf ../defaults/type/* $mainDir/

echo $initToken.$1 created/modified successfully! >&2
