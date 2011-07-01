<?php
/* ARC2 static class inclusion */ 
include_once('arc2/ARC2.php');
set_time_limit(0);
/* MySQL and endpoint configuration */ 
$config = array(
  /* db */
  'db_host' => 'localhost', /* optional, default is localhost */
  'db_name' => 'slodp',
  'db_user' => 'sl', 
  'db_pwd' =>  'ls',

  /* store name */
  'store_name' => 'my_endpoint_store',

  /* endpoint */
  'endpoint_features' => array(
    'select', 'construct', 'ask', 'describe', 
     'insert','load', 'delete', 
    'dump' /* dump is a special command for streaming SPOG export */
  ),
  'endpoint_timeout' => 60, /* not implemented in ARC2 preview */
  'endpoint_read_key' => '', /* optional */
  'endpoint_write_key' => '5A9av7zuDA3', /* optional */
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

