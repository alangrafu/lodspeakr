<?
$conf['version'] = '20120411';
$conf['output']['select'] = 'json';
$conf['output']['describe'] = 'rdf';
$conf['endpointParams']['config']['show_inline'] = 0;
$conf['endpointParams']['config']['named_graph'] = '';
//ALternative endpoints
$conf['endpoint']['dbpedia'] = 'http://dbpedia.org/sparql';
$conf['endpoint']['logd'] = 'http://logd.tw.rpi.edu/sparql';

$conf['metadata']['db']['location'] = 'meta/db.sqlite';

include_once('namespaces.php');

$conf['model']['directory'] = 'components'; #include trailing slash!
$conf['model']['extension'] = '.model';
$conf['model']['default'] = 'rdfs:Resource';

$conf['view']['directory'] = 'components'; #include trailing slash!
$conf['view']['extension'] = '.view';
$conf['view']['default'] = 'rdfs:Resource';

$conf['static']['directory'] = 'static/'; #include trailing slash!
$conf['static']['haanga'] = false; //Should static files be processed by Haanga? 

$conf['resource']['url_delimiter'] = "%u";

$conf['http_accept']['html'] = array('text/html');  
$conf['http_accept']['rdf']  = array('application/rdf+xml');
$conf['http_accept']['ttl']  = array(
  'text/n3', 'application/x-turtle', 'application/turtle', 'text/turtle');
$conf['http_accept']['json'] = array('application/json', 'application/x-javascript', 'text/javascript', 'text/x-javascript', 'text/x-json');
$conf['http_accept']['nt']   = array('text/plain');


$conf['service']['prefix'] = 'services';

$conf['type']['prefix'] = 'types';
$conf['uri']['prefix'] = 'uris';

//Frontpage when user goes to http://example.org/
$conf['root'] = 'index.html';
$conf['extension_connector'] = '.';

//Priority for rdfs:Resource (default). Priorities should be >=0
$conf['type']['priorities']['rdfs:Resource'] = -1;

//Debug
$conf['debug'] = false;

//Modules: LODSPeaKr will try to match the requested URI
//using the modules in the following order
$conf['modules'] = array();
$conf['modules']['directory'] = 'classes/modules/';
$conf['modules']['available'] = array('static','uri', 'type', 'service');
global $lodspk;
include_once('settings.inc.php');
$conf['view']['standard']['baseUrl'] = $conf['basedir'];
?>
