<?php

class Haanga_Extension_Filter_Timeknot{
  public $is_safe = TRUE;
  static function main($obj, $varname){
    global $lodspk;
  	$data = array();
  	$i = 0;
  	$j = 0;
  	$firstColumn = true;
    $options = array();
  	$names = explode(",", $varname);

  	$fieldCounter=0;
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
  	  switch ($fieldCounter){
  	    case 0:
  	      $variable['key'] = 'date';
  	      break;
  	    case 1:
  	      $variable['key'] = 'name';
  	      break;
  	    case 2:
  	      $variable['key'] = 'img';
  	      break;
  	    case 3:
  	      $variable['key'] = 'series';
  	      break;
  	  }
  	  $fieldCounter++;
  	  array_push($varList, $variable);
  	}
  	
  	$options = array();

  	foreach($obj as $k){  	  
  	  $knot = array();
  	  foreach($varList as $v){
  	    $name = $v['name'];
  	    $val = $v['value'];
  	    $key = $v['key'];
  	    $value = $k->$name->$val;
  	    $knot[$key] = $value;  	    
  	  } 
  	  array_push($data, $knot);
  	}

  	
  	//Getting options
    for($z=$fieldCounter; $z < count($names); $z++){
      $pair = explode("=", $names[$z]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
  	    if(strcasecmp($value, 'true') == 0){
  	      $options[$key] = TRUE;
  	    }elseif(strcasecmp($value, 'false') == 0){
  	      $options[$key] = FALSE;
  	    }else{  	      
  	      $options[$key] = $value;     
  	    }
  	}

  	$divId = uniqid("timeknot_div");
  	$pre = "<div id='".$divId."'></div>
<script type='text/javascript' src='".$lodspk['home']."vendor/timeknots/src/d3.v2.min.js'></script>
<script type='text/javascript' src='".$lodspk['home']."vendor/timeknots/src/timeknots-min.js'></script>
<script type='text/javascript'>
    
    TimeKnots.draw(\"#$divId\", ".json_encode($data).", ".json_encode($options).");
    </script>";
    return $pre;
  }
}
