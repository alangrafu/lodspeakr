<?php

class Haanga_Extension_Filter_GoogleVizPieChart{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	$data = "";
  	$i = 0;
  	$randId = rand();
  	$firstColumn = true;
  	$names = explode(",", $varname);
  	$w = "400";
  	$h = "300";
  	
  	if($names[3] != null && $names[3] != ""){
  	  $w = $names[3];
  	}
  	if($names[4] != null && $names[4] != ""){
  	  $h = $names[4];
  	}
  	
  	$data .= "data.addColumn('string', '".$names[0]."');";
    $data .= "data.addColumn('number', '".$names[1]."');";
  	foreach($obj as $k){  	  
  	  $data .="        data.setCell($i, 0, '".$k->$names[0]->value."');\n";
  	  $data .="        data.setCell($i, 1, ".$k->$names[1]->value.");\n";
  	  $i++;
  	}
  	
  	$pre = "<div id='piechart_div_".$randId."' style='width: ".$w."px; height: ".$h."px'></div><script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
    google.load('visualization', '1', {packages:['corechart']});
    google.setOnLoadCallback(drawChart_".$randId.");
    function drawChart_".$randId."() {
    var data = new google.visualization.DataTable();
    data.addRows(".$i.");\n
    ".$data."    var piechart = new google.visualization.PieChart(document.getElementById('piechart_div_".$randId."'));
    piechart.draw(data);
    }
    </script>";
    return $pre;
  }
}
