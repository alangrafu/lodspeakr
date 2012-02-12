<?

class Queries{
  public static function uriExist($uri, $e){
  	$q = "ASK WHERE{
  	{
  	GRAPH ?g{
    	{<$uri> ?p1 []}
    	UNION
    	{[] <$uri> []}
    	UNION
    	{[] ?p2 <$uri>}
    	}
    	}UNION{
    	    	{<$uri> ?p1 []}
    	UNION
    	{[] <$uri> []}
    	UNION
    	{[] ?p2 <$uri>}
    	}
    }";
  	$r = $e->query($q); 
  	if($r['boolean'] && strtolower($r['boolean']) !== false){
  	  return true;
  	}
  	return false;
  }
  
  public static function getClass($uri, $e){
  	$q = "SELECT DISTINCT ?class ?inst WHERE{
  	 {
  	  GRAPH ?g{
  	  {
  	    <$uri> a ?class .
  	  }UNION{
  	    ?inst a <$uri> .
  	  }
  	 }
  	}UNION{
  	  {
  	    <$uri> a ?class .
  	  }UNION{
  	    ?inst a <$uri> .
  	  }
  	 }
  	}";
  	try{
  	  $r = $e->query($q);
  	}catch (Exception $ex){
  	  echo $ex->getMessage();
  	}
  	$result = array();
  	/*if(sizeof($r['results']['bindings']) == 0){
  	  return 'http://www.w3.org/2000/01/rdf-schema#Resource'; //default value if no type is present
  	}*/
  	//$result[] = 'http://www.w3.org/2000/01/rdf-schema#Resource'; //All resources are rdf:type rdfs:Resource
  	foreach($r['results']['bindings'] as $v){
  	  $result[]= $v['class']['value'];
  	}
  	return $result;
  }
  
  
  public static function getMetadata($uri, $format, $e){
		global $conf;
		$q = <<<QUERY
		SELECT uri, doc, format FROM document WHERE 
			(uri = "$uri" AND format = "$format") OR doc = "$uri"
		LIMIT 1
QUERY;
	   $r = $e->query($q);
		if(sizeof($r) > 0){
		 $u = $r[0]['uri'];
		 $p = $r[0]['doc'];
		 $f = $r[0]['format'];
		  return array($u, $p, $f);
		}else{
		  return NULL;
		}
	}
  

	public static function createPage($uri, $localUri, $contentType, $e){
	 global $conf;
	 
		$ext = 'html';
		$inserts = "";
		foreach($conf['http_accept'] as $extension => $f){
		  $page = $localUri.$conf['extension_connector'].$extension;
			foreach($f as $v){
			  if($contentType == $v){
				$returnPage = $localUri.$conf['extension_connector'].$extension;
			  }
			  if($inserts != ""){
				$inserts .= "UNION ";
			  }
			  $inserts .= "SELECT '$uri', '$page', '$v' \n";
			  if($v == $contentType){
				$ext = $extension;
			  }
			}
		  }
		  $q = <<<QUERY
		  INSERT INTO document (uri, doc, format) $inserts
QUERY;
	$r = $e->write($q);
	
		return $returnPage;
	}
	
}

?>
