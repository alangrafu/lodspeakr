#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
components=( types services uris )
cd $DIR

#Components dir
mainDir=$DIR"/../../components/"$1
componentDir=$mainDir"/"$2
queryDir=$componentDir"/queries"
if [ ! -d "$mainDir" ]
then
  echo "ERROR: "$1"' dir doesn't exist. $mainDir" >&2
  exit 1
fi

if [ ! -d "$componentDir" ]
then
  echo "ERROR: "$2"' dir ($componentDir) doesn't exist." >&2
  exit 1
fi

cd $componentDir
views=`ls *.template`

#In certain cases queries may not exist but that's fine

if [  -d "$componentDir" ]
then
  cd $queryDir
  models=`find . -iname "*.query" |sed -e 's/^\.\///g'`
fi


echo -n "views"
for i in $views
do
  NEWLINEVIEW="\n\t$i"
  LIST=$LIST$NEWLINEVIEW 
done
echo -e $LIST


echo -n "models"
for i in $models
do
  NEWLINEMODEL="\n\t$i"
  MODELLIST=$MODELLIST$NEWLINEMODEL 
done
echo -e $MODELLIST


