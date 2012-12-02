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
  	$randId = uniqid("_ID_");

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

  	//options
  	$options = array();
  	$options['width'] = 960;
  	$options['height'] = 500;
  	$options['color'] = '#aec7e8';
  	$options['radius'] = 10;
  	$options['gravity'] = 0.05;
  	$options['distance'] = 100;
  	$options['charge'] = -100;
  	for($z=2; $z < count($names); $z++){
      $pair = explode("=", $names[$z]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
      $options[$key] = $value;     
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
  	
  	
  	$pre = '<div id="'.$randId.'"></div><script src="http://d3js.org/d3.v2.min.js?2.9.3"></script>
<script>
/*
Based on
http://jsfiddle.net/nrabinowitz/QMKm3/
http://bl.ocks.org/3680999
*/
 function initD3ForceGraph'.$randId.'(json) {
  
  var width = '.$options['width'].',
  height = '.$options['height'].'
  

 var svg = d3.select("#'.$randId.'")
  .append("svg:svg")
    .attr("width", width)
    .attr("height", height)
    .attr("pointer-events", "all")
  .append("svg:g")
    .call(d3.behavior.zoom().scaleExtent([0.1, 20]).on("zoom", redraw))
  .append("svg:g");

svg.append("svg:rect")
    .attr("width", width)
    .attr("height", height)
    .attr("fill", "white");

function redraw() {
  svg.attr("transform",
      "translate(" + d3.event.translate + ")"
      + " scale(" + d3.event.scale + ")");
}

  
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
  .gravity('.$options['gravity'].')
  .distance('.$options['distance'].')
  .charge('.$options['charge'].')
  .size([width, height]);
  
  force
  .nodes(json.nodes)
  .links(json.links)
  .start();
  
  var link = svg.append("svg:g").selectAll("path")
  .data(force.links())
  .enter().append("svg:path").style("fill", "none").style("stroke", "#999").style("stroke-width", "1.8px")
  .attr("class", function(d) { return "link " + d.type; })
  .attr("marker-end", "url(#marker)");
  
  var node = svg.selectAll(".node")
  .data(json.nodes)
  .enter().append("g")
  .attr("class", "node")
  .call(force.drag);
  
  node.append("circle").attr("r", '.$options['radius'].').style("fill", "'.$options['color'].'").style("stroke", "#798ba2")
  
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

var jsonD3'.$randId.' = '.json_encode($json).';
initD3ForceGraph'.$randId.'(jsonD3'.$randId.')
</script>';
  	return $pre.$post;
  }
}
