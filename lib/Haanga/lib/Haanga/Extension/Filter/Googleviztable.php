<?php

class Haanga_Extension_Filter_GoogleVizTable{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	$data = "";
  	$i = 0;
  	$j = 0;
  	$randId = rand();
  	$names = explode(",", $varname);
  	$varList = array();
  	foreach($names as $v){
  	  $variable['name'] = $v;
  	  $variable['value'] = 'value';
  	  if(strpos($v, ".")){
  	    $aux = explode(".", $v);
  	    $variable['name'] = $aux[0];
  	    $variable['value'] = $aux[1];
  	  }
  	  array_push($varList, $variable);
  	  $data .= "        data.addColumn('string', '".$variable['name']."');\n";
  	}
  	
  	
  	foreach($obj as $k){  	  
  	  foreach($varList as $v){
  	    $name = $v['name'];
  	    $val = $v['value'];
  	  	$data .="        data.setCell($i, $j, '".str_replace("'", "\'",$k->$name->$val)."');\n";
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
