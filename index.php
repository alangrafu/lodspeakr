<?

//error_reporting(E_ERROR);

if(!file_exists('settings.inc.php')){
  echo 'Need to configure lodspeakr first. Please run "install.sh" first';
  exit(0);
}
include_once('common.inc.php');

include_once('classes/Utils.php');
include_once('classes/Queries.php');
include_once('classes/Endpoint.php');
include_once('classes/MetaDb.php');


$endpoint = new Endpoint($conf['endpoint']['host'], $conf['endpoint']['config']);
$metaDb = new MetaDb($conf['metadata']['db']['location']);

$acceptContentType = Utils::getBestContentType($_SERVER['HTTP_ACCEPT']);
$extension = Utils::getExtension($acceptContentType); 

if($acceptContentType == NULL){
  Utils::send406($uri);
}

$uri = $conf['basedir'].$_GET['q'];
if($uri == $conf['basedir']){
  header('Location: '.$conf['root']['url']);
  exit(0);
}elseif(preg_match("|^".$conf['basedir'].$conf['special']['uri']."|", $uri)){
  include_once($conf['special']['class']);
  $context = array();
  $context['contentType'] = $acceptContentType;
  $context['endpoint'] = $endpoint;
  $sp = new SpecialFunction();
  $sp->execute($uri, $context);
  exit(0);
}

$pair = Queries::getMetadata($uri, $acceptContentType, $metaDb);

if($pair == NULL){ // Original URI is not in metadata
  if(Queries::uriExist($uri, $endpoint)){
  	$page = Queries::createPage($uri, $acceptContentType, $metaDb);
  	if($page == NULL){
  	  Utils::send500(NULL);
  	}
  	Utils::send303($page, $acceptContentType);
  }else{
  	Utils::send404($uri);
  }
}
list($res, $page, $format) = $pair;

//If resource is not the page, send a 303 to the document
if($res == $uri){
  Utils::send303($page, $acceptContentType);
}

$uri = $res;
$extension = Utils::getExtension($format); 

/*Redefine Content type based on the
* dcterms:format for this page
*/
$acceptContentType = $format;

//Check if files for model and view exist
$curieType = Utils::uri2curie(Queries::getClass($uri, $endpoint));
$viewFile = $conf['view']['directory'].$curieType.$conf['view']['extension'].".".$extension;
$modelFile = $conf['model']['directory'].$curieType.$conf['model']['extension'].".".$extension;
if(!file_exists($modelFile) || !file_exists($viewFile) || $curieType == null){
  $modelFile = $conf['model']['directory'].$conf['model']['default'].$conf['model']['extension'].".".$extension;
  $viewFile = $conf['view']['directory'].$conf['view']['default'].$conf['view']['extension'].".".$extension;
}

if(!is_dir($modelFile)){
  $query = file_get_contents($modelFile);
  $query = preg_replace("|".$conf['resource']['url_delimiter']."|", "<".$uri.">", $query);
  $data['results'] = $endpoint->query($query, Utils::getResultsType($query));
  $data['query'] = $query;
}else{
  $modelDir = $modelFile;
  $handle = opendir($modelDir);
  while (false !== ($modelFile = readdir($handle))) {
  	if($modelFile != "." && $modelFile != ".."){
  	  $query = file_get_contents($modelDir."/".$modelFile);
  	  $query = preg_replace("|".$conf['resource']['url_delimiter']."|", "<".$uri.">", $query);
  	  $data[$modelFile]['results'] = $endpoint->query($query, Utils::getResultsType($query));
  	  $data[$modelFile]['query'] = $query;
  	}
  }
  closedir($handle);
}

Utils::processDocument($uri, $acceptContentType, $data, $viewFile);
//}

?>
