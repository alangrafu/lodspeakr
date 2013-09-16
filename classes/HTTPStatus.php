<?php

class HTTPStatus{
  
  public static function send303($uri, $ext){
  	header("HTTP/1.0 303 See Other");
  	header("Location: ".$uri);
  	header("Content-type: ".$ext);
  	echo HTTPStatus::_getContent("303", $uri);
  	exit(0);
  }

  public static function send401($uri){
  	header("HTTP/1.0 401 Forbidden");
  	echo HTTPStatus::_getContent("401", $uri);
  	exit(0);
  }
  
  public static function send404($uri){
    header("HTTP/1.0 404 Not Found");
    $alt = "LODSPeaKr couldn't find the resource ".$uri;
  	echo HTTPStatus::_getContent("404", $alt);
  	exit(0);
  }
  
  public static function send406($uri){
    header("HTTP/1.0 406 Not Acceptable");
    $alt = "LODSPeaKr can't return content acceptable according to the Accept headers sent in the request for ".$uri;
  	echo HTTPStatus::_getContent("406", $alt);
  	exit(0);
  }
  
  public static function send500($uri){
  	header("HTTP/1.0 500 Internal Server Error");
  	$alt = "There was an internal error when processing ".$uri;
  	echo HTTPStatus::_getContent("500", $alt);
  	exit(0);
  }
  
  private static function _getContent($n, $alt){
    global $conf;
    global $lodspk;
    /*$lodspk['root'] = $conf['root'];
  	$lodspk['home'] = $conf['basedir'];
  	$lodspk['baseUrl'] = $conf['basedir'];
  	$lodspk['ns'] = $conf['ns'];
  	$lodspk['this']['value'] = $uri;
  	$lodspk['this']['curie'] = Utils::uri2curie($uri);
  	$lodspk['this']['local'] = $localUri;
  	*/
    $file = $conf['httpStatus']['directory']."/".$n.".template";
   
    if(file_exists($conf['home'].$file)){
      require_once("Utils.php");
      Utils::showView($lodspk, new stdClass(), $file);
    }else{
      return $alt."\n\n";
    }
  }
}

?>
