<?

$conf['endpoint']['host'] = 'http://myendpoint/sparql/';
$conf['endpoint']['config']['output'] = 'json';
$conf['endpoint']['config']['named_graph'] = '';
$conf['endpoint']['config']['show_inline'] = 0;
$conf['basedir'] = 'http://mybaseurl/';

$conf['metaendpoint']['host'] = 'http://mymetadataendpoint/sparql';
$conf['metaendpoint']['config']['output'] = 'json';
$conf['metaendpoint']['config']['show_inline'] = 0;
$conf['metaendpoint']['config']['key'] = 'keyForExecutePostInARC2';
$conf['metaendpoint']['config']['named_graph'] = 'http://lodspeakr.org/metadata';


$conf['ns']['rdf']     = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
$conf['ns']['rdfs']    = 'http://www.w3.org/2000/01/rdf-schema#';
$conf['ns']['dcterms'] = 'http://purl.org/dc/terms/';
$conf['ns']['foaf']    = 'http://xmlns.com/foaf/0.1/';
$conf['ns']['skos']    = 'http://www.w3.org/2004/02/skos/core#';
$conf['ns']['og']      = 'http://opengraphprotocol.org/schema/';
$conf['ns']['owl']     = 'http://www.w3.org/2002/07/owl#';
$conf['ns']['local']   = 'http://shai-hulud/testCMS/';
$conf['ns']['ov']      = 'http://open.vocab.org/terms/';   
$conf['ns']['conv']    = 'http://purl.org/twc/vocab/conversion/';

$conf['model']['directory'] = 'models/'; #include trailing slash!
$conf['model']['extension'] = '.model';
$conf['model']['default'] = 'default';

$conf['view']['directory'] = 'views/'; #include trailing slash!
$conf['view']['extension'] = '.view';
$conf['view']['default'] = 'default';
$conf['view']['standard']['baseUrl'] = $conf['basedir'];

$conf['resource']['url_delimiter'] = "%u";

$conf['http_accept']['html'] = array('text/html', 'application/xhtml');  
$conf['http_accept']['rdf']  = array('application/rdf+xml');
$conf['http_accept']['ttl']  = array('text/n3', 'application/turtle', 'application/turtle', 'text/turtle');
$conf['http_accept']['nt']   = array('text/plain');
?>
