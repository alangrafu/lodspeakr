<?php
/* ARC2 static class inclusion */ 
include_once('lib/arc2/ARC2.php');
include_once('settings.inc.php');
set_time_limit(0);

/* MySQL and endpoint configuration */ 
$config = array(
  /* db */
  'db_host' => $conf['metaendpoint']['config']['host'],
  'db_name' => $conf['metaendpoint']['config']['dbname'],
  'db_user' => $conf['metaendpoint']['config']['user'], 
  'db_pwd' =>  $conf['metaendpoint']['config']['pass'],

  /* store name */
  'store_name' => 'my_endpoint_store',

  /* endpoint */
  'endpoint_features' => array(
    'select', 'construct', 'ask', 'describe', 
     'insert','load', 'delete', 
    'dump' /* dump is a special command for streaming SPOG export */
  ),
  'endpoint_timeout' => 60, /* not implemented in ARC2 preview */
  'endpoint_read_key' => '', 
  'endpoint_write_key' => $conf['metaendpoint']['config']['key'], 
  'endpoint_max_limit' => 0 
);

/* instantiation */
$ep = ARC2::getStoreEndpoint($config);

if (!$ep->isSetUp()) {
  $ep->setUp(); /* create MySQL tables */
}

/* request handling */
$ep->go();

?>

