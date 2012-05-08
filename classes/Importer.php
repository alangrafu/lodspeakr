<?
define("LS", "http://lodspeakr.org/lda/");
define("SKOS", "http://www.w3.org/2004/02/skos/core#");
define("RDF", "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
define("DC", "http://purl.org/dc/terms/");
define("CNT", "http://www.w3.org/2011/content#");
define("RDFS", "http://www.w3.org/2000/01/rdf-schema#");
define("FILE", "settings.inc.php");

class Importer{
  
  private $basedir;
  private $external_basedir;
  public function run(){
  	set_time_limit(0);
  	error_reporting(E_ERROR);
  	if(is_file(FILE)){
  	  echo "There is an existing ".FILE." file on this installation. Please remove it before importing a new one";
  	  exit(0);
  	}
  	if(!isset($_GET['import']) && !isset($_POST['importtext'])){
  	  $this->showInterface();
  	  exit(0);
  	}
  	if(!is_writable('.')){
  	  echo 'The webserver needs write permissions in "lodspeakr/" "lodspeakr/models/" and "lodspeakr/views/" dirs to import settings.';
  	  exit(0);
  	}	  
  	
  	echo $this->external_basedir;
  	include_once('lib/arc2/ARC2.php');
  	$parser = ARC2::getTurtleParser();
  	
  	if(isset($_GET['import'])){
  	  $parser->parse($_GET['import']);
  	  $this->external_basedir = str_replace('export', '', $_GET['import']);
  	}elseif(isset($_POST['importtext'])){
  	  $parser->parse(RDF, $_POST['importtext']);
  	}else{
  	  HTTPStatus::send500();
  	  exit(0);
  	}
  	$triples = $parser->getTriples();
  	
  	$appArr = $this->search($triples, null, RDF.'type', LS.'Application');
  	if(!(sizeof($appArr) > 0)){
  	  echo "I can't find an application from the URL given";
  	  exit(0);
  	}
  	
  	$app = $appArr[0]['s'];
  	$this->external_basedir = $app;
  	$compArr = $this->search($triples, null, SKOS.'broader', $app);
  	$content = "<?\n\$conf['debug'] = false;\n\$conf['mirror_external_uris'] = true;\n\n";
  	
	$this->basedir =  preg_replace('/import$/', '', (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	
	//$arr = explode("lodspeakr/benegesserit", $this->basedir);
	//$this->basedir = $arr[0];
	$content .= "\$conf['basedir'] = \"$this->basedir\";\n";
	$content .= "\$conf['parentApp'] = \"$app\";\n";
	$pwd = getcwd();
	$content .= "\$conf['home'] = \"$pwd/\";\n";
	
	//App params
	
	$q = $this->search($triples, $app, LS.'usedParameter', null);
	$appParams =  array();
	foreach($q as $p){
	  $param = $p['o'];
	  $labelArr = $this->search($triples, $param, RDFS.'label', null);
	  $label = $labelArr[0]['o'];
	  $format = $this->search($triples, $param, DC.'hasFormat', null);
	  $cntArr = $this->search($triples, $format[0]['o'], CNT.'chars', null);
	  $cnt = $cntArr[0]['o'];
	  $appParams[$label] = $cnt;  
	}
	foreach($appParams  as $k => $v){
	  $content .= "\$conf['$k'] = \"$v\";\n";
	}
	$content .= "/*ATTENTION: By default this application is available to
 * be exported and copied (its configuration)
 * by others. If you do not want that, 
 * turn the next option as false
 */ 
\$conf['export'] = true;\n\n";
	//Components
  	foreach($compArr as $v){
  	  $component = $v['s'];
  	  $componentTypeArr = $this->search($triples, $component, RDF.'type', null);
  	  $compType = $componentTypeArr[0]['o'];
  	  
  	  $params = array();
  	  $q = $this->search($triples, $component, LS.'usedParameter', null);
  	  foreach($q as $p){
  	  	$param = $p['o'];
  	  	$labelArr = $this->search($triples, $param, RDFS.'label', null);
  	  	$label = $labelArr[0]['o'];
  	  	$format = $this->search($triples, $param, DC.'hasFormat', null);
  	  	$cntArr = $this->search($triples, $format[0]['o'], CNT.'chars', null);
  	  	$cnt = $cntArr[0]['o'];
  	  	$params[$label] = $cnt;  
  	  }
  	  
  	  $inputs = array();
  	  $q = $this->search($triples, $component, LS.'usedInput', null);
  	  foreach($q as $p){
  	  	$param = $p['o'];
  	  	$labelArr = $this->search($triples, $param, RDFS.'label', null);
  	  	if(sizeof($labelArr)>0){
  	  	  $label = $labelArr[0]['o'];  	  	
  	  	  $format = $this->search($triples, $param, DC.'hasFormat', null);
  	  	  $cntArr = $this->search($triples, $format[0]['o'], CNT.'chars', null);
  	  	  $cnt = $cntArr[0]['o'];
  	  	  $inputs[$label] = $cnt; 
  	  	}
  	  }
  	  if($compType == LS."LodspeakrEndpointManagerComponent"){
 	 	$content .= $this->createEndpoints($params);
 	  }elseif($compType == LS."LodspeakrNamespaceManagerComponent"){
 	 	$content .= $this->createNamespaces($params); 	  
 	  }elseif($compType == LS."LodspeakrSparqlEndpointRetriever"){
 	  	$this->createModels($inputs);
 	  }elseif($compType == LS."LodspeakrStaticElementsComponent"){
 	  	$this->createStatics($inputs);
 	  }elseif($compType == LS."LodspeakrVisualComponent"){
 	  	$this->createViews($inputs);
 	  }else{
 	  	trigger_error("Component '$component' (of type $compType) not supported", E_USER_WARNING);
 	  }
 	}
 	$content .= "?>\n";
 	try{
 	  $fh = fopen(FILE, 'a');
  	  fwrite($fh, $content);
  	  fclose($fh);
  	} catch (Exception $e) {
  	  echo 'Caught exception while writing settings: ',  $e->getMessage(), "\n";
  	  exit(1);
  	}
  	$this->showFinishing();
  }
  
  private function createEndpoints($ep){
  	require('common.inc.php');
  	$endpoints = "";
  	try{
  	  foreach($ep as $k => $v){
  	  	if($conf['endpoint'][$k] != $v){
  	  	  $endpoints .= "\$conf[\"endpoint\"][\"$k\"] = \"$v\";\n";
  	  	}
  	  }
  	  $endpoints .= "\n\n";  	 
  	} catch (Exception $e) {
  	  echo 'Caught exception while importing endpoints: ',  $e->getMessage(), "\n";
  	  exit(1);
  	}	  
  	return $endpoints;
  }
  
  private function createNamespaces($ns){
  	require('namespaces.php');
  	$namespaces = "";
  	try{
  	  foreach($ns as $k => $v){
  	  	if($conf["ns"][$k] != $v){
  	  	  if(preg_match("|^".$this->external_basedir."|", $v)){
  	  	  	$newns = preg_replace("|^".$this->external_basedir."|", $this->basedir, $v);
  	  	  	$namespaces .= "\$conf[\"ns\"][\"".$k."_ext\"] = \"$newns\";\n";
  	  	  }
  	  	  $namespaces .= "\$conf[\"ns\"][\"$k\"] = \"$v\";\n";
  	  	}
  	  }
  	  $namespaces .= "\$conf[\"ns\"][\"basedir\"] = \"$this->basedir\";\n";
  	  $namespaces .= "\n\n";
  	} catch (Exception $e) {
  	  echo 'Caught exception while importing namespaces: ',  $e->getMessage(), "\n";
  	  exit(1);
  	}
  	return $namespaces;
  }
  
  private function createModels($models){
  	try{
  	  foreach($models as $k => $v){
  	  	$path = explode("/", $k);
  	  	for($i=0; $i<sizeof($path)-1; $i++){
  	  	  if(file_exists($path[$i])){
  	  	  	if(!is_dir($path[$i])){
  	  	  	  unlink($path[$i]);
  	  	  	  mkdir($path[$i]);
  	  	  	}
  	  	  }else{
  	  	  	mkdir($path[$i]);
  	  	  }
  	  	  chdir($path[$i]);
  	  	}
  	  	
  	  	$fh = fopen(end($path), 'w');
  	  	fwrite($fh, $v);
  	  	fclose($fh);
  	  	for($i=0; $i<sizeof($path)-1; $i++){
  	  	  chdir('..');
  	  	}
  	  }
  	} catch (Exception $e) {
  	  echo 'Caught exception while importing models: ',  $e->getMessage(), "\n";
  	  exit(1);
  	}
  }
  
  
  private function createViews($views){
  	try{
  	  foreach($views as $k => $v){
  	  	$path = explode("/", $k);
  	  	for($i=0; $i<sizeof($path)-1; $i++){
  	  	  if(file_exists($path[$i])){
  	  	  	if(!is_dir($path[$i])){
  	  	  	  unlink($path[$i]);
  	  	  	  mkdir($path[$i]);
  	  	  	}
  	  	  }else{
  	  	  	mkdir($path[$i]);
  	  	  }
  	  	  chdir($path[$i]);
  	  	}
  	  	
  	  	$fh = fopen(end($path), 'w');
  	  	fwrite($fh, $v);
  	  	fclose($fh);
  	  	for($i=0; $i<sizeof($path)-1; $i++){
  	  	  chdir('..');
  	  	}
  	  }
  	} catch (Exception $e) {
  	  echo 'Caught exception while importing views: ',  $e->getMessage(), "\n";
  	  exit(1);
  	}	  
  }
  
  
    private function createStatics($statics){
  	try{
  	  foreach($statics as $k => $v){
  	  	$path = explode("/", $k);
  	  	for($i=0; $i<sizeof($path)-1; $i++){
  	  	  if(file_exists($path[$i])){
  	  	  	if(!is_dir($path[$i])){
  	  	  	  unlink($path[$i]);
  	  	  	  mkdir($path[$i]);
  	  	  	}
  	  	  }else{
  	  	  	mkdir($path[$i]);
  	  	  }
  	  	  chdir($path[$i]);
  	  	}
  	  	
  	  	$fh = fopen(end($path), 'w');
  	  	fwrite($fh, $v);
  	  	fclose($fh);
  	  	for($i=0; $i<sizeof($path)-1; $i++){
  	  	  chdir('..');
  	  	}
  	  }
  	} catch (Exception $e) {
  	  echo 'Caught exception while importing statics: ',  $e->getMessage(), "\n";
  	  exit(1);
  	}	  
  }
  
  
  private function search($graph, $s = null, $p = null, $o = null){
  	$results =  array();
  	foreach($graph as $v){
  	  $threeOks = 0;
  	  
  	  //Check subject
  	  if($s != null){
  	  	if($v['s'] == $s){
  	  	  $threeOks++;
  	  	}
  	  }else{
  	  	$threeOks++;
  	  }
  	  
  	  //Check predicate
  	  if($p != null){
  	  	if($v['p'] == $p){
  	  	  $threeOks++;
  	  	}
  	  }else{
  	  	$threeOks++;
  	  }
  	  
  	  //Check object
  	  if($o != null){
  	  	if($v['o'] == $o){
  	  	  $threeOks++;
  	  	}
  	  }else{
  	  	$threeOks++;
  	  }
  	  
  	  if($threeOks == 3){
  	  	array_push($results, $v);
  	  }
  	  
  	}
  	return $results;
  	//$this->showFinishing();
  }
  
  private function showInterface(){
  	$doc = "<html>
  	<head>
  	<title>Importing options</title>
  	</head>
  	<body>
  	<h2>Paste application described in LDA</h2>
  	You can paste the data obtained from another LODSPeaKr instance here in the box.
  	You can also automatize this import by adding a parameter '?import=URL' to this page.
  	Usually, the URL will be of the for <tt>http://example.org/foo/export</tt>
  	<form action='import' method='post'>
  	<textarea cols='100' rows='25' name='importtext'></textarea>
  	<input type='submit' value='Import'/>
  	</form>
  	</body>
  	</html>";
  	echo $doc;  	  	
  }
  
  private function showFinishing(){
  	$doc = "<html>
  	<head>
  	<title>Finishing import</title>
  	</head>
  	<body>
  	<h2>Import finished</h2>
  	Your new application is ready. Please go to the <a href='".$this->basedir."'>home page</a>.
  	</body>
  	</html>";
  	echo $doc;  	  	
  }
  
}

?>
