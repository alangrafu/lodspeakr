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
    $list = array();
    foreach($logs as $v){
      $newtokens = array( ".", "/");
      $oldtokens = array("___DOT___", "___SLASH___");
      $label = str_replace($oldtokens, $newtokens, $v);
      $x = explode("_", $label);
      $y = array_shift($x);
      array_shift($x);
      if(!isset($list[$y])){
        $list[$y] = array();
      }
      $list[$y][] = array("name" => implode("_", $x), "url" => $v);
      //$alogs .= "<p><a href='#lodspeakr/cache/$v'>$label</a></p>";
    }
    ksort($list);
    foreach($list as $k => $v){
      $alogs .= "<li>".date("H:i:s", $k)."<ul>\n";
      foreach($v as $w){
        $alogs .= " <li><a href='#lodspeakr/cache/".$w['url']."'>".$w['name']."</a></li>\n";
      }
      $alogs .="</ul></li>";
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
      .bold{
        font-weight: bold;
        font-size: 120%;
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
     <!--div>
      <select id='level'>
       <option value='0'>All</option>
       <option value='1'>Notice</option>
       <option value='2'>Error</option>
      </select>
     </div-->
     <ul>
     $alogs
     </ul>
     
    </div>
    <div class='span8' id='log'>
    </div>
   </div>
   <script>
     $('a').on('click', function(e){
      $('a').removeClass('bold');
      $(e.target).addClass('bold');
       var link = $(this).attr('href').replace('#', '');
       $.ajax({
         url: link,
         dataType: 'json',
         success: function(data){
           var pres = '';
           $.each(data.logs, function(i, item){
             var date = new Date(item.timestamp * 1000), 
                 dateFormatted = date.getFullYear()+'/'+
                                 (date.getMonth()+1)+'/'+
                                 date.getDate()+' '+
                                 date.getHours()+':'+
                                 date.getMinutes()+':'+
                                 date.getSeconds();
             pres += '<h4>On '+dateFormatted+'</h4><pre>'+item.message+'</pre>';
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
  
  public static function createLogFile($url){
    $oldtokens = array( ".", "/");
    $newtokens = array("___DOT___", "___SLASH___");
    $filename = str_replace($oldtokens, $newtokens, $url);
    $logfile = fopen("cache/".time()."_".rand()."_".$filename.".log", "w");
    if($logfile === FALSE){
      die("Can't create log file. Check permissions in <tt>cache/</tt> directory.");
    }
    $initialmsg = array('timestamp' => time(), 'message' => "Starting log for ".$url);
    fwrite($logfile, "{ \"logs\": [".json_encode($initialmsg));

    return $logfile;
  }
}
