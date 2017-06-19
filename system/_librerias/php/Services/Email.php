<?php 

require_once('../funciones.php');

/**
* 
*/
class Email
{
	private $accountname = "";
	private $email = "";
	private $matter = "";
	private $method = "";
	private $filenamePDF = "";
	private $content = "";

	function __construct($params)
	{
		$this->accountname = $params["accountname"];
		$this->email 	   = $params["email"];
		$this->matter      = $params["matter"];
		$this->method      = $params["method"];
		$this->body        = $params["body"];
	}

	function execute(){
		try {
			$_method = $this->method;
			$_method($this->accountname, $this->email, $this->matter, $this->body, $this->filenamePDF, $this->content);
		} catch (Exception $e) {
			echo "Email -> Se ha presentado un error : ".$e->message();
		}
	}
}


	$params["accountname"] 	= $_POST["accountname"];
	$params["email"] 		= $_POST["email"];
	$params["matter"] 		= $_POST["matter"];
	$params["method"] 		= $_POST["method"];
	$params["body"] 		= $_POST["body"];
	$params["class"] 		= $_POST["class"];

	if ($params["class"]!=null && $params["class"]!="") {
		$objEmail = new Email($params);
		$objEmail->execute();
	}