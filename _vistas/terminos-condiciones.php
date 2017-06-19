<?php
require_once './_vistas/layout.php';

class terminosCondiciones{

    private $_parm;
    public  function __construct($_parm=null)
    {
		$idProducto = $_parm["id"];
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
		echo $layout->main($this->viewHome($reg),$datos);

    }



    public function viewHome($reg) {


        $layout  = new Layout();
		$datos = array();
        $datos['datosEmpresa'] = $reg;

        return $layout->render('./_vistas/terminos-condiciones.phtml',$datos);



    }



    public function formContacto($arg) {



        return $arg;

    }



}