<?
error_reporting(E_ERROR);
$s = 'settings.inc.php';
$c = file_get_contents($s);
$optionarray = Array('on' => 'true', '1' => 'true', 'off' => 'false', '0' =>'false');
$antiarray = Array('on' => 'false', '1' => 'false', 'off' => 'true', '0' =>'true');
$option = $optionarray[$argv[1]];
if($option == "" || $option == null){
  echo "Option not recognized. Aborting\n";
  exit(1);
}
if(preg_match('/disableComponents(.+)'.$optionarray[$argv[1]].'/', $c)){
  echo "Default already turned ".$option."\n";
  exit(0);
}
$newC = preg_replace('/disableComponents(.+)'.$antiarray[$argv[1]].'/', "disableComponents'] = ".$option, $c);
if($newC == $c){
  echo "WARNING: Variable 'disableComponents' does not exist. Adding it.\n";
  $newC = preg_replace("/\?>/", "\n\$conf['disableComponents'] = ".$option.";\n?>", $c);
}
if(file_put_contents($s, $newC) === FALSE){
  echo "An error ocurred";
  exit(1);
}else{
  echo "Default mode turned ".$option."\n";
}
?>
