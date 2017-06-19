<?php
session_start();
require_once('./_librerias/php/conexiones.php');
require_once('./_librerias/php/funciones.php');

class AdminDeleteSession{
	private $_parm;
    public  function __construct($_parm=null)
	{
		$value = $_parm["valor"];
		if($value){
			unset($_SESSION["empresa"]);
			unset($_SESSION["user"]);
			echo "<script>
				window.location.href = '/adminloginview';
			</script>";						
		}

		
	}

}



?>

