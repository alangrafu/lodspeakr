<?php

class Haanga_Extension_Filter_D3ForceGraph{
  public $is_safe = TRUE;
  static function main($obj, $varname){

  	$nodesArr = array();
  	$n=0;
  	$first="";
  	$nodes = array();
  	$links = array();
  	$names = explode(",", $varname);
  	$varList = array();

  	foreach($names as $v){
  	  if(strpos($v,"=")){
  	    break;
  	  }
  	  $variable['name'] = $v;
  	  $variable['value'] = 'value';
  	  if(strpos($v, ".")){
  	    $aux = explode(".", $v);
  	    $variable['name'] = $aux[0];
  	    $variable['value'] = $aux[1];
  	  }
  	  $fieldCounter++;
  	  $columnType = 'number';
  	  if($firstColumn){
  	  	$columnType = 'string';
  	  	$firstColumn = false;
  	  }
  	  array_push($varList, $variable);
  	  //$data .= "        data.addColumn('".$columnType."', '".$variable['name']."');\n";
  	}

  	
  	
  	foreach($obj as $k){
  	    $nameSource = $varList[0]['name'];
  	    $valSource = $varList[0]['value'];
   	    if(!isset($nodesArr[$k->$nameSource->value])){
   	      $nodesArr[$k->$nameSource->value] = $n++;
   	      array_push($nodes, array("name" => $k->$nameSource->$valSource));
   	    }
   	    $nameTarget = $varList[1]['name'];
  	    $valTarget = $varList[1]['value'];
   	    if(!isset($nodesArr[$k->$nameTarget->value])){
   	      $nodesArr[$k->$nameTarget->value] = $n++;
   	      array_push($nodes, array("name" => $k->$nameTarget->$valTarget));
   	    }
  	    array_push($links, array("source" => $nodesArr[$k->$nameSource->value], "target" => $nodesArr[$k->$nameTarget->value], "type" => "suit"));

  	}  	

  	$json['nodes'] = $nodes;
  	$json['links'] = $links;
  	
  	
  	$pre = '<script src="http://d3js.org/d3.v2.min.js?2.9.3"></script>
<script>

var width = 960,
    height = 500

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height);

svg.append("svg:defs").append("svg:marker").attr("id", "marker")
    .attr("viewBox", "0 -5 10 10")
    .attr("refX", 15)
    .attr("refY", -1.5)
    .attr("markerWidth", 6)
    .attr("markerHeight", 6)
    .attr("orient", "auto")
  .append("svg:path")
    .attr("d", "M0,-5L10,0L0,5");

    
var force = d3.layout.force()
    .gravity(.05)
    .distance(100)
    .charge(-100)
    .size([width, height]);

 function initD3ForceGraph(json) {
  force
      .nodes(json.nodes)
      .links(json.links)
      .start();

  var link = svg.append("svg:g").selectAll("path")
    .data(force.links())
  .enter().append("svg:path")
    .attr("class", function(d) { return "link " + d.type; })
    .attr("marker-end", "url(#marker)");

  var node = svg.selectAll(".node")
      .data(json.nodes)
    .enter().append("g")
      .attr("class", "node")
      .call(force.drag);
      
  node.append("circle").attr("r", 10).style("fill", "#aec7e8").style("stroke", "#798ba2")
 
  node.append("text").style("font", "10px sans-serif")
      .attr("dx", 12)
      .attr("dy", ".35em")
      .text(function(d) { return d.name });

  force.on("tick", function() {
    link.attr("d", function(d) {
    var dx = d.target.x - d.source.x,
        dy = d.target.y - d.source.y,
        dr = Math.sqrt(dx * dx + dy * dy);
    return "M" + d.source.x + "," + d.source.y + "A" + dr + "," + dr + " 0 0,1 " + d.target.x + "," + d.target.y;
  });
    node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
  });
}
var jsonD3 = '.json_encode($json).';
initD3ForceGraph(jsonD3)
</script>';
  	return $pre.$post;
  }
}
