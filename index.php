<?
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
$curieType = Utils::uri2curie(Queries::getClass($uri, $endpoint));

/*Redefine Content type based on the
* dcterms:format for this page
*/
$acceptContentType = $format;
$extension = Utils::getExtension($acceptContentType); 

$viewFile = $conf['view']['directory'].$curieType.$conf['view']['extension'].".".$extension;

if(!file_exists($viewFile) ){
  $viewFile = $conf['view']['directory'].$conf['view']['default'].$conf['view']['extension'].".".$extension;
}

$modelFile = $conf['model']['directory'].$curieType.$conf['model']['extension'].".".$extension;

if(!file_exists($modelFile) || $curieType == null){
  $modelFile = $conf['model']['directory'].$conf['model']['default'].$conf['model']['extension'].".".$extension;
}

if(is_dir($modelFile)){
  $modelDir = $modelFile;
  if ($handle = opendir($modelDir)) {
    while (false !== ($file = readdir($handle))) {
      if($file != "." && $file != ".."){
      	$results[$file] = Queries::processQuery($file);
      }
    }    
  }else{
  	Utils::send500($uri);
  }
}else{
  $results = Queries::processQuery($modelFile, $uri, $endpoint);
}
Utils::showView($uri, $results, $viewFile);

exit(0);
//}

?>
