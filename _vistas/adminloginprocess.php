<?php
session_start();
require_once('./_librerias/php/conexiones.php');
require_once('./_librerias/php/funciones.php');

class AdminLoginProcess{
    public  function __construct()
	{
		echo $this->viewHome();
	}

	public function viewHome() {

		$cnOwlPDO = PDOConnection();
		$FechaHoraSrv = FechaHoraSrv();
		
		$username = post("username");
		$password = post("password");
	
		$sql = " SELECT USU.Codigo, ENT.Usuario FROM usuarios USU 
		INNER JOIN entidades ENT ON USU.Entidades = ENT.Codigo
		WHERE ENT.Usuario = '{$username}' AND ENT.Clave = '{$password}' AND USU.Entidades_suscriptor = 25 ";
		$rg = fetch($sql,$cnOwlPDO);
		$Codigo = $rg["Codigo"];
        
	    if($Codigo){
			
			$_SESSION['empresa']= 25;
			$_SESSION['user']= $Codigo;
			
			
		    $viewdata = array();
			$viewdata['mensaje'] = "correcto";		
			return json_encode($viewdata);
			
		}else{
		    $viewdata = array();
			$viewdata['mensaje'] = "falso";		
			return json_encode($viewdata);
	   

		}		
	}		

	

	

}