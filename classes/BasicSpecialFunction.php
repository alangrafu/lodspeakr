<?

include_once('AbstractSpecialFunction.php');

class SpecialFunction extends AbstractSpecialFunction{
  protected function getFunction($uri){
  	global $conf;
  	$count = 1;
  	$prefixUri = $conf['basedir'].$conf['special']['uri'];
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
  	$f = $this->getFunction($uri);
  	$params = array();
  	$params = $this->getParams($uri);
  	$params[] = $context;
  	try{
  	  $methods = get_class_methods($this);
  	  if(!in_array($f, $methods)){
  	  	throw new Exception('Method does not exist!');
  	  	Utils::send500($uri);
  	  }
  	  call_user_func_array(array($this, $f), $params);
  	}catch (Exception $e){
  	  Utils::send404($uri);
  	}
  	
  }
  
  private function index($curie, $context){
  	global $conf;
  	//Not sure if I should use files like for 
  	//templating instances
  	//For now it will be "built-in" in the class
  	$uri = Utils::curie2uri($curie);
  	if(!isset($curie) || $curie == ""){
  	  $uri = "Class";
  	  $query = "SELECT DISTINCT ?resource WHERE{
  	    {
  	      [] a ?resource
  	    }UNION{
  	      ?resource a rdfs:Class
  	    }UNION{
  	      ?resource a owl:Class
  	    }UNION{
  	      ?resource rdfs:subClassOf [] 
  	    }
  	  }";
  	}else{
  	  $query = file_get_contents($conf['model']['directory'].'special.index.model.html');
  	  $query = preg_replace("|".$conf['resource']['url_delimiter']."|", "<".$uri.">", $query);
  	}
  	
  	
  	$viewFile = $conf['view']['directory'].'special.index.view.html';
  	$contentType = $context['contentType'];
  	$e = $context['endpoint'];
  	$data['results'] = $e->query($query, Utils::getResultsType($query));
  	$data['query'] = $query;
	Utils::processDocument($uri, $contentType, $data, $viewFile);
  }
}

?>
