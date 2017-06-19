<?php
	session_start();
	require_once('funciones.php');
	$UserEmpresa =  $_SESSION['UserEmpresa']['string'];
	$EntidadPersona = $_SESSION['UserEmpresaAdmin']['string'];
	$UsuarioEntidad = $_SESSION['UsuarioEntidad']['string'];
	$urlEmpresa = $_SESSION['urlEmpresa']['string'];
	$Persona = $_SESSION['Persona']['string'];
	$PersonaUsuario = $_SESSION['PersonaUsuario']['string'];
	if(empty($UserEmpresa)){

		$s = ' <!DOCTYPE html> '; 
		$s .= ' <head> '; 
		$s .= ' <link href="../../_estilos/estilos_alertas.css" rel="stylesheet" type="text/css"> '; 
		$s .= ' </head> '; 
		$s .= ' <!-- BEGIN BODY --> '; 
		$s .= ' <body class="page-404-full-page"> '; 
		$s .= ' 	<div class="row-fluid"> '; 
		$s .= ' 		<div class="span12 page-404"> '; 
		$s .= ' 			<div class="number"> '; 
		$s .= ' 				Alerta '; 
		$s .= ' 			</div> '; 
		$s .= ' 			<div class="details"> '; 
		$s .= ' 				<h3>LA SESIÓN HA TERMINADO</h3> '; 
		$s .= ' 				<p> '; 
		$s .= ' 					Vuelva a acceder '; 
		$s .= ' 					para continuar. '; 
		$s .= ' 				</p> '; 
		$s .= ' 			</div> '; 
		$s .= ' 		</div> '; 
		$s .= ' 	</div> '; 
		$s .= ' </body> '; 
		$s .= ' <!-- END BODY --> '; 
		$s .= ' </html> '; 
 	
	    WE($s);
	}
?>