<?php
require_once './_vistas/layout.php';

class productoEbook{

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
		echo $layout->main($this->viewHome($idProducto,$empresa,$cod_cliente,$datosUser,$cnOwlPDO),$datos);

	}

	

	public function viewHome($idProducto,$empresa,$cod_cliente,$datosUser,$cnOwlPDO) {

		$layout  = new Layout();

        $productosDetalleCatalogo = detallesProductosDeEntidadAPP($cnOwlPDO,$idProducto);
		$Categoria = $productosDetalleCatalogo->Lineaarticulo;
		$SubCategoria = $productosDetalleCatalogo->SectorArticulo;

		
        $docentes = docentesPE($cnOwlPDO,$idProducto);
        $entidadesEducativas = entidadesEducativasPE($cnOwlPDO,$idProducto);
        $curricula = curriculaProductoEducativo($cnOwlPDO,$idProducto);
		
        $productosRelacionados = productosRelacionados($cnOwlPDO,$Categoria,$SubCategoria,$idProducto);
		
        $datosPedidoPendiente = datosPedidoPendiente($cnOwlPDO,$cod_cliente);
		$codPedido = $datosPedidoPendiente->Proformas_Cab;
		
		$autores = autoresLibros($idProducto,$cnOwlPDO);
		
        $datos = array();
        $datos['productosCatalogo'] = $productosDetalleCatalogo;
        $datos['docentes'] = $docentes;
        $datos['entidadesEducativas'] = $entidadesEducativas;
        $datos['curricula'] = $curricula;
        $datos['idProducto'] = $idProducto;
        $datos['empresa'] = $empresa;
        $datos['productosRelacionados'] = $productosRelacionados;
        $datos['codPedido'] = $codPedido;
        $datos['autores'] = $autores;
        $datos['cod_cliente'] = $cod_cliente;
        $datos['datosUser'] = $datosUser;		
		return $layout->render('./_vistas/producto-ebook.phtml',$datos);

	}		

	

	public function formContacto($arg) {

		return $arg;

	}		

	

}