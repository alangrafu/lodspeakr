<?
require_once('abstractModule.php');
class StaticModule extends abstractModule{
  //Static module
  
  public function match($uri){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	$q = preg_replace('|^'.$conf['basedir'].'|', '', $localUri);
  	if(sizeof($q)>0 && file_exists($conf['home'].$conf['static']['directory'].$q)){
  	  return $q;
  	}
  	return FALSE;
  }
  
  public function execute($file){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	header("Content-type: ");
  	echo file_get_contents($conf['static']['directory'].$file);
  }
  
}
?>
