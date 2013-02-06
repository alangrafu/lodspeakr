<?php

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
  	$tokens = $qArr;
  	while(sizeof($tokens) > 0){
  	  $serviceName = join("%2F", $tokens);
  	  //Use .extension at the end of the service to force a particular content type
  	  $lastSegment = end($tokens);
  	  if(strpos($lastSegment, '.')>0){
  	    $aux = explode(".", $lastSegment);
  	    if(sizeof($aux)>1){
  	      $requestExtension = array_pop($aux);
  	      $contentTypes = $conf['http_accept'][$requestExtension];
  	      if($contentTypes != null){
  	        $acceptContentType = $contentTypes[0];
  	        $extension = $requestExtension;
  	      }
  	    }
  	    $serviceName = join(".",$aux);
  	  }
  	  
  	  
  	  $lodspk['model'] = $conf['model']['directory'].'/'.$conf['service']['prefix'].'/'.$serviceName.'/';
  	  $lodspk['view'] = $conf['view']['directory'].'/'.$conf['service']['prefix'].'/'.$serviceName.'/'.$extension.'.template';
  	  $lodspk['serviceName'] = join("/", $tokens);
  	  $lodspk['componentName'] = $lodspk['serviceName'];
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
  	    HTTPStatus::send406($uri);
  	    exit(0);
  	  }
  	  array_pop($tokens);
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
  	//$f = $this->getFunction($localUri);
  	$params = array();
  	$params = $this->getParams($localUri);
  	//$params[] = $context;
  	//$acceptContentType = Utils::getBestContentType($_SERVER['HTTP_ACCEPT']);
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
  	  $lodspk['local']['value'] = $localUri;
  	  $lodspk['local']['curie'] = Utils::uri2curie($localUri);
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
      	$firstAux = Utils::getfirstResults($results);
      	
    //  	chdir($conf['home']);
      	if(is_array($results)){
      	  $resultsObj = Convert::array_to_object($results);
      	  $results = $resultsObj;
      	}else{
      	  $resultsObj = $results;
      	}
      	$lodspk['firstResults'] = Convert::array_to_object($firstAux);
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
  	  HTTPStatus::send500($uri);
  	}
  	exit(0);	
  }
  
  
  /*protected function getFunction($uri){
  	global $conf;
  	$count = 1;
  	$prefixUri = $conf['basedir'];
  	$aux = str_replace($prefixUri, '', $uri, $count);
  	$functionAndParams = explode('/', $aux);
  	return $functionAndParams[0];
  }*/
  
  protected function getParams($uri){
  	global $conf;
  	global $lodspk;
  	$count = 1;
  	$prefixUri = $conf['basedir'];
//  	echo $prefixUri.$lodspk['serviceName'];exit(0);
  	$functionAndParams = explode('/', str_replace($prefixUri.$lodspk['serviceName'], '', $uri, $count));
  	if(sizeof($functionAndParams) > 1){
  	  array_shift($functionAndParams);
  	  return $functionAndParams;
  	}else{
  	  return array(null);
  	}
  }
  
}
?>
