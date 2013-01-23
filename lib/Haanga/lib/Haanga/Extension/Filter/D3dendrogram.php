<?php

class Haanga_Extension_Filter_D3Dendrogram{
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
  	$options['width'] = 600;
  	$options['height'] = 500;
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
  	$rootNode = NULL;
  	$id = $varList[0]['name'];
  	$parentId = $varList[1]['name'];
  	foreach($obj as $k){
  	  if(!isset($k->$parentId)){
  	    //root
  	    $rootNode = $k->$id->value;
  	  }else{
  	    if(!isset($maps[$k->$parentId->value])){
  	      $maps[$k->$parentId->value] = array();
  	    }
  	    array_push($maps[$k->$parentId->value], $k->$id->value);
  	  }
  	}  	
  	
  	function travel($node, $tree){
  	  $total = array();
  	  foreach($tree[$node] as $v){
  	    $branch = array();
  	    if(!isset($tree[$v])){
  	      $branch['value'] = 1000;
  	    }else{
  	      $branch['children'] = travel($v, $tree);
  	    }
  	    $branch['name'] = $v;
  	    array_push($total, $branch);
  	  }
  	  return $total;
  	}
  	
  	$json['name'] = $rootNode;
  	$json['children'] = travel($rootNode, $maps);
  	
  	
  	$pre = '<div id="dendrogram'.$randId.'"><div id="name'.$randId.'" style="font-family:sans-serif;font-size:15px;height:25px"><h2> </h2></div></div><script src="http://d3js.org/d3.v2.min.js?2.9.3"></script>
<script>
// Based on http://bost.ocks.org/mike/treemap/ 
function initD3TreeMaps'.$randId.'(json){
var width = '.$options['width'].',
    height = '.$options['height'].';
  
  var cluster = d3.layout.cluster()
      .size([height, width - 200]);
  
  var diagonal = d3.svg.diagonal()
      .projection(function(d) { return [d.y, d.x]; });
  
 var vis = d3.select("#dendrogram'.$randId.'").append("svg")
     .attr("width", width)
     .attr("height", height)
   .append("g")
     .attr("transform", "translate(80, 0)");
 
   var nodes = cluster.nodes(json);
 
   var link = vis.selectAll("path.link")
       .data(cluster.links(nodes))
     .enter().append("path").style("fill", "none").style("stroke", "#CCC")
       .attr("class", "link")
       .attr("d", diagonal);
 
   var node = vis.selectAll("g.node")
       .data(nodes)
     .enter().append("g")
       .attr("class", "node")
       .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; })
 
   node.append("circle")
       .attr("r", 4.5).style("fill", "white").style("stroke", "steelblue").style("stroke-width", "1.5px");
 
   node.append("text").style("font", "10px sans-serif")
       .attr("dx", function(d) { return d.children ? -8 : 8; })
       .attr("dy", 3)
       .attr("text-anchor", function(d) { return d.children ? "end" : "start"; })
       .text(function(d) { return d.name; });
 
}
    
var jsonD3'.$randId.' = '.json_encode($json).';
initD3TreeMaps'.$randId.'(jsonD3'.$randId.')
</script>';
  	return $pre.$post;
  }
}
