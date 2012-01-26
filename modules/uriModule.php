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
  	
  	require_once('classes/MetaDb.php');
  	$metaDb = new MetaDb($conf['metadata']['db']['location']);
  	
  	$pair = Queries::getMetadata($localUri, $acceptContentType, $metaDb);
  	
  	if($pair == NULL){ // Original URI is not in metadata
  	  if(Queries::uriExist($uri, $endpoints['local'])){
  	  	$page = Queries::createPage($uri, $localUri, $acceptContentType, $metaDb);
  	  	if($page == NULL){
  	  	  Utils::send500("Can't write sqlite database.");
  	  	}
  	  	Utils::send303($page, $acceptContentType);
  	  	exit(0);
  	  }else{
  	  	return false; //Utils::send404($uri);
  	  }
  	}
  	$extension = Utils::getExtension($pair[2]); 
  	$curie = Utils::uri2curie($pair[0]);
  	list($modelFile, $viewFile) = $this::getModelandView($curie, $extension);
  	
  	if($modelFile == NULL){
  	  return FALSE;
  	}
  	return $pair;
  }
  
  public function execute($pair){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	global $results;
  	global $first;
  	list($res, $page, $format) = $pair;
  	$uri = $res;
  	$curie = Utils::uri2curie($res);

  	//If resource is not the page, send a 303 to the document
  	if($res == $localUri){
  	  Utils::send303($page, $acceptContentType);
  	}
  	
  	$uri = $res;
  	if($conf['mirror_external_uris']){
  	  $localUri = preg_replace("|^".$conf['ns']['local']."|", $conf['basedir'], $res);
  	}
  	
  	$extension = Utils::getExtension($format); 
  	
  	/*Redefine Content type based on the
  	* dcterms:format for this page
  	*/
  	$acceptContentType = $format;

  	$curie = Utils::uri2curie($uri);
  	list($modelFile, $viewFile) = $this::getModelandView($curie, $extension);
  	if($modelFile == NULL){
  	  return;
  	}
  	
  	$lodspk = $conf['view']['standard'];
  	$lodspk['type'] = $modelFile;
  	$lodspk['module'] = 'uri';
  	$lodspk['add_mirrored_uris'] = true;
  	$lodspk['this']['value'] = $uri;
  	$lodspk['this']['curie'] = Utils::uri2curie($uri);
  	$lodspk['thislocal']['value'] = $localUri;
  	$lodspk['thislocal']['curie'] = Utils::uri2curie($localUri);
  	
  	$lodspk['this']['contentType'] = $acceptContentType;
  	$lodspk['model']['directory'] = $conf['model']['directory'];
  	$lodspk['view']['directory'] = $conf['view']['directory'];
  	$lodspk['ns'] = $conf['ns'];
  	
  	
  	chdir($conf['home'].$conf['model']['directory']);
  	
  	Utils::queryFile($modelFile, $endpoints['local'], $results, $first);
  	$results = Utils::internalize($results); 
  	
  	$lodspk['first'] = Utils::getFirsts($results);
  	chdir($conf['home']);
  	if(is_array($results)){
  	  $resultsObj = Convert::array_to_object($results);
  	}else{
  	  $resultsObj = $results;
  	}
  	Utils::processDocument($viewFile, $lodspk, $resultsObj);
  	
  }
  
  private static function getModelandView($uri, $extension){  	
  	global $conf;
  	$auxViewFile  = $conf['view']['directory'].$conf['uri']['prefix'].$uri.'/'.$extension.'.template';
  	$auxModelFile = $conf['model']['directory'].$conf['uri']['prefix'].$uri.'/'.$extension.'.queries';
  	if(file_exists($auxModelFile) && file_exists($auxViewFile) ){
  	  $viewFile = $conf['uri']['prefix'].$uri.'/'.$extension.'.template';
  	  $modelFile = $conf['uri']['prefix'].$uri.'/'.$extension.'.queries';
  	  return array($modelFile, $viewFile);
  	}
  	return array(NULL, NULL);
  }
  
}
?>
