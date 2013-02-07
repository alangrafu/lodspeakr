#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='services'
cd $DIR

serviceName=`echo $1 |sed 's/\//%2F/g'`
#Check models
mainDir=$DIR/../../components/$initToken/$serviceName/

if [ -e "$mainDir" ]
then
  echo "ERROR: This service $serviceName already exists." >&2
  exit 1
else
  mkdir -p $mainDir
fi

cp -rf ../defaults/service/* $mainDir/

echo $initToken.$serviceName created/modified successfully! >&2
