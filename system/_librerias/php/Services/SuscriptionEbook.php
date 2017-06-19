<?php 

header("Access-Control-Allow-Origin:*");

require_once(dirname(__FILE__) . '/../conexiones.php');
require_once(dirname(__FILE__) . '/../funciones.php');
require_once(dirname(__FILE__) . '/../global_matricula.php');



if(isset($_REQUEST["action"])){
	$action = $_REQUEST["action"];
	$g = new SuscriptionEbook();
	WE( $g->$action() );
}


class SuscriptionEbook
{
	private $pdo;
	function __construct($params=null)
	{
		$this->pdo = PDOConnection();
	}
	function suscriptionecommerce(){
		$rsp = false;$message="";
		if(isset($_REQUEST["keysuscription"])){

						$rg = fetch("SELECT tab2.IdUsuario ,tab2.Usuario , emp.PaginaWeb
						FROM usuario_entidad AS tab1 
						LEFT JOIN usuarios AS tab2 ON tab1.Usuario = tab2.Usuario
						INNER JOIN empresa AS emp ON  tab1.EntidadCreadora = emp.PaginaWeb 
						WHERE  tab2.CodigoParlante =emp.PaginaWeb  AND emp.KeySuscripcion = '".$_REQUEST["keysuscription"]."' 
						GROUP BY tab2.Usuario");
			if($rg){
				$data['alumno']   			= $_REQUEST["user"].'Alumno';
				$data['productoId'] 		= $_REQUEST["codwherehouse"];
				$data['proveedor']  		= $rg['IdUsuario'];
				$data['tipoarticulo'] 		= "Ebook";
				$data['Nombres'] 			= $_REQUEST["name"];
				$data['Apellidos'] 			= $_REQUEST["lastname"];
				$data['entidadCreadora'] 	= $rg['PaginaWeb'];
				$data['Clave'] 				= $_REQUEST['password'];
				
				$resp = MatriculaEcomerce(conexSys(), $data,$_REQUEST["website"]);

				if($resp["success"]==true)
					$rsp=true;

				$message = $resp["msg"];
			}

		}
		return json_encode(["success"=>$rsp,"message"=>$message]);
	}
	function byidwarehouse(){
		$rsp = false;$data="";

		if(isset($_REQUEST["idproveedor"]) && isset($_REQUEST["idwarehouses"]) ){

			$arrregisters = fetchAll("	SELECT m.IdMatricula,al.AlmacenCod,m.Cliente user,u.Nombres name,u.Apellidos lastname,m.FechaInscripcion FROM matriculas m 
										INNER JOIN almacen al ON m.Producto = al.AlmacenCod
										INNER JOIN articulos a ON al.Producto=a.Producto
										INNER JOIN empresa e ON m.Entidad = e.PaginaWeb
										INNER JOIN usuarios u ON m.Cliente = u.IdUsuario
										WHERE a.Producto LIKE '%DOC%' AND al.AlmacenCod IN (".$_REQUEST["idwarehouses"].") AND e.KeySuscripcion='".$_REQUEST["idproveedor"]."'
										ORDER BY m.IdMatricula DESC");
			if($arrregisters){
				$rsp=true;
				$data=$arrregisters;
			}
		}

		return json_encode(["success"=>$rsp,"data"=>$data]);
	}
}