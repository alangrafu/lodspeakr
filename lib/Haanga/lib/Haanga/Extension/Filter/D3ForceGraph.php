<?php

class Haanga_Extension_Filter_D3ForceGraph{
  public $is_safe = TRUE;
  static function main($obj, $varname){

  	$nodesArr = array();
  	$n=0;
  	$first="";
  	$nodes = "";
  	$links = "";
  	$names = explode(",", $varname);
  	if(count($names)==2){
  	  foreach($obj as $k){
  	  	if(!isset($nodesArr[$k->$names[0]->value])){
  	  	  $nodesArr[$k->$names[0]->value] = $n++;
  	  	  $nodes .= $first."{\"name\": \"".$k->$names[0]->value."\", \"group\": 1}";
  	  	}
  	  	if(!isset($nodesArr[$k->$names[1]->value])){
  	  	  $nodesArr[$k->$names[1]->value] = $n++;
  	  	  $nodes .= ",\n  {\"name\": \"".$k->$names[1]->value."\", \"group\": 1}";
  	  	}
  	  	$links .= $first."{\"source\": ".$nodesArr[$k->$names[0]->value].", \"target\": ".$nodesArr[$k->$names[1]->value].", \"value\": 10}";
  	  	$first = ",\n  ";
  	  } 
  	}
  	
  	  	
  	$json  ='{"nodes":['.$nodes.'],
  	"links":['.$links.']}';
  	
  	
  	$pre = '<script type="text/javascript" src="http://lodspeakr.org/extensions/haanga/filters/d3/js/d3.js"></script>
  	<script type="text/javascript" src="http://lodspeakr.org/extensions/haanga/filters/d3/js/d3.layout.js"></script>
  	<script type="text/javascript" src="http://lodspeakr.org/extensions/haanga/filters/d3/js/d3.geom.js"></script>
  	<link href="http://lodspeakr.org/extensions/haanga/filters/d3/css/force.css" rel="stylesheet" type="text/css" />
  	<script type="text/javascript">
  	var url = "http://alvaro.graves.cl";
  	var data = '.$json.';
  	</script>';
  	$post = '<div style="float: left;border-width: 1px; border-style: solid;" id="chart"></div>
  	<script type="text/javascript" src="http://lodspeakr.org/extensions/haanga/filters/d3/js/force.js">
  	</script>';
  	return $pre.$post;
  }
}
