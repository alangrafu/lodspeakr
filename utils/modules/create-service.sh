#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='services'

modelHtml=`cat  <<QUERY
{%for h in base.header %}
PREFIX {{h.prefix}}: <{{h.ns}}>
{%endfor%}
SELECT DISTINCT ?resource WHERE {
  GRAPH {%if base.args.arg0 %}<{{lodspk.args.arg0}}>{%else%}?g{%endif%} {
  	[] a ?resource .
  }
}
QUERY`

viewHtml=`cat  <<VIEW
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
    "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" {% for i, ns in base.ns %}xmlns:{{i}}="{{ns}}" 
    {%endfor%}version="XHTML+RDFa 1.0" xml:lang="en">
  <head>
    <title>My new Service</title>
    <link href="{{lodspk.baseUrl}}css/basic.css" rel="stylesheet" type="text/css" media="screen" />
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
VIEW`

#Check models
mainDir=$DIR/../../components/$initToken/$1/

if [ -e "$mainDir" ]
then
  echo "WARNING: At least one model for $1 exists." >&2
else
  mkdir -p $mainDir/queries
fi

echo $modelHtml > $mainDir/queries/main.query
echo $viewHtml > $mainDir/html.template

echo $initToken.$1 created/modified successfully! >&2
