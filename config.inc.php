<?

include_once('settings.inc.php');


function query($q){
  global $conf;
  $endpoint = $conf['endpoint']['host'];
  $params = $conf['endpoint']['config'];
  $params['query'] = $q;
  $url = $endpoint.'?'.http_build_query($params, '', '&');
  return json_decode(file_get_contents($url), true);
}


function getClass($uri){
  $q = "SELECT DISTINCT ?class WHERE{
          <$uri> a ?class .
        } LIMIT 1";
        $r = query($q);
        if(sizeof($r['results']['bindings'])>0){
          return $r['results']['bindings'][0]['class']['value'];
        }
        return NULL;
}

function uri2curie($uri){
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


function getTemplate($uri){
  $filename = str_replace(":", "_", $uri);
  if(file_exists ($filename)){
    include_once($filename);
  }
}

function send404($uri){
  header("HTTP/1.0 404 Not Found");
  echo "I could not find ".$uri." or information about it";
  exit(0);
}

function showView($uri, $body){
  global $conf;
  $html = preg_replace("|".$conf['resource']['url_delimiter']."|", $uri, $body);
  echo $html;
}
