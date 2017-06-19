<?php

if(isset($_GET["solicitud"]) != ""){
	require_once('../_librerias/php/funciones.php');
	require_once('../_librerias/php/conexiones.php');
}else{
	require_once('/_librerias/php/funciones.php');
	require_once('/_librerias/php/conexiones.php');
}

$cnPDO = PDOConnection();
$cnOld = conexSys();
$Entidad = $_SESSION['Entidad']['string'];

$Dominio = Dominio();

	function vistaColumnaUnica($valor) {

			$html = '<div class="cuerpoPrincipal">';
			$html = $html . '<div class="ContPanelA001">';
			$html = $html . '<div class="PanelTransparenteTransition">';
			$html = $html . '<div id="cuerpo" class="cuerpo">';
			$html = $html . $valor;
			$html = $html . ' </div>';
			$html = $html . ' </div>';
			$html = $html . ' </div>';
			$html = $html . ' </div>';
			return $html;
	}



	function menu($valor) {

			$html = '<div class="menuCliente">';
			$html = $html . '<div class="branding"><span class="logo">Episodios | </span> <span class="eslogan">Administrador de la página </span>';
			$html = $html . ' </div>';
			$html = $html . '<div class="btn_right">';
			$html = $html . ' </div>';		
			$html = $html . ' </div>';
			return $html;
	}


	function menuHerramienta($valor) {

			$html = '<div class="menu">';
			$html = $html . '<div class="branding"><span class="logo">DEFSEI</span> <span class="eslogan">system developer </span>';
			$html = $html . ' </div>';
			$html = $html . '<div class="btn_right">';
			$html = $html . ' </div>';		
			$html = $html . ' </div>';
			return $html;
	}

	function head($valor) {
		$html = '
			<link rel="shortcut icon"  href="/_imagenes/iconos/icon.ico" >
			<title>Login</title>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="description" content="">
			<meta name="keywords" content="">
			<meta name="author" content="">
			
			
		';
		return $html;
	}



	function libreriasAdmin($valor) {

		$randParam = "?param=" . (rand() * 100);
		$html =   " 
		<script type='text/javascript' src='/system/_librerias/js/global.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/ajaxglobal.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/jquery-2.1.1.min.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/jquery-ui.min.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/owlchat/zilli.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/owlchat/AjaxZilli.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/popup/jquery.popup.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/historyAjax.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/alertify.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/sortable.js'></script>
		
		<link href='/system/_estilos/estilos_admin.css' rel='stylesheet' type='text/css'/>
	    <link href='/system/_estilos/estilos_admin_responsive.css' rel='stylesheet' type='text/css'/>		
		<link href='/system/_estilos/alertify.core.css' rel='stylesheet' type='text/css'/>
		<link href='/system/_estilos/alertify.default.css' rel='stylesheet' type='text/css'/>
		<link href='/system/_estilos/popup/popup.css' rel='stylesheet' type='text/css'/>


		";
		return $html;
	}


	function librerias_app($valor) {

		$randParam = "?param=" . (rand() * 100);
		$html =   " 
		<script type='text/javascript' src='/system/_librerias/js/global.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/ajaxglobal.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/jquery-2.1.1.min.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/jquery-ui.min.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/owlchat/zilli.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/owlchat/AjaxZilli.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/popup/jquery.popup.js'></script>
		<script type='text/javascript' src='/system/_librerias/js/historyAjax.js'></script>
		
		<!-- inicio alertas flotantes-->		
		<script type='text/javascript' src='/system/_librerias/js/alertify.js'></script>
		<!-- fin alertas flotantes-->
		
		<!-- inicio Ordenador de columnas-->
		<script type='text/javascript' src='/system/_librerias/js/sortable.js'></script>
		<!-- fin Ordenador de columnas-->
			
		<link href='/system/_estilos/estilos_app.css' rel='stylesheet' type='text/css'/>
		<link href='/system/_estilos/estilos_app_responsive.css' rel='stylesheet' type='text/css'/>
		
		
		<!-- inicio alertas flotantes-->				
		<link href='/system/_estilos/alertify.core.css' rel='stylesheet' type='text/css'/>
		<link href='/system/_estilos/alertify.default.css' rel='stylesheet' type='text/css'/>
		<!-- fin alertas flotantes-->

		<!-- inicio Drag on drop - Temas del app -->
		  <link rel='stylesheet' href='/system/_librerias/color-thief-master/examples/css/screen.css'>
		<!-- fin Drag on drop - Temas del app  -->	
		
		<link href='/system/_estilos/popup/popup.css' rel='stylesheet' type='text/css'/>
  
		";
		return $html;
	}

	function menuHorizontalAdmin($valor) {

		$html = $html . '<div id="menu" class="mHSinSubElementosA002 tamano">';

		$sClassA = "font-size:0.9em;width:120px;";
		
		$sBotMatris = "<i class='icon-align-justify'></i>]bnt_menu]PanelA]btn_js}";
		$sBotMatris = $sBotMatris . "<div style='" . $sClassA . "'>DCTools</div>]]cuerpo]RZ}";
		$sBotMatris = $sBotMatris . "]]cuerpo]input}";
		$sBotMatris = $sBotMatris . "<i class=icon-cog></i> ]./_vistas/se_productosNew.php?Articulos=Site]cuerpo]C}";  ;
		$sBotMatris = $sBotMatris . "<i class=icon-user></i>]./_vistas/se_usuarios.php?Usuarios=Site]cuerpo]C}";
		$sBotMatris = $sBotMatris . "]]]userbar}";

		$sTipoAjax = "true";
		$sClase = "menuHorz002";
		$sBot = MenuMasterHorizontalB($sBotMatris, $sClase, $sTipoAjax);
		$html = $html . $sBot . ' </div>';
		return $html;
	}
		
	function menuHorizontalApp($valor) {
        global $Entidad, $cnPDO;
		
		$html = $html . '<div id="menu" class="mHSinSubElementosA002 tamano">';

		$sClassA = "font-size:0.9em;width:120px;";
		
		$Query=" SELECT  ImagenLogo FROM entidades WHERE Codigo = :Entidad ";
		$rg = OwlPDO::fetchObj($Query, ["Entidad" => $Entidad ] ,$cnPDO);
		$ImagenLogo = $rg->ImagenLogo;			
		
		$sBotMatris = "<i class='icon-align-justify'></i>]bnt_menu]PanelA]btn_js}";
		$sBotMatris = $sBotMatris . "<div style='" . $sClassA . "'><img src='/_imagenes/usuarios/".$ImagenLogo."' width=50% ></div>]]cuerpo]RZ}";
		$sBotMatris = $sBotMatris . "]]cuerpo]input}";
		$sBotMatris = $sBotMatris . "<i class=icon-comments style='padding:0px 0px 0px 2em;' ></i> ]./_vistas/se_productosNew.php?Articulos=Site]cuerpo]C}";  ;
		$sBotMatris = $sBotMatris . "<i class=icon-warning-sign style='padding:0px 0px 0px 1em;'></i>]./_vistas/se_usuarios.php?Usuarios=Site]cuerpo]C}";
		$sBotMatris = $sBotMatris . "]]]userbar}";

		$sTipoAjax = "true";
		$sClase = "menuHorz002";
		$sBot = MenuMasterHorizontalC($sBotMatris, $sClase, $sTipoAjax);
		$html = $html . $sBot . ' </div>';
		return $html;
	}
	
	function menuHerramientaInterna($valor) {

		$html = $html . '<div id="menu" class="mHSinSubElementosA001 tamano">';
		$sUrlPanelesA = "PanelA[PanelA[./_vistas/carrusel.html?vista=PanelA[1000|";
		$sUrlPanelesA = $sUrlPanelesA . "PanelB[PanelB[./_vistas/site.php?vista=PanelB[2000|";
		// $sUrlPanelesA = $sUrlPanelesA."PanelC[PanelC[./vistas/site.php?vista=PanelC[4000|";	

		$sBotMatris = "<div class='logo'>DEFSEI</div><div class='eslogan'>system developer </div>]" . $sUrlPanelesA . "]cuerpo]RZ}";
		// $sBotMatris = $sBotMatris."Home]index.php?TipoConsulta=juan&usuario=daniel&estado=Abierto]cuerpo]C}";
		// $sBotMatris = $sBotMatris."Login]index.php?TipoConsulta=Felipe&usuario=daniel&estado=Abierto]cuerpo]C}";
		// $sBotMatris = $sBotMatris."Form]./_vistas/form.php?vista=Felipe]cuerpo]C}";
		// $sBotMatris = $sBotMatris."Form Inpt]./_vistas/cronograma.php?vista=Felipe]cuerpo]C}";
		$sBotMatris = $sBotMatris . "<i class=icon-table ></i>  FORMULARIOS Y TABLAS ]./_vistas/adminTablasForms.php?vista=Felipe]cuerpo]C}";
		$sBotMatris = $sBotMatris . " <i class=icon-signal ></i>  REPORTES ]./_vistas/adminTablasForms.php?vista=Felipe]cuerpo]C}";
		$sBotMatris = $sBotMatris . " <i class=icon-cogs ></i> CONFIGURACIÓN ]./_vistas/adminTablasForms.php?vista=Felipe]cuerpo]C}";
		//$sBotMatris = $sBotMatris."Login]./_vistas/listadoReporte2.php?vista=Felipe]cuerpo]C}";
		// $sBotMatris = $sBotMatris . "SALIR]./salir_master.php?CerrarSesion=Yes]site]C}";
		$sBotMatris = $sBotMatris . "<i class=icon-off></i> SALIR ]./salir_master.php]site]C]HREF}";
		$sTipoAjax = "true";
		$sClase = "menuHorz001";
		$sBot = Boton001($sBotMatris, $sClase, $sTipoAjax);
		$html = $html . $sBot . ' </div>';
		return $html;
	}	


	function documentoHtmlAdmin($parm){
		
		$s = '<!DOCTYPE html> 
		<html lang="es">
		<head>
			'.head().'
			'.libreriasAdmin().'	
		</head>
		<body>
		<div class="site" id="site">';
		
		$s .= '<div class="">';	
		$s .= menuHorizontalAdmin("");
		$s .= '</div>';
		$s .= '<div id="panelB2">';	
	
		$s .= '</div>';
		
			$s .= '<div class="panelContenedor">';
			
				$s .= '<div id="PanelA" class="columA">';	
					$s .= '<div class="panelMenu">';
					$s .= menuVerticalAdmin();
					$s .= '</div>';	
				$s .= '</div>';
				
				$s .= '<div id="panelB" class="columB">';	
					
				$s .= $parm;		
				$s .= '</div>';		
				
			$s .= '</div>';
		
		$s .= '</div>';
		$s .= '</body>';
		$s .= '</html>';
		return $s;
			
	}
	
	
	
	function menuVerticalAdmin(){
		
		$menu = " <div class=icon><i class=icon-table ></i></div> Tablas]./_vistas/sys_tablas.php?tablas=Site]panelB}";
		$menu .= " <div class=icon><i class=icon-indent-left ></i></div> Formularios ]./_vistas/sys_form.php?formulario=Site]panelB}";
		$menu .= " <div class=icon><i class=icon-sitemap ></i></div> Empresas ]" . $enlace . "?ActualizaComponentes=progespecial-por-progespecial]panelB-R}";
		$menu .= " <div class=icon><i class=icon-user ></i></div> Usuarios ]./_vistas/sys_usuarios.php?usuarios=Site]panelB}";
		$menu .= " <div class=icon><i class=icon-reorder ></i></div> Base de Datos ]./_vistas/sys_base_datos.php?baseDatos=Site]panelB}";
		$mv = menuVertical($menu, 'menu4');
		
	return 	$mv;			
	}	
	
	function menuVerticalSystem(){

		$menu = " <div class=icon><i class=icon-comments ></i></div> Mensajes ]" . $enlace . "?ActualizaComponentes=ListasTrabajo]panelB-R}";
		$menu .= " <div class=icon><i class=icon-shopping-cart ></i></div> Ventas]" . $enlace . "?ActualizaComponentes=ListasTrabajoCursos]panelB-R}";
		$menu .= " <div class=icon><i class=icon-home ></i></div> Tienda]./_vistas/se_tienda.php?Tienda=Site]panelB}";
		$menu .= " <div class=icon><i class=icon-user ></i></div> Usuarios ]./_vistas/se_usuarios.php?Main=Principal]panelB}";
		$menu .= " <div class=icon><i class=icon-cogs ></i></div> Configuración ]./_vistas/se_configuracion.php?Main=Principal]panelB}";
		$mv = menuVertical($menu, 'menu4');
		
	return 	$mv;			
	}	

	function documentoHtmlApp($parm){
	    global $cnPDO,$Entidad;
		
		$Query=" SELECT ColorMenuHorizontal,ColorMenuHorizontal_Boton,ColorMenuVertical,ColorMenuVerticalBoton,ColorBotonesInternos FROM entidades WHERE Codigo = :Entidad ";
		$rg = OwlPDO::fetchObj($Query, ["Entidad" => $Entidad ] ,$cnPDO);
		$ColorMenuHorizontal = $rg->ColorMenuHorizontal;			
		$ColorMenuHorizontal_Boton = $rg->ColorMenuHorizontal_Boton;			
		$ColorMenuVertical = $rg->ColorMenuVertical;			
		$ColorMenuVerticalBoton = $rg->ColorMenuVerticalBoton;			
		$ColorBotonesInternos = $rg->ColorBotonesInternos;	

		$s = '<!DOCTYPE html> 
		<html lang="es">
		<head>
			'.head().'
			'.librerias_app().'	
		</head>
		<body>
		<style>
		.mHSinSubElementosA002.tamano{
			background-color:'.$ColorMenuHorizontal.';
			border-color:'.$ColorMenuHorizontal.';
		}
		.menuHorz002 ul button{
			color: '.$ColorMenuHorizontal_Boton.';
		}
		.menuHorz002 ul a{
			color: '.$ColorMenuHorizontal_Boton.';			
		}
		.botones1 .boton a{
			background-color:'.$ColorBotonesInternos.';
			border-color:'.$ColorBotonesInternos.';
		}
		.layoutV{
			border-top: 3px solid '.$ColorBotonesInternos.';
		}
		.menuV1 .boton .btn-dsactivado{
			border-color: '.$ColorBotonesInternos.';
		}
		.menu4 li .btnMenuVetical{
			color:'.$ColorMenuVerticalBoton.';			
		}	
		.menu4 li .btnMenuVetical .icon{
			color:'.$ColorMenuVerticalBoton.';			
		}
		.panelContenedor .columB{
			background-color: '.$ColorMenuVertical.';
		}
		.btnMenuVetical{
		    color:'.$ColorMenuVerticalBoton.';
		}
		.CuadroA button{
			background-color: '.$ColorBotonesInternos.';			
			border-color: '.$ColorBotonesInternos.';			
		}
		.TituloALM {
			background-color: '.$ColorBotonesInternos.';
			border-bottom: 8px solid '.$ColorBotonesInternos.';
		}
		div.popup{
			border-top-color: '.$ColorBotonesInternos.';
		}
		.TituloALM span{
		   color:'.$ColorMenuVertical.';
		}
		.botones1 .boton .cabezera{
			background-color: '.$ColorMenuHorizontal.';			
		}
		.PanelUser ul li .cabezera{
			background-color: '.$ColorMenuHorizontal.';			
		}
		
		.btnOcultaPanel{
			background-color: '.$ColorBotonesInternos.';
            border: 1px solid '.$ColorBotonesInternos.';			
		}
		</style>
		<div class="site" id="site">';
		
		$s .= '<div class="">';	
		$s .= menuHorizontalApp("");
		$s .= '</div>';
		$s .= '<div id="panelB2">';	
	
		$s .= '</div>';
		
			$s .= '<div class="panelContenedor">';
			
				$s .= '<div id="PanelA" class="columA">';	
					$s .= '<div class="panelMenu">';
					$s .= menuVerticalSystem();
					$s .= '</div>';	
				$s .= '</div>';
				
				$s .= '<div id="panelB" class="columB">';	
					
				$s .= $parm;		
				$s .= '</div>';		
				
			$s .= '</div>';
		
		$s .= '</div>';
		$s .= '</body>';
		$s .= '</html>';
		return $s;
			
	}
		
?>