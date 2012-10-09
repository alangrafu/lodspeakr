<?php

require_once('abstractModule.php');
class AdminModule extends abstractModule{
  //Service module
     private $head = "<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <title>LODSPeaKr Admin Menu</title>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta name='description' content=''>
    <meta name='author' content=''>
    <link href='../css/bootstrap.min.css' rel='stylesheet' type='text/css' media='screen' />
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
      .wait{
        background-image:url('img/wait.gif');
        background-repeat:no-repeat;
        padding-right:20px;
        background-position: right;
      }
      /* Base class */
.bs-docs-template {
  position: relative;
  margin: 0px 0;
  padding: 39px 19px 14px;
  *padding-top: 19px;
  background-color: #fff;
  border: 1px solid #ddd;
  -webkit-border-radius: 4px;
     -moz-border-radius: 4px;
          border-radius: 4px;
}

/* Echo out a label for the example */
.bs-docs-template:after {
  content: 'Template';
  position: absolute;
  top: -1px;
  left: -1px;
  padding: 3px 7px;
  font-size: 12px;
  font-weight: bold;
  background-color: #f5f5f5;
  border: 1px solid #ddd;
  color: #9da0a4;
  -webkit-border-radius: 4px 0 4px 0;
     -moz-border-radius: 4px 0 4px 0;
          border-radius: 4px 0 4px 0;
}

.bs-docs-query {
  position: relative;
  margin: 0px 0;
  padding: 39px 19px 14px;
  *padding-top: 19px;
  background-color: #fff;
  border: 1px solid #ddd;
  -webkit-border-radius: 4px;
     -moz-border-radius: 4px;
          border-radius: 4px;
}

/* Echo out a label for the example */
.bs-docs-query:after {
  content: 'Query';
  position: absolute;
  top: -1px;
  left: -1px;
  padding: 3px 7px;
  font-size: 12px;
  font-weight: bold;
  background-color: #f5f5f5;
  border: 1px solid #ddd;
  color: #9da0a4;
  -webkit-border-radius: 4px 0 4px 0;
     -moz-border-radius: 4px 0 4px 0;
          border-radius: 4px 0 4px 0;
}
textarea{ font-family: Monaco,'Droid Sans Mono'; font-size: 80%}
    </style>
    <link href='../css/bootstrap-responsive.min.css' rel='stylesheet' type='text/css' media='screen' />
    <script type='text/javascript' src='../js/jquery.js'></script>
    <script type='text/javascript' src='../js/bootstrap.min.js'></script>
  </head>
  <body>
 <div class='navbar navbar-fixed-top'>
      <div class='navbar-inner'>
        <div class='container'>
          <a class='btn btn-navbar' data-toggle='collapse' data-target='.nav-collapse'>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
          </a>
          <a class='brand' href='../admin'>LODSPK Admin Menu</a>
          <div class='nav-collapse'>
            <ul class='nav'>
              <li ><a href='../admin'>Admin home</a></li>
              <li class='dropdown'>
               <a class='dropdown-toggle' data-toggle='dropdown' href='#'>SPARQL Endpoint<b class='caret'></b></a>
               <ul class='dropdown-menu'>
              <li><a href='../admin/start'>Start endpoint</a></li>
              <li><a href='../admin/stop'>Stop endpoint</a></li>
              <li><a href='../admin/load'>Add RDF</a></li>
              <li><a href='../admin/remove'>Remove RDF</a></li>
               </ul>
              </li>
              <li>
               <a class='dropdown-toggle' data-toggle='dropdown' href='../admin/namespaces'>Namespaces<b class='caret'></b></a>
              </li>
              <li>
               <a class='dropdown-toggle' data-toggle='dropdown' href='../admin/endpoints'>Endpoints<b class='caret'></b></a>
              </li>
              <li>
               <a href='../'>Go to main site</a>
              </li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class='container'>
      <img src='img/lodspeakr_logotype.png' style='opacity: 0.1; position: absolute; right:0px; top:60%'/>
";
  private $foot ="    </div>
  </body>
</html>
";


  public function match($uri){
    global $localUri;
    global $conf;
  	$q = preg_replace('|^'.$conf['basedir'].'|', '', $localUri);
  	$qArr = explode('/', $q);
  	if(sizeof($qArr)==0){
  	  return FALSE;
  	}
  	if($qArr[0] == "admin"){
  	  return $qArr;
  	}  	
  	return FALSE;  
  }
  
  public function execute($params){
  	global $conf;
  	global $localUri;
  	global $uri;
  	global $acceptContentType;
  	global $endpoints;
  	global $lodspk;
  	global $firstResults;
  	if(!$this->auth()){
  	  HTTPStatus::send401("Forbidden\n");
  	  exit(0);
  	}
  	if(sizeof($params) == 1){
  	  header( 'Location: admin/menu' ) ;
  	  exit(0);
  	}
  	if($params[1] == ""){
  	  header( 'Location: menu' ) ;
  	  exit(0);
  	}
  	switch($params[1]){
  	case "menu":
  	  $this->homeMenu();
  	  break;
  	case "start":
  	  $this->startEndpoint();
  	  break;
  	case "stop":
  	  $this->stopEndpoint();
  	  break;
  	case "load":
  	  $this->loadRDF();
  	  break;
  	case "remove":
  	  $this->deleteRDF();
  	  break;
  	case "namespaces":
  	  $this->editNamespaces();
  	  break;
  	case "endpoints":
  	  $this->editEndpoints();
  	  break;
  	case "components":
  	  if(sizeof($params) == 2){
  	    $this->componentEditor();
  	  }else{
  	    $this->componentEditorApi(array_slice($params, 2));
  	  }
  	  break;
  	default:
  	  HTTPStatus::send404($params[1]);
  	}
  	exit(0);
  }
  
  protected function loadRDF(){
    global $conf;
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
      echo $this->head."
     <div class='fluid-row'>
      <div class='span5'>
      <form action='load' method='post'
      enctype='multipart/form-data'>
      <legend>Load RDF into the endpoint</legend>
      <div class='alert alert-info'><span class='label label-info'>Important</span> If you load data into an existing Named graph, the content will be overwritten!</div>
      <label for='file'>RDF file</label>
      <input type='file' name='file' id='file' />
      <span class='help-block'>LODSPeaKr accepts RDF/XML, Turtle and N-Triples files</span>
      <label for='file'>Named graph</label>
      <input type='text' name='namedgraph' id='namedgraph' value='default'/>
      <span class='help-block'>The named graph where the RDF will be stored (optional).</span>
      <br />
      <button type='submit' class='btn btn-large'>Submit</button>
      </form>
      </div>
      <div class='span6'>
       <legend>Named Graphs</legend>
       <div id='ng'></div>
      </div>
     </div>
     <script type='text/javascript' src='".$conf['basedir']."js/jquery.js'></script>
     <script type='text/javascript' src='".$conf['basedir']."js/namedgraphs.js'></script>
      ".$this->foot;
    }elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
      if ($_FILES["file"]["error"] > 0){
        HTTPStatus::send409("No file was included in the request");
      }else{
        $ng = (isset($_POST['namedgraph']))?$_POST['namedgraph']:'default';
        
        require_once($conf['home'].'lib/arc2/ARC2.php');          	        
        $parser = ARC2::getRDFParser();
        $parser->parse($_FILES["file"]["tmp_name"]);
        $triples = $parser->getTriples();
        if(sizeof($triples) > 0){
          $c = curl_init();          
          $body = $parser->toTurtle($triples);
          $fp = fopen('php://temp/maxmemory:256000', 'w');
          if (!$fp) {
            die('could not open temp memory data');
          }
          fwrite($fp, $body);
          fseek($fp, 0); 
          
          curl_setopt($c, CURLOPT_URL, $conf['updateendpoint']['local']."?graph=".$ng);
          curl_setopt($c, CURLOPT_CUSTOMREQUEST, "PUT");
          curl_setopt($c, CURLOPT_PUT, 1);
          curl_setopt($c, CURLOPT_BINARYTRANSFER, true);
          curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($c, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT',"Content-Type: text/turtle"));
          curl_setopt($c, CURLOPT_USERAGENT, "LODSPeaKr version ".$conf['version']);
          curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($c, CURLOPT_INFILE, $fp); // file pointer
          curl_setopt($c, CURLOPT_INFILESIZE, strlen($body)); 
          curl_exec($c); // execute the curl command 
          $http_status = curl_getinfo($c, CURLINFO_HTTP_CODE);
          if(intval($http_status)>=200 && intval($http_status) <300){
             echo $this->head."<h2>Success!!</h2><div class='alert alert-success'>The file ".$_FILES["file"]["name"]." (".$_FILES["file"]["size"]." bytes, ".sizeof($triples)." triples) was stored successfully on $ng.</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot;
          }else{
            HTTPStatus::send502($this->head."<h2>Error!!</h2><div class='alert alert-success'>The file ".$_FILES["file"]["name"]." couldn't be loaded into the triples store. The server was acting as a gateway or proxy and received an invalid response (".$http_status.") from the upstream server</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot);
          }
          curl_close($c);
          fclose($fp);

        }else{
          HTTPStatus::send409($this->head."<h2>Error!!</h2><div class='alert alert-error'>The file was not a valid RDF document.</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot);
        }
      }
    }else{
      HTTPStatus::send405($_SERVER['REQUEST_METHOD']);
    }
    exit(0);
  }

  protected function deleteRDF(){
    global $conf;
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
      echo $this->head."
     <div class='fluid-row'>
      <div class='span5'>
      <form action='remove' method='post'
      enctype='multipart/form-data'>
      <legend>Remove a Named Graph containing RDF</legend>
      <label for='file'>Named graph</label>
      <input type='text' name='namedgraph' id='namedgraph' />
      <span class='help-block'>The named graph where the RDF is stored.</span>
      <br />
      <button type='submit' class='btn'>Submit</button></form>
      </div>
      <div class='span6'>
       <legend>Named Graphs</legend>
       <div id='ng'></div>
      </div>
     </div>
     <script type='text/javascript' src='".$conf['basedir']."js/jquery.js'></script>
     <script type='text/javascript' src='".$conf['basedir']."js/namedgraphs.js'></script>
      ".$this->foot;
    }elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
      $ng = (isset($_POST['namedgraph']))?$_POST['namedgraph']:'default';
      $c = curl_init();          
      curl_setopt($c, CURLOPT_URL, $conf['updateendpoint']['local']."?graph=".$ng);
      curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'DELETE');
      curl_setopt($c, CURLOPT_BINARYTRANSFER, true);
      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($c, CURLOPT_USERAGENT, "LODSPeaKr version ".$conf['version']);
      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
      curl_exec($c); // execute the curl command 
      $http_status = curl_getinfo($c, CURLINFO_HTTP_CODE);
      if(intval($http_status)>=200 && intval($http_status) <300){
        echo $this->head."<h2>Success!!</h2><div class='alert alert-success'>The named graph $ng was removed successfully</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot;
      }else{
        HTTPStatus::send502($this->head."<h2>Error!!</h2><div class='alert alert-error'>The named graph $ng couldn't be removed from the endpoint. The server was acting as a gateway or proxy and received an invalid response (".$http_status.") from the upstream server</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot);
      }
      curl_close($c);
      
    }else{
      HTTPStatus::send405($_SERVER['REQUEST_METHOD']);
    }
    exit(0);
  }


  protected function editNamespaces(){
    global $conf;
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
      $nstable = "";
      foreach($conf['ns'] as $k=>$v){
       $nstable .= "<tr><td>".$k."</td><td id='$k'>".$v."</td><td><button class='button edit-button' data-prefix='$k' data-ns='$v'>Edit</button></tr>";
      }
      echo $this->head."
     <div class='fluid-row'>
      <div class='span7'>
      <form action='namespaces' method='post'
      enctype='multipart/form-data'>
      <legend>Edit main namespace</legend>
      <label for='file'>Prefix</label>
      <input type='text' name='prefix' id='prefix' value='local'/>
      <span class='help-block'>The prefix to describe this namespace ('local' is the one used to mirror URIs of the data in this server)</span>
      <label for='file'>Namespace</label>
      <input type='text' name='namespace' id='namespace' value='".$conf['ns']['local']."'/>
      <span class='help-block'>The namespace of the data being served</span>
      <br />
      <button type='submit' class='btn'>Submit</button></form>
      </div>
      <div class='span4 well'>
      <legend>Edit local namespace</legend>
      <p>'local' namespace defines which types of URI will be mirrored in this server. Thus, it is possible to serve data about <code>http://example.org/myresource</code> by dereferencing <code>".$conf['ns']['local']."myresource</code></p>
      <legend>Add a new  namespace</legend>
      <p>To add a new namespace, simply change the prefix from 'local' to the new one you want to add and include the namespaces in the following box.</p>
      <legend>Edit other namespace</legend>
      <p>Click on 'edit' in the proper row in the following table and modify the values in the form.</p>
      </div>
     </div>
     <script type='text/javascript' src='".$conf['basedir']."js/jquery.js'></script>
     <script type='text/javascript'>
     //<![CDATA[
     $(document).ready(function(){
                 $('.edit-button').on('click', function(target){
                    $('#prefix').val($(this).attr('data-prefix'));
                    $('#namespace').val($(this).attr('data-ns'));
                    $('html, body').stop().animate({
                      scrollTop: $('body').offset().top
                    }, 1000);
                 })
     });
     //]]>
     </script>
     <div class='fluid-row'>
      <div class='span8'>
       <legend>Edit other namespaces</legend>
       <table class='table table-striped'>
        <thead><td>Prefix</td><td>Namespace</td><td>Edit</td></thead>$nstable</table>
      ".$this->foot;
    }elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
      $ns = (isset($_POST['namespace']))?$_POST['namespace']:'http://'.$_SERVER['SERVER_NAME'].'/';
      $prefix = (isset($_POST['prefix']))?$_POST['prefix']:'local';
      $return_var = 0;
      exec ("php utils/modules/remove-namespace.php ".$prefix, &$output, $return_var);
      exec ("php utils/modules/add-namespace.php ".$prefix." ".$ns, &$output, $return_var);  
      if($return_var == 0){
        echo $this->head ."<div class='alert alert-success'>Your main namespace was updated successfully to $ns</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot;
      }else{
        echo $this->head ."<div class='alert alert-error'>Error: Update did not finished successfullt. Please check setting.inc.php located at ".$conf['home'].".</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot;
    }
    }
  }
  
  
  
  protected function editEndpoints(){
    global $conf;
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
      $nstable = "";
      foreach($conf['endpoint'] as $k=>$v){
        $nstable .= "<tr><td>".$k."</td><td id='$k'>".$v."</td><td><button class='button edit-button' data-prefix='$k' data-ns='$v'>Edit</button></tr>";
      }
      echo $this->head."
      <div class='fluid-row'>
      <div class='span7'>
      <form action='endpoints' method='post'
      enctype='multipart/form-data'>
      <legend>Edit Endpoints</legend>
      <label for='file'>Shortname</label>
      <input type='text' name='prefix' id='prefix' value='local'/>
      <span class='help-block'>The prefix to describe this namespace ('local' is the one used to mirror URIs of the data in this server)</span>
      <label for='file'>Endpoint</label>
      <input type='text' name='endpoint' id='endpoint' value='".$conf['ns']['local']."'/>
      <span class='help-block'>The endpoint URL</span>
      <br />
      <button type='submit' class='btn'>Submit</button></form>
      </div>
      <div class='span4 well'>
      <legend>Add or edit an endpoint</legend>
      <p>To add a new endpoint, simply add a new prefix, the SPARQL endpoint URL and click on Submit.</p>
      <p>To edit an endpoint, click on 'edit' in the proper row in the following table and modify the values in the form.</p>
      </div>
      </div>
      <script type='text/javascript' src='".$conf['basedir']."js/jquery.js'></script>
           <script type='text/javascript'>
     //<![CDATA[
     $(document).ready(function(){
                 $('.edit-button').on('click', function(target){
                    $('#prefix').val($(this).attr('data-prefix'));
                    $('#endpoint').val($(this).attr('data-ns'));
                    $('html, body').stop().animate({
                      scrollTop: $('body').offset().top
                    }, 1000);
                 })
     });
     //]]>
     </script>
      <div class='fluid-row'>
      <div class='span8'>
      <legend>Edit other namespaces</legend>
      <table class='table table-striped'>
      <thead><td>Prefix</td><td>Namespace</td><td>Edit</td></thead>$nstable</table>
      ".$this->foot;
    }elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
      $ns = (isset($_POST['endpoint']))?$_POST['endpoint']:'http://'.$_SERVER['SERVER_NAME'].'/';
      $prefix = (isset($_POST['prefix']))?$_POST['prefix']:'local';
      $return_var = 0;
      exec ("php utils/modules/remove-endpoint.php ".$prefix, &$output, $return_var);
      exec ("php utils/modules/add-endpoint.php ".$prefix." ".$ns, &$output, $return_var);  
      if($return_var == 0){
        echo $this->head ."<div class='alert alert-success'>Your endpoint was updated successfully to $ns</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot;
      }else{
        echo $this->head ."<div class='alert alert-error'>Error: Update did not finished successfully. Please check setting.inc.php located at ".$conf['home'].".</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot;
      }
    }
  }
  
  protected function startEndpoint(){
    $return_var = 0;
    exec ("utils/modules/start-endpoint.sh", &$output, $return_var);  
    if($return_var == 0){
      echo $this->head ."<div class='alert alert-success'>Endpoint starter successfully</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot;
    }else{
      echo $this->head ."<div class='alert alert-error'>Error: /tmp/fusekiPid already exists. This probably means Fuseki is already running. You could also try to <a href='stop'>stop</a> the endpoint first.</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot;
    }
  }
  
  protected function componentEditor(){
    global $lodspk;
    global $conf;
    exec ("utils/lodspk.sh list components", &$output, $return_var);
    $menu = "";
    $lastComponentType="";
    foreach($output as $line){
      if($line == ""){
          $menu .= "</ul>\n";
      }else{
        if(preg_match("/^\w/", $line) ){
            $lastComponentType = trim($line);
            $menu .= "<ul class='nav nav-list'>
            <li class='nav-header'>".$lastComponentType."  <button class='btn btn-mini btn-info'>new</button></li>\n";
        }else{
          $componentName = trim($line);
          $menu .= "<li><a href='#' class='lodspk-component' data-component-type='$lastComponentType' data-component-name='$componentName'>".$componentName."</a></li>\n";
        }
      }
    }
    echo $this->head ."
    <script src='".$conf['basedir'] ."js/editor.js'></script>
    <div class='row-fluid'>
     <div class='span3 well'>$menu</div>
     <div class='bs-docs-template span9'>
      <textarea class='field span12' rows='8' cols='25' id='template-editor'></textarea>
      <button class='btn btn-info disabled' id='template-save-button' data-url=''>Save</button>
      <div class='alert alert-success hide' id='template-msg'></div>
     </div>
    </div> 
    <div class='row-fluid'>
     <div class='span3'>
      <div class='container'>
       <div class='row-fluid'>
        <div class='span3 well'>
          <legend>Templates  <button class='btn btn-mini btn-info'>new</button></legend>
         <ul class='nav nav-list' id='template-list'>
         </ul>        
        </div>
       </div>
       <div class='row-fluid'>
        <div class='span3 well'>
          <legend>Queries  <button class='btn btn-mini btn-info'>new</button></legend>
         <ul class='nav nav-list' id='query-list'>
         </ul>
        </div>
       </div>
      </div>
     </div>
     <div class='span9  bs-docs-query'>
      <textarea class='field span12' rows='8' cols='25' id='query-editor'></textarea>
      <button class='btn btn-info disabled' id='query-save-button' data-url=''>Save</button>
      <div class='alert alert-success hide' id='query-msg'></div>
     </div>
     </div>
    </div>
   </div>
  </div>

    ".$this->foot;
  }

  
  protected function componentEditorApi($params){
    switch($params[0]){
  	case "details":
  	  $this->getComponentDetails(array_slice($params, 1));
  	  break;
  	case "save":
  	  if(sizeof($params) > 2){
  	    $this->saveComponent($params);
  	  }else{
  	    HTTPStatus::send404($params[1]);
  	  }
  	  break;  	  
  	default:
  	  HTTPStatus::send404($params[1]);
  	}
  }

  protected function getComponentDetails($params){
    $componentType = $params[0];
    $componentName = $params[1];
    if(!isset($componentType) || !isset($componentName)){
      HTTPStatus::send404();
  	  exit(0);
    }    
    $return_var = 0;
    exec ("utils/modules/detail-component.sh $componentType $componentName", &$output, $return_var);  
    if($return_var == 0){
      $comps = array();
      $lastKey = "";
      foreach($output as $line){
        if($line == ""){
          $menu .= "</ul>\n";
        }else{
          if(preg_match("/^\w/", $line) ){
            $lastKey = trim($line);
            $comps[$lastKey] = array();
          }else{
            array_push($comps[$lastKey], trim($line));
          }
        }
      }
      header("Content-type: application/json");
      echo json_encode($comps);
    }else{
      HTTPStatus::send500();
      exit(0);
    }
  }

  protected function saveComponent($params){
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
      $path = implode("/", array_slice($params, 1));
      if(file_exists("components/".$path)){
        $result = file_put_contents("components/".$path,$_POST['content'] );
        if($result === FALSE){
                HTTPStatus::send500();
        }else{
          echo json_encode(array('success' => true, 'size' => $result));          
        }
      }else{
        HTTPStatus::send500();
        exit(0);
      }
    }
  }
  protected function stopEndpoint(){
    $return_var = 0;
    exec ("utils/modules/stop-endpoint.sh", &$output, $return_var);  
    if($return_var == 0){
      echo $this->head ."<div class='alert alert-success'>Endpoint stopped successfully</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot;
    }else{
      echo $this->head ."<div class='alert alert-error'>Error: Something went wrong. Are you sure the endpoint is running?</div><div class='alert'>You can now return to the <a href='menu'>home menu</a>.</div>".$this->foot;
    }
  }
  
  protected function homeMenu(){
    global $conf;
    $output = array();
    exec ("utils/modules/test-endpoint.sh", $output, $return_var);
    $msg = "<div class='alert alert-success'>Endpoint running</div>";
    if($return_var != 0)
      $msg = "<div class='alert alert-error'>Endpoint stopped</div>";
    echo $this->head."
      <div class='span6 well'>
      <h2>Triplestore (Fuseki)</h2>
      <p>You can do several operations on this page:</p>
      <ul>
        <li>You can <a href='start'>start</a> and <a href='stop'>stop</a> the triple store.</li>
        <li>You can <a href='load'>load</a> the triple store using an existing RDF document.</li>
        <li>You can <a href='remove'>remove</a> RDF data from a named graph.</li>    
      </ul>
      </div>
      <div class='span4 well'>
      <h2>Endpoint status</h2>
      ".$msg."      </div>
            <div class='span6 well'>
      <!--h2>Components Editor</h2>
      <p>You can edit the components using the <a href='components'>editor</a></p-->
      <h2>Edit your namespaces</h2>
      <p>LODSPeaKr needs to know the namespace of the URIs you want to publish. In this way you can create resolvable URIs in this server. You can <a href='namespaces'>edit your main namespace</a> here.</p>
</div>
      ".$this->foot;

  }
  
  protected function auth(){    
    global $conf;
    $realm = 'Restricted area';    
    //user => password
    $users = array('admin' => $conf['admin']['pass']);
    if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
      header('HTTP/1.1 401 Unauthorized');
      header('WWW-Authenticate: Digest realm="'.$realm.
        '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
      
      die('Access to administration menu requires valid authentication');
    }
    // analyze the PHP_AUTH_DIGEST variable
    if (!($data = $this->http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
      !isset($users[$data['username']]))
      return FALSE;
    //die('Wrong Credentials!');
    // generate the valid response
    $A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
    $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
    $valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
    
    if ($data['response'] != $valid_response)
      return FALSE;
//      die('Wrong Credentials!');
    
    // ok, valid username & password
    //echo 'You are logged in as: ' . $data['username'];
    return TRUE;
    
  }
  
  // function to parse the http auth header
  protected function http_digest_parse($txt)
  {
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));
    
    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $m) {
      $data[$m[1]] = $m[3] ? $m[3] : $m[4];
      unset($needed_parts[$m[1]]);
    }
    
    return $needed_parts ? false : $data;
  }
  
}
?>
