<?php
require_once('common.inc.php');
require_once('settings.inc.php');
error_reporting(E_ERROR);
$s = 'settings.inc.php';
$c = file_get_contents($s);

$newModule = $argv[1];

$lines = explode("\n", $c);
$newLines = array();
var_export($lines, true);
foreach($lines as $k => $v){
  if(preg_match("/\?>/", $v) == 0){
    if(preg_match("/[\"']modules[\"']/", $v) == 0 ||
      preg_match("/[\"']available[\"']/", $v) == 0){
    array_push($newLines,$v);
      }
  }
}

$listOfModules = array();
$newModules = array();
foreach($conf['modules']['available'] as $v){
  if(!isset($listOfModules["'".$v."'"]) && $v != $newModule){
    $listOfModules["'".$v."'"] = 1;
    array_push($newModules, "'".$v."'");
  }
  $i++;
}
$modules = implode(",", $newModules);
$newEndpoint  = "\$conf['modules']['available'] = array(".$modules.");";
array_push($newLines,$newEndpoint);
array_push($newLines,"?>");

$c = implode("\n", $newLines);
//echo $c;
file_put_contents($s, $c);
?>
