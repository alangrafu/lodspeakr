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
  	$f = $this->getFunction($uri);
  	$params = array();
  	$params = $this->getParams($uri);
  	$params[] = $context;
  	$acceptContentType = Utils::getBestContentType($_SERVER['HTTP_ACCEPT']);
  	$extension = Utils::getExtension($acceptContentType); 
  	try{
  	  $viewFile = $conf['view']['directory'].$conf['special']['uri'].".".$f.$conf['view']['extension'].".".$extension;
  	  $modelFile = $conf['model']['directory'].$conf['special']['uri'].".".$f.$conf['model']['extension'].".".$extension;
  	  if(!is_file($modelFile) || !is_file($viewFile)){
  	  	throw new Exception('Method does not exist!');
  	  	Utils::send404($uri);
  	  }
  	  $e = $context['endpoint'];
  	  $query = file_get_contents($modelFile);
  	  array_pop($params);
  	  array_shift($params);
  	  $queryHeader = "";
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
  	  $data['query'] =$queryHeader . $query;
  	  $data['results'] = $e->query($data['query'], Utils::getResultsType($query));
  	  $data['params'] = $params;
  	  Utils::processDocument($uri, $acceptContentType, $data, $viewFile);  	
  	}catch (Exception $ex){
  	  Utils::send500($uri);
  	}
  	
  }
  
}

?>

