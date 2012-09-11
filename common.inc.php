<?php

$conf['version'] = '20120704';
$conf['output']['select'] = 'json';
$conf['output']['ask'] = 'json';
$conf['output']['describe'] = 'rdf';
$conf['endpointParams']['config']['show_inline'] = 0;
$conf['endpointParams']['config']['named_graph'] = '';
//ALternative endpoints
$conf['endpoint']['dbpedia'] = 'http://dbpedia.org/sparql';
$conf['endpoint']['logd'] = 'http://logd.tw.rpi.edu/sparql';

$conf['metadata']['db']['location'] = 'meta/db.sqlite';
$conf['httpStatus']['directory'] = 'components/status';

include_once('namespaces.php');

$conf['model']['directory'] = 'components'; #include trailing slash!
$conf['model']['extension'] = '.model';
$conf['model']['default'] = 'rdfs:Resource';

$conf['view']['directory'] = 'components'; #include trailing slash!
$conf['view']['extension'] = '.view';
$conf['view']['default'] = 'rdfs:Resource';       

$conf['static']['directory'] = 'components/static/'; #include trailing slash!
$conf['static']['haanga'] = false; //Should static files be processed by Haanga? 

$conf['resource']['url_delimiter'] = "%u";

$conf['http_accept']['html'] = array('text/html');  
$conf['http_accept']['rdf']  = array('application/rdf+xml');
$conf['http_accept']['ttl']  = array(
  'text/n3', 'application/x-turtle', 'application/turtle', 'text/turtle');
$conf['http_accept']['json'] = array('application/json', 'application/x-javascript', 'text/javascript', 'text/x-javascript', 'text/x-json');
$conf['http_accept']['nt']   = array('text/plain');


$conf['service']['prefix']      = 'services';
$conf['type']['prefix']         = 'types';
$conf['uri']['prefix']          = 'uris';
$conf['redirect']['prefix']     = 'redirect';
$conf['sparqlFilter']['prefix']     = 'sparqlFilter';
$conf['sparqlFilter']['filterFileName']     = 'filter.query';

//Frontpage when user goes to http://example.org/
$conf['root'] = 'index.html';
$conf['extension_connector'] = '.';

//Priority for rdfs:Resource (default). Priorities should be >=0
$conf['type']['priorities']['rdfs:Resource'] = -1;

//Debug
$conf['debug'] = false;

//Session module
//First version: really simple user/pass
$conf['session']['user'] = 'admin';
$conf['session']['password'] = 'admin';


//Modules: LODSPeaKr will try to match the requested URI
//using the modules in the following order
$conf['modules'] = array();
$conf['modules']['directory'] = 'classes/modules/';

$conf['modules']['available'] = array('static','uri', 'type', 'service');


//To add sparqlFilter module, copy the following line in your settings.inc.php
//$conf['modules']['available'] = array('static','uri', 'sparqlFilter', 'type', 'service');

//Uncomment next line to enable sessions
//$conf['modules']['available'] = array('session', 'static','uri', 'type', 'service');


global $lodspk;
include_once('settings.inc.php');
$conf['view']['standard']['baseUrl'] = $conf['basedir'];
?>
