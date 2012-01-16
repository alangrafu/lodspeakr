<?

class Exporter{
  private $serialization;
  private $graph;
  public function __construct(){
  	$this->serialization = "";
  	$this->graph = array();
  	define("CNT", "http://www.w3.org/2011/content#");
  	define("NSVIZON", "http://graves.cl/vizon/");
  	define("LS", "http://lodspeakr.org/lda/");
  	define("LDA", "http://tw.rpi.edu/lda/");
  	define("DC", "http://purl.org/dc/terms/");
  	define("RDF", "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
  	define("RDFS", "http://www.w3.org/2000/01/rdf-schema#");
  	define("OPMV", "http://openprovenance.org/ontology#");
  	define("SKOS", "http://www.w3.org/2004/02/skos/core#");
  }
  
  public function run(){
  	global $conf;
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
  	
  	$t['p']      = LS.'usedParameter';
  	$t['o']      = uniqid("_:b");
  	$t['o_type'] = 'bnode';  	   	  
  	array_push($triples, $t);
  	
  	$t2 = array();
  	$t2['s']      = $t['o'];
  	$t2['s_type'] = 'uri';
  	$t2['p']      = RDF.'type';
  	$t2['o']      = LS."Parameter";
  	$t2['o_type'] = 'uri';  	 	 
  	array_push($triples, $t2);
  	$t2['p']      = RDFS.'label';
  	$t2['o']      = 'root';
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
  	$t2['o']      = ($conf['root']);
  	$t2['o_type'] = 'literal';  	 	 
  	array_push($triples, $t2);
  	$t2['p']      = DC.'format';
  	$t2['o']      = 'text/plain;charset=utf-8';
  	$t2['o_type'] = 'literal';  	 	 
  	array_push($triples, $t2);
  	
  	
  	$t['s']      = $conf['basedir'].'endpointManagerComponent';
  	$t['s_type'] = 'uri';
  	$t['p']      = SKOS.'broader';
  	$t['o']      = $conf['basedir'];
  	$t['o_type'] = 'uri';  	 	   	
  	array_push($triples, $t);
  	$t['p']      = RDF.'type';
  	$t['o']      = LS.'LodspeakrEndpointManagerComponent';
  	$t['o_type'] = 'uri';  	 	   	
  	array_push($triples, $t);
  	
  	$t2 = array();
  	$t2['s']      = $t['o'];
  	$t2['s_type'] = 'uri';
  	$t2['p']      = RDFS.'subClassOf';
  	$t2['o']      = LDA."SparqlEndpointRetriever";
  	$t2['o_type'] = 'uri';  	 	 
  	array_push($triples, $t2);
  	
  	//Endpoints
  	foreach($conf['endpoint'] as $k => $v){	
  	  $t['p']      = LS.'usedParameter';
  	  $t['o']      = uniqid("_:b");
  	  $t['o_type'] = 'bnode';  	   	  
  	  array_push($triples, $t);
  	  
  	  $t2 = array();
  	  $t2['s']      = $t['o'];
  	  $t2['s_type'] = 'uri';
  	  $t2['p']      = RDF.'type';
  	  $t2['o']      = LS."Parameter";
  	  $t2['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t2);
  	   $t2['p']      = RDFS.'label';
  	  $t2['o']      = $k;
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
  	  $t2['o']      = ($v);
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  $t2['p']      = DC.'format';
  	  $t2['o']      = 'text/plain;charset=utf-8';
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  
  	}
  	
  	$t['s']      = $conf['basedir'].'namespaceManagerComponent';
  	$t['s_type'] = 'uri';
  	$t['p']      = SKOS.'broader';
  	$t['o']      = $conf['basedir'];
  	$t['o_type'] = 'uri';  	 	   	
  	array_push($triples, $t);
  	$t['p']      = RDF.'type';
  	$t['o']      = LS.'LodspeakrNamespaceManagerComponent';
  	$t['o_type'] = 'uri';  	 	   	
  	array_push($triples, $t);
  	$t2['s']      = $t['o'];
  	$t2['s_type'] = 'uri';
  	$t2['p']      = RDFS.'subClassOf';
  	$t2['o']      = LDA."ProcessComponent";
  	$t2['o_type'] = 'uri';  	 	 
  	array_push($triples, $t2);
  	
  	  	//Namepsaces
  	foreach($conf['ns'] as $k => $v){	
  	  $t['p']      = LS.'usedParameter';
  	  $t['o']      = uniqid("_:b");
  	  $t['o_type'] = 'bnode';  	   	  
  	  array_push($triples, $t);
  	  
  	  $t2 = array();
  	  $t2['s']      = $t['o'];
  	  $t2['s_type'] = 'uri';
  	  $t2['p']      = RDF.'type';
  	  $t2['o']      = LS."Parameter";
  	  $t2['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t2);
  	   $t2['p']      = RDFS.'label';
  	  $t2['o']      = $k;
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
  	  $t2['o']      = ($v);
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  $t2['p']      = DC.'format';
  	  $t2['o']      = 'text/plain;charset=utf-8';
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  
  	}
  	
  	
  	
  	require($conf['home'].'lib/arc2/ARC2.php');
  	$ser = ARC2::getTurtleSerializer();
  	
  	$sparqlComponent = $conf['basedir'].'sparqlComponent';//uniqid("_:b");
  	//echo $ser->getSerializedTriples($triples);
  	//var_dump($this->getComponents($conf['home'].$conf['view']['directory']));
  	$models = $this->getComponents($conf['home'].$conf['model']['directory'], '');
  	
  	  //Define Process
  	  $t = array();
  	  $t['s']      = uniqid("_:b");
  	  $t['s_type'] = 'bnode';
  	  $t['p']      = RDF.'type';
  	  $t['o']      = OPMV.'Process';
  	  $t['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t);    	
  	foreach($models as $k=>$m){
  	  
	 
  	  
  	  //Controlled by
  	  $t['p']      = OPMV.'wasControlledBy';
  	  $t['o']      = $sparqlComponent;
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
  	  $t['o'] = $sparqlComponent;
  	  
  	  // Type of query
  	  $t2 = array();
  	  $t2['s']      = $t['o'];
  	  $t2['s_type'] = 'bnode';
  	  $t2['p']      = RDF.'type';
  	  $t2['o']      = LS.'LodspeakrSparqlEndpointRetriever';
  	  $t2['o_type'] = 'uri';  
  	  
  	  $t3 = array();
  	  $t3['s']      = $t2['o'];
  	  $t3['s_type'] = 'uri';
  	  $t3['p']      = RDFS.'subClassOf';
  	  $t3['o']      = LDA."ProcessComponent";
  	  $t3['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t3);
  	
  	  array_push($triples, $t2);
  	  $t2['p']      = RDFS.'label';
  	  $t2['o']      = 'Sparql endpoint component for LODSPeaKr';
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  
  	  $t['p']      = LS.'usedInput';
  	  $t['o']      = $conf['basedir'].$conf['model']['directory'].$k;
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
  	  $t2['o']      = $conf['model']['directory'].$k;
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
  	
  	
  	//Views
  	
  	  	$viewsComponent = $conf['basedir'].'visualizationComponent';//uniqid("_:b");
  	$views = $this->getComponents($conf['home'].$conf['view']['directory'], '');
  	
  	  //Define Process
  	  $t = array();
  	  $t['s']      = uniqid("_:b");
  	  $t['s_type'] = 'bnode';
  	  $t['p']      = RDF.'type';
  	  $t['o']      = OPMV.'Process';
  	  $t['o_type'] = 'uri';  	 	 
  	  array_push($triples, $t);    	
  	foreach($views as $k=>$m){
  	  
	 
  	  
  	  //Controlled by
  	  $t['p']      = OPMV.'wasControlledBy';
  	  $t['o']      = $viewsComponent;
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
  	  $t['o'] = $viewsComponent;
  	  
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
  	  $t2['o_type'] = 'literal';  	 	 
  	  array_push($triples, $t2);
  	  //break;
  	}
  	
  	//Static files  	
    	
  	  	$staticComponent = $conf['basedir'].'staticComponent';//uniqid("_:b");
  	$statics = $this->getComponents($conf['home'].$conf['static']['directory'], '');
  	
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
  
  private function getComponents($dir, $prefix){
  	global $conf;
  	$files = array();
  	$subDirs = array();
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
      $files = array_merge($files, $this->getComponents($dir, $prefix.$dir."/")); 
    }
  	chdir("..");
  	return $files;
  }
}

?>
