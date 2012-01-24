<?
require_once('abstractModule.php');
class ClassModule extends abstractModule{
  //Class module
  
  public function match($uri){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $base;
  	
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
  	return $pair;
  }
  
  public function execute($pair){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $base;
  	global $results;
  	global $first;
  	list($res, $page, $format) = $pair;
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
  	
  	//Check if files for model and view exist
  	$t=Queries::getClass($uri, $endpoints['local']);
  	
  	list($modelFile, $viewFile) = $this::getModelandView($t, $extension);
  	$base = $conf['view']['standard'];
  	$base['type'] = $modelFile;
  	$base['this']['value'] = $uri;
  	$base['this']['curie'] = Utils::uri2curie($uri);
  	$base['thislocal']['value'] = $localUri;
  	$base['thislocal']['curie'] = Utils::uri2curie($localUri);
  	
  	$base['this']['contentType'] = $acceptContentType;
  	$base['model']['directory'] = $conf['model']['directory'];
  	$base['view']['directory'] = $conf['view']['directory'];
  	$base['ns'] = $conf['ns'];
  	
  	
  	chdir($conf['home'].$conf['model']['directory']);
  	
  	Utils::queryFile($modelFile, $endpoints['local'], $results, $first);
  	$results = Utils::internalize($results); 

  	$base['first'] = Utils::getFirsts($results);
  	chdir($conf['home']);
  	if(is_array($results)){
  	  $resultsObj = Convert::array_to_object($results);
  	}else{
  	  $resultsObj = $results;
  	}
  	Utils::processDocument($viewFile, $base, $resultsObj);
  	
  }
  
  private static function getModelandView($t, $extension){  	
  	global $conf;
  	global $results;
  	global $rPointer;
  	//Defining default views and models
  	$curieType="";
  	$modelFile = 'class.rdfs:Resource/'.$extension.'.queries';
  	$viewFile = 'class.rdfs:Resource/'.$extension.'.template';
  	
  	//Get the first class available
  	$typesAndValues = array();
  	foreach($t as $v){
  	  $curie = Utils::uri2curie($v);
  	  $typesAndValues[$curie] = 0;
  	  if(isset($conf['types']['priorities'][$curie]) && $conf['types']['priorities'][$curie] >= 0){
  	  	$typesAndValues[$curie] = $conf['types']['priorities'][$curie];
  	  }
  	}
  	arsort($typesAndValues);
  	foreach($typesAndValues as $v => $w){
  	  $auxViewFile  = $conf['view']['directory'].$conf['class']['prefix'].$v.'/'.$extension.'.template';
  	  $auxModelFile = $conf['model']['directory'].$conf['class']['prefix'].$v.'/'.$extension.'.queries';
  	  if(file_exists($auxModelFile) && file_exists($auxViewFile) && $v != null){
  	  	$viewFile = $conf['class']['prefix'].$v.'/'.$extension.'.template';
  	  	$modelFile = $conf['class']['prefix'].$v.'/'.$extension.'.queries';
  	  	break;
  	  }
  	}
  	return array($modelFile, $viewFile);
  }

}
?>
