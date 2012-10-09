#!/bin/bash

PIDFILE=/tmp/fusekiPid
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"


if [ -f $PIDFILE ]
then
  exit 1
fi
fusekiDir=$DIR/../../lib/fuseki
cacheDir=$DIR/../../cache
cd $fusekiDir

./fuseki-server --loc data --update /ds  &> /dev/null  &
echo $! > $PIDFILE

exit
