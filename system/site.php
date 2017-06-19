<?php

	require_once('_librerias/disenoVisual/layout.php');
	error_reporting(E_ERROR);
	
	$cnPDO = PDOConnection();
	$cnOld = conexSys();
	$EntidadGet = get("sitio");
	$Entidad = $_SESSION['Entidad']['string'];

	$siteUrl = siteUrl();
	
	if(empty($EntidadGet)){
	    $EntidadGet = $_SESSION['EntidadUrl']['string'];	
	}else{
	    $EntidadGet = get("sitio");		
	}
	
	$Query=" SELECT  Codigo,ImagenLogo,ColorMenuHorizontal,SubDominio,ColorMenuVertical,ColorMenuVerticalBoton,ColorBotonesInternos FROM entidades WHERE SubDominio = :SubDominio ";
	$rg = OwlPDO::fetchObj($Query, ["SubDominio" => $EntidadGet ] ,$cnPDO);
	$ImagenLogo = $rg->ImagenLogo;	
	$CodigoEntidad = $rg->Codigo;	
	$ColorMenuHorizontal = $rg->ColorMenuHorizontal;	
	$ColorBotonesInternos = $rg->ColorBotonesInternos;	
	$SubDominio = $rg->SubDominio;	
	
	$_SESSION['EntidadUrl']['string'] = $SubDominio;
	

	
    $s = '<!DOCTYPE html> 
    <html lang="es">
	<head>
		'.head().'
		'.librerias_app().'
		<style type="text/css">
		 .PanelA{  
			min-height: 700px;
			width: 100%;
			height:100%;
			font-family: "Open Sans", sans-serif;
			background: #e6eef5;
			padding:40px 0px 0px 0px;
		 }
		 .PanelA{
			background:'.$ColorMenuHorizontal.';			 
		 }
		 .panelCentralL{
			width: 300px;
			margin: auto;
		 }
		 .CuadroA input[type=password] {
			border-width: 1px;
			box-shadow: none;
			padding: 7px 5px;
			font-size: 0.9em;
			font-family: "Segoe UI", Helvetica, Arial, sans-serif;
			color: #404040;
			float: left;
			border: 1px solid #d5d8de;
			border-radius: 2px;
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
			-webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
		}
		.CuadroA input[type=text] {
			border-width: 1px;
			box-shadow: none;
			padding: 7px 5px;
			font-size: 0.9em;
			font-family: "Segoe UI", Helvetica, Arial, sans-serif;
			color: #404040;
			float: left;
			border: 1px solid #d5d8de;
			border-radius: 2px;
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
			-webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
		}
		 #Form_login input[type=text]{width:270px;}		 
		 #Form_login input[type=password]{width:270px;}	
         .CuadroA .Botonera{    
			float: left;
			padding: 20px 0px;
			width: 94%;
		}	
        .CuadroA button{
			margin-bottom: 0;
			width: 100%;
			font-weight: 700;
			text-align: center;
			vertical-align: middle;
			-ms-touch-action: manipulation;
			touch-action: manipulation;
			cursor: pointer;
			background-image: none;
			border: 1px solid;
			white-space: nowrap;
			padding: 10px 12px;
			font-size: 13px;
			line-height: 1.42857143;
			border-radius: 2px;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
			color: #FFFFFF;
			background-color: #2a75f3;
			border-color: #2a75f3;
			margin-right: 9px;			
		}
        .CuadroA button{
			background-color: '.$ColorBotonesInternos.';
			border-color: '.$ColorBotonesInternos.';			
		}		
		.TituloALM{
			padding: 4% 1% 4% 1%;
			background-color: #fff;
			float:left;width:96%;
			border-bottom: 1px solid #dcdfe4;
		}		
        .TituloALM span{
			color: #202021;
		}		
		.TituloALM p{
			color: #262038;
		}
		.panelCentralL .panelCentralEstilo{
			border-top-color:'.$ColorBotonesInternos.';
		}
		.CuadroA ul li{
			padding: 5px 0px 3px 0px;
			min-height: 0px;
			border-bottom: none;
		}
		.TituloALM p{
			text-align: center;
		}
		.TituloALM p{
			text-align: center;
		}
		.TituloALM span{
			text-align: center;			
		}		
		</style>	
    </head>
    <body>
	<div class="site">';
	$s .= '<div style="float:left;width:100%;height:100%;">';
	$s .= vistaColumnaUnica("");
	$s .= '</body>';
	$s .= '<//html>';
	W($s);

	
	$sUrlPanelesA = $sUrlPanelesA."PanelA[PanelA[_vistas/se_login.php?site=Login&sitio=".$EntidadGet."[1000[true|";	
	$script =	'
	        <script type=text/javascript>
			var sCuerpo = document.getElementById("cuerpo");
			sCuerpo.innerHTML = "";
			controlaActivacionPaneles("'.$sUrlPanelesA.'",true);
			
			function procesaFormularioLogin(e,Url,Panel,Form) {
				 console.log(e.responseText);
			     if(e.responseText =="True"){
				    window.location.assign( "'.$siteUrl.'system/administrador.php");
				 }else{
					var PanelR = document.getElementById(Panel);
			        PanelR.innerHTML = e.responseText; 
				 }
			}	
		</script>    ';
	WE($script);
	
?>
