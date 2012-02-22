<?
require_once('abstractModule.php');
class ServiceModule extends abstractModule{
  //Service module
  
  public function match($uri){
  	global $conf; 
  	global $acceptContentType; 
    global $localUri;
    global $lodspk;
  	$q = preg_replace('|^'.$conf['basedir'].'|', '', $localUri);
 	$qArr = explode('/', $q);
  	if(sizeof($qArr)==0){
  	  return FALSE;
  	}
  	$extension = Utils::getExtension($acceptContentType);
  	$viewFile  = null;
  	
  	$lodspk['model'] = $conf['model']['directory'].'/'.$conf['service']['prefix'].'/'.$qArr[0].'/';
  	$lodspk['view'] = $conf['view']['directory'].'/'.$conf['service']['prefix'].'/'.$qArr[0].'/'.$extension.'.template';
  	$modelFile = $lodspk['model'].$extension.'.queries';
  	if(file_exists($lodspk['model'].$extension.'.queries')){
  	  if(!file_exists($lodspk['view'])){
  	  	$viewFile = null;
  	  }else{
  	  	$viewFile = $lodspk['view'];
  	  }
  	  return array($modelFile, $viewFile);
  	}elseif(file_exists($lodspk['model'].'queries')){
 	  $modelFile = $lodspk['model'].'queries';
  	  if(!file_exists($lodspk['view'])){
  	  	$lodspk['resultRdf'] = true;
  	  	$viewFile = null;
  	  }else{
  	  	$viewFile = $lodspk['view'];
  	  }
  	  return array($modelFile, $viewFile);
  	}elseif(file_exists($lodspk['model'])){
  	  Utils::send406($uri);
  	  exit(0);
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
  	global $firstResults;
  	$context = array();
  	$context['contentType'] = $acceptContentType;
  	$context['endpoints'] = $endpoints;
  	//$sp = new SpecialFunction();
  	//$sp->execute($localUri, $context);
  	$f = $this->getFunction($localUri);
  	$params = array();
  	$params = $this->getParams($localUri);
  	//$params[] = $context;
  	$acceptContentType = Utils::getBestContentType($_SERVER['HTTP_ACCEPT']);
  	$extension = Utils::getExtension($acceptContentType); 
  	$args = array();
  	list($modelFile, $viewFile) = $service;
  	try{
  	  $prefixHeader = array();
  	  
  	  for($i=0;$i<sizeof($params);$i++){
  	  	if($conf['mirror_external_uris'] != false){
  	  	  $altUri = Utils::curie2uri($params[$i]);
  	  	  $altUri = preg_replace("|^".$conf['basedir']."|", $conf['ns']['local'], $altUri);
  	  	  $params[$i] = Utils::uri2curie($altUri);
  	  	}
  	  }
  	  
  	  $segmentConnector = "";
  	  for($i=0;$i<sizeof($params);$i++){  
  	  	Utils::curie2uri($params[$i]);
  	  	//echo $params[$i]." ".Utils::curie2uri($params[$i]);exit(0);
  	  	$auxPrefix = Utils::getPrefix($params[$i]);
  	  	if($auxPrefix['ns'] != NULL){
  	  	  $prefixHeader[] = $auxPrefix;
  	  	}
  	  	$args["arg".$i]=$params[$i];
  	  	$args["all"] .= $segmentConnector.$params[$i];
  	  	if($segmentConnector == ""){
  	  	  $segmentConnector = "/";
  	  	}
  	  }
  	  $results['params'] = $params;
  	  
  	  
  	  $lodspk['home'] = $conf['basedir'];
  	  $lodspk['baseUrl'] = $conf['basedir'];
  	  $lodspk['module'] = 'service';
  	  $lodspk['root'] = $conf['root'];
  	  $lodspk['contentType'] = $acceptContentType;
  	  $lodspk['ns'] = $conf['ns'];  	  	
  	  $lodspk['this']['value'] = $uri;
  	  $lodspk['this']['curie'] = Utils::uri2curie($uri);
  	  $lodspk['this']['local'] = $localUri;  	
  	  $lodspk['contentType'] = $acceptContentType;
  	  $lodspk['endpoint'] = $conf['endpoint'];
  	  
  	  $lodspk['type'] = $modelFile;
  	  $lodspk['header'] = $prefixHeader;
  	  $lodspk['args'] = $args;
  	  $lodspk['add_mirrored_uris'] = false;
  	  $lodspk['baseUrl'] = $conf['basedir'];
  	  $lodspk['this']['value'] = $uri;
  	  if($viewFile == null){
  	  	$lodspk['transform_select_query'] = true;
  	  }
  	//  chdir($lodspk['model']);
  	  
  	  Utils::queryFile($modelFile, $endpoints['local'], $results, $firstResults);
      if(!$lodspk['resultRdf']){
      	$results = Utils::internalize($results); 
      	$lodspk['firstResults'] = Utils::getfirstResults($results);
      	
    //  	chdir($conf['home']);
      	if(is_array($results)){
      	  $resultsObj = Convert::array_to_object($results);
      	}else{
      	  $resultsObj = $results;
      	}
      }else{
      	$resultsObj = $results;
      }
  	  
  	  //Need to redefine viewFile as 'local' i.e., inside service.foo/ so I can load files with the relative path correctly
  	  //$viewFile = $extension.".template";
  	  //chdir($conf['home']);  	  
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
