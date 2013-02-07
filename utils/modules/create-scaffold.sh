#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
initToken='services'
serviceName=`echo $2 |sed 's/\//%2F/g'`
mainDir=$DIR/../../components/$initToken/$serviceName/

if [ ! -d "$mainDir" ]; then
  echo "No service $serviceName found"
  exit 1
fi

scaffold="@prefix lodspk: <http://lodspeakr.org/vocab/> .\n"
scaffold+="@prefix dcterms: <http://purl.org/dc/terms/> .\n"
scaffold+="\n"
scaffold+="<> a lodspk:ScaffoldedService ;\n"


finished_asking="y"
confirmed_creation="n"
while [ "$confirmed_creation" != "y" ]; do
  patternCounter=1
  regexes=()
  while [ "$finished_asking" != "n" ]; do
    echo
    echo "==== NEW SCAFFOLD PATTERN ($patternCounter) ==="
    echo
    while [ "$regex_correct" != "y" ]; do
      echo -n "What pattern the arguments should follow (Hint: use regular expressions)? "
      regex_correct="n"
      read -r regex
      echo -n "Is '$regex' correct? "
      read regex_correct
      echo
    done
    regex_correct="n"
    let patternCounter=$patternCounter+1 
    #add pattern
    regexes+=($regex)
    echo -n "Do you want to add a new a new pattern [y/n]? "
    read finished_asking
  done
  echo
  echo
  echo "==== PATTERNS ===="
  echo "These are the regular expressions you entered"
  echo
  echo
  for var in "${regexes[@]}"
  do
    echo "${var}"
  done
  echo 
  echo
  echo -n "Confirm they are OK [y/n]? "
  read confirmed_creation
  finished_asking="y"
done

patternCounter=0
for var in "${regexes[@]}"
do
  scaffold+="   lodspk:scaffold <#pattern$patternCounter> ;\n"
  let patternCounter=$patternCounter+1
done
scaffold+="   dcterms:identifier \"$2\" .\n\n\n"

patternCounter=0
for var in "${regexes[@]}"
do
  currentPattern=$(echo ${regexes[$patternCounter]} |sed -e 's|\\|\\\\\\|g')
  scaffold+="<#pattern$patternCounter> a lodspk:Pattern; \n"
  scaffold+="                          lodspk:uriPattern \""$currentPattern"\"; \n"
  scaffold+="                          dcterms:identifier \""$(echo $patternCounter)"\"; \n"
  scaffold+="                          lodspk:subComponent \"pattern"$(echo $patternCounter)"\". \n"
  patternDir=$mainDir"/pattern"$patternCounter
  mkdir -p $patternDir
  cp -rf $DIR/../defaults/service/* $patternDir
  let patternCounter=$patternCounter+1
done

echo -e ${scaffold} > $mainDir/scaffold.ttl

echo "Scaffold created"
exit 0

