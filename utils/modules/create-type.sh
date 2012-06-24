#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='types'

cd $DIR
modelHtmlSP=`cat type-model-1.inc`

modelHtmlPO=`cat type-model-2.inc`

viewHtml=`cat type-view.inc`


#Check models
mainDir=$DIR/../../components/$initToken/$1/

if [ -e "$mainDir" ]
then
  echo "WARNING: At least one model for $1 exists." >&2
else
  mkdir -p $mainDir/queries
fi

echo -e "$modelHtmlSP" > $mainDir/queries/sp.query
echo -e "$modelHtmlPO" > $mainDir/queries/po.query
echo -e "$viewHtml" > $mainDir/html.template

echo $1 created successfully! >&2
