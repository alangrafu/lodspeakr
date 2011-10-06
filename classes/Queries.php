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
  	$q = "SELECT DISTINCT ?class ?inst WHERE{
  	{
  	  <$uri> a ?class .
  	}UNION{
  	  ?inst a <$uri> .
  	}
  	}";
  	try{
  	  $r = $e->query($q);
  	}catch (Exception $ex){
  	  echo $ex->getMessage();
  	}
  	$result = array();
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
		  $page = $localUri.".".$extension;
			foreach($f as $v){
			  if($contentType == $v){
				$returnPage = $localUri.".".$extension;
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
