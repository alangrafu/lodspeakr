<? 

class Utils{
  public static function send404($uri){
  	header("HTTP/1.0 404 Not Found");
  	echo "I could not find ".$uri." or information about it";
  	exit(0);
  }
  
  public static function send303($uri){
  	header("HTTP/1.0 303 See Other");
  	header("Location: ".$uri);
  	exit(0);
  }
  
  public static function uri2curie($uri){
  	global $conf;
  	$ns = $conf['ns'];
  	$curie = $uri;
  	foreach($ns as $k => $v){
  	  $curie = preg_replace("|^$v|", "$k:", $uri);
  	  if($curie != $uri){
  	  	break;
  	  }
  	}
  	return $curie;
  }
  
  public static function getTemplate($uri){
  	$filename = str_replace(":", "_", $uri);
  	if(file_exists ($filename)){
  	  include_once($filename);
  	}
  }
  
  public static function showView($uri, $data, $view){
  	global $conf;
  	$body = file_get_contents($view);
  	$r = $data['results']['bindings'];
  	if(sizeof($r)> 0){
  	  foreach($r as $v){
  	  	foreach($v as $k => $w){
  	  	  $body = preg_replace("|%".$k."|", $w['value'], $body);
  	  	  echo $html;
  	  	}
  	  }
  	}
  	$html = preg_replace("|".$conf['resource']['url_delimiter']."|", $uri, $body);
  	echo $html;
  }
  
}
?>
