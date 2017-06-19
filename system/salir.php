<?php
    session_start();
	require_once('_librerias/php/funciones.php');
	error_reporting(E_ERROR);
	
	unset($_SESSION['Usuario']['string']);
	unset($_SESSION['Entidad']['string']);
	
	WE("<script>
				window.location.href = '/system/site.php';
			</script>");
	
