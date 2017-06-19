<?php
require_once('./_librerias/php/conexiones.php');
require_once('./_librerias/php/funciones.php');

class Contactos{
	private $_parm;	
    public  function __construct($_parm=null)
	{   
	    $id = $_parm["id"];
	    $site = $_parm["site"];
        // echo $id;
		echo $this->viewHome($id,$site);
	}

	public function viewHome($id,$site) {

		$cnOwlPDO = PDOConnection();
		$FechaHoraSrv = FechaHoraSrv();
		$name = post("name");
		$phone = post("phone");
		$emailEmisor = post("email");
		$message = post("message");
		
		if($name){
			
			$tableValue 	=	array();
			$tableValue["Nombres"] =   $name;
			$tableValue["Email"] =   $emailEmisor;
			$tableValue["Telefono"] =   $phone;
			$tableValue["Mensaje"] =   $message;
			$tableValue["FechaHoraCreacion"] =   $FechaHoraSrv;
			$tableValue["FechaHoraActualizacion"] =   $FechaHoraSrv;
			$tableValue["Origen"] =   $site;
			$tableValue["MovimientoAlmacen"] =   $id;
			$return 			= 	insertPDO("contactos",$tableValue,$cnOwlPDO);
			
			$asunto = "SOLICITUD DE CONTACTO";
			$body = '
			<html>
			<head>
			<title>Notificación de episodios</title>
			</head>
			<body>
			<p>¡Llegó una solicitud de contacto!</p>
			<table>
				<tr><th>NOMBRES Y APELLIDOS: </th><td>'.$name.'</td></tr>
				<tr><th>EMAIL: </th><td>'.$emailEmisor.'</td></tr>
				<tr><th>TELÉFONO: </th><td>'.$phone.'</td></tr>
				<tr><th>MENSAJE: </th><td>'.$message.'</td></tr>
				<tr><th>DÍA Y HORA: </th><td>'.$FechaHoraSrv.'</td></tr>
				
			</table>
			</body>
			</html>
			';
			
			EMail($emailEmisor,$name,"informes@episodiosplanning.com",$asunto,$body);
			
			$viewdata = array();
			$viewdata['mensaje'] = "correcto";		
			return json_encode($viewdata);
		}else{
			
				$viewdata = array();
			$viewdata['mensaje'] = "incorrecto";		
			return json_encode($viewdata);
		}			
	}		

	

	public function formContacto($arg) {

		

		return $arg;

	}		

	

}