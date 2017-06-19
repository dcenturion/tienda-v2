<?php

function funcion_local_pestanas_A($arg){
		
	$menu .= "Datos Generales]./_vistas/se_configuracion.php?Main=Principal]panelB]Marca}";
	$menu .= "Pantone]./_vistas/se_configuracion.php?Main=Temas]panelB}";
	$menu .= "Colores del Sistema]./_vistas/se_configuracion_temas.php?Main=Principal]panelB}";
	$menu .= "Imágenes del Sitio]./_vistas/se_configuracion_imagenes.php?Main=Principal]panelB}";
	// $menu .= "Colores - Email]./_vistas/se_configuracion_imagenes.php?Main=Principal]panelB}";
	
	$pestanas = menuHorizontal($menu, 'menuV1','menuV01',$arg);
	return $pestanas;
}



?>
