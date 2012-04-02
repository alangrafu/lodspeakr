<?
require_once('abstractModule.php');
class PostModule extends abstractModule{
  //Post module
  
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
  	$method = ucwords($_SERVER['REQUEST_METHOD']);
    if($method != 'POST'){
      return FALSE;
    }  	
  	//Use .extension at the end of the service to force a particular content type
  	if(strpos($qArr[0], '.')>0){
  	  $aux = explode('.', $qArr[0]);
  	  if(isset($aux[1])){
  	    $contentTypes = $conf['http_accept'][$aux[1]];
  	    if($contentTypes == null){
  	      HTTPStatus::send406('Content type not acceptable\n');
  	    }
  	    $acceptContentType = $contentTypes[0];
  	  }
  	  $qArr[0] = $aux[0];
  	}
  	
  	$extension = Utils::getExtension($acceptContentType);
  	$viewFile  = null;
  	$lodspk['model'] = $conf['model']['directory'].'/'.$conf['post']['prefix'].'/'.$qArr[0].'/';
  	$lodspk['view'] = $conf['view']['directory'].'/'.$conf['post']['prefix'].'/'.$qArr[0].'/'.$extension.'.template';
  	if(!file_exists($lodspk['model'])){
  	    HTTPStatus::send405($localUri);
  	}
  	$lodspk['serviceName'] = $qArr[0];
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
  	  return array($modelFile, $viewFile, $method);
  	}elseif(file_exists($lodspk['model'])){
  	  HTTPStatus::send406($uri);
  	  exit(0);
  	}
  	return FALSE;  
  }
  
  public function execute($service){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $update_endpoints;
  	global $lodspk;
  	global $firstResults;
  	$context = array();
  	$context['contentType'] = $acceptContentType;
  	$context['update_endpoints'] = $update_endpoints;
  	$f = $this->getFunction($localUri);
  	list($modelFile, $viewFile, $method) = $service;
  	$params = array();
	  $args = $this->getParamsPost($_POST);

  	try{
  	  
  	  $results['params'] = $params;
  	  
  	  $lodspk['method'] = 'POST';
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
  	  $lodspk['update_endpoint'] = $conf['update_endpoint'];
  	  
  	  $lodspk['type'] = $modelFile;
  	  $lodspk['header'] = $prefixHeader;
  	  $lodspk['args'] = $args;
  	  $lodspk['add_mirrored_uris'] = false;
  	  $lodspk['baseUrl'] = $conf['basedir'];
  	  $lodspk['this']['value'] = $uri;
  	  $lodspk['status'] = Utils::updateFile($modelFile, $update_endpoints['local'], $results, $firstResults);

  	  Utils::processDocument($viewFile, $lodspk, $results);    	  
  	}catch (Exception $ex){
  	  echo $ex->getMessage();
  	  trigger_error($ex->getMessage(), E_ERROR);
  	  HTTPStatus::send500($uri);
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
  
  protected function getParamsGet($uri){
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
  
  protected function getParamsPost($pArgs){
    $p = array();
    foreach($pArgs as $k => $v){
      $forbiddenChars  = array( ':', '.', '/', '\\', '?', '*' );
      $acceptableChars = array( '_', '_', '_', '_', '_', '_');
      //$p[str_replace($forbiddenChars, $acceptableChars, $k)] = $v;
      $p[$k] = $v;
    }
    return $p;
  }
  
}
?>
