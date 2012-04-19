<?
//Import
if($_GET['q'] == 'import'){
  include_once('classes/Importer.php');
  $imp = new Importer();
  $imp->run();
  exit(0);
}

//Test if LODSPeaKr is configured
if(!file_exists('settings.inc.php')){
  echo 'Need to configure lodspeakr firstResults. Please run "install.sh" firstResults. Alternatively, you can <a href="import">import an existing application</a>';
  exit(0);
}

include_once('common.inc.php');

//Debug output
if($conf['debug']){
  error_reporting(E_ALL);
}else{
  error_reporting(E_ERROR);
}

include_once('classes/HTTPStatus.php');
include_once('classes/Utils.php');
include_once('classes/Queries.php');
include_once('classes/Endpoint.php');
include_once('classes/Convert.php');
$results = array();
$firstResults = array();
$endpoints = array();
$endpoints['local'] = new Endpoint($conf['endpoint']['local'], $conf['endpointParams']['config']);

$acceptContentType = Utils::getBestContentType($_SERVER['HTTP_ACCEPT']);
$extension = Utils::getExtension($acceptContentType); 


//Check content type is supported by LODSPeaKr
if($acceptContentType == NULL){
  HTTPStatus::send406($uri);
}

//Export
if($conf['export'] && $_GET['q'] == 'export'){
  include_once('settings.inc.php');
  include_once('classes/Exporter.php');
  $exp = new Exporter();
  header('Content-Type: text/plain');
  $exp->run();
  exit(0);
}

//Redirect to root URL if necessary
$uri = $conf['basedir'].$_GET['q'];
$localUri = $uri;
if($uri == $conf['basedir']){
  header('Location: '.$conf['root']);
  exit(0);
}

//Configure external URIs if necessary
if(isset($conf['mirror_external_uris']) && $conf['mirror_external_uris'] != false){
  $localUri = $conf['basedir'].$_GET['q'];
  
  if(is_bool($conf['mirror_external_uris'])){
  	$uri = $conf['ns']['local'].$_GET['q'];
  }elseif(is_string($conf['mirror_external_uris'])){
  	$uri = $conf['mirror_external_uris'].$_GET['q'];
  }else{
  	HTTPStatus::send500("Error in mirroring configuration");
  	exit(1);
  }
  
}


//Modules
foreach($conf['modules']['available'] as $i){
  $className = $i.'Module';
  $currentModule = $conf['modules']['directory'].$className.'.php';
  if(!is_file($currentModule)){
  	HTTPStatus::send500("<br/>Can't load or error in module <tt>".$currentModule."</tt>" );
  	exit(1);
  }
  require_once($currentModule);
  $module = new $className();
  $matching = $module->match($uri);
  if($matching != FALSE){
  	$module->execute($matching);
  	exit(0);
  }
}

HTTPStatus::send404($uri);
?>
