<?php

class Logging{
  
  public static function init(){
    $logs = array();
    if ($handle = opendir('cache/')) {
      while (false !== ($entry = readdir($handle))) {
        if (strpos($entry, ".log") == strlen($entry)-4) {
            $logs[] = $entry;
        }
      }
    closedir($handle);
    }
    sort($logs);
    $alogs = "";
    foreach($logs as $v){
      $alogs .= "<p><a href='#lodspeakr/cache/$v'>$v</a></p>";
    }
    echo "
<!DOCTYPE html>
<html>
 <head>
  <meta charset='UTF-8'>
   <meta name='viewport' content='width=device-width, initial-scale=1.0'>
   <link href='css/bootstrap.min.css' rel='stylesheet' type='text/css' media='screen' />
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href='css/bootstrap-responsive.min.css' rel='stylesheet' type='text/css' media='screen' />
    <script type='text/javascript' src='js/jquery.js'></script>
    <script type='text/javascript' src='js/bootstrap.min.js'></script>   
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
      <a class='brand' href='logs'>Logs</a>
      <div class='nav-collapse'>
       <ul class='nav'>
        <li class='active'><a href='#'>Home</a></li>
       </ul>
      </div><!--/.nav-collapse -->
     </div>
    </div>
   </div>
   <div class='row'>
    <div class='span4 well'>
     <h3>Logs</h3>
     $alogs
    </div>
    <div class='span8' id='log'>
    </div>
   </div>
   <script>
     $('a').on('click', function(){
       var link = $(this).attr('href').replace('#', '');
       $.ajax({
         url: link,
         dataType: 'json',
         success: function(data){
           var pres = '';
           $.each(data.logs, function(i, item){
             pres += '<h4>'+item.timestamp+'</h4><pre>'+item.message+'</pre>';
           });
           $('#log').html(pres);
         }
       });
     });
   </script>
  </body>
</html>";
  }
  public static function log($msg){
    global $conf;
    $log = array('timestamp' => time());
    $log['message'] = $msg;
    if($conf['logfile'] != null){
      fwrite($conf['logfile'], ", ".json_encode($log));
    }
  }
}
