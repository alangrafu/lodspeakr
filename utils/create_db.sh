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
  $SQLITE3 $1 'CREATE INDEX IF NOT EXISTS document_uri ON document(uri);'
  $SQLITE3 $1 'CREATE INDEX IF NOT EXISTS document_uri_format ON document(uri, format);'
  $SQLITE3 $1 'CREATE INDEX IF NOT EXISTS document_doc ON document(doc);'
else
  echo "WARNING: SQLite database already exists."
fi
cd ..
