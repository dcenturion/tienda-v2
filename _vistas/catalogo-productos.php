<?php
require_once './_vistas/layout.php';

class CatalogoProductos{

    private $_parm;
    public  function __construct($_parm=null)
    {
        $empresa = $_parm["entidad"];
		
		$categoria = $_parm["categoria"];
        $subcategoria = $_parm["subcategoria"];

        $layout  = new Layout();
		$cnOwlPDO = PDOConnection();
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
		
		$dataB = totalItemPedidosAPP($cnOwlPDO,$sessionId);
		
		$categoriasData =categoriasAPP($cnPDO,$empresa);
		
		$propiedadesEntidad = array();
        $propiedadesEntidad['principal'] = $reg;		
        $propiedadesEntidad['totalItem'] = $dataB;		
        $propiedadesEntidad['datosUser'] = $datosUser;	
        $propiedadesEntidad['empresa'] = $empresa;		
        $propiedadesEntidad['categoria_get'] = $categoria;		
        $propiedadesEntidad['categorias'] = $categoriasData;		
        $propiedadesEntidad['sub_categorias'] = $categoriasData;	
        $propiedadesEntidad['cod_cliente'] = $cod_cliente;						
		
		$datos = array();
        $datos['propiedadesEntidad'] = $propiedadesEntidad;  
		echo $layout->main($this->viewHome($empresa,$categoria,$subcategoria,$cod_cliente,$cnOwlPDO),$datos);

    }



    public function viewHome($empresa,$categoria,$subcategoria,$cod_cliente,$cnOwlPDO) {
		
		$productosCatalogo = catalogoProductosDeEntidadAPP($cnOwlPDO,$empresa,$busqueda,$categoria,$subcategoria);
		
		$layout  = new Layout();
		$datos = array();		
		$datos['productosCatalogo'] = $productosCatalogo;
		$datos['categoria'] = $categoria;
		$datos['empresa'] = $empresa;
		$datos['subcategoria'] = $subcategoria;
		$datos['categoriaFiltro'] = categoriasAPPSubMenu($cnPDO,$empresa,$categoria);
		$datos['cod_cliente'] = $cod_cliente;		
        return $layout->render('./_vistas/catalogo_productos.phtml',$datos);



    }



    public function formContacto($arg) {



        return $arg;

    }



}