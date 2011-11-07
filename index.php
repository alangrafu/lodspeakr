<?

if($_GET['q'] == 'import'){
  include_once('classes/Importer.php');
  $imp = new Importer();
  $imp->run();
  exit(0);
}

include_once('common.inc.php');
if($conf['debug']){
  error_reporting(E_ALL);
}else{
  error_reporting(E_ERROR);
}
if(!file_exists('settings.inc.php')){
  echo 'Need to configure lodspeakr first. Please run "install.sh" first';
  exit(0);
}


include_once('classes/Utils.php');
include_once('classes/Queries.php');
include_once('classes/Endpoint.php');
include_once('classes/MetaDb.php');
include_once('classes/Convert.php');

$results = array();
$endpoints = array();
$endpoints['local'] = new Endpoint($conf['endpoint']['local'], $conf['endpointParams']['config']);
$metaDb = new MetaDb($conf['metadata']['db']['location']);

$acceptContentType = Utils::getBestContentType($_SERVER['HTTP_ACCEPT']);
$extension = Utils::getExtension($acceptContentType); 

if($acceptContentType == NULL){
  Utils::send406($uri);
}
if(file_exists($conf['static']['directory'].$_GET['q']) && sizeof($_GET['q'])>0){
  echo file_get_contents($conf['static']['directory'].$_GET['q']);
  exit(0);
}
if($_GET['q'] == 'export'){
  include_once('settings.inc.php');
  include_once('classes/Exporter.php');
  $exp = new Exporter();
  header('Content-Type: text/plain');
  $exp->run();
  exit(0);
}


$uri = $conf['basedir'].$_GET['q'];
$localUri = $uri;
if($uri == $conf['basedir']){
  header('Location: '.$conf['root']);
  exit(0);
}elseif(preg_match("|^".$conf['basedir'].$conf['special']['uri']."|", $uri)){
  include_once($conf['special']['class']);
  $context = array();
  $context['contentType'] = $acceptContentType;
  $context['endpoints'] = $endpoints;
  $sp = new SpecialFunction();
  $sp->execute($uri, $context);
  exit(0);
}
if($conf['use_external_uris']){
  $uri = $conf['ns']['local'].$_GET['q'];
  $localUri = $conf['basedir'].$_GET['q'];
} 

$pair = Queries::getMetadata($localUri, $acceptContentType, $metaDb);

if($pair == NULL){ // Original URI is not in metadata
  if(Queries::uriExist($uri, $endpoints['local'])){
  	$page = Queries::createPage($uri, $localUri, $acceptContentType, $metaDb);
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
if($res == $localUri){
  Utils::send303($page, $acceptContentType);
}

$uri = $res;
$extension = Utils::getExtension($format); 

/*Redefine Content type based on the
* dcterms:format for this page
*/
$acceptContentType = $format;

//Check if files for model and view exist
$t=Queries::getClass($uri, $endpoints['local']);

//Defining default views and models
$curieType="";
$modelFile = $conf['model']['default'].$conf['model']['extension'].".".$extension;
$viewFile = $conf['view']['default'].$conf['view']['extension'].".".$extension;

//Get the first class available
/* TODO: Allow user to priotize 
 * which class should be used
 * Example: URI is foaf:Person and ex:Student
 *          If both, prefer ex:Student
 */

foreach($t as $v){
  $curieType = Utils::uri2curie($v);
  $auxViewFile  = $conf['view']['directory'].$curieType.$conf['view']['extension'].".".$extension;
  $auxModelFile = $conf['model']['directory'].$curieType.$conf['model']['extension'].".".$extension;
  if(file_exists($auxModelFile) && file_exists($auxViewFile) && $curieType != null){
  	$viewFile = $curieType.$conf['view']['extension'].".".$extension;
  	$modelFile = $curieType.$conf['model']['extension'].".".$extension;
  	break;
  }
}
$base = $conf['view']['standard'];
$base['type'] = $modelFile;
$base['this']['value'] = $uri;
$base['this']['curie'] = Utils::uri2curie($uri);
$base['this']['contentType'] = $acceptContentType;
$base['model']['directory'] = $conf['model']['directory'];
$base['view']['directory'] = $conf['view']['directory'];
$base['ns'] = $conf['ns'];


chdir($conf['model']['directory']);

Utils::queryFile($modelFile, $endpoints['local'], $results);

chdir("..");
if(is_array($results)){
  $resultsObj = Convert::array_to_object($results);
}else{
  $resultsObj = $results;
}
Utils::processDocument($viewFile, $base, $resultsObj);
//}

?>
