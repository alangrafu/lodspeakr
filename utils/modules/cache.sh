#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cacheDir=$DIR/../../cache
metaDir=$DIR/../../meta
if [[ $1 == "clear" ]]; then
  if [ -d "$cacheDir" ]; then
    rm -f $cacheDir/*
    if [[ $2 != "nometa" ]]; then
      sqlite3 $metaDir/db.sqlite 'delete from document'
    fi
  else
    echo "ERROR: Couldn't find cache directory" >&2 
    exit 1
  fi
else
    echo "ERROR: Invalid command" >&2 
    exit 1  
fi
