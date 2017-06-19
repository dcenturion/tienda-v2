<?php

function funcion_local_pestanas_A($arg){
		
	$menu .= "Usuarios]./_vistas/sys_usuarios.php?usuarios=Site]panelB]Marca}";
	$menu .= "Perfiles]./_vistas/sys_usuarios.php?usuarios=Site_Perfiles]panelB}";
	
	$pestanas = menuHorizontal($menu, 'menuV1','menuV01',$arg);
	return $pestanas;
}



?>
