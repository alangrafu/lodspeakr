<?php

require_once('abstractModule.php');
class StaticModule extends abstractModule{
  //Static module
  
  public function match($uri){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	$q = preg_replace('|^'.$conf['basedir'].'|', '', $localUri);
  	if(sizeof($q)>0 && file_exists($conf['home'].$conf['static']['directory'].$q)){
  	  return $q;
  	}
  	return FALSE;
  }
  
  public function execute($file){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;  	
  	$extension = array_pop(explode(".", $file));
  	$ct = $this->getContentType($extension);
  	header("Content-type: ".$ct);
  	$uri = $localUri;
  	if($conf['debug']){
  	  Utils::log("In ".$conf['static']['directory']." static file $file");
	  }
	  $htmlExtension = 'html';
	  if($conf['static']['haanga'] && substr_compare($file, $htmlExtension, -strlen($htmlExtension), strlen($htmlExtension)) === 0){
	    $lodspk['home'] = $conf['basedir'];
	    $lodspk['baseUrl'] = $conf['basedir'];
	    $lodspk['module'] = 'static';
	    $lodspk['root'] = $conf['root'];
	    $lodspk['contentType'] = $acceptContentType;
	    $lodspk['ns'] = $conf['ns'];  	  	
	    $lodspk['this']['value'] = $localUri;
	    $lodspk['this']['curie'] = Utils::uri2curie($localUri);
  	  $lodspk['local']['value'] = $localUri;
  	  $lodspk['local']['curie'] = Utils::uri2curie($localUri);
	    $lodspk['contentType'] = $acceptContentType;
	    $lodspk['endpoint'] = $conf['endpoint'];	    
	    $lodspk['type'] = $modelFile;
	    $lodspk['header'] = $prefixHeader;
	    $lodspk['baseUrl'] = $conf['basedir'];
	    
	    Utils::processDocument($conf['static']['directory'].$file, $lodspk, null);    	  
  	}else{
  	  echo file_get_contents($conf['static']['directory'].$file);
  	}
  }
  
  private function getContentType($e){
    $contentTypes = array('html' => 'text/html',
                          'css'  => 'text/css',
                          'js'   => 'application/javascript',
                          'json' => 'application/json',
                          'nt'   => 'text/plain',
                          'ttl'  => 'text/turtle',
                          'png'  => 'image/png',
                          'jpg'  => 'image/jpeg',
                          'gif'  => 'image/gif',
                          'bmp'  => 'image/bmp',
                          'pdf'  => 'application/pdf',
                          'zip'  => 'application/zip',
                          'gz'   => 'application/gzip'
                          );
    
   //Add new/override existing mime types defined by user
   foreach($conf['static']['mimetypes'] as $k => $v){
     $contentTypes[$k] = $v;
   }
   
   if(isset($contentTypes[$e])){
     return $contentTypes[$e];
   }
   return ""; //empty string seems to work fine with browsers
  }
  
  
  
}
?>
