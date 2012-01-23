<?

$conf['output']['select'] = 'json';
$conf['output']['describe'] = 'rdf';
$conf['endpointParams']['config']['show_inline'] = 0;
$conf['endpointParams']['config']['named_graph'] = '';
//ALternative endpoints
$conf['endpoint']['dbpedia'] = 'http://dbpedia.org/sparql';
$conf['endpoint']['logd'] = 'http://logd.tw.rpi.edu/sparql';

$conf['metadata']['db']['location'] = 'meta/db.sqlite';

include_once('namespaces.php');

$conf['model']['directory'] = 'models/'; #include trailing slash!
$conf['model']['extension'] = '.model';
$conf['model']['default'] = 'rdfs:Resource';

$conf['view']['directory'] = 'views/'; #include trailing slash!
$conf['view']['extension'] = '.view';
$conf['view']['default'] = 'rdfs:Resource';

$conf['static']['directory'] = 'static/'; #include trailing slash!

$conf['resource']['url_delimiter'] = "%u";

$conf['http_accept']['html'] = array('text/html');  
$conf['http_accept']['rdf']  = array('application/rdf+xml');
$conf['http_accept']['ttl']  = array(
  'text/n3', 'application/x-turtle', 'application/turtle', 'text/turtle');
$conf['http_accept']['json'] = array('application/json', 'application/x-javascript', 'text/javascript', 'text/x-javascript', 'text/x-json');
$conf['http_accept']['nt']   = array('text/plain');


$conf['special']['uri'] = 'special';
$conf['special']['class'] = 'classes/BasicSpecialFunction.php';

//Frontpage when user goes to http://example.org/
$conf['root'] = 'index.html';
$conf['extension_connector'] = '.';

//Priority for rdfs:Resource (default). Priorities should be >=0
$conf['types']['priorities']['rdfs:Resource'] = -1;

//Debug
$conf['debug'] = false;

//Modules: LODSPeaKr will try to match the requested URI
//using the modules in the following order
$conf['modules'] = array();
$conf['modules']['directory'] = 'modules/';
$conf['modules']['available'] = array('class', 'service');

include_once('settings.inc.php');
$conf['view']['standard']['baseUrl'] = $conf['basedir'];
?>
