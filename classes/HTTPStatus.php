<? 

class HTTPStatus{
  
  public static function send303($uri, $ext){
  	header("HTTP/1.0 303 See Other");
  	header("Location: ".$uri);
  	header("Content-type: ".$ext);
  	echo $uri."\n\n";
  	exit(0);
  }
  
  public static function send404($uri){
  	header("HTTP/1.0 404 Not Found");
  	echo "LODSPeaKr could not find ".$uri." or information about it.\nNo URIs in the triple store, or services configured with that URI\n";
  	exit(0);
  }
  
  public static function send406($uri){
  	header("HTTP/1.0 406 Not Acceptable");
  	echo "LODSPeaKr can't find a representation suitable for the content type you accept\n\n";
  	exit(0);
  }
  
  public static function send500($msg = null){
  	header("HTTP/1.0 500 Internal Server Error");
  	echo "An internal error ocurred. Please try later\n\n";
  	if($msg != null){
  	  echo $msg;
  	}
  	exit(0);
  }
}

?>
