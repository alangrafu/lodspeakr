<?

include_once('AbstractSpecialFunction.php');

class SpecialFunction extends AbstractSpecialFunction{
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
  
  public function execute($uri, $context){
  	global $conf;
  	global $lodspk;
  	global $results;
  	global $rRoot;
  	$f = $this->getFunction($uri);
  	$params = array();
  	$params = $this->getParams($uri);
  	//$params[] = $context;
  	$acceptContentType = Utils::getBestContentType($_SERVER['HTTP_ACCEPT']);
  	$extension = Utils::getExtension($acceptContentType); 
  	$args = array();
  	try{
  	  $viewFile = $conf['service']['prefix'].$f."/".$extension.".template";
  	  $modelFile = $conf['service']['prefix'].$f."/".$extension.".queries";
  	  if(!(is_dir($conf['model']['directory'].$modelFile) || is_file($conf['model']['directory'].$modelFile))){
  	  	$msg = '<h1>Method does not exist!</h1><br/>This means that <tt>'.$modelFile."</tt> doesn't exist.<br/>Please refer to this tutorial to create one.<br/>";
  	  	throw new Exception($msg);
  	  }
  	  if(!is_file($conf['view']['directory'].$viewFile)){
  	  	  $msg='<h1>Method does not exist!</h1><br/>This means that <tt>'.$conf['view']['directory'].$viewFile."</tt> doesn't exist.<br/>Please refer to this tutorial to create one.<br/>";
  	  	  throw new Exception($msg);
  	  }
  	  $endpoints = $context['endpoints'];
  	  //array_pop($params);
  	  //array_shift($params);
  	  
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
  	  $lodspk['view']['directory'] = $conf['home'].$conf['view']['directory'].$conf['service']['prefix'].$f.'/';
  	  $lodspk['model']['directory'] = $conf['home'].$conf['model']['directory'];
  	  chdir($conf['model']['directory']);
  	  $first = array();
  	  Utils::queryFile($modelFile, $endpoints['local'], $results, $first);
  	  chdir($conf['home']);
  	  $results = Utils::internalize($results);

  	  if(is_array($results)){
  	  	$results = Convert::array_to_object($results);
  	  }
  	  
  	  //Need to redefine viewFile as 'local' i.e., inside service.foo/ so I can load files with the relative path correctly
  	  $viewFile = $extension.".template";
  	  Utils::processDocument($viewFile, $lodspk, $results);  
  	  
  	}catch (Exception $ex){
  	  echo $ex->getMessage();
  	  trigger_error($ex->getMessage(), E_ERROR);
  	  Utils::send500($uri);
  	}
  	
  }
  
}

?>

