<?php
require_once './_vistas/layout.php';

class productoEstandar{

	private $_parm;
    public  function __construct($_parm=null)
	{
		$id = $_parm["id"];
        $site = "Eventos";
	    $layout  = new Layout();

		echo $layout->main($this->viewHome($id),$id,$site);

	}

	

	public function viewHome($id) {

		$cnOwlPDO = PDOConnection();
		$FechaHoraSrv = FechaHoraSrv();
		$empresa = $_SESSION['empresa'];
		$user = $_SESSION['user'];	

		$layout  = new Layout();

		$datos = array();
        $datos['numeros'] = "996 614 532 / 999 777 768 ";

        $datos['direccion'] = "Lima - Perú ";

        $datos['email'] = "informes@episodiosplanning.com";
		
	    $sql = "SELECT AR.ImagenPresentacionA, MA.Codigo AS CodigoMov
		, AR.Nombre, FA.Codigo AS Familia, FA.Descripcion as FamiliaDesc
		, AR.Descripcion, LA.Descripcion as LineaDescripcion, MA.Referencias
		, MA.Inversion, MA.FormaPago, MA.FormaPago, MA.	Detalles, MA.InformesInscripciones
		FROM  articulos AR
		INNER JOIN movimiento_almacen MA ON AR.Codigo = MA.Articulo
		INNER JOIN lineaarticulo LA ON AR.Lineaarticulo = LA.Codigo
		INNER JOIN familiaarticulo FA ON AR.Familiaarticulo = FA.Codigo
		WHERE LA.Codigo = 2 AND MA.Codigo = {$id}
		GROUP BY MA.Codigo
		";    
		
		$rg = fetch($sql,$cnOwlPDO);
		$ImagenPresentacionA = $rg["ImagenPresentacionA"];		
		$Descripcion = $rg["Descripcion"];		
		$Nombre = $rg["Nombre"];		
		$Familia = $rg["Familia"];		
		$FamiliaDesc = $rg["FamiliaDesc"];		
		$LineaDescripcion = $rg["LineaDescripcion"];		
		$Referencias = $rg["Referencias"];		
		$Inversion = $rg["Inversion"];		
		$FormaPago = $rg["FormaPago"];		
		$Detalles = $rg["Detalles"];		
		$InformesInscripciones = $rg["InformesInscripciones"];	
		
		$sql = "SELECT AR.ImagenPresentacionA, MA.Codigo AS CodigoMov
		, AR.Nombre, FA.Codigo AS Familia
		, AR.Descripcion
		FROM  articulos AR
		INNER JOIN movimiento_almacen MA ON AR.Codigo = MA.Articulo
		INNER JOIN lineaarticulo LA ON AR.Lineaarticulo = LA.Codigo
		INNER JOIN familiaarticulo FA ON AR.Familiaarticulo = FA.Codigo
		WHERE LA.Codigo = 2 AND AR.Familiaarticulo = {$Familia}
		GROUP BY MA.Codigo
		";    
		$productos_familia = fetchAll($sql,$cnOwlPDO);
		
		
		$datosEvento = array();
        $datosEvento['img'] = "/system/_articulos/".$ImagenPresentacionA;			
        $datosEvento['NombreArticulo'] = $Nombre;			
        $datosEvento['DescripcionArticulo'] = $Descripcion;			
        $datosEvento['FamiliaDesc'] = $FamiliaDesc;			
		

		$layout->productos_familia = $productos_familia;
		$layout->formContacto = $layout->render("./_vistas/form_contactos.phtml",$datos); 

		return $layout->render('./_vistas/producto-estandar.phtml',$datosEvento);

		

	}		

	

	public function formContacto($arg) {

		

		return $arg;

	}		

	

}