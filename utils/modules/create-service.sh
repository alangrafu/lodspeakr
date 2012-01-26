#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='service'

modelHtml=$(cat  <<QUERY
{%for h in base.header %}
PREFIX {{h.prefix}}: <{{h.ns}}>
{%endfor%}
SELECT DISTINCT ?resource WHERE {
  {%if base.args.arg0 %}GRAPH <{{lodspk.args.arg0}}>{ {%endif%}
  	[] a ?resource .
  {%if base.args.arg0 %} } {%endif%}
}
QUERY)

viewHtml=$(cat  <<VIEW
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
    "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" {% for i, ns in base.ns %}xmlns:{{i}}="{{ns}}" 
    {%endfor%}version="XHTML+RDFa 1.0" xml:lang="en">
  <head>
    <title>My new Service</title>
    <link href="{{lodspk.baseUrl}}/lodspeakr/css/basic.css" rel="stylesheet" type="text/css" media="screen" />
  </head>
  <body>
    <h1>Classes available</h1>
	<ul>
    {% for row in models.main %}
        <li><a href="{{lodspk.baseUrl}}special/instances/{{ row.resource.curie }}">{{row.resource.curie}}</a></li>
    {% endfor %}
    </ul>
  </body>
</html>
VIEW)

modelRdf=$(cat  <<QUERY
DESCRIBE ?resource WHERE {
  	[] a ?resource .
}
QUERY)

viewRdf=$(cat  <<QUERY
{{r|safe}}
QUERY)

modelTtl=$modelRdf
viewTtl=$viewRdf
modelNt=$modelRdf
viewNt=$viewRdf
modelJson=$modelRdf
viewJson=$viewJson

#Check models
mainModelDir=$DIR/../../models/$initToken.$1

if [ -e "$mainModelDir" ]
then
  echo "WARNING: At least one model for $1 exists." >&2
else
  mkdir $mainModelDir
fi

obj=( )
if [ "$2" == "all" ]
then
  obj=( html rdf ttl nt json )
else
  obj=( $2 )
fi

for i in ${obj[@]}
do
  if [ -e $mainModelDir/$i.queries ]
  then
    echo ERROR: $initToken.$1/$i.queries exists in models. Operation aborted >&2
    exit 1
  fi
done


#Check views
mainViewDir=$DIR/../../views/$initToken.$1

if [ -e "$mainViewDir" ]
then
  echo "WARNING: At least one view for $1 exists." >&2
else
  mkdir $mainViewDir
fi


for i in ${obj[@]}
do
  if [ -e $mainViewDir/$i.template ]
  then
    echo ERROR: $initToken.$1/$i already exist in views. Operation aborted >&2
    exit 1
  fi
done


#Create  file structure

for i in ${obj[@]}
do
  mkdir $mainModelDir/$i.queries
  if [ "$i" == "html" ]
  then
    echo "$modelHtml" > $mainModelDir/$i.queries/main.query
    echo "$viewHtml" > $mainViewDir/$i.template
  else
    echo "$modelRdf" > $mainModelDir/$i.queries/main.query
    echo "$viewRdf" > $mainViewDir/$i.template   
  fi
done

echo $initToken.$1 created/modified successfully! >&2
