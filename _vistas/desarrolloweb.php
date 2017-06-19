<?php

require_once './_vistas/layout.php';

class DesarrolloWeb{

	

    public  function __construct()

	{

	    $layout  = new Layout();

		echo $layout->mainB($this->viewHome());

	}

	

	public function viewHome() {

	

		$layout  = new Layout();

		$datos = array();

        $datos['numeros'] = "996 614 532 / 999 777 768 ";

        $datos['direccion'] = "Valladolid 144 - La molina ";

        $datos['email'] = "ventas@flexzinn.com";

		$layout->formContacto = $layout->render("./_vistas/form_contactos.phtml",$datos); 

		return $layout->render('./_vistas/desarrolloweb.phtml');

		

	}		

	

	public function formContacto($arg) {

		

		return $arg;

	}		

	

}