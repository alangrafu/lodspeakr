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
  	$viewFile  = $conf['service']['prefix'].$qArr[0]."/".$extension.".template";
  	$modelFile = $conf['service']['prefix'].$qArr[0]."/".$extension.".queries";
  	if(file_exists($conf['home'].$conf['model']['directory'].$modelFile) && file_exists($conf['home'].$conf['view']['directory'].$viewFile) && $qArr[0] != null){
  	  trigger_error("Using model ".$modelFile." and view ".$viewFile, E_USER_NOTICE);
  	  return array($modelFile, $viewFile);
  	}elseif($extension != 'html' && file_exists($conf['model']['directory'].$conf['service']['prefix'].$qArr[0].'/html.queries')){
  	  $modelFile = $conf['service']['prefix'].$qArr[0].'/html.queries';
  	  $viewFile = null;
  	  trigger_error("Using ".$modelFile." as model. It will be used as a CONSTRUCT", E_USER_NOTICE);
  	  return array($modelFile, $viewFile);
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
  $context = array();
  $context['contentType'] = $acceptContentType;
  $context['endpoints'] = $endpoints;
  //$sp = new SpecialFunction();
  //$sp->execute($localUri, $context);
  $f = $this->getFunction($uri);
  $params = array();
  $params = $this->getParams($uri);
  //$params[] = $context;
  $acceptContentType = Utils::getBestContentType($_SERVER['HTTP_ACCEPT']);
  $extension = Utils::getExtension($acceptContentType); 
  $args = array();
  list($modelFile, $viewFile) = $service;
  
  try{
  	$prefixHeader = array();
  	
  	for($i=0;$i<sizeof($params);$i++){
  	  if($conf['mirror_external_uris']){
  	  	$altUri = Utils::curie2uri($params[$i]);
  	  	$altUri = preg_replace("|^".$conf['basedir']."|", $conf['ns']['local'], $altUri);
  	  	$params[$i] = Utils::uri2curie($altUri);
  	  }
  	}
  	
  	for($i=0;$i<sizeof($params);$i++){  
  	  $auxPrefix = Utils::getPrefix($params[$i]);
  	  if($auxPrefix['ns'] != NULL){
  	  	$prefixHeader[] = $auxPrefix;
  	  }
  	  $args["arg".$i]=$params[$i];
  	}
  	$results['params'] = $params;
  	$lodspk = $conf['view']['standard'];
  	$lodspk['type'] = $modelFile;
  	$lodspk['root'] = $conf['root'];
  	$lodspk['home'] = $conf['basedir'];
  	$lodspk['this']['value'] = $uri;
  	$lodspk['this']['curie'] = Utils::uri2curie($uri);
  	$lodspk['this']['contentType'] = $acceptContentType;
  	$lodspk['model']['directory'] = $conf['model']['directory'];
  	$lodspk['view']['directory'] = $conf['view']['directory'];
  	$lodspk['ns'] = $conf['ns'];
  	$lodspk['endpoint'] = $conf['endpoint'];
  	$lodspk['type'] = $modelFile;
  	$lodspk['header'] = $prefixHeader;
  	$lodspk['args'] = $args;
  	$lodspk['module'] = 'service';
  	$lodspk['add_mirrored_uris'] = false;
  	$lodspk['baseUrl'] = $conf['basedir'];
  	$lodspk['this']['value'] = $uri;
  	$lodspk['this']['contentType'] = $acceptContentType;
  	$lodspk['view']['directory'] = $conf['home'].$conf['view']['directory'];//.$conf['service']['prefix'].$f.'/';
  	$lodspk['model']['directory'] = $conf['home'].$conf['model']['directory'];
  	if($viewFile == null){
  	  $lodspk['transform_select_query'] = true;
  	}
  	
  	chdir($conf['model']['directory']);
  	$first = array();
  	Utils::queryFile($modelFile, $endpoints['local'], $results, $first);
  	chdir($conf['home']);
  	$results = Utils::internalize($results);
  	
  	if(is_array($results)){
  	  $results = Convert::array_to_object($results);
  	}
  	
  	//Need to redefine viewFile as 'local' i.e., inside service.foo/ so I can load files with the relative path correctly
  	//$viewFile = $extension.".template";
  	Utils::processDocument($viewFile, $lodspk, $results);  
  	
  }catch (Exception $ex){
  	echo $ex->getMessage();
  	trigger_error($ex->getMessage(), E_ERROR);
  	Utils::send500($uri);
  }
  exit(0);	
}


protected function getFunction($uri){
  global $conf;
  $count = 1;
  $prefixUri = $conf['basedir'];
  $aux = str_replace($prefixUri, '', $uri, $count);
  $functionAndParams = explode('/', $aux);
  return $functionAndParams[0];
}

protected function getParams($uri){
  global $conf;
  $count = 1;
  $prefixUri = $conf['basedir'];
  $functionAndParams = explode('/', str_replace($prefixUri, '', $uri, $count));
  if(sizeof($functionAndParams) > 1){
  	array_shift($functionAndParams);
  	return $functionAndParams;
  }else{
  	return array(null);
  }
}

}
?>
