<?php 

header("Access-Control-Allow-Origin:*");
require_once('../funciones.php');
require_once('../conexiones.php');

if( isset($_POST["action"]) ){
	$action	 = $_POST["action"];
	$g       = new Ebooks($params);
	WE( $g->$action() );
}



/**
*   
*/
class Ebooks 
{	
	private $PDO     = null;
	private $entity = "";

	function __construct($params)
	{
		$this->PDO 	     = PDOConnection();
		$this->entity    = $_POST["entity"];

	}

	function getAllebooks(){
		$qEbooks = "    SELECT
						T1.Titulo,AL.AlmacenCod
						FROM almacen AL
						INNER JOIN articulos AS T1 ON AL.Producto = T1.Producto 
						INNER JOIN documento AS doc ON T1.ProductoFab = doc.codigo 
						WHERE doc.entidad = '$this->entity' AND  
						AL.tipoproducto IN ('revista','libro') 
						group by T1.ProductoFab 
						ORDER BY AL.FechReg DESC   
						";

		$stmt = $this->PDO;
		$stmt = $stmt->prepare($qEbooks);
		$ebooks = $stmt->execute();
		$arrEbooks = [];
		while ($ebook = $stmt->fetchObject()) {
			$arrEbooks[] =  $ebook;
		}
		WE(json_encode($arrEbooks));

	}
	function deletebyidwarehouse(){

		$rsp = fetch("SELECT al.AlmacenCod id_warehouse,ar.IdArticulo id_article,doc.codigo id_document FROM almacen al 
						INNER JOIN articulos ar ON al.Producto=ar.Producto 
						INNER JOIN empresa emp ON al.Entidad = emp.PaginaWeb
						INNER JOIN documento doc ON ar.ProductoFab = doc.codigo 
						WHERE emp.KeySuscripcion = '".$_POST['idproveedor']."' AND al.AlmacenCod IN (".$_POST['idwarehouses'].") ");

		if($rsp){
			$rsp = (object)$rsp;
			delete("almacen",["AlmacenCod"=>$rsp->id_warehouse]);
			delete("articulos",["IdArticulo"=>$rsp->id_article]);
			delete("detalle_epub",["coddocumento"=>$rsp->id_document]);
			delete("documento",["codigo"=>$rsp->id_document]);
		}
		WE( json_encode(["success"=>true]) );
	}

}