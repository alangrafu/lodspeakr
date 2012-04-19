#!/bin/bash
#
# https://github.com/alangrafu/lodspeakr/blob/master/utils/ldspk.sh
USAGE="Usage:\n"
USAGE=$USAGE" Create component:\t\t\t\t\t$0 create uri|type|service foo [html|rdf|ttl|nt|json]\n"
USAGE=$USAGE" Delete component:\t\t\t\t\t$0 delete uri|type|service foo [html|rdf|ttl|nt|json]\n"
USAGE=$USAGE" Turn debug:\t\t\t\t\t\t$0 debug on|off\n"
USAGE=$USAGE" Switch to standard view/models temporaly:\t\t$0 disable on|off\n"
USAGE=$USAGE" Backup current installation:\t\t\t\t$0 backup\n"
USAGE=$USAGE" Restore previous installation:\t\t\t\t$0 restore\n"
USAGE=$USAGE" Clear cache:\t\t\t\t\t\t$0 cache clear\n"
USAGE=$USAGE" Version:\t\t\t\t\t\t$0 version\n"
USAGEDEBUG="Usage: $0 debug on|off"
if [[ $# -eq 0 || "$1" == "--help" ]]; then
  echo -e $USAGE
  exit 1
fi

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
operations=( create delete debug backup restore disable cache version )
currentOperation=

if [[ ${operations[@]} =~ $1 ]]; then
  currentOperation=$1
else
  echo "Operation \"$1\" not valid"
  echo -e $USAGE
  exit 1
fi

## Backup
if [[ $currentOperation == "backup" ]]; then
  $DIR/modules/backup.sh
  exit
fi  

## Restore
if [[ $currentOperation == "restore" ]]; then
  $DIR/modules/restore.sh
  exit
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
    echo -e $USAGE
    exit 1
  fi
  
  currentUnit=$3
  currentFormat="html"
  if [ ! -z "$4" ]; then
    if [[ ${formats[@]} =~ $4 ]]; then
      currentFormat=$4
    else
      echo "Format \"$4\" not valid"
      echo -e $USAGE
      exit 1
    fi
  else
  	if [[ $currentOperation == "delete" ]]; then
  	  currentFormat="all"
  	fi  	
  fi
  $DIR/modules/$currentOperation-$currentModule.sh "$currentUnit" "$currentFormat"
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
    echo -e $USAGE
    exit 1
  fi
  php $DIR/modules/debug.php "$debugOperation" 
  $DIR/modules/cache.sh clear
  exit
fi

## Disable
if [[ $currentOperation == "disable" ]]; then
 defaultOptions=( on off 0 1 )
  defaultOperation=
  if [[ ${defaultOptions[@]} =~ $2 ]]
  then
    defaultOperation=$2
  else
    echo "Disable option not supported. Operation aborted" >&2
    echo -e $USAGE
    exit 1
  fi
  php $DIR/modules/default.php "$defaultOperation" 
  $DIR/modules/cache.sh clear
  exit
fi  

## Cache
if [[ $currentOperation == "cache" ]]; then
  cacheOptions=( clear )
  if [[ ${cacheOptions[@]} =~ $2 ]]
  then
    cacheOperation=$2
  else
    echo -e "Cache option not supported. Operation aborted\n" >&2
    echo -e $USAGE
    exit 1
  fi
  $DIR/modules/cache.sh $2
  exit
fi

## Version
if [[ $currentOperation == "version" ]]; then
  $DIR/modules/version.sh
fi
