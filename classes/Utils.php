<? 

class Utils{
  
  public static function send303($uri, $ext){
  	header("HTTP/1.0 303 See Other");
  	header("Location: ".$uri);
  	header("Content-type: ".$ext);
  	echo $uri."\n\n";
  	exit(0);
  }
  
  public static function send404($uri){
  	header("HTTP/1.0 404 Not Found");
  	echo "I could not find ".$uri." or information about it.\n\n";
  	exit(0);
  }
  
  public static function send406($uri){
  	header("HTTP/1.0 406 Not Acceptable");
  	echo "I can't find a representation suitable for the content type you accept\n\n";
  	exit(0);
  }
  
  public static function send500($uri){
  	header("HTTP/1.0 500 Internal Server Error");
  	echo "An internal error ocurred. Please try later\n\n";
  	exit(0);
  }
  
  public static function uri2curie($uri){
  	global $conf;
  	$ns = $conf['ns'];
  	$curie = $uri;
  	foreach($ns as $k => $v){
  	  $curie = preg_replace("|^$v|", "$k:", $uri);
  	  if($curie != $uri){
  	  	break;
  	  }
  	}
  	return $curie;
  }
  
  public static function curie2uri($curie){
  	global $conf;
  	$ns = $conf['ns'];
  	$parts = explode(':', $curie);
  	//Avoid if we have a namespace prefix called 'http'
  	if(preg_match('|^//|', $parts[1])){
  	  return $curie;
  	}  	
  	return $ns[$parts[0]].$parts[1];
  }
  
  public static function getPrefix($curie){
  	global $conf;
  	$ns = $conf['ns'];
  	$parts = explode(':', $curie);
  	//Avoid if we have a namespace prefix called 'http'
  	if(preg_match('|^//|', $parts[1])){
  	  return $curie;
  	}  	
  	return array('ns' => $ns[$parts[0]], 'prefix' => $parts[0]);;
  }
  
  public static function getTemplate($uri){
  	$filename = str_replace(":", "_", $uri);
  	if(file_exists ($filename)){
  	  include_once($filename);
  	}
  }
  
  private static function sparqlResult2Obj($data){
  	$aux = $data['results']['bindings'];
  	$obj = array();
  	if(!isset($aux)){
  	  foreach($data as $k => $v){
  	  	$obj[$k] = Utils::sparqlResult2Obj($v);
  	  }
  	}else{
  	  if(sizeof($aux)>0){
  	  	foreach($aux as $w){
  	  	  $row = array();
  	  	  foreach($w as $k => $v){
  	  	  	$row['value'][$k] = $v['value'];
  	  	  	if($v['type'] == 'uri'){
  	  	  	  $row['curie'][$k] = Utils::uri2curie($v['value']);
  	  	  	  $row['uri'][$k] = 1;
  	  	  	}elseif($v['type'] == 'bnode'){
  	  	  	  $row['curie'][$k] = 'blankNode';
  	  	  	}
  	  	  }
  	  	  if(sizeof($row) >0){
  	  	  	array_push($obj, $row);
  	  	  }
  	  	  if(sizeof($aux) == 1){
  	  	  	$obj = $row;
  	  	  }
  	  	}
  	  }
  	}
  	return $obj;
  }
  
  public static function showView($baseData, $data, $view){
  	global $conf;
  	$base = $conf['view']['standard'];
  	$base['this']['value'] = $baseData['uri'];
  	$base['this']['curie'] = Utils::uri2curie($baseData['uri']);
  	$base['ns'] = $conf['ns'];
  	if(isset($baseData['params'])){
  	  $base['this']['params'] = $baseData['params'];
  	}
  	require('lib/Haanga/lib/Haanga.php');
  	Haanga::configure(array(
  	  'template_dir' => './',
  	  'cache_dir' => 'cache/',
  	  ));
  	
  	$r = array();
  	  $r = Utils::sparqlResult2Obj($data);  	
 	$vars = compact('base', 'r');
	if(is_file($view)){
	  Haanga::Load($view, $vars);
	}else{
	  $fnc = Haanga::compile($view);
	  $fnc($vars, FALSE);
	}
  	
  }
  
  public static function getExtension($accept_string){
  	global $conf;
  	$extension = "html";
  	foreach($conf['http_accept'] as $ext => $accept_arr){
  	  if(in_array($accept_string, $accept_arr)){
  	    $extension = $ext;
  	  }
  	}
  	return $extension;
  }
  
  public static function getBestContentType($accept_string){
  	global $conf;
  	/*
  	* TODO: Choose best content type from
  	* things like
  	* "text/html;q=0.2,application/xml;q=0.1"
  	* and so on. In the meantime,
  	* assume there is only one CT
  	*/
  	$a = split(",", $accept_string);
  	$ct = 'text/html';
  	if(strstr($a[0], ";")){
  	  $a = split(";", $a[0]);
  	}
  	foreach($conf['http_accept'] as $ext => $arr){
  	  if(in_array($a[0], $arr)){
  	  	$ct = $a[0];
  	  }
  	}
  	
  	return $ct;
  }
  
  
  private static function serializeByQueryType($data, $extension){
  	global	$conf;
  	if(!isset($data['results'])){
  	  foreach($data as $k => $v){
  	  	$results[$k] = Utils::serializeByQueryType($v, $extension); 	  	
 	  }
  	}else{
  	  if(preg_match("/describe/i", $data['query'])){  	  
  	  	require('lib/arc2/ARC2.php');
  	  	$parser = ARC2::getRDFParser();
  	  	$parser->parse($conf['basedir'], $data['results']);
  	  	$triples = $parser->getTriples();
  	  	$ser;
  	  	switch ($extension){
  	  	case 'ttl':
  	  	  $ser = ARC2::getTurtleSerializer();
  	  	  break;
  	  	case 'nt':
  	  	  $ser = ARC2::getNTriplesSerializer();
  	  	  break;
  	  	case 'rdf':
  	  	  $ser = ARC2::getRDFXMLSerializer();
  	  	  break;
  	  	}
  	  	$doc = $ser->getSerializedTriples($triples);
  	  	echo $doc;
  	  	exit(0);
  	  }
  	  elseif(preg_match("/select/i", $data['query'])){
  	  	$results = $data['results'];
  	  	if(sizeof($results['results']['bindings']) == 0){
  	  	  //Avoid for now
  	  	  //Utils::send404($uri);
  	  	}
  	  }
  	}
  	return $results;
  }
  
  public static function processDocument($uri, $contentType, $data, $viewFile){
  	global $conf;
  	$extension = Utils::getExtension($contentType); 
  	
  	header('Content-Type: '.$contentType);
  	$results = Utils::serializeByQueryType($data, $extension);
  	
  	$baseData['uri'] = $uri;
  	$baseData['params'] = $data['params'];
  	Utils::showView($baseData, $results, $viewFile);  	
  	exit(0);
  }
  
  public static function getResultsType($query){
  	global $conf;
  	if(preg_match("/select/i", $query)){
  	  return $conf['endpoint']['select']['output'];
  	}elseif(preg_match("/describe/i", $query)){
  	  return $conf['endpoint']['describe']['output'];
  	}elseif(preg_match("/construct/i", $query)){
  	  return $conf['endpoint']['describe']['output'];
  	}else{
  	  Utils::send500(null);
  	} 
  }
  
  public static function queryDir($modelDir, $e){
  	global $conf;
  	global $uri;
  	$originalDir = getcwd();
  	chdir($modelDir);
  	$handle = opendir('.');
  	$data = array();
  	while (false !== ($modelFile = readdir($handle))) {
  	  if($modelFile != "." && $modelFile != ".."){
  	  	if(is_dir($modelFile)){
  	  	  $data[$modelFile] = Utils::queryDir($modelFile, $e);
  	  	}else{
  	  	  $query = file_get_contents($modelFile);
  	  	  $query = preg_replace("|".$conf['resource']['url_delimiter']."|", "<".$uri.">", $query);
 	  	  if(isset($conf['endpoint'][$modelDir])){
  	  	  	//Use or create new endpoint
  	  	  	
  	  	  	if(!isset($e[$modelDir])){
  	  	  	  $e[$modelDir] = new Endpoint($conf['endpoint'][$modelDir], $conf['endpoint']['config']);
  	  	  	}
  	  	  	$data[$modelFile]['results'] = $e[$modelDir]->query($query, Utils::getResultsType($query));
 	  	  }else{
  	  	  	//User default endpoint
  	  	  	$data[$modelFile]['results'] = $e['base']->query($query, Utils::getResultsType($query));  
  	  	  }
  	  	  $data[$modelFile]['query'] = $query;
  	  	}
  	  }
  	}
  	chdir($originalDir);
  	return $data;
  }
  
}
?>
