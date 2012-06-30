<? 

class HTTPStatus{
  
  public static function send303($uri, $ext){
    global $conf;
    $file = $conf['home'].$conf['httpStatus']['directory']."/303.template";
    if(file_exists($file)){
      $content = file_get_contents($file);
    }else{
      $content = $uri."\n\n";
    }
  	header("HTTP/1.0 303 See Other");
  	header("Location: ".$uri);
  	header("Content-type: ".$ext);
  	echo $content;
  	exit(0);
  }

  public static function send401($uri){
    global $conf;
    $file = $conf['home'].$conf['httpStatus']['directory']."/401.template";
    if(file_exists($file)){
      $content = file_get_contents($file);
    }else{
      $content = $uri."\n\n";
    }
  	header("HTTP/1.0 401 Forbidden");
  	echo $content;
  	exit(0);
  }
  
  public static function send404($uri){
    global $conf;
    $file = $conf['home'].$conf['httpStatus']['directory']."/404.template";
    if(file_exists($file)){
      $content = file_get_contents($file);
    }else{
      $content = "LODSPeaKr could not find ".$uri." or information about it.\nNo URIs in the triple store, or services configured with that URI\n";
    }  	
    header("HTTP/1.0 404 Not Found");
  	echo $content;
  	exit(0);
  }
  
  public static function send406($uri){
    global $conf;
    $file = $conf['home'].$conf['httpStatus']['directory']."/406.template";
    if(file_exists($file)){
      $content = file_get_contents($file);
    }else{
      $content = "LODSPeaKr can't find a representation suitable for the content type you accept for $uri\n\n";
    }  	
    header("HTTP/1.0 406 Not Acceptable");
  	echo $content;
  	exit(0);
  }
  
  public static function send500($uri){
    global $conf;
    $file = $conf['home'].$conf['httpStatus']['directory']."/406.template";
    if(file_exists($file)){
      $content = file_get_contents($file);
    }else{
      $content = "An internal error ocurred. Please try later\n\n";
    }  
  	header("HTTP/1.0 500 Internal Server Error");
  	echo $content;
  	exit(0);
  }
}

?>
