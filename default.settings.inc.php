<?php

# Where is LODSPeaKr's root located? (don't include word 'lodspeakr')
$conf['basedir'] = 'http://foo/bar/'; #include final slash

# What is the namespace of your data?
$conf['ns']['local']   = 'http://foo/bar/data/';
# If you want to add/override a namespace, add it here
$conf['ns']['other']   = 'http://example.org/data/';

# Where is your SPARQL endpoint
$conf['endpoint']['host'] = 'http://myendpoint/sparql';

$conf['home'] = '/Users/alvarograves/github/lodspeakr/'; #change to the location of LODSPeaKr in the dir tree

$conf['debug'] = false; #Ugly dump of queries and values obtained
$conf['mirror_external_uris'] = false; #TRUE is local namespace != basedir

/*ATTENTION: By default this application is available to
 * be exported and copied (its configuration)
 * by others. If you do not want that, 
 * turn the next option as false
 */ 
$conf['export'] = true;


?>
