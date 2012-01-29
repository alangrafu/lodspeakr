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
operations=( create delete debug backup )
currentOperation=

if [[ ${operations[@]} =~ $1 ]]; then
  currentOperation=$1
else
  echo "Operation \"$1\" not valid"
  echo $USAGE
  exit 1
fi

## Backup
if [[ $currentOperation == "backup" ]]; then
  $DIR/modules/backup.sh
fi  

## Create/delete
if [[ $currentOperation == "create" ||  $currentOperation == "delete" ]]; then
  modules=( type service uri )
  formats=( html rdf ttl nt json all )
  currentModule=
  currentFormat=
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
  $DIR/modules/create-$currentModule.sh "$currentUnit" "$currentFormat"
fi

## Debug
if [[ $currentOperation == "debug" ]]; then
  debugOptions=( on off 0 1 )
  debugOperation=
  if [[ ${debugOptions[@]} =~ $2 ]]
  then
    debugOperation=$2
  else
    echo "Debug option not supported. Operation aborted" >&2
    echo $USAGEDEBUG
    exit 1
  fi
  php $DIR/modules/debug.php "$debugOperation" 
fi
