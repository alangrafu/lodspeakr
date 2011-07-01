<?

include_once('config.inc.php');

$uri =  $conf['basedir'].$_GET['q'];


$curieType = uri2curie(getClass($uri));

$modelFile = $conf['model']['directory'].$curieType.$conf['model']['extension'];

if(!file_exists($modelFile)){
  $modelFile = $conf['model']['directory'].$conf['model']['default'].$conf['model']['extension'];
}
include_once($modelFile);
$query = preg_replace("|".$conf['resource']['url_delimiter']."|", "<".$uri.">", $resource['params']['query']);
$results = query($query);
//var_dump($results);
if(sizeof($results['results']['bindings']) == 0){
  send404($uri);
}

$viewFile = $conf['view']['directory'].$curieType.$conf['view']['extension'];
if(!file_exists($viewFile)){
  $viewFile = $conf['view']['directory'].$conf['view']['default'].$conf['view']['extension'];
}
include_once($viewFile);
showView($uri, $resource['view']['body']);

exit(0);
/*$result = query("SELECT * WHERE{ <$uri> ?p ?o } LIMIT 10");
if(isset($result['results']['bindings'])){
foreach($result['results']['bindings'] as $k => $v){
if($v['o']['type'] == 'literal'){
print $v['p']['value']." => ".uri2curie($v['o']['value'])."<br/>";
}else{
print $v['p']['value']." => <a href='".$v['o']['value']."'>".uri2curie($v['o']['value'])."</a><br/>";
}
}
}*/
?>
