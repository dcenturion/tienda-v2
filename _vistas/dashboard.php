<?php
session_start();
require_once './_vistas/layout.php';

class Dashboard{

    public  function __construct(){
		
		$empresa = $_SESSION['empresa'];
		$user = $_SESSION['user'];
			
	    $layout  = new Layout();
		echo $layout->dashboard($this->viewHome());
	}

	public function viewHome(){
		$layout  = new Layout();
		return $layout->render('./_vistas/admin_indicadores_hoy.phtml');
	}		

}