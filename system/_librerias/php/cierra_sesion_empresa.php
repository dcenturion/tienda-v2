<?php
	session_start();
	// require_once('funciones.php');
	$UserEmpresa =  $_SESSION['UserEmpresa']['string'];
	$EntidadPersona = $_SESSION['UserEmpresaAdmin']['string'];
	$UsuarioEntidad = $_SESSION['UsuarioEntidad']['string'];
	$urlEmpresa = $_SESSION['urlEmpresa']['string'];
	$Persona = $_SESSION['Persona']['string'];
	$PersonaUsuario = $_SESSION['PersonaUsuario']['string'];
	if(empty($UserEmpresa)){
	    $UrlAppTrans = siteUrl();
	 	rd($UrlAppTrans);
	}
?>