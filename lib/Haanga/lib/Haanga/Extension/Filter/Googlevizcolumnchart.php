<?php

class Haanga_Extension_Filter_GoogleVizColumnChart{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	$data = "";
  	$i = 0;
  	$j = 0;
  	$firstColumn = true;
  	$names = explode(",", $varname);
  	foreach($names as $v){
  	  $columnType = 'number';
  	  if($firstColumn){
  	  	$columnType = 'string';
  	  	$firstColumn = false;
  	  }
  	  $data .= "        data.addColumn('".$columnType."', '".$v."');\n";
  	}
  	foreach($obj as $k){  	  
  	  foreach($names as $v){
  	    $value = ($j==0)?"'".$k->$v->value."'":$k->$v->value;
  	  	$data .="        data.setCell($i, $j, ".$value.");\n";
  	  	$j++;
  	  } 
  	  $i++;
  	  $j=0;
  	}
  	
  	$pre = "<div id='columnchart_div'></div><script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
    google.load('visualization', '1', {packages:['corechart']});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
    var data = new google.visualization.DataTable();
    data.addRows(".$i.");\n
".$data."    var columnchart = new google.visualization.ColumnChart(document.getElementById('columnchart_div'));
columnchart.draw(data, {showRowNumber: true, width: '80%'});
    }
    </script>";
    return $pre;
  }
}
