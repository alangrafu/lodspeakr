<?php

class Haanga_Extension_Filter_GoogleVizAnnotatedTimeline{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	$data = "";
  	$i = 0;
  	$j = 0;
  	$firstColumn = true;
    $options = array();
  	$names = explode(",", $varname);

  	$fieldCounter=0;
  	$varList = array();
  	foreach($names as $v){
  	  $variable['name'] = $v;
  	  $variable['value'] = 'value';
  	  if(strpos($v,"=")){
  	    break;
  	  }
  	  if(strpos($v, ".")){
  	    $aux = explode(".", $v);
  	    $variable['name'] = $aux[0];
  	    $variable['value'] = $aux[1];
  	  }
  	  array_push($varList, $variable);
  	  
  	  $columnType = 'number';

  	  if($firstColumn){
  	  	$columnType = 'date';
  	  	$firstColumn = false;
  	  }elseif(($fieldCounter - 2) %3 == 0 || ($fieldCounter - 3) %3 == 0){
  	    $columnType = 'string';
  	  }
  	  array_push($varList, $v);
  	  $data .= "        data.addColumn('".$columnType."', '".$variable['name']."');\n";
  	  $fieldCounter++;
  	}

  	foreach($obj as $k){  	  
  	  $j=0;
  	  foreach($varList as $v){
  	    $name = $v['name'];
  	    $val = $v['value'];
  	    $value = $k->$name->$val."ASDASD";
  	    if($j==0){
  	      $value = "new Date(".date("Y, m, d", strtotime($k->$name->$val)).")";
  	    }elseif($j-2>=0 && (($j - 2) %3 == 0 || ($j - 3) %3 == 0)){
  	      $value = "'".$k->$name->$val."'";
  	    }
  	  	$data .="        data.setCell($i, $j, ".$value.");\n";
  	  	$j++;
  	  } 
  	  $i++;
  	}

  	
  	//Getting options
  	$options['height'] = 400;
  	$options['width'] = 400;
  	$options['displayAnnotations'] = 'true';
    for($z=$fieldCounter; $z < count($names); $z++){
      $pair = explode("=", $names[$z]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
      $options[$key] = $value;     
    }

  	$divId = uniqid("timeline_div");
  	$pre = "<div id='".$divId."' style='width: 700px; height: 240px;'></div><script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
    var options_$divId = ".json_encode($options)."; 
    google.load('visualization', '1', {packages:['annotatedtimeline']});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
    var data = new google.visualization.DataTable();
    data.addRows(".$i.");\n
".$data."    var timeline = new google.visualization.AnnotatedTimeLine(document.getElementById('".$divId."'));
timeline.draw(data, options_$divId);
    }
    </script>";
    return $pre;
  }
}
