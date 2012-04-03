#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='post'

modelHtml=`cat  <<QUERY
{%for h in base.header %}
PREFIX {{h.prefix}}: <{{h.ns}}>
{%endfor%}
#PREFIX dc: <http://purl.org/dc/elements/1.1/>
#INSERT { <http://example/egbook3> dc:title  "This is an example title" }
QUERY`

viewHtml=`cat  <<VIEW
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" {% for i, ns in lodspk.ns %}xmlns:{{i}}="{{ns}}" {%endfor%}
      version="XHTML+RDFa 1.0" xml:lang="en">
  <head>
    <title>$1</title>
    <link href="{{lodspk.baseUrl}}css/basic.css" rel="stylesheet" type="text/css" media="screen" />
    <style type="text/css">
    </style>
  </head>
  <body>
    <h1>$1</h1>
    This is a post service
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

echo -e "$modelHtml" > $mainDir/queries/main.update
echo -e "$viewHtml" > $mainDir/html.template

echo $initToken.$1 created/modified successfully! >&2
