<?

class Queries{
  public static function uriExist($uri, $e){
  	$q = "SELECT * WHERE{
  	{<$uri> ?p1 ?o1}
  	UNION
  	{?s1 <$uri> ?o2}
  	UNION
  	{?s2 ?p2 <$uri>}
  	}LIMIT 1";
  	
  	$r = $e->query($q);
  	if(sizeof($r['results']['bindings'])>0){
  	  return true;
  	}
  	return false;
  }
  
  public static function getClass($uri, $e){
  	$q = "SELECT DISTINCT ?class WHERE{
  	<$uri> a ?class .
  	} LIMIT 1";
  	$r = $e->query($q);
  	if(sizeof($r['results']['bindings'])>0){
  	  return $r['results']['bindings'][0]['class']['value'];
  	}
  	return NULL;
  }
  
  public static function getMetaData($uri, $e){
  	global $conf;
  	$named_graph = $conf['metaendpoint']['config']['named_graph'];
  	$q = <<<QUERY
  	PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
  	SELECT ?page ?uri WHERE{
  	GRAPH <$named_graph>{
  	?s ?p ?o . #Stupid dummy triple to make it work with ARC2
  	OPTIONAL{?page foaf:primaryTopic <$uri>} .
  	OPTIONAL{<$uri> foaf:primaryTopic ?uri} .
  	}
  	}LIMIT 1
QUERY;

$r = $e->query($q);
if(sizeof($r['results']['bindings'])>0){
  $u = (isset($r['results']['bindings'][0]['uri']))?$r['results']['bindings'][0]['uri']['value']:NULL;
  $p = (isset($r['results']['bindings'][0]['page']))?$r['results']['bindings'][0]['page']['value']:NULL;
  
  return array($u, $p);
}
return NULL;
  }
}
  	?>
