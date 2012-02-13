#!/bin/bash

if [[ "$1" = "" ]]; then
  echo Usage: $0 sqliteFile
  exit 1
fi

mkdir -p meta
cd meta
if [ ! -e $1 ]; then
  SQLITE3=`which sqlite3`
  if [ -z "$SQLITE3" ]; then
  	echo "SQLlite3 is required to continue installation. Please add it to your \$PATH."
  	exit 1
  fi
  $SQLITE3 $1 'CREATE TABLE document (uri varcharg(1000), doc varchar(1000), format varchar(50));'
else
  echo "WARNING: SQLite database already exists."
fi
cd ..
