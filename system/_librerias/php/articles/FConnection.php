<?php
/**
* fyupanquia
* status conection
*/
class FConnection 
{
	private $svr ;
	private $nam ;
	private $usr ;
	private $pss ;
	private $rsp ;

	public function __construct($obj)
	{
		$this->svr = $obj->server;
		$this->nam = $obj->name;
		$this->usr = $obj->user;
		$this->pss = $obj->password;
	}

	public function connect(){
		
		 try {
			$pdo = new PDO("mysql:host=".$this->svr.";dbname=".$this->nam."", $this->usr , $this->pss);
			$this->rsp = true;
		 } catch (PDOException $e) {
		 	$this->rsp = false;
    	 }	

    	return 	$pdo;
	}	

}


