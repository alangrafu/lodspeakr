<?

class Endpoint{
  private $sparqlUrl;
  private $params; 
  
  public function __construct($sparqlUrl, $params){
  	$this->sparqlUrl = $sparqlUrl;
  	$this->params = $params;
  }
  
  public function query($q){
  	$context = stream_context_create(array(
  	  'http' => array('header'=>'Connection: close')));
  	$params = $this->params;
  	$params['query'] = $q;
  	$url = $this->sparqlUrl.'?'.http_build_query($params, '', '&');
  	$aux = file_get_contents($url, false,$context);
  	return json_decode($aux, true);
  }
  
  
}

?>
