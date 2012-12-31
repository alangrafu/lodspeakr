<?php

class Haanga_Extension_Filter_D3CirclePacking{
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
  	$options['height'] = 600;
  	$options['color'] = '#aec7e8';
  	$options['highlightedColor'] = '#00477f';
  	$options['radius'] = 10;
  	$options['highlightedStrokeWidth'] = '3px';
  	$options['strokeWidth'] = '1px';
  	for($z=2; $z < count($names); $z++){
      $pair = explode("=", $names[$z]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
      $options[$key] = $value;     
    }

  	$maps = array();
  	$quantities = array();
  	$links = array();
  	$rootNode = NULL;
  	$id = $varList[0]['name'];
  	$quantity  = $varList[2]['name'];
  	$link  = $varList[3]['name'];
  	$parentId = $varList[1]['name'];

    foreach($obj as $k){
  	    if(!isset($maps[$k->$parentId->value])){
  	      $maps[$k->$parentId->value] = array();
  	    }
  	    if(isset($k->$quantity)){
  	      $quantities[$k->$id->value] = $k->$quantity->value;
  	    }
  	    if(isset($k->$link)){
  	      $links[$k->$id->value] = $k->$link->value;
  	    }
  	    array_push($maps[$k->$parentId->value], $k->$id->value);
  	}  
  	function travelCirclePacking($node, $tree, $quantities, $links){
  	  $total = array();
  	  foreach($tree[$node] as $v){
  	    $branch = array();
  	    if(!isset($tree[$v])){
  	      if(isset($quantities[$v])){
  	        $branch['size'] = $quantities[$v];
  	      }
  	      if(isset($links[$v])){
  	        $branch['link'] = $links[$v];
  	      }
  	    }else{
  	      $branch['children'] = travelCirclePacking($v, $tree, $quantities, $links);
  	    }
  	    $branch['name'] = $v;
  	    array_push($total, $branch);
  	  }
  	  return $total;
  	}
  	
  	$json['name'] = $rootNode;
  	$json['children'] = travelCirclePacking($rootNode, $maps, $quantities, $links);
  	
  	
  	$pre = '<div id="clusterpacking'.$randId.'"><div id="name'.$randId.'" style="font-family:sans-serif;font-size:15px;height:25px"><h2> </h2></div></div><script src="http://d3js.org/d3.v2.min.js?2.9.3"></script>
<script>
// Based on http://bost.ocks.org/mike/treemap/ 
function initD3CirclePacking'.$randId.'(json){
var width = '.$options['width'].',
    height = '.$options['height'].',
    format = d3.format(",d");

var pack = d3.layout.pack()
    .size([width - 4, height - 4])
    .value(function(d) { return d.size; });

var vis = d3.select("#clusterpacking'.$randId.'").append("svg")
    .attr("width", width)
    .attr("height", height)
    .attr("class", "pack").style("font", "10px sans-serif")
  .append("g")
    .attr("transform", "translate(2, 2)");

  var node = vis.data([json]).selectAll("g.node")
      .data(pack.nodes)
    .enter().append("g")
      .attr("class", function(d) { return d.children ? "node" : "leaf node"; })
      .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

  node.append("title")
      .text(function(d) { return d.name + (d.children ? "" : ": " + format(d.size)); });

  node.append("circle").style("fill", function(d) { return d.children ? "rgb(31, 119, 180)" : "#ff7f0e"; })
      .style("fill-opacity", function(d) { return d.children ? ".25" : "1"; })
      .style("stroke", "rgb(31, 119, 180)").style("stroke-width", "1px")
      .attr("r", function(d) { return d.r; });

  node.filter(function(d) { return !d.children && !d.link; }).append("text")
      .attr("text-anchor", "middle")
      .attr("dy", ".3em")
      .text(function(d) { return d.name.substring(0, d.r / 3); });
      
  node.filter(function(d) { return !d.children && d.link; }).append("a").attr("xlink:href", function(d){return d.link}).append("text")
      .attr("text-anchor", "middle")
      .attr("dy", ".3em")
      .text(function(d) { return d.name.substring(0, d.r / 3); });


}
    
var jsonD3'.$randId.' = '.json_encode($json).';
initD3CirclePacking'.$randId.'(jsonD3'.$randId.')
</script>';
  	return $pre.$post;
  }
}
