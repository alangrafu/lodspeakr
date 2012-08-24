SELECT DISTINCT ?resource WHERE {
  {
    GRAPH {%if lodspk.args.arg0 %}<{{lodspk.args.arg0}}>{%else%}?g{%endif%} {
  	  [] a ?resource .
    }
  }UNION{
    [] a ?resource .
  }
}
LIMIT 10
