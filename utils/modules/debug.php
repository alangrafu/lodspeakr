<?
error_reporting(E_ERROR);
$s = 'settings.inc.php';
$c = file_get_contents($s);
$optionarray = Array('on' => 'true', '1' => 'true', 'off' => 'false', '0' =>'false');
$antiarray = Array('on' => 'false', '1' => 'false', 'off' => 'true', '0' =>'true');
$option = $optionarray[$argv[1]];;
$newC = preg_replace('/debug(.+)'.$antiarray[$argv[1]].'/', "debug'] = ".$option, $c);
if(file_put_contents($s, $newC) === FALSE){
  echo "An error ocurred";
  exit(1);
}else{
  echo "Debug mode turned ".$option."\n";
}
?>
