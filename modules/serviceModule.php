<?
require_once('abstractModule.php');
class ServiceModule extends abstractModule{
  //Service module
  
  public function match($uri){
  	global $conf; 
  	global $acceptContentType; 
    global $localUri;
  	$q = preg_replace('|^'.$conf['basedir'].'|', '', $localUri);
 	$qArr = explode('/', $q);
  	if(sizeof($qArr)==0){
  	  return FALSE;
  	}
  	$extension = Utils::getExtension($acceptContentType); 
  	$auxViewFile  = $conf['home'].$conf['view']['directory'].$conf['service']['prefix'].$qArr[0]."/".$extension.".template";
  	$auxModelFile = $conf['home'].$conf['model']['directory'].$conf['service']['prefix'].$qArr[0]."/".$extension.".queries";
  	if(is_dir($auxModelFile) && is_file($auxViewFile)){
  	  return $uri;// $qArr[0];
  	}
  	$auxViewFile  = $conf['home'].$conf['view']['directory'].$conf['service']['prefix'].$qArr[0];
  	$auxModelFile = $conf['home'].$conf['model']['directory'].$conf['service']['prefix'].$qArr[0];
  	if(is_dir($auxModelFile) && is_dir($auxViewFile)){
  	  Utils::send406($uri);// $qArr[0];
  	}
  	if(is_dir($auxModelFile) && is_file($auxViewFile)){
  	  return $localUri;// $qArr[0];
  	}

  	return FALSE;
  }
  
  public function execute($service){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	require_once($conf['home'].$conf['service']['class']);
  	$context = array();
  	$context['contentType'] = $acceptContentType;
  	$context['endpoints'] = $endpoints;
  	$sp = new SpecialFunction();
  	$sp->execute($localUri, $context);
  	exit(0);	
  }
  
}
?>
