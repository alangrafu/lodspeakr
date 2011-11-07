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
  	if(sizeof($parts)>1 && isset($ns[$parts[0]])){
  	  return $ns[$parts[0]].$parts[1];
  	}else{
  	  return $curie;
  	}
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
  	global $conf;
  	$obj = array();
  	if(!isset($data['results'])){
  	  foreach($data as $k => $v){
  	  	$obj[$k] = Utils::sparqlResult2Obj($v);
  	  }
  	}else{
  	  $aux = $data['results']['bindings'];
  	  if(sizeof($aux)>0){
  	  	foreach($aux as $w){
  	  	  $row = array();
  	  	  foreach($w as $k => $v){
  	  	  	if($conf['use_external_uris'] === true && $v['type'] == 'uri'){
  	  	  	  $v['value'] = preg_replace("|^".$conf['ns']['local']."|", $conf['basedir'], $v['value']);
  	  	  	} 	  	
  	  	  	$row[$k]['value'] = $v['value'];
  	  	  	if($v['type'] == 'uri'){
  	  	  	  $row[$k]['curie'] = Utils::uri2curie($v['value']);
  	  	  	  $row[$k]['uri'] = 1;
  	  	  	}elseif($v['type'] == 'bnode'){
  	  	  	  $row[$k]['curie'] = 'blankNode';
  	  	  	}  	  	  	
  	  	  }
  	  	  /*if(sizeof($aux) == 1){
  	  	  $obj = $row;
  	  	  }*/
  	  	  if(sizeof($row) >0){
  	  	  	array_push($obj, $row);
  	  	  }
  	  	  
  	  	}
  	  }
  	}
  	return $obj;
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
  	$a = explode(",", $accept_string);
  	$b = array();
  	foreach($a as $v){
  	  foreach($conf['http_accept'] as $formatTypeArray){
  	  	if(strstr($v, ";")){
  	  	  $aux = explode(";q=", $v);
  	  	  if(in_array($aux[0], $formatTypeArray)){
  	  	  	$b[$aux[0]] = $aux[1];
  	  	  }
  	  	}else{
  	  	  if(in_array($v, $formatTypeArray)){
  	  	  	$b[$v] = 1;
  	  	  }
  	  	}
  	  }
  	}
  	$a = $b;
  	arsort($a);
  	$ct = 'text/html';
  	foreach($a as $k => $v){
  	  $ct = $k;
  	  break;
  	}
  	if($ct == NULL || $ct == "" || $ct == "*/*"){
  	  $ct = 'text/html';
  	}
  	return $ct;
  }
  
  private static function serializeRdf($data, $extension){
  	global	$conf;
  	require('lib/arc2/ARC2.php');
  	$parser = ARC2::getRDFParser();
  	$parser->parse($conf['basedir'], $data);
  	$triples = $parser->getTriples();
  	$ser;
  	switch ($extension){
  	case 'ttl':
  	  $ser = ARC2::getTurtleSerializer();
  	  break;
  	case 'nt':
  	  $ser = ARC2::getNTriplesSerializer();
  	  break;
  	case 'json':
  	  $ser = ARC2::getRDFJSONSerializer();
  	  break;
  	case 'rdf':
  	  $ser = ARC2::getRDFXMLSerializer();
  	  break;
  	}
  	$doc = $ser->getSerializedTriples($triples);
  	return $doc;
  }
  
  public static function processDocument($viewFile, $base, $data){
  	global $conf;
  	$contentType = $base['this']['contentType'];
  	$extension = Utils::getExtension($contentType); 
  	
  	header('Content-Type: '.$contentType);
  	if(!is_object($data)){
  	  $data = Utils::serializeRdf($data, $extension);
  	}
  	Utils::showView($base, $data, $viewFile);  	
  }
  
  public static function getResultsType($query){
  	global $conf;
  	if(preg_match("/select/i", $query)){
  	  return $conf['output']['select'];
  	}elseif(preg_match("/describe/i", $query)){
  	  return $conf['output']['describe'];
  	}elseif(preg_match("/construct/i", $query)){
  	  return $conf['output']['describe'];
  	}else{
  	  Utils::send500(null);
  	} 
  }
  
  public static function queryDir($modelDir, &$r){
  	global $conf;
  	global $uri;
  	global $base;
  	global $endpoints;
  	global $results;
  	$base['model']['directory'] = $modelDir;
  	$originalDir = getcwd();
  	
  	trigger_error("Entering $modelDir from ".getcwd(), E_USER_NOTICE);
  	chdir($modelDir);
  	$handle = opendir('.');
  	
  	while (false !== ($modelFile = readdir($handle))) {
  	  if($modelFile != "." && $modelFile != ".."){
  	  	if(is_dir($modelFile)){
  	  	  //Save it for later, after all the queries in the current directory has been resolved
  	  	  $subDirs[]=$modelFile;
  	  	}else{
  	  	  $e = null;
  	  	  if(!isset($endpoints[$modelDir])){
  	  	  	trigger_error("Creating endpoint for $modelDir", E_USER_NOTICE);
  	  	  	if(!isset($conf['endpoint'][$modelDir])){
  	  	  	  trigger_error("Couldn't find $modelDir as a list of available endpoints. Will continue using local", E_USER_WARNING);
  	  	  	  $e = $endpoints['local'];
  	  	  	}else{  
  	  	  	  $endpoints[$modelDir] = new Endpoint($conf['endpoint'][$modelDir], $conf['endpoint']['config']);
  	  	  	  $e = $endpoints[$modelDir];
  	  	  	}
  	  	  }else{
  	  	  	$e = $endpoints[$modelDir];
  	  	  }
  	  	  if($modelDir != $base['type']){
  	  	  	if(!isset($r[$modelDir]) ){
  	  	  	  $r[$modelDir] = array();
  	  	  	}
  	  	  	Utils::queryFile($modelFile, $e, $r[$modelDir]);
  	  	  }else{
  	  	  	Utils::queryFile($modelFile, $e, $r);
  	  	  }
 	  	}
  	  }
  	}
  	closedir($handle);
  	$originalDir = $base['model']['directory'];
  	foreach($subDirs as $v){
  	  if(!isset($r[$modelDir])){
  	  	$r[$modelDir] = array();
  	  }
  	  if($modelDir != $base['type']){
  	  	Utils::queryDir($v, $r[$modelDir]);
  	  }else{
  	  	Utils::queryDir($v, $r);
  	  }
  	}  	
  	chdir("..");
  	//return $data;
  }
  
  
  public static function queryFile($modelFile, $e, &$rPointer){
  	global $conf;
  	global $base;
  	global $results;
   	
  	$uri = $base['this']['value'];
  	$data = array();
  	
  	
 	if(!is_dir($modelFile)){
  	  require_once($conf['home'].'lib/Haanga/lib/Haanga.php');
  	  Haanga::configure(array(
  	  	'cache_dir' => $conf['home'].'cache/',
  	  	'autoescape' => FALSE,
  	  	));
  	  
  	  //Haanga supports the dot (.) convention only for objects
  	  if(is_array($base)){
  	  	$baseObj = Convert::array_to_object($base);
  	    $base = $baseObj;
  	  }
  	  $r2 = Convert::array_copy($results);
  	  $r = Convert::array_to_object($r2);
 	  $vars = compact('uri', 'base', 'r');
 	  $fnc = Haanga::compile(file_get_contents($modelFile));
  	  $query = $fnc($vars, TRUE);
  	  if(is_object($base)){
  	  	$baseObj = Convert::object_to_array($base);
  	    $base = $baseObj;
  	  }
  	  if($conf['debug']){
  	  	echo $query;
  	  }
  	  trigger_error("Running query on endpoint", E_USER_NOTICE);
  	  $aux = $e->query($query, Utils::getResultsType($query));  	  
  	  if($modelFile != $base['type']){
  	  	if(!isset($rPointer[$modelFile])){
  	  	  $rPointer[$modelFile] = array();
  	  	}
  	  	if(Utils::getResultsType($query) == $conf['output']['select']){
  	  	  $rPointer[$modelFile] = Utils::sparqlResult2Obj($aux);
  	  	  /*if(sizeof($rPointer)>0){
  	  	  $rPointer[$modelFile]['first'] = $rPointer[$modelFile][0];
  	  	  }*/
  	  	}else{
  	  	  $rPointer[$modelFile] = $aux;
  	  	}
  	  }else{
  	  	if(Utils::getResultsType($query) == $conf['output']['select']){
  	  	  $rPointer = Utils::sparqlResult2Obj($aux);
  	  	  /*if(sizeof($rPointer)>0){
  	  	  $rPointer['first'] = $rPointer[0];
  	  	  }*/
  	  	}else{
  	  	  $rPointer = $aux;
  	  	}  	 
  	  }
  	}else{
  	  trigger_error("$modelFile is a directory, will process it later", E_USER_NOTICE);  	  
  	  if($modelFile != $base['type']){
  	  	if(!isset($rPointer[$modelFile])){
  	  	  $rPointer[$modelFile] = array();
  	  	}
  	  	Utils::queryDir($modelFile, $rPointer[$modelFile]);
  	  }else{
  	  	Utils::queryDir($modelFile, $rPointer);
  	  }
  	}
  }
  
  
  public static function showView($baseData, $data, $view){
  	global $conf;
  	$base = $conf['view']['standard'];
  	$base = $baseData;
  	if(isset($baseData['params'])){
  	  $base['this']['params'] = $baseData['params'];
  	}
  	require_once('lib/Haanga/lib/Haanga.php');
  	Haanga::configure(array(
  	  'template_dir' => $base['view']['directory'],
  	  'cache_dir' => $conf['home'].'cache/',
  	  ));
  	$r = $data;
  	$vars = compact('base', 'r');
 	if($conf['debug']){
 	  var_dump($r); 	
 	}
	if(is_file($base['view']['directory'].$view)){
	  Haanga::Load($view, $vars);
	}else{
	  $fnc = Haanga::compile($view);
	  $fnc($vars, FALSE);
	}
  	
  }
  
  
}

?>
