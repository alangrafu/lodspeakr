<?


class MetaDb{
  private $dbLocation;
  
  public function __construct($location){
  	$this->dbLocation = $location;
  }
  
   public function query($q){
        global $conf;
        try{
	        $db = new PDO('sqlite:'.$this->dbLocation);
	        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	        $results = array();
	        foreach($db->query($q) as $entry) {
	        	array_push($results, $entry);
	        }
			$db = NULL;
		}catch(PDOException $e){
				print 'Exception query : '.$e->getMessage()."\n".$this->dbLocation."\n";
				exit(10);
		}
        return $results;
  }
  
   public function write($q){
        global $conf;
        try{
	    	$db = new PDO('sqlite:'.$this->dbLocation);
	    	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	        $results = $db->exec($q);
			$db = NULL;
		}catch(PDOException $e){
				print 'Exception exec: '.$e->getMessage()."\n\n";
				exit(10);
		}
        return $results;
  }
  
}

?>
