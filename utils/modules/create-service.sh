#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='services'
cd $DIR
modelHtml=`cat service-model.inc`

viewHtml=`cat  service-view.inc`

#Check models
mainDir=$DIR/../../components/$initToken/$1/

if [ -e "$mainDir" ]
then
  echo "WARNING: At least one model for $1 exists." >&2
else
  mkdir -p $mainDir/queries
fi

echo -e "$modelHtml" > $mainDir/queries/main.query
echo -e "$viewHtml" > $mainDir/html.template

echo $initToken.$1 created/modified successfully! >&2
