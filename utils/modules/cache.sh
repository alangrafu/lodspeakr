#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cacheDir=$DIR/../../cache

if [[ $1 == "clear" ]]; then
  if [ -d "$cacheDir" ]; then
    rm -f $cacheDir/*
  else
    echo "ERROR: Couldn't find cache directory" >&2 
    exit 1
  fi
else
    echo "ERROR: Invalid command" >&2 
    exit 1  
fi
