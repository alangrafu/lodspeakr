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
        curl_setopt($c, CURLOPT_USERAGENT, "LODSPeaKr version ".$conf['version']);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        $aux = curl_exec($c); // execute the curl command 
        if($conf['debug']){
          if($aux == false){
          	trigger_error("Error executing SPARQL query (".$this->sparqlUrl."): ".curl_error($c), E_USER_ERROR);
          	echo("Error executing SPARQL query (".$this->sparqlUrl."): ".curl_error($c));
          }
        }
        curl_close($c);
        $this->params['output'] = $auxoutput;
        if(preg_match("/select/i", $q)){
          $r = json_decode($aux, true);
          if($conf['debug']){
          	if($r == false){
          	  trigger_error("Warning: Results from a SELECT sparql query couldn't get parsed", E_USER_WARNING);
          	  echo("Warning: Results from a SELECT sparql query couldn't get parsed");
          	}
          }
          return $r;
        }
        if(preg_match("/describe/i", $q)){
          return $aux;
        }
        if(preg_match("/construct/i", $q)){
          return $aux;
        }
        if(preg_match("/ask/i", $q)){
          $r = json_decode($aux, true);
          return $r;
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
  
  public function getSparqlURL(){
  	return $this->sparqlUrl;
  }
  
}

?>
