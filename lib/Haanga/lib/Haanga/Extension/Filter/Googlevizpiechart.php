<?php

class Haanga_Extension_Filter_GoogleVizPieChart{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	$data = "";
  	$i = 0;
  	$j = 0;
    $options = array();
  	$randId = rand();
  	$firstColumn = true;
  	$names = explode(",", $varname);
  	$w = "400";
  	$h = "400";
  	
  	$options['width'] = $w;
  	$options['height'] = $h;
  	
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
  	  $fieldCounter++;
  	  $columnType = 'number';
  	  if($firstColumn){
  	  	$columnType = 'string';
  	  	$firstColumn = false;
  	  }
  	  array_push($varList, $variable);
  	  $data .= "        data.addColumn('".$columnType."', '".$variable['name']."');\n";
  	}

  	foreach($obj as $k){
   	  foreach($varList as $v){
  	    $name = $v['name'];
  	    $val = $v['value'];
  	    $value = ($j==0)?"'".str_replace("'", "\'",$k->$name->$val)."'":floatval($k->$name->$val);
  	  	$data .="        data.setCell($i, $j, ".$value.");\n";
  	  	$j++;
  	  } 
  	  $i++;
  	  $j=0;
  	}

    //Getting options
    for($j=2; $j < count($names); $j++){
      $pair = explode("=", $names[$j]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
      $options[$key] = $value;     
    }
  	
  	$pre = "<div id='piechart_div_".$randId."'></div><script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
    var options_$randId = ".json_encode($options)."; 
    google.load('visualization', '1', {packages:['corechart']});
    google.setOnLoadCallback(drawChart_".$randId.");
    function drawChart_".$randId."() {
    var data = new google.visualization.DataTable();
    data.addRows(".$i.");\n
    ".$data."    var piechart = new google.visualization.PieChart(document.getElementById('piechart_div_".$randId."'));
    piechart.draw(data, options_$randId);
    }
    </script>";
    return $pre;
  }
}
