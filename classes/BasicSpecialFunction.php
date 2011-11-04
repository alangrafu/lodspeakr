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
  	  	throw new Exception('Method does not exist!');
  	  }
  	  $endpoints = $context['endpoints'];
  	  array_pop($params);
  	  array_shift($params);
  	  //$query = file_get_contents($conf['model']['directory'].$modelFile);
  	  /*$queryHeader = "";
  	  $prefixHeader = array();
  	  for($i=0;$i<sizeof($params);$i++){
  	  $auxPrefix = Utils::getPrefix($params[$i]);
  	  if($auxPrefix['ns'] != NULL){
  	  $prefixHeader[$auxPrefix['ns']] = $auxPrefix['prefix'];
  	  }
  	  $query = preg_replace("|%".$i."|", $params[$i], $query);
  	  }
  	  foreach($prefixHeader as $n => $p){
  	  $queryHeader .= "PREFIX $p: <$n> \n";
  	  }
  	  $data['query'] =$queryHeader . $query;*/
  	  //$e->query($data['query'], Utils::getResultsType($query));
  	  
  	  $prefixHeader = array();
  	  for($i=0;$i<sizeof($params);$i++){
  	  	if($conf['use_external_uris']){
  	  	  $altUri = Utils::curie2uri($params[$i]);
  	  	  $altUri = preg_replace("|^".$conf['basedir']."|", $conf['ns']['local'], $altUri);
echo $altUri."\n";
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
 	  $base['this']['value'] = $uri;
 	  $base['this']['curie'] = Utils::uri2curie($uri);
 	  $base['this']['contentType'] = $acceptContentType;
 	  $base['model']['directory'] = $conf['model']['directory'];
 	  $base['view']['directory'] = $conf['view']['directory'];
 	  $base['ns'] = $conf['ns'];  	  $base['ns'] = $conf['ns'];
  	  $base['type'] = $modelFile;
  	  $base['header'] = $prefixHeader;
  	  $base['args'] = $args;
  	  $base['baseUrl'] = $conf['basedir'];
  	  $base['this']['value'] = $uri;
  	  $base['this']['contentType'] = $acceptContentType;
  	  $base['view']['directory'] = $conf['home'].$conf['view']['directory'];
  	  $base['model']['directory'] = $conf['home'].$conf['model']['directory'];
  	  chdir($conf['model']['directory']);
  	  Utils::queryFile($modelFile, $endpoints['local'], $data);
  	  chdir("..");
  	  if(is_array($data)){
  	  	$results = Convert::array_to_object($data);
  	  }else{
  	  	$results = $data;
  	  }
  	  $rRoot = &$resulst;
  	  Utils::processDocument($viewFile, $base, $results);  	
  	}catch (Exception $ex){
  	  echo $ex->getMessage();
  	  trigger_error($ex->getMessage(), E_ERROR);
  	  Utils::send500($uri);
  	}
  	
  }
  
}

?>

