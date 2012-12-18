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
    <link href='codemirror/lib/codemirror.css' rel='stylesheet' type='text/css' media='screen' />
    
    <style>
    body {
    padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
    }
    .CodeMirror {border: 1px solid black;}
    .cm-mustache {color: #0ca;}
    .wait{
    background-image:url('img/wait.gif');
    background-repeat:no-repeat;
    padding-right:20px;
    background-position: right;
    }
    .strong{font-weight: 900; font-size:120%}
    .cheat-sheet{
    -moz-border-radius: 15px;
    -webkit-border-radius: 15px;
    border-radius: 15px;
    min-height: 200px;
    background:lightgray;
    width:400px;
    padding:5px;
    position:absolute;
    border:1px solid black;
    right:-370px;
    top:120px;
    opacity:0.9
    }
    .cheat-title{
    writing-mode:tb-rl;
    -webkit-transform:rotate(90deg);
    -moz-transform:rotate(90deg);
    -o-transform: rotate(90deg);
    white-space:nowrap;
    display:block;
    width:20px;
    height:40px;
    font-size:24px;
    font-weight:normal;
    text-shadow: 0px 0px 1px #333;      
    }
    .first-editor{
	  top:120px;
	  }
	  .second-editor{
	  top:540px;
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
	  .cheat-list{
	  margin-left:60px;
	  margin-top:-30px;
	  }
	  
	  .sparql-list{
	  margin-left:60px;
	  margin-top:-40px;
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
          <a class='brand' href='../admin'>LODSPeaKr menu</a>
          <div class='nav-collapse'>
            <ul class='nav'>
              <!--li class='dropdown'>
               <a class='dropdown-toggle' data-toggle='dropdown' href='#'>SPARQL Endpoint<b class='caret'></b></a>
               <ul class='dropdown-menu'>
              <li><a href='../admin/start'>Start endpoint</a></li>
              <li><a href='../admin/stop'>Stop endpoint</a></li>
              <!--li><a href='../admin/load'>Add RDF</a></li>
              <li><a href='../admin/remove'>Remove RDF</a></li>
               </ul>
              </li-->
              <li>
               <a class='dropdown-toggle' data-toggle='dropdown' href='../admin/namespaces'>Namespaces<b class='caret'></b></a>
              </li>
              <li>
               <a class='dropdown-toggle' data-toggle='dropdown' href='../admin/endpoints'>Endpoints<b class='caret'></b></a>
              </li>
              <li>
               <a class='dropdown-toggle' data-toggle='dropdown' href='../admin/components'>Component Editor<b class='caret'></b></a>
              </li>
              <li>
               <a href='../'><i class='icon-share icon-white'></i> Go to main site</a>
              </li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class='container'>
      <img src='../img/lodspeakr_logotype.png' style='opacity: 0.1; position: absolute; right:0px; top:60%'/>
";
  private $foot ="        <div id='embed-box' class='modal hide fade'>
    <div class='modal-header'>
    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
    <h3>Embed this code</h3>
    </div>
    <div  class='modal-body'>
     <form class='form-inline'><fieldset><label>Width:</label> <input type='text' class='input-small embed-size' id='embed-width' value='600px'/>  <label>Height:</label> <input type='text' class='input-small embed-size' id='embed-height' value='400px'/></fieldset></form>
     <div id='embed-body'>
     </div>
    </div>
    <div class='modal-footer'>
    <a href='#' class='btn' data-dismiss='modal'>Close</a>
    </div>
    </div></div>
  </body>
</html>
";


  public function match($uri){
    global $localUri;
    global $conf;
    //URLs used by this component. Other URLs starting with admin/* won't be considered by this module
    
    $operations = array("menu", /*"load", "remove",*/ "endpoints", "namespaces", "components", "");
  	$q = preg_replace('|^'.$conf['basedir'].'|', '', $localUri);
  	$qArr = explode('/', $q);
  	if(sizeof($qArr)==0){
  	  return FALSE;
  	}
  	if($qArr[0] == "admin" && array_search($qArr[1], $operations) !== FALSE){
  	  if($conf['admin']['pass'] !== FALSE && !$this->auth()){
  	    HTTPStatus::send401("Forbidden\n");
  	    exit(0);
  	  }
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
  	/*case "start":
  	  $this->startEndpoint();
  	  break;
  	case "stop":
  	  $this->stopEndpoint();
  	  break;*/
  	/*case "load":
  	  $this->loadRDF();
  	  break;
  	case "remove":
  	  $this->deleteRDF();
  	  break;*/
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
    if(!isset($conf['updateendpoint']['local'])){
      echo $this->head."
      <div class='fluid-row'>
      <div class='span8'>
      <div class='alert alert-error'><strong>Error:</strong> No SPARQL/UPDATE server found. Please include it in <code>\$conf['updateendpoint']['local']</code> at <strong>settings.inc.php</strong></div>
      </div>
      </div>
      ".$this->foot;
    }else{
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
  }
  
  protected function deleteRDF(){
    global $conf;
    if(!isset($conf['updateendpoint']['local'])){
      echo $this->head."
      <div class='fluid-row'>
      <div class='span8'>
      <div class='alert alert-error'><strong>Error:</strong> No SPARQL/UPDATE server found. Please include it in <code>\$conf['updateendpoint']['local']</code> at <strong>settings.inc.php</strong></div>
      </div>
      </div>
      ".$this->foot;
    }else{
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
    }
    exit(0);
  }


  protected function editNamespaces(){
    global $conf;
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
      $nstable = "";
      foreach($conf['ns'] as $k=>$v){
       $nstable .= "<tr><td>".$k."</td><td id='$k'>".$v."</td><td><button class='button btn edit-button' data-prefix='$k' data-ns='$v'>Edit</button></tr>";
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
        $nstable .= "<tr><td>".$k."</td><td id='$k'>".$v."</td><td><button class='button btn edit-button' data-prefix='$k' data-ns='$v'>Edit</button></tr>";
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
      <input type='text' name='endpoint' id='endpoint' value='".$conf['endpoint']['local']."'/>
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
    $endpointOptions = "";
    foreach($conf['endpoint'] as $k => $v){
      $selected = "";
      if($k == "local")
        $selected = 'selected';
      $endpointOptions .= "<option $selected value='$k'>$k ($v)</option>";
    }
    $namespaces = "var ns = ".json_encode($conf['ns']);
    $lastComponentType="";
    $onlyService = false;
    foreach($output as $line){
      if($line == ""){
          $menu .= "</ul>\n";
      }else{
        if(preg_match("/^\w/", $line) ){
            $lastComponentType = trim($line);
            $singleLastComponentType = preg_replace('/(.*)s$/', '\1', $lastComponentType);
            $menu .= "<ul class='nav nav-list'>
              <li class='nav-header'>".$lastComponentType."  <button class='btn btn-mini btn-info new-button' style='float:right' data-type='$singleLastComponentType'>new</button></li>\n";
        }else{
          $componentName = trim($line);
            $menu .= "<li class='component-li'> <button type='button' class='close hide lodspk-delete-component' data-component-type='$singleLastComponentType' data-component-name='$componentName' style='align:left'>x</button>
          <a href='#$componentName' class='lodspk-component' data-component-type='$lastComponentType' data-component-name='$componentName'>".$componentName."</a></li>\n";
        }
      }
    }
    echo $this->head ."
    <script type='application/javascript'> 
    var home='".$conf['basedir']."';
    $namespaces
    </script>
    <div class='row-fluid'>
     <div class='span3 well'>$menu<div id='component-msg' class='alert hide'></div></div>
     <div class='bs-docs-template span9'>
      <textarea class='field span12' rows='8' cols='25' id='template-editor' name='template-editor'></textarea>
      <button class='btn btn-info disabled' id='template-save-button' data-url=''>Save</button>
      <div class='alert alert-success hide' id='template-msg'></div>
     </div>
    </div> 
    <div class='row-fluid'>
     <div class='span3'>
      <div class='container'>
       <div class='row-fluid'>
        <div class='span3 well'>
          <legend>Views  <!-- button class='btn btn-mini btn-info new-file-button hide new-file-button-view' data-component=''>new</button --></legend>
         <ul class='nav nav-list' id='template-list'>
         </ul>        
        </div>
       </div>
       <div class='row-fluid'>
        <div class='span3 well'>
          <legend>Models  <button class='btn btn-mini btn-info new-file-button hide new-file-button-model' data-component=''>new</button></legend>
         <ul class='nav nav-list' id='query-list'>
         </ul>
        </div>
       </div>
       <div class='row-fluid'>
        <div class='span3'>
         <p><a href='#' id='preview-button' class='hide'><button class='btn btn-success btn-large'>View component</button></a></p>
         <p><button id='embed-button' class='btn btn-success btn-large hide'>Embed component</button></p>
        </div>
       </div>
      </div>
     </div>
     <div class='span9  bs-docs-query'>
      <textarea class='field span12' rows='8' cols='25' id='query-editor'></textarea>
      <button class='btn btn-info disabled' id='query-save-button' data-url=''>Save</button>
      <select style='float:right' id='endpoint-list'>$endpointOptions</select>
      
      <button class='btn btn-success' style='float:right; margin-right:20px' id='query-test-button'>Test this query against</button>
      <div class='alert alert-success hide' id='query-msg'></div>
     </div>
     </div>
     <div class='row-fluid'>
      <div class='span12'>
       <h2>Query results preview</h2>
       <span class='alert alert-error hide' id='results-msg'></span>
       <table class='table' id='results'></table>
       <div style='height:300px'></div>
      </div>
     </div>
    </div>
   </div>
  </div>
    <script src='".$conf['basedir'] ."admin/codemirror/lib/codemirror.js'></script>
    <script src='".$conf['basedir'] ."admin/codemirror/lib/util/overlay.js'></script>
    <script src='".$conf['basedir'] ."admin/codemirror/mode/xml/xml.js'></script>
    <script src='".$conf['basedir'] ."admin/codemirror/mode/sparql/sparql.js'></script>
    <script src='".$conf['basedir'] ."admin/js/editor.js'></script>


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
  	case "create":
  	  if(sizeof($params) > 2){
  	    $this->createComponent($params);
  	  }else{
  	    HTTPStatus::send404($params[1]);
  	  }
  	  break;  	  
  	case "delete":
  	  if(sizeof($params) > 2){
  	    $this->deleteComponent($params);
  	  }else{
  	    HTTPStatus::send404($params[1]);
  	  }
  	  break;  	  
  	case "add":
  	  if(sizeof($params) > 2){
  	    $this->addFile($params);
  	  }else{
  	    HTTPStatus::send404($params[1]);
  	  }
  	  break;  	  
  	case "remove":
  	  if(sizeof($params) > 2){
  	    $this->deleteFile($params);
  	  }else{
  	    HTTPStatus::send404($params[1]);
  	  }
  	  break; 
  	case "query":
  	  $this->queryEndpoint($_POST);
  	  break;
  	default:
  	  HTTPStatus::send404($params[1]);
  	}
  }

  protected function queryEndpoint($data){
    global $endpoints;
    global $conf;
    $query = $data['query'];
    $endpoint = $data['endpoint'];
    if(isset($endpoint) && isset($conf['endpoint'][$endpoint])){
      if(!isset($endpoints[$endpoint])){
        $e = new Endpoint($conf['endpoint'][$endpoint], $conf['endpoint']['config']);  
      }else{
        $e = $endpoints[$endpoint];
      }
      $aux = $e->query($query, Utils::getResultsType($query));
      header("Content-type: ".$data['format']);
      $jaux = json_encode($aux);
      if(isset($jaux)){
        echo $jaux;
      }else{
        echo $aux;
        HTTPStatus::send404($params[1]);
      }
    }else{
     echo "no endpoint"; 
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
  
  protected function createComponent($params){
    $path = implode("/", array_slice($params, 1));
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
      if(sizeof($params) != 3){
        HTTPStatus::send404();
        exit(0);
      }
      $return_var = 0;
      exec ("utils/lodspk.sh create ".$params[1]." ".$params[2], &$output, $return_var);  
      //echo $return_var;exit(0);
      if($return_var !== 0){
        HTTPStatus::send500($params[0]." ".$params[1]);
      }else{
        echo json_encode(array('success' => true, 'size' => $result));          
      }
    }else{
      HTTPStatus::send406();
      exit(0);
    }
  }

  protected function deleteComponent($params){
    $path = implode("/", array_slice($params, 1));
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
      if(sizeof($params) != 3){
        HTTPStatus::send404();
        exit(0);
      }
      $return_var = 0;
      exec ("utils/lodspk.sh delete ".$params[1]." ".$params[2], &$output, $return_var);  
      if($return_var !== 0){
        HTTPStatus::send500($params[0]." ".$params[1]);
      }else{
        echo json_encode(array('success' => true, 'size' => $result));          
      }
    }else{
      HTTPStatus::send406();
      exit(0);
    }
  }
  
  protected function deleteFile($params){
    $path = "components/".implode("/", array_slice($params, 1));
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
      if(sizeof($params) < 3){
        HTTPStatus::send404();
        exit(0);
      }
      $return_var = 0;
      if(strpos($path, "components") === 0 && strpos($path, '..') === FALSE){
        exec ("rm ".$path, &$output, $return_var);  
        if($return_var !== 0){
          echo json_encode(array('success' => false, path => $path));          
        }else{
          echo json_encode(array('success' => true, path => $path));          
        }
      }else{
        HTTPStatus::send406();
        exit(0);
      }
    }else{
      echo json_encode(array('success' => false, path => $path));              
    }
  }
  
  
  protected function addFile($params){
    $path = "components/".implode("/", array_slice($params, 1));
    $basicContent = "SELECT * WHERE{
      ?s ?p ?o
    }LIMIT 10";
    if(strpos($path, ".template") !== FALSE){
      //It is not a query, but a template
      $basicContent = "<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv='Content-type' content='text/html; charset=utf-8'>
  </head>
  <body>
  </body>
</html>";
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
      if(sizeof($params) < 3){
        HTTPStatus::send404();
        exit(0);
      }
      $return_var = 0;
      if(file_exists($path)){
        echo json_encode(array('success' => false));
        return;
      }
      $dirpath=$path;
      $dirArray = explode("/", $path);
      array_pop($dirArray);
      $dirpath = implode("/", $dirArray);
      if(!is_dir($dirpath)){
        $oldumask = umask(0);
        $return_var = mkdir($dirpath, 0755, true);
        umask($oldumask);
        if($return_var === FALSE){
          HTTPStatus::send500("mkdir ".var_export($return_var, true)." ".$dirpath);
        }
      }
      $return_var = file_put_contents($path, $basicContent );

      //echo $return_var;exit(0);
      if($return_var === FALSE){
        HTTPStatus::send500("file_puts_content ".var_export($return_var, true)." ".$path);
      }else{
        echo json_encode(array('success' => true, 'return' => $return_var));          
      }
    }else{
      HTTPStatus::send406();
      exit(0);
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
    echo $this->head."
      <div class='well span5'>
            <h2>Components Editor</h2>
            <p>You can create, remove and edit components (services types, etc) using the <a href='components'>editor</a></p>
            <a href='components'><button class='btn btn-large btn-info'>Go to Editor</button></a>
</div>
      <div class='span5 well'>
      <h2>Options</h2>
      <p>You can also:</p>
      <ul>
        <li>Add, remove or <a href='namespaces'>edit namespaces</a></li>    
        <li>Add, remove or <a href='endpoints'>edit endpoints</a></li>    
      </ul>
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
