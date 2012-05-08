#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='services'


#Check models
mainDir=$DIR/../../components/$initToken/$1

if [ ! -e "$mainDir" ]
then
  echo "ERROR: $initToken/	$1 doesn't exist in models. Operation aborted" >&2
  exit 1
fi

rm -rf $mainDir

echo Service $1 deleted >&2
