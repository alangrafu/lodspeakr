#!/bin/bash
#
# https://github.com/alangrafu/lodspeakr/blob/master/utils/ldspk.sh
USAGE="Usage:\n"
USAGE=$USAGE"===COMPONENTS==\n"
USAGE=$USAGE" Create component:\t\t\t\t\t$0 create uri|type|service foo [html|json]\n"
USAGE=$USAGE" Delete component:\t\t\t\t\t$0 delete uri|type|service foo [html|json]\n"
USAGE=$USAGE" List components:\t\t\t\t\t$0 list components\n"
USAGE=$USAGE"\n===DEBUG==\n"
USAGE=$USAGE" Turn debug:\t\t\t\t\t\t$0 debug on|off\n"
USAGE=$USAGE" Switch to standard view/models temporaly:\t\t$0 disable on|off\n"
USAGE=$USAGE" Clear cache:\t\t\t\t\t\t$0 cache clear\n"
USAGE=$USAGE"\n===BACKUP==\n"
USAGE=$USAGE" Backup current installation:\t\t\t\t$0 backup\n"
USAGE=$USAGE" Restore previous installation:\t\t\t\t$0 restore\n"
USAGE=$USAGE"\n===ENDPOINT MANAGEMENT==\n"
USAGE=$USAGE" Add endpoint:\t\t\t\t\t\t$0 add endpoint prefix http://example.com/sparql\n"
USAGE=$USAGE" Remove endpoint:\t\t\t\t\t$0 remove endpoint prefix \n"
USAGE=$USAGE" List endpoints:\t\t\t\t\t$0 list endpoints\n"
USAGE=$USAGE"\n===NAMESPACE MANAGEMENT==\n"
USAGE=$USAGE" Add namespace:\t\t\t\t\t\t$0 add namespace prefix http://example.com/sparql\n"
USAGE=$USAGE" Remove namespace:\t\t\t\t\t$0 remove namespace prefix \n"
USAGE=$USAGE" List namespaces:\t\t\t\t\t$0 list namespaces\n"
USAGE=$USAGE"\n===MODULES===\n"
USAGE=$USAGE" Enable module:\t\t\t\t\t\t$0 enable module position\n"
USAGE=$USAGE" Disable module:\t\t\t\t\t$0 disable module\n"
USAGE=$USAGE" List modules:\t\t\t\t\t\t$0 list modules\n"
USAGE=$USAGE"\n===VARIABLES===\n"
USAGE=$USAGE" Add any variable:\t\t\t\t\t$0 add variable value\n"
USAGE=$USAGE" Where variable has the form conf.something or lodspk.something\n"
USAGE=$USAGE" Remove any variable:\t\t\t\t\t$0 remove variable\n"
USAGE=$USAGE" If the variable is part of LODSPeaKr, it will return to its default value\n"
USAGE=$USAGE" \n===ADMIN USER===\n"
USAGE=$USAGE" Change password:\t\t\t\t\t$0 change password NEWPASSWORD\n"
USAGE=$USAGE"\n===VERSION==\n"
USAGE=$USAGE" Version:\t\t\t\t\t\t$0 version\n"
USAGEDEBUG="Usage: $0 debug on|off"
if [[ $# -eq 0 || "$1" == "--help" ]]; then
  echo -e $USAGE
  exit 1
fi

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
operations=( create delete debug backup restore default cache version enable disable add remove list details change )
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
  $DIR/modules/cache.sh clear nometa
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

## Add
if [[ $currentOperation == "add" ]]; then
  addOperation=( endpoint namespace variable )
  if [[ ${addOperation[@]} =~ $2 && $2 != "" && $3 != ""  ]]
  then
    addOperation=$2
  else
    echo -e "Option '$2'not supported. Operation aborted\n" >&2
    echo -e $USAGE
    exit 1
  fi
  cd $DIR/..
  args=$@
  php $DIR/modules/add-$addOperation.php $3 "${4}"
  rc=$?
  if [[ $rc = 123 ]] ; then
    echo -e "The $addOperation with prefix '$3' already exist, please remove it first." >&2
    exit
  fi
  if [[ $rc = 124 ]] ; then
    echo -e "The $addOperation with did not stated with 'conf' or 'lodspk'. Please correct that." >&2
    exit
  fi
  echo -e "The $addOperation $4 was added successfully as $3!" >&2
  exit
fi

## Remove
if [[ $currentOperation == "remove" ]]; then
  addOperation=( endpoint namespace variable )
  if [[ ${addOperation[@]} =~ $2 && $2 != "" && $3 != "" ]]
  then
    addOperation=$2
  else
    echo -e "Options '$2' and '$3' not supported. Operation aborted\n" >&2
    echo -e $USAGE
    exit 1
  fi
  cd $DIR/..
  php $DIR/modules/remove-$addOperation.php ${3}
  rc=$?
  if [[ $rc != 0 ]] ; then
    echo -e "Something went wrong while removing '$3'. Please check your settings.inc.php" >&2
    exit
  fi
  echo -e "The $addOperation $3 was removed successfully!" >&2
  exit
fi

## Enable
if [[ $currentOperation == "enable" ]]; then
  enableOperation=( module )
  if [[ ${enableOperation[@]} =~ $2 && $2 != "" && $3 != ""  && $4 != "" ]]
  then
    enableOperation=$2
  else
    echo -e "Options '$2', '$3' and '$4' are not supported. Operation aborted\n" >&2
    echo -e $USAGE
    exit 1
  fi
  cd $DIR/..
  moduleDir="classes/modules/"
  moduleFile=$moduleDir$3"Module.php"
  if [ -f "$moduleFile" ]
  then
    php $DIR/modules/enable-$enableOperation.php $3 $4
    rc=$?
    if [[ $rc != 0 ]] ; then
      echo -e "Something went wrong while enable $3. Please check your settings.inc.php." >&2
      exit
    fi
    echo -e "Module $3 enabled successfully" >&2    
  else
    echo -e "Module $3 does N O T exist!" >&2
  fi
  exit
fi


## Default
if [[ $currentOperation == "default" ]]; then
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
  exit
fi  


## Disable
if [[ $currentOperation == "disable" ]]; then
  disableOperation=( module types )
  if [[ ${disableOperation[@]} =~ $2 && $2 != "" && $3 != ""  ]]
  then
    disableOperation=$2
  else
    echo -e "Options '$2' and '$3' are not supported. Operation aborted\n" >&2
    echo -e $USAGE
    exit 1
  fi
  cd $DIR/..
  moduleDir="classes/modules/"
  moduleFile=$moduleDir$3"Module.php"
  if [ -f "$moduleFile" ]
  then
    php $DIR/modules/disable-$disableOperation.php $3 $4
    rc=$?
    if [[ $rc != 0 ]] ; then
      echo -e "Something went wrong while disabling $3. Please check your settings.inc.php." >&2
      exit
    fi
    echo -e "$3 disabled" >&2    
  else
    echo -e "$3 does N O T exist!" >&2
  fi
  exit
fi


## List
if [[ $currentOperation == "list" ]]; then
  listOperation=( endpoints modules components namespaces )
  if [[ ${listOperation[@]} =~ $2 && $2 != "" ]]
  then
    listOperation=$2
  else
    echo -e "Option '$2' not supported. Operation aborted\n" >&2
    echo -e $USAGE
    exit 1
  fi
  cd $DIR/..
  if [[ $listOperation == "components" ]]
  then
    $DIR/modules/list-$listOperation.sh
  else
    php $DIR/modules/list-$listOperation.php
  fi
  exit
fi


## Details
if [[ $currentOperation == "details" ]]; then
  if [ "$#" != "3" ]; then
    echo -e $USAGE
    exit 1
  fi
  detailOperation=( type service uri )
  if [[ ${detailOperation[@]} =~ $2 && $2 != "" ]]
  then
    detailOperation=$2
  else
    echo -e "Option '$2' not supported. Operation aborted\n" >&2
    echo -e $USAGE
    exit 1
  fi
  cd $DIR/..
  $DIR/modules/detail-component.sh $detailOperation $3
  exit
fi

## Change
if [[ $currentOperation == "change" ]]; then
  if [ "$#" != "3" ]; then
    echo -e $USAGE
    exit 1
  fi
  changeOperation=( password )
  if [[ ${changeOperation[@]} =~ $2 && $2 != "" ]]
  then
    changeOperation=$2
  else
    echo -e "Option '$2' not supported. Operation aborted\n" >&2
    echo -e $USAGE
    exit 1
  fi
  if [[ $3 == "" ]]; then
    echo "Error: No new password given"
    echo -e $USAGE;
    exit 1
  fi
  php $DIR/modules/change-password.php $3
  exit
fi
