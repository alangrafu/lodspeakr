<?php

require_once('abstractModule.php');
class ExportModule extends abstractModule{
  private $serialization;
  private $graph;
  
  public function match($uri){
  	global $conf;
  	global $localUri;
  	$q = preg_replace('|^'.$conf['basedir'].'|', '', $localUri);
  	return $q == "export";
  }
  
  public function execute($service){
    global $conf;
  	$this->serialization = "";
  	$this->graph = array();
  	header('Content-Type: text/plain');
  	define("CNT", "http://www.w3.org/2011/content#");
  	define("NSVIZON", "http://graves.cl/vizon/");
  	define("LS", "http://lodspeakr.org/lda/");
  	define("LDA", "http://tw.rpi.edu/lda/");
  	define("DC", "http://purl.org/dc/terms/");
  	define("RDF", "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
  	define("RDFS", "http://www.w3.org/2000/01/rdf-schema#");
  	define("OPMV", "http://openprovenance.org/ontology#");
  	define("SKOS", "http://www.w3.org/2004/02/skos/core#");
  	require($conf['home'].'lib/arc2/ARC2.php');
  	$ser = ARC2::getTurtleSerializer();

  	$triples = array();
  	$t = array();
  	$t['s']      = $conf['basedir'];
  	$t['s_type'] = 'uri';
  	$t['p']      = RDF.'type';
  	$t['o']      = OPMV.'Agent';
  	$t['o_type'] = 'uri';  	 	   	
  	array_push($triples, $t);
  	$t['o']      = SKOS.'Concept';	
  	array_push($triples, $t);
  	$t['o']      = LS.'Application';	
  	array_push($triples, $t);
  	if($conf['parentApp'] != NULL){
  	 	$t['p'] = OPMV.'wasDerivedFrom';
  	 	$t['o'] = $conf['parentApp'];
  	 	array_push($triples, $t);
  	}
  	
  	$sparqlComponent = $conf['basedir'].'sparqlComponent';//uniqid("_:b");
  	$baseComponent = $conf['basedir'];//uniqid("_:b");
  	$components = $this->getComponents($conf['home'].$conf['view']['directory']."/".$conf['service']['prefix'], '');
  	//var_dump($components);exit(0);
  	//Define Process
  	$t = array();
  	foreach($components as $k=>$m){
  	  
  	  $process = uniqid("_:b");
  	  $t['s']      = $process;
  	  $t['s_type'] = 'bnode';
  	  $t['p']      = RDF.'type';
  	  $t['o']      = OPMV.'Process';
  	  $t['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t);    	
  	  
  	  
  	  //Controlled by
  	  $component = $baseComponent.$conf['service']['prefix']."/".$k;
  	  $t['p']      = OPMV.'wasControlledBy';
  	  $t['o']      = $component;
  	  $t['o_type'] = 'uri';  	   	  
  	  array_push($triples, $t);
  	  
  	  //Associated Agent to this installation
  	  $aux = $t['o'];
  	  $t['s'] = $t['o'];
  	  $t['p']      = RDF.'type';
  	  $t['o']      =OPMV.'Component';
  	  array_push($triples, $t);
  	  $t['p']      = SKOS.'broader';
  	  $t['o']      = $conf['basedir'];
  	  $t['o_type'] = 'uri';
  	  array_push($triples, $t);
  	  
  	  //$t['s']      = $process;
  	  //$t['s_type'] = 'bnode';  	  
  	  $visualPart=uniqid("_:b");
  	  $queryPart=uniqid("_:b");
  	  $t['p'] = SKOS.'broader';
  	  $t['s'] = $queryPart;
  	  $t['s_type'] = 'bnode';  	 	 
  	  $t['o'] = $component; 	 
  	  array_push($triples, $t);
  	  $t['s'] = $visualPart;
  	  array_push($triples, $t);
  	  
  	  foreach($m as $l => $v){
  	    if(strpos($l, "query")>-1){
  	      $t2['s'] = $queryPart;
  	      $t2['p']      = RDF.'type';
  	      $t2['o']      = LS.'LodspeakrDataComponent';
  	      $t2['o_type'] = 'uri';  	 	 
  	      
  	    }else{
  	      $t2['s'] = $visualPart;
  	      $t2['p']      = RDF.'type';
  	      $t2['o']      = LS.'LodspeakrVisualComponent';
  	      $t2['o_type'] = 'uri';  	 	   	      
  	    }
  	    array_push($triples, $t2);
  	    $t2['p']      = NSVIZON.'hasInput';
  	    $t2['o']      = $baseComponent.$conf['service']['prefix']."/".$k."/".$l;
  	    $t2['o_type'] = 'uri';  	 	   	      
  	    
  	    array_push($triples, $t2);
  	    $t2['s'] = $t2['o'];
  	    $t2['p'] = DC."hasFormat";
  	    $t2['o'] = uniqid("_:b");
  	    $t2['o_type'] = 'bnode';
  	    array_push($triples, $t2);
  	    $t2['s'] = $t2['o'];
  	    $t2['s_type'] = $t2['o_type'];
  	    $t2['p'] = RDF.'type';
  	    $t2['o'] = NSVIZON.'Component';
  	    $t2['o_type'] = 'uri';
  	    array_push($triples, $t2);
  	    $t2['s_type'] = $t2['o_type'];
  	    $t2['p'] = DC.'format';
  	    $t2['o'] = 'text/plain;charset=utf-8';
  	    $t2['o_type'] = 'literal';
  	    array_push($triples, $t2);
  	    $t2['p'] = CNT.'ContentAsText';
  	    $t2['o'] = $v;
  	    array_push($triples, $t2);  	  }
  	  //Return object for later triple
  	  //$t['o'] = $baseComponent;
  	  /*
  	  // Type of query
  	  $t2 = array();
  	  $t2['s']      = $t['o'];
  	  $t2['s_type'] = 'bnode';
  	  $t2['p']      = RDF.'type';
  	  $t2['o']      = LS.'LodspeakrVisualComponent';
  	  $t2['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t2);
  	  
  	  $t3 = array();
  	  $t3['s']      = $t2['o'];
  	  $t3['s_type'] = 'uri';
  	  $t3['p']      = RDFS.'subClassOf';
  	  $t3['o']      = LDA."VisualComponent";
  	  $t3['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t3);
  	  
  	  $t2['p']      = RDFS.'label';
  	  $t2['o']      = 'Haanga-based visualization component for LODSPeaKr';
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  
  	  $t['p']      = LS.'usedInput';
  	  $t['o']      = $conf['basedir'].$conf['view']['directory'].$k;
  	  $t['o_type'] = 'uri';  	   	  
  	  array_push($triples, $t);
  	  
  	  
  	  $t2 = array();
  	  $t2['s']      = $t['o'];
  	  $t2['s_type'] = 'uri';
  	  $t2['p']      = RDF.'type';
  	  $t2['o']      = LS."Input";
  	  $t2['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t2);
  	  $t2['p']      = RDFS.'label';
  	  $t2['o']      = $conf['view']['directory'].$k;
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  $t2['p']      = DC.'hasFormat';
  	  $t2['o']      = uniqid("_:b");
  	  $t2['o_type'] = 'bnode';  	 	 
  	  array_push($triples, $t2);
  	  
  	  $t2['s']      = $t2['o'];
  	  $t2['s_type'] = 'bnode';
  	  $t2['p']      = RDF.'type';
  	  $t2['o']      = CNT."ContentAsText";
  	  $t2['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t2);
  	  $t2['p']      = CNT.'chars';
  	  $t2['o']      = ($m);
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  $t2['p']      = DC.'format';
  	  $t2['o']      = 'text/plain;charset=utf-8';
  	  $t2['o_type'] = 'literal';  	 	 */
  	//  array_push($triples, $t2);
  	  //break;
  	}
  	
  	//Static files  	
  	
  	$staticComponent = $conf['basedir'].'staticComponent';//uniqid("_:b");
  	//$statics = $this->getComponents($conf['home'].$conf['static']['directory'], '');
  	
  	//Define Process
  	$t = array();
  	$t['s']      = uniqid("_:b");
  	$t['s_type'] = 'bnode';
  	$t['p']      = RDF.'type';
  	$t['o']      = OPMV.'Process';
  	$t['o_type'] = 'uri';  	 	 
  	array_push($triples, $t);    	
  	foreach($statics as $k=>$m){
  	  
  	  
  	  
  	  //Controlled by
  	  $t['p']      = OPMV.'wasControlledBy';
  	  $t['o']      = $staticComponent;
  	  $t['o_type'] = 'bnode';  	   	  
  	  array_push($triples, $t);
  	  
  	  //Associated Agent to this installation
  	  $aux = $t['o'];
  	  $t['s'] = $t['o'];
  	  $t['p']      = SKOS.'broader';
  	  $t['o']      = $conf['basedir'];
  	  $t['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t);
  	  
  	  //Return object for later triple
  	  $t['o'] = $staticComponent;
  	  
  	  // Type of query
  	  $t2 = array();
  	  $t2['s']      = $t['o'];
  	  $t2['s_type'] = 'bnode';
  	  $t2['p']      = RDF.'type';
  	  $t2['o']      = LS.'LodspeakrStaticElementsComponent';
  	  $t2['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t2);
  	  
  	  $t3 = array();
  	  $t3['s']      = $t2['o'];
  	  $t3['s_type'] = 'uri';
  	  $t3['p']      = RDFS.'subClassOf';
  	  $t3['o']      = LDA."ProcessComponent";
  	  $t3['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t3);
  	  
  	  array_push($triples, $t2);
  	  $t2['p']      = RDFS.'label';
  	  $t2['o']      = 'Component of LODSPeaKr in charge of static content';
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  
  	  $t['p']      = LS.'usedInput';
  	  $t['o']      = $conf['basedir'].$conf['static']['directory'].$k;
  	  $t['o_type'] = 'uri';  	   	  
  	  array_push($triples, $t);
  	  
  	  $t2 = array();
  	  $t2['s']      = $t['o'];
  	  $t2['s_type'] = 'uri';
  	  $t2['p']      = RDF.'type';
  	  $t2['o']      = LS."Input";
  	  $t2['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t2);
  	  $t2['p']      = RDFS.'label';
  	  $t2['o']      = $conf['static']['directory'].$k;
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  
  	  $t2['p']      = DC.'hasFormat';
  	  $t2['o']      = uniqid("_:b");
  	  $t2['o_type'] = 'bnode';  	 	 
  	  array_push($triples, $t2);
  	  
  	  $t2['s']      = $t2['o'];
  	  $t2['s_type'] = 'bnode';
  	  $t2['p']      = RDF.'type';
  	  $t2['o']      = CNT."ContentAsText";
  	  $t2['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t2);
  	  $t2['p']      = CNT.'chars';
  	  $t2['o']      = ($m);
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  $t2['p']      = DC.'format';
  	  $t2['o']      = 'text/plain;charset=utf-8';
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  //break;
  	}
  	
  	
  	echo "#You can copy and paste the following data into a new\n";
  	echo "#LODSPeaKr instance at http://exampleofinstance.org/import\n";
  	echo "#As a side note: this is a turtle document but is served as text/plain to make it easier to copy&paste\n\n\n";
  	echo $ser->getSerializedTriples($triples);
  }
  
  private static function getTriple($s, $p, $o){
  }
  
  private function createParameter($name, $value){
  	$s = uniqid("_:b");
  	$s_type = 'bnode';
  	$p = RDF."type";
  	$o = NSVIZON."Parameter";
  	$o_type = 'uri';
  	
  	
  }
  
  private function getViews($dir){
  	global $conf;
  	$files = "";
  	chdir($dir);
  	$handle = opendir('.');
  	while (false !== ($viewFile = readdir($handle))) {
  	  if($viewFile != "." && $viewFile != ".."){
  	  	$files .= $viewFile.": ".htmlspecialchars(file_get_contents($viewFile))."\n";
  	  	$t = array();
  	  }
  	}
  	chdir("..");
  	return $files;
  }
  
  private function getComponents($dir){
    global $conf;
  	$list = array();
  	$components = array();
  	chdir($dir);
  	$handle = opendir('.');
  	while (false !== ($componentDir = readdir($handle))) {
  	  if($componentDir != "." && $componentDir != ".."){
  	    if(is_dir($componentDir)){
  	      $list[] = $componentDir;
  	    }
  	  }
  	}
  	closedir($handle);
  	foreach($list as $v){
  	  $components[$v] = $this::getComponentsFiles($v);
  	}
  	return $components;
  }
  
  
  private function getComponentsFiles($dir, $prefix){
  	global $conf;
  	$files = array();
  	$subDirs = array();
  	$currentDir = getcwd();
  	chdir($dir);
  	$handle = opendir('.');
  	while (false !== ($modelFile = readdir($handle))) {
  	  if($modelFile != "." && $modelFile != ".."){
  	  	if(is_dir($modelFile)){
  	  	  //Save it for later, after all the queries in the current directory has been resolved
  	  	  $subDirs[]=$modelFile;
  	  	}else{
  	  	  $files[$prefix.$modelFile] = (file_get_contents($modelFile))."\n";
  	  	}
  	  }
  	}
  	
    foreach($subDirs as $dir){      
      //$files[$dir] = array();
      $files = array_merge($files, $this->getComponentsFiles($dir, $prefix.$dir."/")); 
    }
  	chdir($currentDir);
  	return $files;
  }
}

?>
