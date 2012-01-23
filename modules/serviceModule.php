<?
require_once('abstractModule.php');
class ServiceModule extends abstractModule{
  //Service module
  
  public function match($uri){
  	global $conf;  	
  	return preg_match("|^".$conf['basedir'].$conf['special']['uri']."|", $uri);
  }
  
  public function execute($pair){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $base;
  	require_once($conf['home'].$conf['special']['class']);
  	$context = array();
  	$context['contentType'] = $acceptContentType;
  	$context['endpoints'] = $endpoints;
  	$sp = new SpecialFunction();
  	$sp->execute($uri, $context);
  	exit(0);	
  }
  
}
?>
