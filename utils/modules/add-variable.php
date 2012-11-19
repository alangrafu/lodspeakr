<?php

error_reporting(E_ERROR);
$s = 'settings.inc.php';
$c = file_get_contents($s);

$varArray = explode(".", $argv[1]);
//Convert varName into a proper variable for LODSPeaKr
$varName = array_shift($varArray);
if($varName != "conf" && $varName != "lodspk"){
  exit(124);
}
$varString = $varName."['".join("']['",$varArray)."']";
$varValue = $argv[2];
$newLines = array();
$lines = explode("\n", $c);
foreach($lines as $k => $v){
  if(preg_match("/\?>/", $v) == 0){
    if(strstr($v, $varString) === FALSE){ 
       array_push($newLines,$v);
    }
  }
}
$newLine  = "\$".$varString." = '".$varValue."';";
array_push($newLines,$newLine);
array_push($newLines,"?>");

$c = implode("\n", $newLines);
file_put_contents($s, $c);
?>
