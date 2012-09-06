<?php

require_once('abstractModule.php');
class sparqlFilterModule extends abstractModule{
  //Class module
  
  public function match($uri){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	global $results;
  	global $firstResults;
  	
  	require_once($conf['home'].'classes/MetaDb.php');
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
  	
  	list($res, $page, $format) = $pair;
    $uri = $res;
	  $queries = $this->getQueries();
	  $e = $endpoints['local'];
	  require_once($conf['home'].'lib/Haanga/lib/Haanga.php');
	  Haanga::configure(array(
	    'cache_dir' => $conf['home'].'cache/',
	    'autoescape' => FALSE,
	    ));
 	  $vars = compact('uri', 'lodspk', 'models', 'first');
 	  
	  foreach($queries as $l => $v){
	    $q = Utils::addPrefixes(file_get_contents($v));
	    $fnc = Haanga::compile($q);
  	  $query = $fnc($vars, TRUE);
  	  $aux = $e->query($query, Utils::getResultsType($query)); 
	    if($aux["boolean"] === true){
	      $pair[] = $l;
	      return $pair;
	    }
  	}
  	return false;  
  }
  
public function execute($pair){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	global $results;
  	global $firstResults;
  	$results = array();
  	list($res, $page, $format, $filter) = $pair;
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
  	//Check if files for model and view exist
  	$t = array($pair[3]);
  	$obj = $this->getModelandView($t, $extension); 
  	$modelFile = $obj['modelFile'];
  	$lodspk['model'] = $conf['model']['directory'];  	
  	$viewFile = $obj['viewFile'];
  	$lodspk['view'] = $obj['view']['directory'];
  	if($viewFile == null){
  	  $lodspk['transform_select_query'] = true;
  	}
  	
  	$lodspk['sparqlFilter'] = $modelFile;
  	$lodspk['home'] = $conf['basedir'];
  	$lodspk['baseUrl'] = $conf['basedir'];
  	$lodspk['module'] = 'sparqlFilter';
  	$lodspk['root'] = $conf['root'];
  	$lodspk['contentType'] = $acceptContentType;
  	$lodspk['ns'] = $conf['ns'];
  	$lodspk['endpoint'] = $conf['endpoint'];
  	$lodspk['view'] = $conf['view']['directory'];
  	$lodspk['type'] = $modelFile;

  	$lodspk['add_mirrored_uris'] = true;
  	$lodspk['this']['value'] = $uri;
  	$lodspk['this']['curie'] = Utils::uri2curie($uri);
  	$lodspk['local']['value'] = $localUri;
  	$lodspk['local']['curie'] = Utils::uri2curie($localUri);
   	$lodspk['this']['extension'] = $extension;
  	//chdir($conf['home'].$conf['model']['directory']);
  	
  	Utils::queryFile($modelFile, $endpoints['local'], $results, $firstResults);
    if(!$lodspk['resultRdf']){
  	  $results = Utils::internalize($results); 
  	  $firstAux = Utils::getfirstResults($results);
  	  
  	  //chdir($conf['home']);
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
  	//chdir($conf['home'].$conf['model']['directory']);
  	Utils::processDocument($viewFile, $lodspk, $resultsObj);
  	
  }
  
  private function getQueries(){
    global $conf;
    $results = array();
    $dir = $conf['home'].$conf['model']['directory'].'/'.$conf['sparqlFilter']['prefix'];
    if ($handle = opendir($dir)) {
      
      /* This is the correct way to loop over the directory. */
      while (false !== ($entry = readdir($handle))) {
        if($entry != '.' && $entry != '..'){
          if(file_exists($dir.'/'.$entry.'/'.$conf['sparqlFilter']['filterFileName']))
            $results[$entry] = $dir.'/'.$entry.'/'.$conf['sparqlFilter']['filterFileName'];
        }
      }
      
      closedir($handle);
      return $results;
    }else{
      return null;
    }
  }
  
  
  private static function getModelandView($t, $extension){  	
  	global $conf;
  	global $results;
  	global $rPointer;
  	global $lodspk;
  	$objResult = array('modelFile' => null, 'viewFile' => null);
  	//Defining default views and models
  	$curieType="";
  	//Get the firstResults type available
  	foreach($t as $v){
  	  if($v == null){continue;}
  	  $auxViewFile  = $conf['view']['directory'].'/'.$conf['sparqlFilter']['prefix'].'/'.$v.'/'.$extension.'.template';
  	  $auxModelFile = $conf['model']['directory'].'/'.$conf['sparqlFilter']['prefix'].'/'.$v.'/'.$extension.'queries';
  	  if(file_exists($auxModelFile)){
  	  	$objResult['modelFile'] = $auxModelFile;//$conf['sparqlFilter']['prefix'].'/'.$v.'/'.$extensionModel.'queries';
  	  	if(file_exists($auxViewFile)){
  	  	  $objResult['viewFile'] = $auxViewFile;//$conf['sparqlFilter']['prefix'].'/'.$v.'/'.$extensionView.'template';
  	  	}elseif($extension != 'html'){ //View doesn't exists (and is not HTML)
  	  	  $objResult['viewFile'] = null;
  	  	}
  	  	return $objResult;
  	  }elseif(file_exists($conf['model']['directory'].'/'.$conf['sparqlFilter']['prefix'].'/'.$v.'/queries')){
  	  	$objResult['modelFile'] = $conf['model']['directory'].'/'.$conf['sparqlFilter']['prefix'].'/'.$v.'/queries';
  	  	if(file_exists($auxViewFile) ){
  	  	  $objResult['viewFile'] = $auxViewFile;
  	  	}else{
  	  	  $lodspk['transform_select_query'] = true;
  	  	  $objResult['viewFile'] = null;
  	  	}
  	  	trigger_error("LODSPeaKr can't find the proper query. Using HTML query instead.", E_USER_NOTICE);
  	  	break;
  	  }
  	}
  	return $objResult;
  }

}
?>
