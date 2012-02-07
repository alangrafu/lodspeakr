#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='type'


#Check models
mainModelDir=$DIR/../../models/$initToken.$1
mainViewDir=$DIR/../../views/$initToken.$1

if [ ! -e "$mainModelDir" ]
then
  echo "ERROR: $initToken.$1 doesn't exist in models. Operation aborted" >&2
  exit 1
fi

obj=( )
if [ "$2" == "all" ]
then
  rm -rf $mainModelDir
  rm -rf $mainViewDir
  echo $initToken.$1 deleted >&2
  exit
else
  obj=( $2 )
fi

for i in ${obj[@]}
do
  if [ ! -e $mainModelDir/$i.queries ]
  then
    echo "WARNING: $initToken.$1/$i.query does not exists in models." >&2
  fi
done


#Check views

if [ ! -e "$mainViewDir" ]
then
  echo "ERROR: $initToken.$1 doesn't exist in views. Operation aborted." >&2
fi


for i in ${obj[@]}
do
  if [ ! -e $mainViewDir/$i.template ]
  then
    echo "WARNING: $mainViewDir/$i.template doesn't exist in views." >&2
  fi
done


#Delete  file structure

if [ "$2" == "all" ]
then
  rm -rf $mainModelDir
  rm -rf $mainViewDir
else
  for i in ${obj[@]}
  do
    rm -rf  $mainModelDir/$i.queries
    rm -rf  $mainViewDir/$i.template   
  done
fi
echo $initToken.$1 deleted successfully! >&2
