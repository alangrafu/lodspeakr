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
        $accept = 'application/sparql-results+json';
        if($output != null){
          $this->params['output'] = $output;
        }

        if($output == 'xml'){
          $accept = 'application/sparql-results+xml';
        }elseif($output == 'rdf'){
          $accept = 'application/rdf+xml';
        }
        $c = curl_init();
        $context = array();
        $context[0] = 'Connection: close';
        $context[1] = 'Accept: '.$accept;
        $params = $this->params;
        $params['query'] = $q;
        $url = $this->sparqlUrl.'?'.http_build_query($params, '', '&');

        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_HTTPHEADER, $context);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        $aux = curl_exec($c); // execute the curl command 
        curl_close($c);
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
