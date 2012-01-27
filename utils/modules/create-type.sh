#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

initToken='type'

modelHtml=$(cat <<QUERY
SELECT ?s2 ?p2 ?s1 ?p1  WHERE {
  {
    GRAPH ?g{
              {
                <{{uri}}> ?s1 ?p1 .
        }UNION{
                ?s2 ?p2 <{{uri}}> .
        }
    }
  }UNION{
        {
                <{{uri}}> ?s1 ?p1 .
        }UNION{
                ?s2 ?p2 <{{uri}}> .
        }
  }
}
QUERY
)


viewHtml=$(cat <<VIEW
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
    "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" {% for i, ns in base.ns %}xmlns:{{i}}="{{ns}}" 
    {%endfor%}version="XHTML+RDFa 1.0" xml:lang="en">
    <head>
    <title>Page about {{lodspk.this.value}}</title>
    <link href="{{lodspk.baseUrl}}css/basic.css" rel="stylesheet" type="text/css" media="screen" />
    <link rel="alternate" type="application/rdf+xml" title="RDF/XML Version" href="{{lodspk.this.value}}.rdf" />
    <link rel="alternate" type="text/turtle" title="Turtle Version" href="{{lodspk.this.value}}.ttl" />
    <link rel="alternate" type="text/plain" title="N-Triples Version" href="{{lodspk.this.value}}.nt" />
    <link rel="alternate" type="application/json" title="RDFJSON Version" href="{{lodspk.this.value}}.json" />
  </head>
  <body about="{{lodspk.this.value}}">
    <h1>Page about <a href='{{lodspk.this.value}}'>{{lodspk.this.curie}}</a></h1>
    <br/>
    <h2>Class $1</h2>
  <div>
    <h2>Information from {{lodspk.this.curie}}</h2>
    <table>
    {% for row in models.main %}

      {% if row.s1%}
      <tr>
        <td><a href='{{row.s1.value}}'>{{row.s1.curie}}</a></td>

        {% if row.p1.uri == 1 %}
        <td><a rel='{{row.s1.curie}}' href='{{row.p1.value}}'>{{row.p1.curie}}</a></td>
        {% else %}
        <td><span property='{{row.s1.curie}}'>{{row.p1.value}}</span></td>
        {% endif %}

        </tr>
      {% endif %}
    {% endfor %}
    </table>

    <br/><br/>
    <h2>Information pointing to {{lodspk.this.curie}}</h2>
    <table>
    {% for row in models.main %}
      {% if row.s2%}
     <tr>
        <td><a href='{{row.s2.value}}'>{{row.s2.curie}}</a></td>
        <td><a rev='[{{row.p2.curie}}]' href='{{row.s2.value}}'>{{row.p2.curie}}</a></td>
    </tr>
    {%endif %}
    {% endfor %}
    </table>
    </div>
  </body>
</html>
VIEW)

modelRdf=$(cat <<QUERY
DESCRIBE ?resource WHERE {
  	[] a ?resource .
}
QUERY)

viewRdf=$(cat <<VIEW
{{models.main|safe}}
VIEW)

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
