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
  	$uri = $localUri;
  	if($conf['debug']){
  	  echo "\n-------------------------------------------------\nIn ".$conf['static']['directory']."\n";
  	  echo "Static file $file\n-------------------------------------------------\n\n";
	  }
	  if($conf['static']['haanga']){
	    $lodspk['home'] = $conf['basedir'];
	    $lodspk['baseUrl'] = $conf['basedir'];
	    $lodspk['module'] = 'static';
	    $lodspk['root'] = $conf['root'];
	    $lodspk['contentType'] = $acceptContentType;
	    $lodspk['ns'] = $conf['ns'];  	  	
	    $lodspk['this']['value'] = $localUri;
	    $lodspk['this']['curie'] = Utils::uri2curie($localUri);
	    $lodspk['this']['local'] = $localUri;  	
	    $lodspk['contentType'] = $acceptContentType;
	    $lodspk['endpoint'] = $conf['endpoint'];	    
	    $lodspk['type'] = $modelFile;
	    $lodspk['header'] = $prefixHeader;
	    $lodspk['baseUrl'] = $conf['basedir'];
	    
	    Utils::processDocument($conf['static']['directory'].$file, $lodspk, null);    	  
  	}else{
  	  echo file_get_contents($conf['static']['directory'].$file);
  	}
  }
  
  
  
}
?>
