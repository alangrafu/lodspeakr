<?php

require_once('abstractModule.php');
class FormModule extends abstractModule{
  //Form module
  
  public function match($uri){
  	global $conf; 
  	global $acceptContentType; 
    global $localUri;
    global $lodspk;
    
    $lodspk['model'] = null;
    $lodspk['view'] = null;
  	$q = preg_replace('|^'.$conf['basedir'].'|', '', $localUri);
 	$qArr = explode('/', $q);

  	if(sizeof($qArr)==0){
  	  return FALSE;
  	}


  	$extension = Utils::getExtension($acceptContentType);
  	$viewFile  = null;
  	$tokens = $qArr;
  	$arguments = array();
  	$matchData = array();
  	
  	$matchData['post'] = (strtoupper($_SERVER['REQUEST_METHOD']) == "POST");
  	while(sizeof($tokens) > 0){
  	  $formName = join("%2F", $tokens);
  	  //Use .extension at the end of the form to force a particular content type
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
  	    $formName = join(".",$aux);
  	  }
      //checking default components  	  
      if(file_exists($conf['home'].$conf['model']['directory'].'/'.$conf['form']['prefix'].'/'.$formName)){  	    
  	    $lodspk['model'] = $conf['home'].$conf['model']['directory'].'/'.$conf['form']['prefix'].'/'.$formName.'/';
  	    $lodspk['view'] = $conf['home'].$conf['view']['directory'].'/'.$conf['form']['prefix'].'/'.$formName.'/'.$extension.'.template';
  	  }else{
  	    if($lodspk['model'] == null && $lodspk['view'] == null){
  	      //checking other components
  	      if(sizeof($conf['components']['forms'])>0){
  	        foreach($conf['components']['forms'] as $form){
  	          $formArray = explode("/", $form);
  	          if($formName == end($formArray)){
  	            array_pop($formArray);
  	            $conf['form']['prefix'] = array_pop($formArray);
  	            $conf['model']['directory'] = join("/", $formArray);
  	            $conf['view']['directory'] = $conf['model']['directory'];
  	            if(file_exists($conf['model']['directory'].'/'.$conf['form']['prefix'].'/'.$formName.'/scaffold.ttl')){
  	              $subDir = $this->readScaffold($conf['model']['directory'].'/'.$conf['form']['prefix'].'/'.$formName.'/scaffold.ttl', join("/", $arguments));
  	              $subDir.= '/';
  	              $lodspk['model'] = $conf['model']['directory'].'/'.$conf['form']['prefix'].'/'.$formName.'/'.$subDir;
  	              $lodspk['view'] = $conf['view']['directory'].'/'.$conf['form']['prefix'].'/'.$formName.'/'.$subDir.$extension.'.template';  	    
  	            }elseif(file_exists($conf['model']['directory'].'/'.$conf['form']['prefix'].'/'.$formName)){ 
  	              $lodspk['model'] = $conf['model']['directory'].'/'.$conf['form']['prefix'].'/'.$formName.'/';
  	              $lodspk['view'] = $conf['view']['directory'].'/'.$conf['form']['prefix'].'/'.$formName.'/'.$extension.'.template';
  	            }
  	          }
  	        }
  	      }
  	    }
  	  }
  	  $lodspk['formName'] = join("/", $tokens);
  	  $lodspk['componentName'] = $lodspk['formName'];
  	  $modelFile = $lodspk['model'].$extension.'.queries';
  	  if(file_exists($lodspk['model'].$extension.'.queries')){
  	    if(!file_exists($lodspk['view'])){
  	      $viewFile = null;
  	    }else{
  	      $viewFile = $lodspk['view'];
  	    }
  	    $matchData['model'] = $modelFile;
  	    $matchData['view'] = $viewFile;
  	    return $matchData;
  	  }elseif(file_exists($lodspk['model'].'queries')){
  	    $modelFile = $lodspk['model'].'queries';
  	    if(!file_exists($lodspk['view'])){
  	      $lodspk['resultRdf'] = true;
  	      $viewFile = null;
  	    }else{
  	      $viewFile = $lodspk['view'];
  	    }
  	    $matchData['model'] = $modelFile;
  	    $matchData['view'] = $viewFile;
  	    return $matchData;
  	  }elseif(file_exists($lodspk['model'])){
  	    HTTPStatus::send406($uri);
  	    exit(0);
  	  }
  	  array_unshift($arguments, array_pop($tokens));
  	}
  	return FALSE;  
  }
  
  public function execute($form){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	global $firstResults;
  	global $results;
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
  	$modelFile = $form['model'];
  	$viewFile = $form['view'];
  	
  	if($form['post']){
  	  
  	  $myUri = $_POST['uri'];
  	  $props = $_POST['properties'];
  	  $query = " DELETE { <$myUri> ?p ?o}WHERE{<$myUri> ?p ?o} INSERT DATA{<$myUri> ";
  	  $firstTriple = true;
  	  foreach($props as $v){
  	   if(!$firstTriple){
  	     $query .= ";\n";
  	   }
  	   $firstTriple = false;
  	   if($v['isUri'] ==  true){
  	     $query .= $v['predicate'].' '.$v['object'].' ';  	   
  	   }else{
  	     $query .= $v['predicate'].' """'.$v['object'].'"""';  	   
  	   }
  	  }
  	  $query .= " . }";
  	  $query = Utils::addPrefixes($query);
  	  $response = array('success' => false);
  	  
  	  if(isset($conf['update']['local'])){
  	    $endpoints['local']->setSparqlUpdateUrl($conf['update']['local']);
  	    $response['success'] = $endpoints['local']->queryPost($query);
  	  }
  	  echo json_encode($response);exit(0);
  	}else{
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
  	    $lodspk['module'] = 'form';
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
  	    //Need to redefine viewFile as 'local' i.e., inside form.foo/ so I can load files with the relative path correctly
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
  }
  
  
  protected function getParams($uri){
  	global $conf;
  	global $lodspk;
  	$count = 1;
  	$prefixUri = $conf['basedir'];
  	$functionAndParams = explode('/', str_replace($prefixUri.$lodspk['formName'], '', $uri, $count));
  	if(sizeof($functionAndParams) > 1){
  	  array_shift($functionAndParams);
  	  return $functionAndParams;
  	}else{
  	  return array(null);
  	}
  }
}
?>
