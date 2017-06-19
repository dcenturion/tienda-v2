<?php
require_once './_vistas/layout.php';
require_once('./_librerias/php/conexiones.php');
require_once('./_librerias/php/funciones.php');

class Home{

    public  function __construct()
	{
		$empresa = get("uri");
		$cierrasesion = get("cierrasesion");
		$_SESSION['empresa']['string'] = $empresa;
		$cnOwlPDO = PDOConnection();
	    $layout  = new Layout();
		
		if(!empty($cierrasesion)){
			unset($_SESSION['usuario_entidad']['string']);
		} 
		
		$reg = atributosEntidadAPP($cnOwlPDO,$empresa);
		
		$sessionId = sessionId();
		$datosCliente = datosClienteAPP($cnOwlPDO,$sessionId);
		$cod_cliente = $datosCliente->Codigo;			
		
		$usuario_entidad = $_SESSION['usuario_entidad']['string'];

		if(!empty($usuario_entidad)){
			$datosUser = datosUserAPP($cnOwlPDO,$usuario_entidad);	
		    $cod_cliente = $datosUser->Clientes;			
		}
		// var_dump($cod_cliente);		
		$dataB = totalItemPedidosAPP($cnOwlPDO,$cod_cliente);
		$categorias = categoriasAPP($cnPDO,$empresa);
		
		$propiedadesEntidad = array();
        $propiedadesEntidad['principal'] = $reg;		
        $propiedadesEntidad['totalItem'] = $dataB;		
        $propiedadesEntidad['datosUser'] = $datosUser;	
        $propiedadesEntidad['empresa'] = $empresa;		
        $propiedadesEntidad['categorias'] = $categorias;		
        $propiedadesEntidad['sub_categorias'] = $categorias;		
        $propiedadesEntidad['cod_cliente'] = $cod_cliente;		
					
		
		$datos = array();
        $datos['propiedadesEntidad'] = $propiedadesEntidad;  
		echo $layout->main($this->viewHome($empresa,$categorias,$cod_cliente,$cnOwlPDO),$datos);
	}

	public function viewHome($empresa,$categorias,$cod_cliente, $cnOwlPDO) {

		$layout  = new Layout();
		$busqueda = get("busqueda");
		
		$productosCatalogo = productosDeEntidadAPP($cnOwlPDO,$empresa,$busqueda);

        $datosPedidoPendiente = datosPedidoPendiente($cnOwlPDO,$cod_cliente);
		$codPedido = $datosPedidoPendiente->Proformas_Cab;
		// vd($codPedido);
		$datos = array();		
		$datos['productosCatalogo'] = $productosCatalogo;
		$datos['sub_categorias'] = $categorias;
		$datos['codPedido'] = $codPedido;
		$datos['cod_cliente'] = $cod_cliente;
		// $datos['sub_categorias'] = $categorias;
		return $layout->render('./_vistas/conten_page.phtml',$datos);
	}		


	public function formContacto($arg) {

		return $arg;

	}	

}