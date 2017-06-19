<?php
require_once './_vistas/layout.php';

class carritoCompras{

    private $_parm;
    public  function __construct($_parm=null)
    {
		$pedido = $_parm["pedido"];
		$empresa = $_parm["entidad"];
        $site = "Eventos";
        $layout  = new Layout();
		
		$reg = atributosEntidadAPP($cnOwlPDO,$empresa);
		
		$sessionId = sessionId();
		$datosCliente = datosClienteAPP($cnOwlPDO,$sessionId);
		$cod_cliente = $datosCliente->Codigo;			
		
		$usuario_entidad = $_SESSION['usuario_entidad']['string'];
		if(!empty($usuario_entidad)){
			$datosUser = datosUserAPP($cnOwlPDO,$usuario_entidad);	
		    $cod_cliente = $datosUser->Clientes;			
		}
		
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
        $datos['idProducto']=$idProducto;
		
		echo $layout->main($this->viewHome($empresa,$pedido,$cod_cliente,$cnOwlPDO),$datos);

    }



    public function viewHome($empresa,$pedido,$cod_cliente,$cnOwlPDO) {

        $layout  = new Layout();
		$sessionId = sessionId();
		
        $detallePedido = detallePedidoAPP($cnOwlPDO,$pedido);

        $datos = array();
        $datos['detallePedido'] = $detallePedido;
        $datos['empresa'] = $empresa;
        $datos['pedido'] = $pedido;
        return $layout->render('./_vistas/carrito-compras.phtml',$datos);
		
    }



    public function formContacto($arg) {



        return $arg;

    }



}