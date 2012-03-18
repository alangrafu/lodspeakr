#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='types'


#Check models
mainDir=$DIR/../../components/$initToken/$1

if [ ! -e "$mainDir" ]
then
  echo "ERROR: $initToken/$1 doesn't exist in models. Operation aborted" >&2
  exit 1
fi

#Delete  file structure

rm -rf $mainDir
echo $initToken.$1 deleted successfully! >&2
