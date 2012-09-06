<?php

error_reporting(E_ERROR);
$s = 'settings.inc.php';
$c = file_get_contents($s);

$prefix = $argv[1];
$url = $argv[2];
$lines = explode("\n", $c);
$newLines = array();
var_export($lines, true);
foreach($lines as $k => $v){
  if(preg_match("/\?>/", $v) == 0){
    if(preg_match("/[\"']endpoint[\"']/", $v) == 0 ||
      preg_match("/[\"']".$prefix."[\"']/", $v) == 0){
    array_push($newLines,$v);
      }else{
        exit(123);
      }
  }
}
$newEndpoint  = "\$conf['endpoint']['".$prefix."'] = '".$url."';";
array_push($newLines,$newEndpoint);
array_push($newLines,"?>");

$c = implode("\n", $newLines);
file_put_contents($s, $c);
?>
