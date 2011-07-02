<?

$conf['endpoint']['host'] = 'http://graves.cl/testCMS/endpoint.php';
$conf['endpoint']['config']['output'] = 'json';
$conf['endpoint']['config']['show_inline'] = 0;
$conf['basedir'] = 'http://graves.cl/testCMS/';



$conf['ns']['rdf']     = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
$conf['ns']['rdfs']    = 'http://www.w3.org/2000/01/rdf-schema#';
$conf['ns']['dcterms'] = 'http://purl.org/dc/terms/';
$conf['ns']['foaf']    = 'http://xmlns.com/foaf/0.1/';
$conf['ns']['skos']    = 'http://www.w3.org/2004/02/skos/core#';
$conf['ns']['og']      = 'http://opengraphprotocol.org/schema/';
$conf['ns']['owl']     = 'http://www.w3.org/2002/07/owl#';
$conf['ns']['test']     = 'http://graves.cl/testCMS/';


$conf['model']['directory'] = 'models/'; #include trailing slash!
$conf['model']['extension'] = '.model.php';
$conf['model']['default'] = 'default';

$conf['view']['directory'] = 'views/'; #include trailing slash!
$conf['view']['extension'] = '.view.php';
$conf['view']['default'] = 'default';


$conf['resource']['url_delimiter'] = '%u';

?>
