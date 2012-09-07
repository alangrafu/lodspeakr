<?php
require_once('namespaces.php');
require_once('settings.inc.php');

foreach ($conf['ns'] as $k => $v){
  echo "$k: $v\n";
}
?>
