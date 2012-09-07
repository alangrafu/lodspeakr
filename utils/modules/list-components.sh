#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
components=( types services uris )
cd $DIR

#Components dir
mainDir=$DIR/../../components/

if [ ! -d "$mainDir" ]
then
  echo "ERROR: Components' dir doesn't exist." >&2
  exit 1
fi

cd $mainDir

for i in "${components[@]}"
do
  LIST="$i\n"
  for j in `ls $i`
  do
    if [[ -d "$i/$j" ]]
    then
      NEWLINE=`echo -e "\n\t$j"`
      LIST=$LIST$NEWLINE"\n" 
    fi
  done
  echo -e $LIST
done

