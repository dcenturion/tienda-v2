<?php

function funcion_local_pestanas_A($arg){
		
	$menu .= "Tablas]./_vistas/sys_tablas.php?tablas=Site]panelB]Marca}";
	$menu .= "Importación Lógica]./_vistas/sys_tablas_imp_logica.php?tablas=Site]panelB]]}";

	$pestanas = menuHorizontal($menu, 'menuV1','menuV01',$arg);
	return $pestanas;
}
function funcion_local_pestanas_A1($arg){

    $menu .= "Tablas]./_vistas/sys_tablas.php?tablas=Site]panelB]Marca}";
    $menu .= "Importar]./sys_tablas.php?tablas=Site_Perfiles]panelB]CHECK]sys_tabla_form}";
    $menu .= "Exportar]./sys_tablas.php?tablas=Site_Perfiles]panelB]CHECK]sys_tabla_form}";

    $pestanas = menuHorizontal($menu, 'menuV1','menuV01',$arg);
    return $pestanas;
}

function funcion_local_pestanas_form($arg){
		
	$menu .= "Formularios]./_vistas/sys_form.php?formulario=Site]panelB]Marca}";
	$menu .= "Importar]./_vistas/sys_tablas.php?tablas=Site_Perfiles]panelB}";
	$menu .= "Exportar]./_vistas/sys_tablas.php?tablas=Site_Perfiles]panelB}";
	
	$pestanas = menuHorizontal($menu, 'menuV1','menuV01',$arg);
	return $pestanas;
}

function funcion_local_pestanas_form1($arg){

    $menu .= "Formularios]./_vistas/sys_form.php?formulario=Site]panelB]Marca}";
    $menu .= "Importar]./sys_tablas.php?tablas=Site_Perfiles]panelB]CHECK]sys_tabla_form}";
    $menu .= "Exportar]./sys_tablas.php?tablas=ExportFrom]panelB]CHECK]sys_tabla_form}";

    $pestanas = menuHorizontal($menu, 'menuV1','menuV01',$arg);
    return $pestanas;
}

?>
