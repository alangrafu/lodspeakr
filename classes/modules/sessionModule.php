<?php

require_once('abstractModule.php');
class SessionModule extends abstractModule{
  //Session module
  private $sessionUri = "session";
  
  public function match($uri){
  	global $conf; 
    global $localUri;
    global $lodspk;
    $method = ucwords($_SERVER['REQUEST_METHOD']);
    $uriSegment = str_replace($conf['basedir'], '', $localUri);
    //Check if looking for session validation
    if($uriSegment === $this->sessionUri){
      //GET will return the form
      if($method == "GET"){
        $this->showSessionForm();
        return true;
      }      
      //POST will take the data and validate it
      if($method == "POST"){
        if($this->validateAuthentication($_POST)){
          session_start();
          $_SESSION['lodspk'] = 1;
          HTTPStatus::send303($conf['basedir'], '');
          return false;
        }else{
          HTTPStatus::send401("Authentication not valid.");
          return true;
        }
      }
    }else{
      session_start();
      if(isset($_SESSION['lodspk'])){
        return false;
      }else{
        HTTPStatus::send303($conf['basedir'].$this->sessionUri, '');
        return true;
      }
    }
    
  }
  
  public function execute($uri){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	global $firstResults;
  	return true;
  }
  
  
  private function showSessionForm(){
    echo "<html>
    <head>
    <title>Login</title>
    </head>
    <body>
    <form action='".$this->sessionUri."' method='POST'>
    <input name='user' type='text' />
    <input name='password' type='password' /><br/>
    <input name='submit' type='submit' />
    </form>
    </body>
    </html>";
    exit(0);    
  }
  
  private function validateAuthentication($data){
    global $conf;
    if(isset($conf['session']['user']) && isset($conf['session']['password'])){
      if($data['user'] == $conf['session']['user'] && $data['password'] == $conf['session']['password']){
        return true;
      }
      
      return false;
    }
    return false;
  }
}
?>
