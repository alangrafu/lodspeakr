#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='uris'


#Check models
mainDir=$DIR/../../components/$initToken/$1

if [ ! -e "$mainDir" ]
then
  echo "ERROR: $mainDir doesn't exist in models. Operation aborted" >&2
  exit 1
fi

rm -rf $mainDir

echo Uri $1 deleted >&2
