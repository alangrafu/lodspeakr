#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

formats=( html rdf ttl nt json all )
operations=( create delete )
modules=( class service uri )

currentOperation=
currentFormat=
currentModule=

if [[ ${operations[@]} =~ $1 ]]
then
  currentOperation=$1
else
  echo "Operation \"$1\" not valid"
  exit 1
fi

if [[ ${modules[@]} =~ $2 ]]
then
  currentModule=$2
else
  echo "Module \"$2\" not valid"
  exit 1
fi

if [[ ${formats[@]} =~ $4 ]]
then
  currentFormat=$4 
else
  if [ -z "$4" ]
  then
    currentFormat="all"
  else
    echo "Format \"$4\" not valid"
    exit 1
  fi
fi


currentUnit=$3


if [[ $currentOperation == "create" ]]
then
      $DIR/modules/create-$currentModule.sh "$currentUnit" "$currentFormat"
fi
if [[ $currentOperation == "delete" ]]
then
      $DIR/modules/delete-$currentModule.sh "$currentUnit" "$currentFormat"
fi


