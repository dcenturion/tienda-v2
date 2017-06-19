<?php
session_start();
require_once('_librerias/disenoVisual/menus.php');
require_once('_librerias/disenoVisual/cuerposite.php');
require_once('_librerias/php/conexiones.php');
require_once('_librerias/php/funciones.php');

error_reporting(E_ERROR);

$vConex = conexSys();
$enlaceMenu="/system/_vistas/adminTablasForms.php";
$master_access = $_SESSION["master_access"];

if (!$master_access) {
    rd("admin_master.php");
}

$Q_U = " SELECT * FROM administradores WHERE AdminCod = {$master_access}";
$Obj = fetchOne($Q_U, $vConex);
$AdminUserTipo = $Obj->Tipo;

define("MASTER", 1);
define("DEVELOPER", 2);
define("COORDINADOR", 3);
define("VISITANTE", 4);

// $menu_pie = menuPie("pie");
$CuerpoSite = vistaColumnaUnica("");


  if($AdminUserTipo == DEVELOPER){
		$JSF = "enviaVistaIA('{$enlaceMenu}?Mostrar=Menu','cuerpo','');";
		$Boton .= '<li><div onclick="'.$JSF.'"> Formulario </div></li>'; 

		$JST = "enviaVistaIA('{$enlaceMenu}?accionCT=tablas','cuerpo','');";  
		$Boton .= '<li><div onclick="'.$JST.'"> Tablas </div></li>';	

		$JSDA = "enviaVistaIA('{$enlaceMenu}?accionDA=DAlternos','cuerpo','');";  
		$Boton .= '<li><div onclick="'.$JSDA.'"> Datos Alternos </div></li>';

    }elseif($AdminUserTipo == COORDINADOR){
		$JSDA = "enviaVistaIA('{$enlaceMenu}?accionDA=DAlternos','cuerpo','');";  
		$Boton .= '<li><div onclick="'.$JSDA.'"> Datos Alternos </div></li>';

		$JSDM = "enviaVistaIA('{$enlaceMenu}?accionDA=DMaestros','cuerpo','');";  
		$Boton .= '<li><div onclick="'.$JSDM.'"> Datos Maestros </div></li>';

		$JSRM = "enviaVistaIA('{$enlaceMenu}?Reportes=Menu','cuerpo','');";  
		$Boton .= '<li><div onclick="'.$JSRM.'"> Reporte Menu </div></li>';
	
	}elseif($AdminUserTipo == VISITANTE){
        $JSRM = "enviaVistaIA('{$enlaceMenu}?Reportes=Menu','cuerpo','');";  
        $Boton .= '<li><div onclick="'.$JSRM.'"> Reporte Menu </div></li>';
		
    }else{#MASTER
		$JSF = "enviaVistaIA('{$enlaceMenu}?Mostrar=Menu','cuerpo','');";
		$Boton .= '<li><div onclick="'.$JSF.'"> Formulario </div></li>'; 

		$JST = "enviaVistaIA('{$enlaceMenu}?accionCT=tablas','cuerpo','');";  
		$Boton .= '<li><div onclick="'.$JST.'"> Tablas </div></li>';	

		$JSID = "enviaVistaIA('{$enlaceMenu}?action=seleccion-db','cuerpo','');";  
		$Boton .= '<li><div onclick="'.$JSID.'"> Importar Datos </div></li>';

		$JSDA = "enviaVistaIA('{$enlaceMenu}?accionDA=DAlternos','cuerpo','');";  
		$Boton .= '<li><div onclick="'.$JSDA.'"> Datos Alternos </div></li>';

		$JSDM = "enviaVistaIA('{$enlaceMenu}?accionDA=DMaestros','cuerpo','');";  
		$Boton .= '<li><div onclick="'.$JSDM.'"> Datos Maestros </div></li>';

		$JSPS = "enviaVistaIA('{$enlaceMenu}?ProcesosSistema=Menu','cuerpo','');";  
		$Boton .= '<li><div onclick="'.$JSPS.'"> Procesos De Sistema </div></li>';	

		$JSRM = "enviaVistaIA('{$enlaceMenu}?Reportes=Menu','cuerpo','');";  
		$Boton .= '<li><div onclick="'.$JSRM.'"> Reporte Menu </div></li>';
		}
				
$Menu = "<ul class='sysdc-Ul' style='width:100%'>
<h2>Panel de Control  <i class='icon-briefcase top-news-icon'  style='right: 8px; bottom: 15px; opacity: 0.3; font-size: 35px; padding-left: 8px;'></i></h2>
			   {$Boton} 

</ul>";

$s = menuMaster("menu");
$s .= '<div class=cuerpobody >';
$s .= '<div id="Menu" class="Menu" >'.$Menu.'</div>';
$s .= '<div id="Body" class="Body" >';
$s .= '<div style="float:left;width:100%;height:100%;padding:10px 0px;">';
$s .= $CuerpoSite;
$s .= '</div>';

$s .= '</div>';
$s .= '</div>';
$s .= '</div>';

W($s);
?>
<style type="text/css">
    .PanelA{ width:100%;}
    .PanelB{width:100%;}
</style>
