<?php

error_reporting(E_ERROR);
$s = 'settings.inc.php';
$c = file_get_contents($s);

$newPass = $argv[1];

$lines = explode("\n", $c);
$newLines = array();
foreach($lines as $k => $v){
  if(preg_match("/\?>/", $v) == 0){
    if(preg_match("/[\"']admin[\"']/", $v) == 0 ||
      preg_match("/[\"']pass[\"']/", $v) == 0){

    array_push($newLines,$v);
      }
  }
}
$newLine  = "\$conf['admin']['pass'] = '".$newPass."';";
array_push($newLines,$newLine);
array_push($newLines,"?>");

$c = implode("\n", $newLines);
file_put_contents($s, $c);
echo "Password changed successfully!\n\n";
?>
