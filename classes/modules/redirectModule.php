<?php

require_once('abstractModule.php');
class RedirectModule extends abstractModule{
  //Class module
  
  public function match($uri){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	
  	require_once($conf['home'].'classes/MetaDb.php');
  	$metaDb = new MetaDb($conf['metadata']['db']['location']);

  	return true;
  }
  
  public function execute($pair){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	global $results;
  	global $firstResults;

  	echo $uri." is The uri";
  }
  
}
?>
