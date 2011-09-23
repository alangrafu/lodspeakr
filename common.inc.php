<?

$conf['endpoint']['select']['output'] = 'json';
$conf['endpoint']['describe']['output'] = 'rdf';
$conf['endpoint']['config']['output'] = $conf['endpoint']['select']['output'];
$conf['endpoint']['config']['named_graph'] = '';
$conf['endpoint']['config']['show_inline'] = 0;
//ALternative endpoints
$conf['endpoint']['dbpedia'] = 'http://dbpedia.org/sparql';
$conf['endpoint']['logd'] = 'http://logd.tw.rpi.edu/sparql';

$conf['metadata']['db']['location'] = 'meta/db.sqlite';

include_once('namespaces.php');

$conf['model']['directory'] = 'models/'; #include trailing slash!
$conf['model']['extension'] = '.model';
$conf['model']['default'] = 'default';

$conf['view']['directory'] = 'views/'; #include trailing slash!
$conf['view']['extension'] = '.view';
$conf['view']['default'] = 'default';

$conf['resource']['url_delimiter'] = "%u";

$conf['http_accept']['html'] = array('text/html');  
$conf['http_accept']['rdf']  = array('application/rdf+xml');
$conf['http_accept']['ttl']  = array('text/n3', 'application/x-turtle', 'application/turtle', 'text/turtle');
$conf['http_accept']['nt']   = array('text/plain');


$conf['special']['uri'] = 'special';
$conf['special']['class'] = 'classes/BasicSpecialFunction.php';

//Frontpage when user goes to http://example.org/
$conf['root']['url'] = 'special/index';

//Debug
$conf['debug'] = false;

include_once('settings.inc.php');
$conf['view']['standard']['baseUrl'] = $conf['basedir'];

?>
