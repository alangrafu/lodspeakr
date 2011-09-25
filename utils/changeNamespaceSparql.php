<?
/*
Simple proxy Sparql changer 
for "translating" the query and results from one namespace to another.

For example:

Imagine you want installed LODSpeaKr in

http://example.org/data

But you want to test it with data from 

http://another.com/mydata

Clearly, LODSPeaKr can't handle those URIs since they are in another namespace (and probably server). What you need then is that for any URI of the form

http://another.com/mydata/thisIsAnUri

you want to have 

http://example.com/mydata/thisIsAnUri

This script acts like a proxy to the sparql endpoint where the original data is located and changes de namespaces properly, so from your point of view you have 
data in your domain, without the need to download it from the original source and change it.

Since it is very simple, it may be prone to errors, but so far it is usable.



*/

//The remote namespace
$remote = 'http://originalNamespace.com/data/';

//The local namespace (i.e., the one you want to resolve
$local = 'http://localNamespace.com/mydata/';

//The SPARQL endpoint where the data is located
$url = 'http://example.org/sparql';

$newget = array();
foreach($_GET as $k => $v){
  $newget[$k] = preg_replace("|$local|", $remote, $v);
}
$url .= '?';
$r = file_get_contents($url.http_build_query($newget));


echo preg_replace("|$remote|", $local, $r);

?>
