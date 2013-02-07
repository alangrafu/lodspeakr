#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='services'

serviceName=`echo $1 |sed 's/\//%2F/g'`


#Check models
mainDir=$DIR/../../components/$initToken/$serviceName

if [ ! -e "$mainDir" ]
then
  echo "ERROR: $initToken/	$serviceName doesn't exist in models. Operation aborted" >&2
  exit 1
fi

rm -rf $mainDir

echo Service $serviceName deleted >&2
