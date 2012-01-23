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
  echo 'Need to configure lodspeakr first. Please run "install.sh" first. Alternatively, you can <a href="import">import an existing application</a>';
  exit(0);
}

include_once('common.inc.php');

//Debug output
if($conf['debug']){
  error_reporting(E_ALL);
}else{
  error_reporting(E_ERROR);
}



include_once('classes/Utils.php');
include_once('classes/Queries.php');
include_once('classes/Endpoint.php');
include_once('classes/Convert.php');
$results = array();
$first = array();
$endpoints = array();
$endpoints['local'] = new Endpoint($conf['endpoint']['local'], $conf['endpointParams']['config']);

$acceptContentType = Utils::getBestContentType($_SERVER['HTTP_ACCEPT']);
$extension = Utils::getExtension($acceptContentType); 

if($acceptContentType == NULL){
  Utils::send406($uri);
}
if(sizeof($_GET['q'])>0 && file_exists($conf['static']['directory'].$_GET['q'])){
  echo file_get_contents($conf['static']['directory'].$_GET['q']);
  exit(0);
}
if($conf['export'] && $_GET['q'] == 'export'){
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
}

if($conf['mirror_external_uris']){
  $uri = $conf['ns']['local'].$_GET['q'];
  $localUri = $conf['basedir'].$_GET['q'];
}

foreach($conf['modules']['available'] as $i){
  $className = $i.'Module';
  $currentModule = $conf['modules']['directory'].$className.'.php';
  if(!is_file($currentModule)){
  	Utils::send500("<br/>Can't load or error in module <tt>".$currentModule."</tt>" );
  	exit(1);
  }
  require_once($currentModule);
  $module = new $className();
  $matching = $module->match($uri) ;
  if($matching != FALSE){
  	$module->execute($matching);
  	exit(0);
  }
}

//Service module
if(preg_match("|^".$conf['basedir'].$conf['special']['uri']."|", $uri)){
  include_once($conf['special']['class']);
  $context = array();
  $context['contentType'] = $acceptContentType;
  $context['endpoints'] = $endpoints;
  $sp = new SpecialFunction();
  $sp->execute($uri, $context);
  exit(0);
}
//End of Service module





Utils::send404($uri);
//end of Class module
?>
