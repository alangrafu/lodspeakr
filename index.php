<?
include_once('common.inc.php');

include_once('classes/Utils.php');
include_once('classes/Queries.php');
include_once('classes/Endpoint.php');


$endpoint = new Endpoint($conf['endpoint']['host'], $conf['endpoint']['config']);
$metaEndpoint = new Endpoint($conf['metaendpoint']['host'], $conf['metaendpoint']['config']);


$uri =  $conf['basedir'].$_GET['q'];
if($uri == $conf['basedir']){
  //TODO: A better frontpage
  echo "root";
  return;
}

$acceptContentType = Utils::getBestContentType($_SERVER['HTTP_ACCEPT']);
$extension = Utils::getExtension($acceptContentType); 
if($acceptContentType == NULL){
  Utils::send406($uri);
}
$pair = Queries::getMetadata($uri, $acceptContentType, $metaEndpoint);
if($pair == NULL){ // Original URI is not in metadata
  if(Queries::uriExist($uri, $endpoint)){
  	$page = Queries::createPage($uri, $acceptContentType, $metaEndpoint);
  	if($page == null){
  	  Utils::send500(null);
  	}
  	Utils::send303($page, $acceptContentType);
  }else{
  	Utils::send404($uri);
  }
}
list($res, $page, $format) = $pair;

if($page != NULL && $res == NULL){ 
  Utils::send303($page, $acceptContentType);
}

if($res != NULL && $page == NULL){ // Original URI is a page
  $uri = $res;
  $curieType = Utils::uri2curie(Queries::getClass($uri, $endpoint));
  
    
  /*Redefine Content type based on the
   * dcterms:format for this page
   */
  $acceptContentType = $format;
  $extension = Utils::getExtension($acceptContentType); 

  //Check if files for model and view exist
  $viewFile = $conf['view']['directory'].$curieType.$conf['view']['extension'].".".$extension;
  $modelFile = $conf['model']['directory'].$curieType.$conf['model']['extension'].".".$extension;
  if(!file_exists($modelFile) || !file_exists($viewFile) || $curieType == null){
  	$modelFile = $conf['model']['directory'].$conf['model']['default'].$conf['model']['extension'].".".$extension;
  	$viewFile = $conf['view']['directory'].$conf['view']['default'].$conf['view']['extension'].".".$extension;
  }
  $query = file_get_contents($modelFile);
  $query = preg_replace("|".$conf['resource']['url_delimiter']."|", "<".$uri.">", $query);
  
  header('Content-Type: '.$acceptContentType);
  if(preg_match("/describe/i", $query)){
  	$results = $endpoint->query($query, $conf['endpoint']['config']['describe']['output']);
  	require('lib/arc2/ARC2.php');
  	$parser = ARC2::getRDFParser();
  	$parser->parse($conf['basedir'], $results);
  	$triples = $parser->getTriples();
  	$ser;
  	switch ($acceptContentType){
  	case 'text/turtle':
  	  $ser = ARC2::getTurtleSerializer();
  	  break;
  	case 'text/plain':
  	  $ser = ARC2::getNTriplesSerializer();
  	  break;
  	default:
  	  $ser = ARC2::getRDFXMLSerializer();
  	  break;
  	}
  	$doc = $ser->getSerializedTriples($triples);
  	echo $doc;
  	exit(0);
  }
  elseif(preg_match("/select/i", $query)){
  	$results = $endpoint->query($query, $conf['endpoint']['config']['select']['output']);
  	if(sizeof($results['results']['bindings']) == 0){
  	  Utils::send404($uri);
  	}
  }
  Utils::showView($uri, $results, $viewFile);
  
  exit(0);
}

?>
