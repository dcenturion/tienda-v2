<?php
session_start();
require_once './_vistas/layout.php';
require_once('./_librerias/php/conexiones.php');
require_once('./_librerias/php/funciones.php');

class favoritos{

    private $_parm;
    public  function __construct($_parm=null)
    {
        $empresa = $_parm["entidad"];

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
		
		echo $layout->main($this->viewHome($empresa,$cod_cliente,$cnOwlPDO),$datos);

    }



    public function viewHome($empresa,$cod_cliente,$cnOwlPDO) {
		
		$layout  = new Layout();
		$productosPreferidos = productosPreferidos($cnOwlPDO,$cod_cliente);	

		$Query = " 
		SELECT PFC.Codigo AS CodigoProforma
		FROM   proformas_cab PFC 
		INNER JOIN clientes CL ON PFC.Clientes = CL.Codigo
		WHERE CL.Codigo = :Codigo_Cliente AND PFC.Estado = :Estado
		GROUP BY PFC.Codigo 
		";
		$where = ["Codigo_Cliente"=>$cod_cliente,"Estado"=>"Pendiente"];	
		$regData =  OwlPDO::fetchObj($Query, $where ,$cnOwlPDO);
		$codPedido = $regData->CodigoProforma;
		
        $datos = array();
        $datos['productosPreferidos'] = $productosPreferidos;
        $datos['empresa'] = $empresa;
        $datos['codPedido'] = $codPedido;
		
		
        return $layout->render('./_vistas/favoritos.phtml',$datos);



    }



    public function formContacto($arg) {



        return $arg;

    }



}