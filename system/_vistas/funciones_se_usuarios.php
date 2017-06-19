<?php

function pestanasLocal($arg){
		
	$menu .= "Usuarios]./_vistas/se_usuarios.php?Main=Principal]panelB]Marca}";
	$menu .= "Perfil]./_vistas/se_usuarios_perfil.php?Main=Principal]panelB}";
	// $menu .= "Cargos]./_vistas/se_usuarios.php?TipoProducto=Site]panelB}";
	
	$pestanas = menuHorizontal($menu, 'menuV1','menuV01',$arg);
	return $pestanas;
}


?>
