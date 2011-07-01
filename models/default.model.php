<?
$resource['params']['query'] = <<<QUERYSTRING
SELECT ?s1 ?p1 ?s2 ?p2 WHERE {
	{
		%u ?s1 ?p1
	}UNION{
		?s2 ?p2 %u
	}
}
QUERYSTRING;

?>


