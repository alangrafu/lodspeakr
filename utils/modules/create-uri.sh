#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='uris'

modelHtmlSP=`cat <<QUERY
SELECT ?s ?p  WHERE {
  {
    GRAPH ?g{
                ?s ?p <{{uri}}> .
    }
  }UNION{     
                ?s ?p <{{uri}}> .
  }
}
QUERY`

modelHtmlPO=`cat <<QUERY
SELECT ?p ?o  WHERE {
  {
    GRAPH ?g{
                <{{uri}}> ?p ?o.
    }
  }UNION{     
                <{{uri}}> ?p ?o .
  }
}
QUERY`


viewHtml=`cat <<VIEW
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
    "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" {% for i, ns in lodspk.ns %}xmlns:{{i}}="{{ns}}" 
    {%endfor%}version="XHTML+RDFa 1.0" xml:lang="en">
    <head>
    <title>Page about {{lodspk.this.value}}</title>
    <link href="{{lodspk.home}}css/basic.css" rel="stylesheet" type="text/css" media="screen" />
    <link rel="alternate" type="application/rdf+xml" title="RDF/XML Version" href="{{lodspk.this.value}}.rdf" />
    <link rel="alternate" type="text/turtle" title="Turtle Version" href="{{lodspk.this.value}}.ttl" />
    <link rel="alternate" type="text/plain" title="N-Triples Version" href="{{lodspk.this.value}}.nt" />
    <link rel="alternate" type="application/json" title="RDFJSON Version" href="{{lodspk.this.value}}.json" />
  </head>
  <body about="{{lodspk.this.value}}">
    <h1>Default view</h1>

    <div style='margin-top: 40px'>
    <table>
    <tr><th>Subject</th><th>Predicate</th><th>Object</th></tr>
    {% for row in models.po %}
     <tr>
<td>{%if forloop.first%}<a href='{{lodspk.this.value}}'>{{lodspk.this.curie}}</a>{%endif%}</td>
     <td style='background-color:#c9f9c9'><a href='{{row.p.value}}'>{{row.p.curie}}</a></td>
     
        <td style='background-color:#c9f9c9'>
        {%if row.o.uri == 1%}
        <a rev='[{{row.p.curie}}]' href='{{row.o.value}}'>{{row.o.curie}}</a>
        {%else%}
        {{row.o.value}}
        {%endif%}
        </td>

        </tr>
    {% endfor %}
<tr><td></td><td><a href='{{lodspk.this.value}}'>{{lodspk.this.curie}}</a></td><td></td></tr>

    {% for row in models.sp %}
      <tr>
        <td style='background-color:#c9f9c9'><a href='{{row.s.value}}'>{{row.s.curie}}</a></td>

        <td style='background-color:#c9f9c9'><a rel='{{row.s.curie}}' href='{{row.p.value}}'>{{row.p.curie}}</a></td>
<td>{%if forloop.first%}<a href='{{lodspk.this.value}}'>{{lodspk.this.curie}}</a>{%endif%}</td>
        </tr>
    {% endfor %}
    <tr><th>Subject</th><th>Predicate</th><th>Object</th></tr>

    </table>
    </div>    
    
    <br/>
  </body>
</html>
VIEW`


#Check models
mainDir=$DIR/../../components/$initToken/$1

if [ -e "$mainDir" ]
then
  echo "WARNING: At least one model for $1 exists." >&2
else
  mkdir -p $mainDir
fi

#Create  file structure

mkdir $mainDir/queries
echo -e "$modelHtmlSP" > $mainDir/queries/sp.query
echo -e "$modelHtmlPO" > $mainDir/queries/po.query
echo -e "$viewHtml" > $mainDir/html.template

echo $initToken.$1 created/modified successfully! >&2
