<?php

error_reporting(E_ERROR);
$s = 'settings.inc.php';
$c = file_get_contents($s);

$prefix = $argv[1];
$lines = explode("\n", $c);
$newLines = array();
var_export($lines, true);
foreach($lines as $k => $v){
  if(preg_match("/[\"']ns[\"']/", $v) == 0 ||
    preg_match("/[\"']".$prefix."[\"']/", $v) == 0){
  array_push($newLines,$v);
    }
}

$c = implode("\n", $newLines);
file_put_contents($s, $c);
?>
