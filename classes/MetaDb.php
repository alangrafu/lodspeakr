<?php


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
				print 'Exception query : '.$e->getMessage()."\nDatabase used:".$this->dbLocation."\nPlease check the webserver has write permissions to the file AND the directory, as well as you have installed the driver for PHP and SQLite";
				exit(10);
		}
        return $results;
  }
  
   public function write($q){
        global $conf;
        try{
	    	$db = new PDO('sqlite:'.$this->dbLocation);
	    	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    	$results = $db->exec($q);
			$db = NULL;
		}catch(Exception $e){
				echo "Can't write in SQLite database. Please check you have granted write permissions to <tt>meta/</tt> and <tt>meta/db.sqlite</tt>.<br/>Also you can check a list of <a href='https://github.com/alangrafu/lodspeakr/wiki/CommonErrors'>common errors</a> and how to <a href='https://github.com/alangrafu/lodspeakr/wiki/Wipe-out-the-database'>wipe out the database</a>'";
		  		trigger_error('Exception exec: '.$e->getMessage(), E_USER_ERROR);
				exit(1);
		}
        return $results;
  }
  
}

?>
