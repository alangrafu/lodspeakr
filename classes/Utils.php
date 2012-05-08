<? 

class Utils{
    
  public static function uri2curie($uri){
  	global $conf;
  	$ns = $conf['ns'];
  	$curie = $uri;
  	
  	$aux = $uri;
  	foreach($ns as $k => $v){
  	  $aux = preg_replace("|^$v|", "", $uri);
  	  if($aux != $uri){
  	  	$uriSegments = explode("/", $aux);
  	  	$lastSegment = array_pop($uriSegments);
  	  	if(sizeof($uriSegments)>0){
  	  	  $prefix = $k."_".(implode("_", $uriSegments));
  	  	  //Adding "new" namespace
  	  	  $conf['ns'][$prefix] = $v.implode("/", $uriSegments)."/";
  	  	}else{
  	  	  $prefix = $k;
  	  	}
  	  	$curie = $prefix.":".$lastSegment;
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
  	if(sizeof($parts)>1 ){
  	  if(!isset($ns[$parts[0]])){
  		$prefixSegments = explode("_", $parts[0]);
  		$realPrefix = array_shift($prefixSegments);
  		$conf['ns'][$parts[0]] = $ns[$realPrefix].join("/", $prefixSegments);
  		return $ns[$realPrefix].join("/", $prefixSegments)."/".$parts[1];
  	  }else{
  	  	return $ns[$parts[0]].$parts[1];
  	  }
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
  	return array('ns' => $ns[$parts[0]], 'prefix' => $parts[0]);
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
  	  	  	
  	  	  	$row[$k]['value'] = $v['value'];
  	  	  	if($v['type'] == 'uri'){
  	  	  	  $row[$k]['curie'] = Utils::uri2curie($v['value']);
  	  	  	  $row[$k]['uri'] = 1;
  	  	  	}elseif($v['type'] == 'bnode'){
  	  	  	  $row[$k]['curie'] = 'blankNode';
  	  	  	}else{
  	  	  	  if($v['datatype']){
  	  	  	    $row[$k]['type'] = $v['datatype'];
  	  	  	  }
  	  	  	  if($v['xml:lang']){
  	  	  	    $row[$k]['lang'] = $v['xml:lang'];
  	  	  	  }

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
  
  
  private static function travelTree($tree){
  	$results = array();
  	if(is_string($tree)){
  	  return $tree;
  	}
  	foreach($tree as $t){
  	  $aux = Utils::travelTree($t);
  	  if(is_array($aux)){
  	  	$results = array_merge($results, $aux);
  	  }else{
  	  	array_push($results, $aux);
  	  }
  	}
  	return $results;
  }
  
  
  public static function serializeRdf($data, $extension='rdf'){
  	global $conf;
  	global $lodspk;
  	$ser;
  	$dPointer;
  	$docs = Utils::travelTree($data);
  	require_once($conf['home'].'lib/arc2/ARC2.php');
  	$parser = ARC2::getRDFParser();
  	 $triples = array();
  	 
  	foreach($docs as $d){
  	  $parser->parse($conf['basedir'], $d);
  	  $t = $parser->getTriples();
  	  $triples = array_merge($triples, $t);
  	}
  	if($lodspk['mirror_external_uris']){
  	  global $uri;
  	  global $localUri;
  	  $t = array();
  	  $t['s']      = $localUri;
  	  $t['s_type'] = 'uri';
  	  $t['p']      = "http://www.w3.org/2002/07/owl#sameAs";
  	  $t['o']      = $uri;
  	  $t['o_type'] = 'uri';  	 
  	  array_push($triples, $t);
  	  $t['p']      = "http://www.w3.org/2000/10/swap/pim/contact#preferredURI";
  	  array_push($triples, $t);
  	}
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
  	default:
  	  $ser = null;
  	}
  	if($ser != null){
  	$doc = $ser->getSerializedTriples($triples);
  	}else{
  	$doc = var_export($data, true);
  	}
  	return $doc;
  }
  
  public static function processDocument($viewFile, $lodspk, $data){
  	global $conf;
  	global $lodspk;
  	$contentType = $lodspk['contentType'];
  	$extension = Utils::getExtension($contentType); 
  	
  	header('Content-Type: '.$contentType);
  	if(isset($lodspk['resultRdf']) && $lodspk['resultRdf'] == true){
  	  echo Utils::serializeRdf($data, $extension);
  	}else{
  	  Utils::showView($lodspk, $data, $viewFile);  	
  	}
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
  	  HTTPStatus::send500(null);
  	} 
  }
  
  public static function queryDir($modelDir, &$r, &$f){
  	global $conf;
  	global $uri;
  	global $lodspk;
  	global $endpoints;
  	global $results;
  	$strippedModelDir = str_replace('endpoint.', '', $modelDir); 	  
  	$lodspk['model'] = $modelDir;
  	$originalDir = getcwd();
  	$subDirs= array();
  	trigger_error("Entering $strippedModelDir from ".getcwd(), E_USER_NOTICE);
  	chdir($modelDir);
  	$handle = opendir('.');
  	
  	while (false !== ($modelFile = readdir($handle))) {
  	  if($modelFile != "." && $modelFile != ".." && strpos($modelFile, ".") !== 0){
  	  	if(is_dir($modelFile)){
  	  	  if(strpos('endpoint.', $modelFile) == 0){
  	  	  	trigger_error("Save $modelFile for later, after all the queries in the current directory has been resolved", E_USER_NOTICE);
  	  	  	$subDirs[]=$modelFile;
  	  	  }
  	  	}else{
  	  	  if(preg_match('/\.query$/', $modelFile)){
  	  	    $e = null;
  	  	    if(!isset($endpoints[$strippedModelDir])){
  	  	      trigger_error("Creating endpoint for $strippedModelDir", E_USER_NOTICE);
  	  	      if(!isset($conf['endpoint'][$strippedModelDir])){
  	  	        trigger_error("Couldn't find $strippedModelDir as a list of available endpoints. Will continue using local", E_USER_WARNING);
  	  	        $e = $endpoints['local'];
  	  	      }else{  
  	  	        $endpoints[$strippedModelDir] = new Endpoint($conf['endpoint'][$strippedModelDir], $conf['endpoint']['config']);
  	  	        $e = $endpoints[$strippedModelDir];
  	  	      }
  	  	    }else{
  	  	      $e = $endpoints[$strippedModelDir];
  	  	    }
  	  	    if($modelDir != $lodspk['type']){
  	  	      if(!isset($r[$strippedModelDir]) ){
  	  	        $r[$strippedModelDir] = array();
  	  	        $f[$strippedModelDir] = array();
  	  	      }
  	  	      Utils::queryFile($modelFile, $e, $r[$strippedModelDir], $f);
  	  	    }else{
  	  	      Utils::queryFile($modelFile, $e, $r, $f);
  	  	    }
  	  	  }
 	  	  }
  	  }
  	}
  	closedir($handle);
  	$originalDir = $lodspk['model'];
  	if(isset($subDirs)){
  	  foreach($subDirs as $v){
  	  	if(!isset($r[$modelDir])){
  	  	  $r[$modelDir] = array();
  	  	}
  	  	if($modelDir != $lodspk['type']){
  	  	  Utils::queryDir($v, $r[$strippedModelDir], $f[$strippedModelDir]);
  	  	}else{
  	  	  Utils::queryDir($v, $r, $f);
  	  	}
  	  }  	
  	}
  //	chdir($conf['home']);
  	//return $data;
  }
  
  
  public static function queryFile($modelFile, $e, &$rPointer, &$fPointer){
  	global $conf;
  	global $lodspk;
  	global $results;
  	global $firstResults;
	$uri = $lodspk['this']['value'];
  	$data = array();
  	$strippedModelFile = str_replace('endpoint.', '', str_replace('.query', '',$modelFile)); 	  
 	if(!is_dir($modelFile)){
  	  require_once($conf['home'].'lib/Haanga/lib/Haanga.php');
  	  Haanga::configure(array(
  	  	'cache_dir' => $conf['home'].'cache/',
  	  	'autoescape' => FALSE,
  	  	));
  	  
  	  //Haanga supports the dot (.) convention only for objects
  	  if(is_array($lodspk)){
  	  	$lodspkObj = Convert::array_to_object($lodspk);
  	    $lodspk = $lodspkObj;
  	  }
  	  $r2 = Convert::array_copy($results);
  	  $models = Convert::array_to_object($r2);
  	  $f2 = Convert::array_copy($firstResults);
  	  $first = Convert::array_to_object($f2);
 	  $vars = compact('uri', 'lodspk', 'models', 'first');
 	  $q = file_get_contents($modelFile);
 	  if($q == false){
 	  	HTTPStatus::send500("I can't load ".$modelFile." in ".getcwd());
 	  }
 	  $fnc = Haanga::compile($q);
  	  $query = $fnc($vars, TRUE);
  	  
  	  if(is_object($lodspk)){
  	  	$lodspkObj = Convert::object_to_array($lodspk);
  	    $lodspk = $lodspkObj;
  	  }
  	  
  	  if($lodspk['transform_select_query']==true){
  	  	include_once($conf['home'].'lib/arc2/ARC2.php');
  	  	$parser = ARC2::getSPARQLParser();
  	  	$parser->parse($query);
  	  	$sparqlConstruct = array();
  	  	if (!$parser->getErrors()) {
  	  	  $resultVars = array();
  	  	  $q_infos = $parser->getQueryInfos();
  	  	  foreach($q_infos['query']['result_vars'] as $v){
  	  	  	if($v['type'] == 'var'){
  	  	  	  $resultVars[$v['value']] = 1;
  	  	  	}
  	  	  };
  	  	  $x = Utils::extractObj($q_infos['query']['pattern']);
  	  	  foreach($x as $v){
  	  	  	if(($resultVars[$v['s']] && $v['s_type'] == 'var')
  	  	  	  || ($resultVars[$v['p']] && $v['p_type'] == 'var')
	  	  	|| ($resultVars[$v['o']] && $v['o_type'] == 'var')){
	  	  	array_push($sparqlConstruct, $v);
	  	  	}	  	  
	  	  }
	  	  $construct = "";
	  	  foreach($sparqlConstruct as $v){
	  	  	if($v['s_type'] == 'uri'){
	  	  	  $construct .= "<".$v['s']."> ";
	  	  	}elseif($v['s_type'] == 'var'){
	  	  	  $construct .= '?'.$v['s'].' ';
	  	  	}else{
	  	  	  $construct.= $v['s']." ";
	  	  	}
	  	  	
	  	  	if($v['p_type'] == 'uri'){
	  	  	  $construct .= "<".$v['p']."> ";
	  	  	}elseif($v['p_type'] == 'var'){
	  	  	  $construct .= '?'.$v['p'].' ';
	  	  	}else{
	  	  	  $construct.= $v['p']." ";
	  	  	}
	  	  	
	  	  	if($v['o_type'] == 'uri'){
	  	  	  $construct .= "<".$v['o']."> ";
	  	  	}elseif($v['o_type'] == 'literal'){
	  	  	  $construct .= '"'.$v['o'].'" ';
	  	  	}elseif($v['o_type'] == 'var'){
	  	  	  $construct .= '?'.$v['o'].' ';
	  	  	}else{
	  	  	  $construct.= $v['o']." ";
	  	  	}
	  	  	
	  	  	$construct .= ".\n";
	  	  }
	  	  if($construct == ""){
	  	  	if(sizeof($q_infos['query']['result_vars'])>0){
	  	  	  //For now, assuming variables are in the GRAPH ?g
	  	  	  $query = "CONSTRUCT {?g ?x ?y} WHERE{GRAPH ?g{?g ?x ?y}}";
	  	  	}else{
	  	  	  HTTPStatus::send500();
	  	  	}
	  	  }else{
	  	  	$query = preg_replace('/select\n?.*\n?where/i', 'CONSTRUCT {'.$construct.'} WHERE', $query);
	  	  }
	  	}else {
	  	  HTTPStatus::send500("invalid query: " . $parser->getErrors());
	  	}
	  }
  	  $query = Utils::addPrefixes($query);
  	  if($conf['debug']){
  	  	echo "\n-------------------------------------------------\nIn ".getcwd()."\n";
  	    echo "$modelFile (against ".$e->getSparqlUrl().")\n-------------------------------------------------\n\n";
  	  	echo $query;
  	  }
  	  trigger_error("Running query from ".$modelFile." on endpoint ".$e->getSparqlURL(), E_USER_NOTICE);
  	  $aux = $e->query($query, Utils::getResultsType($query)); 
  	  if($modelFile != $lodspk['type']){
  	  	if(!isset($rPointer[$strippedModelFile])){
  	  	  $rPointer[$strippedModelFile] = array();
  	  	  $firstResults[$strippedModelFile] = array();
  	  	}
  	  	if(Utils::getResultsType($query) == $conf['output']['select']){
  	  	  $rPointer[$strippedModelFile] = Utils::sparqlResult2Obj($aux);
  	  	  $fPointer[$strippedModelFile] = $rPointer[$strippedModelFile][0];
  	  	}else{
  	  	  $lodspk['resultRdf'] = true;
  	  	  $rPointer[$strippedModelFile] = $aux;
  	  	}
  	  }else{
  	  	if(Utils::getResultsType($query) == $conf['output']['select']){
  	  	  $rPointer = Utils::sparqlResult2Obj($aux);
  	  	  $fPointer[$strippedModelFile] = $rPointer[0];
  	  	}else{
  	  	  $lodspk['resultRdf'] = true;
  	  	  $rPointer = $aux;
  	  	}  	 
  	  }
  	}else{
  	  if(strpos('endpoint.', $modelFile) == 0){
  	  	
  	  	trigger_error("$modelFile is a directory, will process it later", E_USER_NOTICE);
  	  	if($modelFile != $lodspk['type']){
  	  	  if(!isset($rPointer[$strippedModelFile])){
  	  	  	$rPointer[$strippedModelFile] = array();
  	  	  }
  	  	  Utils::queryDir($modelFile, $rPointer[$strippedModelFile], $fPointer[$strippedModelFile]);
  	  	}else{
  	  	  Utils::queryDir($modelFile, $rPointer, $fPointer);
  	  	}
  	  }
  	}
  }
  
  public static function internalize($array){
  	global $conf;
  	$firstResultsKeyAppearance = true;
  	foreach($array as $key => $value){
  	  if(!isset($value['value'])){
  	  	$array[$key] = Utils::internalize($value);
  	  	/*if($firstResultsKeyAppearance){
  	  	$firstResultsKeyAppearance = false;
  	  	$array['_firstResults']=$array[$key];
  	  	}*/
  	  }else{
  	  	if(isset($value['uri']) && $value['uri'] == 1){
  	  	  if(isset($conf['mirror_external_uris']) && $conf['mirror_external_uris'] != false){
  	  	  	$value['mirroredUri'] = $value['value'];
  	  	  	
  	  	  	if(is_bool($conf['mirror_external_uris'])){
  	  	  	  $value['value'] = preg_replace("|^".$conf['ns']['local']."|", $conf['basedir'], $value['value']);
  	  	  	}elseif(is_string($conf['mirror_external_uris'])){
  	  	  	  $value['value'] = preg_replace("|^".$conf['mirror_external_uris']."|", $conf['basedir'], $value['value']);
  	  	  	}else{
  	  	  	  HTTPStatus::send500("Error in mirroring configuration");
  	  	  	  exit(1);
  	  	  	}
  	  	  }
  	  	  $value['curie'] = Utils::uri2curie($value['value']);
  	  	  $array[$key] = $value;
  	  	}  	  	  	  	
  	  } 
  	}
  	return $array;
  }
  
  public static function getfirstResults($array){
  	global $conf;
  	$firstResultsKeyAppearance = true;
  	foreach($array as $key => $value){
  	  if(!isset($value['value'])){
  	  	$aux = Utils::getfirstResults($value);
  	  	if(isset($aux['0'])){
  	  	  $array[$key] = $aux['0'];
  	  	}else{
  	  	  $array[$key] = $aux;
  	  	}
  	  } 
  	}
  	return $array;
  }
  
  
  public static function showView($lodspkData, $data, $view){
  	global $conf;
  	global $uri;
  	global $lodspk;
  	global $extension;
  	//$lodspk = $conf['view']['standard'];
  	$lodspk = $lodspkData;
  	if(isset($lodspkData['params'])){
  	  $lodspk['this']['params'] = $lodspkData['params'];
  	}
  	require_once($conf['home'].'lib/Haanga/lib/Haanga.php');
  	$viewAux = explode("/",$view);
  	$viewFile = array_pop($viewAux);
  	$viewPath = join("/", $viewAux);
  	Haanga::configure(array(
  	  'template_dir' => $conf['home'].$viewPath,
  	  'cache_dir' => $conf['home'].'cache/',
  	  ));
  	$models = $data;
  	$first = $lodspk['firstResults'];
  	unset($lodspk['firstResults']);
  	$lodspk = $lodspk;
  	//unset($lodspk);
  	$vars = compact('uri','lodspk', 'models', 'first');
 	if($conf['debug']){
 	  var_dump($vars); 	
 	}
	if(is_string($data)){
	  echo($data);
	}elseif(is_file($conf['home'].$view)){
         try{
	  Haanga::Load($viewFile, $vars);
          }catch(Exception $e){
echo '<pre>';
           echo $e->getMessage();
var_dump($vars);
echo($e->getMessage()."' in ".$e->getFile().":".$e->getLine()."\nStack trace:\n".$e->getTraceAsString());
echo '</pre>';
         }
	}elseif($view == null){
	  $fnc = Haanga::compile('{{models|safe}}');
	  $fnc($vars, TRUE);
	}else{
	  echo $conf['home'].$viewPath." ".$viewFile;
	  $fnc = Haanga::compile($view);
	  $fnc($vars, TRUE);
	}
  	
  }
  
  private static function extractObj($obj, $term = 'triple'){
  	$triples = array();
  	if(is_array($obj)){
  	  foreach($obj as $k => $v){
  	  	if($v['type'] != 'triple'){
  	  	  $aux = Utils::extractObj($v);
  	  	  if($aux['type'] != 'triple'){
  	  	  	$triples = array_merge($triples,$aux);
  	  	  }else{
  	  	  	$triples = array_merge($triples, $aux);
  	  	  }
  	  	}else{  	  	
  	  	  array_push($triples, $v);
  	  	}
  	  }
  	}
  	return $triples;
  }
  
  private static function addPrefixes($q){
    global $conf;
    $matches = array();
    $visited = array();
    $newQuery = $q;
    if(preg_match_all("|\s(\w+):\w+|", $q, $matches) > 0){
      foreach($matches[1] as $v){
        if(!isset($visited[$v]) && isset($conf['ns'][$v])){
          $newQuery = "PREFIX ".$v.": <".$conf['ns'][$v].">\n".$newQuery;
          $visited[$v] = true;
        }
      }
    }
    
    return $newQuery;
  }
  
}

?>
