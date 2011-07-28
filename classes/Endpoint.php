<?

class Endpoint{
  private $sparqlUrl;
  private $params; 
  
  public function __construct($sparqlUrl, $params){
  	$this->sparqlUrl = $sparqlUrl;
  	$this->params = $params;
  }
  
  public function query($q, $output = 'json'){
  	global $conf;
  	$auxoutput = $this->params['output'];
  	if($output != null){
  	  $this->params['output'] = $output;
    }
  	$context = stream_context_create(array(
  	  'http' => array('header'=>'Connection: close')));
  	$params = $this->params;
  	$params['query'] = $q;
  	$url = $this->sparqlUrl.'?'.http_build_query($params, '', '&');
  	$aux = file_get_contents($url, false,$context);
  	
  	$this->params['output'] = $auxoutput;
  	
  	if(preg_match("/select/i", $q)){
  	  return json_decode($aux, true);
  	}
  	if(preg_match("/describe/i", $q)){
  	  return $aux;
  	}  	
  }
  
  public function queryPost($q){
  	$params =  $this->params;
  	$params['query'] = $q;
  	$ch = curl_init();
  	curl_setopt($ch,CURLOPT_URL,$this->sparqlUrl);
  	curl_setopt($ch,CURLOPT_POST,count($params));
  	curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($params));
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  	//execute post
  	$result = curl_exec($ch);
  	return $result;
  }
  
}

?>
