<?php

class Haanga_Extension_Filter_D3RadarChart{
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
  	}
  	
  	//options
  	$options = array();
  	$options['width'] = 400;
  	$options['height'] = 400;
  	for($z=$fieldCounter; $z < count($names); $z++){
      $pair = explode("=", $names[$z]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
      $options[$key] = $value;     
    }
    $optionJson = json_encode($options);
  	$rows = array();
  	foreach($obj as $k){
  	  $row = array();
  	  foreach($varList as $v){
  	    $variable = $v['name'];
  	    $val = $v['value'];
  	    $row[] = array('axis' => $variable, 'value' => floatval($k->$variable->$val));
  	  }
  	  array_push($rows, $row);
  	}  	

  	$json = $rows;
  	
  	
  	$pre = '<div id="'.$randId.'"></div>
<script src="http://d3js.org/d3.v2.min.js?2.9.3"></script>
<script src="https://raw.github.com/alangrafu/radar-chart-d3/master/src/radar-chart-min.js"></script>  	
<script>
var jsonD3'.$randId.' = '.json_encode($json).';
options'.$randId.' = '.$optionJson.';
RadarChart.draw("#'.$randId.'", jsonD3'.$randId.', options'.$randId.');    
</script>';
  	return $pre.$post;
  }
}
