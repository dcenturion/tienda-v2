<?php
	require_once('_librerias/disenoVisual/layout.php');
	error_reporting(E_ERROR);
	
	$siteUrl = siteUrl();

    $s = '<!DOCTYPE html> 
    <html lang="es">
	<head>
		'.head().'
		'.librerias().'
		<style type="text/css">
		 .PanelA{  width: 100%;height:100%;
			min-height: 700px;
			width: 100%;
			height:100%;
			font-family: "Open Sans", sans-serif;
			background: #e6eef5;
			padding:40px 0px 0px 0px;
		 }
		 .panelCentralL{
			width: 300px;
			margin: auto;
		 }
		 #Form_login input[type=text]{width:230px;}		 
		 #Form_login input[type=password]{width:230px;}		 
		</style>	
    </head>
    <body>
	<div class="site">';
	$s .= menuHerramienta("");
	$s .= '<div style="float:left;width:100%;height:100%;">';
	$s .= vistaColumnaUnica("");
	$s .= '</body>';
	$s .= '<//html>';
	W($s);
	
	
	$sUrlPanelesA = $sUrlPanelesA."PanelA[PanelA[_vistas/se_login_master.php?site=cambiar_contrasena[1000[true|";	
	$script =	'
	        <script type=text/javascript>
			var sCuerpo = document.getElementById("cuerpo");
			sCuerpo.innerHTML = "";
			controlaActivacionPaneles("'.$sUrlPanelesA.'",true);
			
			function procesaFormularioLogin(e,Url,Panel,Form) {
				 console.log(e.responseText);
			     if(e.responseText =="True"){
					 
				    window.location.assign( "'.$siteUrl.'system/master.php");
				 }else{
					var PanelR = document.getElementById(Panel);
			        PanelR.innerHTML = e.responseText; 
				 }
			}	
		</script>    ';
	WE($script);
	
?>
