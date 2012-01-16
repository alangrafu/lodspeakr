<?php

class Haanga_Extension_Filter_GoogleVizTable{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	$data = "";
  	$i = 0;
  	$j = 0;
  	$randId = rand();
  	$names = explode(",", $varname);
  	foreach($names as $v){
  	  $data .= "        data.addColumn('string', '".$v."');\n";
  	}
  	foreach($obj as $k){  	  
  	  foreach($names as $v){
  	  	$data .="        data.setCell($i, $j, '".$k->$v->value."');\n";
  	  	$j++;
  	  } 
  	  $i++;
  	  $j=0;
  	}
  	
  	$pre = "<div id='table_div_".$randId."'></div><script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
    google.load('visualization', '1', {packages:['table']});
    google.setOnLoadCallback(drawTable);
    function drawTable() {
    var data = new google.visualization.DataTable();
    data.addRows(".$i.");\n
".$data."    var table = new google.visualization.Table(document.getElementById('table_div_".$randId."'));
table.draw(data, {showRowNumber: true, width: '80%'});
    }
    </script>";
    return $pre;
  }
}
