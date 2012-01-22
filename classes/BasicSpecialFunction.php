<?

include_once('AbstractSpecialFunction.php');

class SpecialFunction extends AbstractSpecialFunction{
  protected function getFunction($uri){
  	global $conf;
  	$count = 1;
  	$prefixUri = $conf['basedir'].$conf['special']['uri']."/";
  	$aux = str_replace($prefixUri, '', $uri, $count);
  	$functionAndParams = explode('/', $aux);
  	return $functionAndParams[0];
  }
  
  protected function getParams($uri){
  	global $conf;
  	$count = 1;
  	$prefixUri = $conf['basedir'].$conf['special']['uri'];
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
  	global $base;
  	global $results;
  	global $rRoot;
  	$f = $this->getFunction($uri);
  	$params = array();
  	$params = $this->getParams($uri);
  	$params[] = $context;
  	$acceptContentType = Utils::getBestContentType($_SERVER['HTTP_ACCEPT']);
  	$extension = Utils::getExtension($acceptContentType); 
  	$args = array();
  	try{
  	  $viewFile = $conf['special']['uri'].".".$f.$conf['view']['extension'].".".$extension;
  	  $modelFile = $conf['special']['uri'].".".$f.$conf['model']['extension'].".".$extension;
  	  if(!(is_dir($conf['model']['directory'].$modelFile) || is_file($conf['model']['directory'].$modelFile)) || !is_file($conf['view']['directory'].$viewFile)){
  	  	throw new Exception('<h1>Method does not exist!</h1><br/>This means that <tt>'.$conf['model']['directory'].$modelFile.'</tt> or <tt>'.$conf['view']['directory'].$viewFile."</tt> (or both) don't exist.<br/>Please refer to this tutorial to create one.<br/>");
  	  }
  	  $endpoints = $context['endpoints'];
  	  array_pop($params);
  	  array_shift($params);
  	  
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
 	  $data['params'] = $params;
 	  $base = $conf['view']['standard'];
 	  $base['type'] = $modelFile;
 	  $base['root'] = $conf['root'];
 	  $base['home'] = $conf['basedir'];
 	  $base['this']['value'] = $uri;
 	  $base['this']['curie'] = Utils::uri2curie($uri);
 	  $base['this']['contentType'] = $acceptContentType;
 	  $base['model']['directory'] = $conf['model']['directory'];
 	  $base['view']['directory'] = $conf['view']['directory'];
 	  $base['ns'] = $conf['ns'];
 	  $base['sparqlendpoint'] = $conf['endpoint'];
  	  $base['type'] = $modelFile;
  	  $base['header'] = $prefixHeader;
  	  $base['args'] = $args;
  	  $base['baseUrl'] = $conf['basedir'];
  	  $base['this']['value'] = $uri;
  	  $base['this']['contentType'] = $acceptContentType;
  	  $base['view']['directory'] = $conf['home'].$conf['view']['directory'];
  	  $base['model']['directory'] = $conf['home'].$conf['model']['directory'];
  	  chdir($conf['model']['directory']);
  	  $first = array();
  	  Utils::queryFile($modelFile, $endpoints['local'], $data, $first);
  	  chdir($conf['home']);
  	  $data = Utils::internalize($data);

  	  if(is_array($data)){
  	  	$results = Convert::array_to_object($data);
  	  }else{
  	  	$results = $data;
  	  }
  	  Utils::processDocument($viewFile, $base, $results);  	
  	}catch (Exception $ex){
  	  echo $ex->getMessage();
  	  trigger_error($ex->getMessage(), E_ERROR);
  	  Utils::send500($uri);
  	}
  	
  }
  
}

?>

