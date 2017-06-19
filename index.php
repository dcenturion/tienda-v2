<?php
	require_once('_librerias/php/conexiones.php');
	require_once('_librerias/php/funciones.php');
	require_once '_librerias/php/route.php';
	require_once '_vistas/home.php';
	// require_once '_vistas/home_product.php';
	require_once '_vistas/catalogo-productos.php';
	require_once '_vistas/contactos.php';
	require_once '_vistas/producto-programa-educativo.php';
	require_once '_vistas/producto-ebook.php';
	require_once '_vistas/carrito-compras.php';
	require_once '_vistas/informacion-pago.php';
	require_once '_vistas/pago-finalizado.php';
	require_once '_vistas/laptop.php';
	require_once '_vistas/gobierno-central.php';
	require_once '_vistas/terminos-condiciones.php';
	require_once '_vistas/compras-realizadas.php';
	require_once '_vistas/editar-perfil.php';
	require_once '_vistas/favoritos.php';
	require_once '_vistas/registrate.php';
	require_once '_vistas/cambiar-clave.php';

	error_reporting(E_ERROR);
	$route  = new Route();
	$route->add('/home','Home');
	$route->add('/eguru','Home');
	$route->add('/rimac','Home');
	$route->add('/fri','Home');
	$route->add('/empresa','Home');

	$route->add('/catalogo-productos','CatalogoProductos');
    $route->add('/producto-programa-educativo','productoProgramaEducativo');
    $route->add('/producto-ebook','productoEbook');
    $route->add('/producto-estandar','productoEstandar');
    $route->add('/carrito-compras','carritoCompras');
    $route->add('/informacion-pago','informacionPago');
    $route->add('/pago-finalizado','pagoFinalizado');
    $route->add('/laptop','laptop');
    $route->add('/gobierno-central','gobiernoCentral');
    $route->add('/terminos-condiciones','terminosCondiciones');
    $route->add('/compras-realizadas','comprasRealizadas');
    $route->add('/editar-perfil','editarPerfil');
    $route->add('/favoritos','favoritos');
    $route->add('/registrate','registrate');
    $route->add('/cambiar-clave','cambiarClave');
	$route->submit();


?>