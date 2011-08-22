<?
include_once('settings.inc.php');

$conf['endpoint']['select']['output'] = 'json';
$conf['endpoint']['describe']['output'] = 'rdf';
$conf['endpoint']['config']['output'] = $conf['endpoint']['select']['output'];
$conf['endpoint']['config']['named_graph'] = '';
$conf['endpoint']['config']['show_inline'] = 0;

$conf['metadata']['db']['location'] = 'meta/db.sqlite';

include_once('namespaces.php');

$conf['model']['directory'] = 'models/'; #include trailing slash!
$conf['model']['extension'] = '.model';
$conf['model']['default'] = 'default';

$conf['view']['directory'] = 'views/'; #include trailing slash!
$conf['view']['extension'] = '.view';
$conf['view']['default'] = 'default';
$conf['view']['standard']['baseUrl'] = $conf['basedir'];

$conf['resource']['url_delimiter'] = "%u";

$conf['http_accept']['html'] = array('text/html');  
$conf['http_accept']['rdf']  = array('application/rdf+xml');
$conf['http_accept']['ttl']  = array('text/n3', 'application/x-turtle', 'application/turtle', 'text/turtle');
$conf['http_accept']['nt']   = array('text/plain');

?>
