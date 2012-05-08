<?
require_once('abstractModule.php');
class UriModule extends abstractModule{
  //Uri module
  
  public function match($uri){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	
  	if($conf['disableComponents'] == true){
  	  return FALSE;
  	}
  	require_once('classes/MetaDb.php');
  	$metaDb = new MetaDb($conf['metadata']['db']['location']);
  	$pair = Queries::getMetadata($localUri, $acceptContentType, $metaDb);
  	if($pair == NULL){ // Original URI is not in metadata
  	  if(Queries::uriExist($uri, $endpoints['local'])){
  	  	$page = Queries::createPage($uri, $localUri, $acceptContentType, $metaDb);
  	  	if($page == NULL){
  	  	  HTTPStatus::send500("Can't write sqlite database.");
  	  	}
  	  	HTTPStatus::send303($page, $acceptContentType);
  	  	exit(0);
  	  }else{
  	  	return false;
  	  }
  	}
  	$extension = Utils::getExtension($pair[2]); 
  	$curie = Utils::uri2curie($pair[0]);
  	list($modelFile, $viewFile) = $this->getModelandView($curie, $extension);
  	if($modelFile == NULL){
  	  return FALSE;
  	}
  	$result = array( 'res' => $pair[0],
  	  					 'page' => $pair[1], 
  	  					 'format' => $pair[2], 
  	  					 'modelFile' => $modelFile, 
  	  					 'viewFile' => $viewFile);

  	return $result;
  }
  
  public function execute($p){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	global $results;
  	global $firstResults;
  	$res = $p['res'];
  	$page = $p['page'];
  	$format = $p['format'];
  	$modelFile = $p['modelFile'];
  	$viewFile = $p['viewFile'];
  	$uri = $res;
  	$curie = Utils::uri2curie($res);
  	
  	//If resource is not the page, send a 303 to the document
  	if($res == $localUri){
  	  HTTPStatus::send303($page, $acceptContentType);
  	}
  	
  	$uri = $res;
  	if($conf['mirror_external_uris'] != false){
  	  $localUri = preg_replace("|^".$conf['ns']['local']."|", $conf['basedir'], $res);
  	}
  	
  	$extension = Utils::getExtension($format); 
  	
  	/*Redefine Content type based on the
  	* dcterms:format for this page
  	*/
  	$acceptContentType = $format;
  	
  	$curie = Utils::uri2curie($uri);
  	if($modelFile == NULL){
  	  return;
  	}
  	
  	//$lodspk = $conf['view']['standard'];

  	$lodspk['type'] = $modelFile;
  	  	$lodspk['home'] = $conf['basedir'];

  	$lodspk['module'] = 'uri';
  	$lodspk['add_mirrored_uris'] = true;
  	$lodspk['this']['value'] = $uri;
  	$lodspk['this']['curie'] = Utils::uri2curie($uri);
  	$lodspk['local']['value'] = $localUri;
  	$lodspk['local']['curie'] = Utils::uri2curie($localUri);
  	$lodspk['contentType'] = $acceptContentType;
  	$lodspk['model'] = $conf['model']['directory'];
  	$lodspk['view'] = $conf['view']['directory'];
  	$lodspk['ns'] = $conf['ns'];
  	
  	
  	//chdir($conf['home'].$conf['model']['directory']);
  	Utils::queryFile($modelFile, $endpoints['local'], $results, $firstResults);
  	if(!$lodspk['resultRdf']){
  	  $results = Utils::internalize($results); 
  	  $firstAux = Utils::getfirstResults($results);
  	  
  	  chdir($conf['home']);
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
  	//chdir($conf['home']);
  	if($conf['debug']){
  	  trigger_error("Using template ".$viewFile, E_USER_NOTICE);
  	  echo("TEMPLATE: ".$viewFile."\n\n");
  	}
  	Utils::processDocument($viewFile, $lodspk, $resultsObj);
  	
  }
  
  private static function getModelandView($uri, $extension){  	
  	global $conf;
  	global $lodspk;
  	$auxViewFile  = $conf['view']['directory'].'/'.$conf['uri']['prefix'].'/'.$uri.'/'.$extension.'.template';
  	$auxModelFile = $conf['model']['directory'].'/'.$conf['uri']['prefix'].'/'.$uri.'/'.$extension.'.queries';
  	if(file_exists($auxModelFile)){
 	  //Model exists
  	  $modelFile = $auxModelFile;//$conf['uri']['prefix'].$uri.'/'.$extension.'.queries';
  	  if(file_exists($auxViewFile) ){
  	  	//View exists, everything is fine
  	  	$viewFile = $conf['model']['directory'].'/'.$conf['uri']['prefix'].'/'.$uri.'/'.$extension.'.template';
  	  }elseif($extension != 'html'){
  	  	//View doesn't exists (and is not HTML)
  	  	$viewFile = null;  	  	
  	  }else{
  	  	//No HTML representation as fallback, then not recognized by URI module 
  	  	return array(null, null);
  	  }
  	  return array($modelFile, $viewFile);
  	}elseif(file_exists($conf['model']['directory'].'/'.$conf['uri']['prefix'].'/'.$uri.'/queries')){
  	  $modelFile = $conf['model']['directory'].'/'.$conf['uri']['prefix'].'/'.$uri.'/queries';//$conf['uri']['prefix'].$uri.'/html.queries';
	  if(file_exists($auxViewFile) ){
  	  	//View exists, everything is fine
  	  	$viewFile = $conf['model']['directory'].'/'.$conf['uri']['prefix'].'/'.$uri.'/'.$extension.'.template';
  	  }elseif($extension != 'html'){
  	  	//View doesn't exists (and is not HTML)
  	  	$lodspk['transform_select_query'] = true;
  	  	$viewFile = null;  	  	
  	  }
  	  return array($modelFile, $viewFile);
  	}

  	return array(NULL, NULL);
  }
  
}
?>
