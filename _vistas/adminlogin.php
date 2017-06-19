<?php
require_once './_vistas/layout.php';
class AdminLogin{

    public  function __construct()
	{
	    $layout  = new Layout();
		echo $layout->loginAdmin($this->viewHome());
	}

	public function viewHome(){
		
		$layout  = new Layout();
		return $layout->render('./_vistas/loginAdmin.phtml');
		
	}		

}