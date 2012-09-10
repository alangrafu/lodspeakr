<?php

class Haanga_Extension_Filter_GoogleVizPieChart{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	$data = "";
  	$i = 0;
    $options = array();
  	$randId = rand();
  	$firstColumn = true;
  	$names = explode(",", $varname);
  	$w = "400";
  	$h = "400";
  	
  	$options['width'] = $w;
  	$options['height'] = $h;
  	
  	$data .= "data.addColumn('string', '".$names[0]."');";
    $data .= "data.addColumn('number', '".$names[1]."');";
  	foreach($obj as $k){  	  
  	  $data .="        data.setCell($i, 0, '".$k->$names[0]->value."');\n";
  	  $data .="        data.setCell($i, 1, ".$k->$names[1]->value.");\n";
  	  $i++;
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
