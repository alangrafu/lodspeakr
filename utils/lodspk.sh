#!/bin/bash
#
# https://github.com/alangrafu/lodspeakr/blob/master/utils/ldspk.sh

USAGE="Usage: $0 create|delete uri|type|service foo [html|rdf|ttl|nt|json]"
USAGEDEBUG="Usage: $0 debug on|off"
if [[ $# -eq 0 || "$1" == "--help" ]]; then
   echo $USAGE
   exit 1
fi

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
operations=( create delete debug )
modules=( type service uri )
formats=( html rdf ttl nt json all )
debugOptions=( on off 0 1 )
currentOperation=
currentModule=
currentFormat=
debugOperation=

if [[ ${operations[@]} =~ $1 ]]; then
  currentOperation=$1
else
  echo "Operation \"$1\" not valid"
  echo $USAGE
  exit 1
fi

if [[ $currentOperation = "debug" ]]
then
  if [[ ${debugOptions[@]} =~ $2 ]]
  then
    debugOperation=$2
  else
    echo "Debug option not supported. Operation aborted" >&2
    echo $USAGEDEBUG
    exit 1
  fi
else
  if [[ ${modules[@]} =~ $2 ]]; then
    currentModule=$2
  else
    echo "Module \"$2\" not valid"
    echo $USAGE
    exit 1
  fi

  currentUnit=$3

  if [[ ${formats[@]} =~ $4 ]]; then
    currentFormat=$4 
  else
    if [ -z "$4" ]; then
      currentFormat="all"
    else
      echo "Format \"$4\" not valid"
      echo $USAGE
      exit 1
    fi
  fi
fi


if [[ $currentOperation == "create" ]]; then
      $DIR/modules/create-$currentModule.sh "$currentUnit" "$currentFormat"
fi
if [[ $currentOperation == "delete" ]]; then
      $DIR/modules/delete-$currentModule.sh "$currentUnit" "$currentFormat"
fi
if [[ $currentOperation == "debug" ]]; then
       php $DIR/modules/debug.php "$debugOperation" 
fi
