<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_tienda.php');

if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_tienda.php";
$enlacePopup = "se_tienda.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
$_SESSION['menuVertical']= "2";

if (get('Main') != '') {Main(get('Main'));}

if (get("metodo") != "") {
        
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario, $Entidad;
        
 
              
    }

    function p_before($codigo) {
        if (get("transaccion") == "INSERT") {	
			if (get("metodo") == "articulos") {		
				 IngresaAlmacen($codigo);
				 // GeneraAlias($codigo);
			}
        }		
    }

    if (get("TipoDato") == "texto") {

        if (get("transaccion") == "UPDATE") {
			
        }

        if (get("transaccion") == "INSERT") {
			
        }
    }

    if (get("transaccion") == "DELETE") {
        if (get("metodo") == "sys_tipo_input") {
            DReg("sys_tipo_input", "Codigo", "'" . get("codigo") . "'", $vConex);
            datosAlternos("CreacionTipoDato");
        }
    }

    exit();
}


function Main($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup ,$Entidad;
	
    switch ($Arg) {

        case 'Conexion':
		
			$sesionUsuarioEntidad = $_SESSION['usuario_entidad']['string'];
			
		    $Query=" SELECT US.Email, US.Nombre, US.Descripcion, ET.Clave
			         FROM usuarios AS US 
					 INNER JOIN entidades ET ON US.Entidades = ET.Codigo
					 WHERE US.Codigo = :Codigo  ";
			$rg = OwlPDO::fetchObj($Query, ["Codigo" => $sesionUsuarioEntidad] ,$cnPDO);
		    $Cod_Email = $rg->Email;
		    $Nombre = $rg->Nombre;
		    $Descripcion = $rg->Descripcion;
		    $Clave = $rg->Clave;
			
		
		    $entidad = get("empresa");
		    $producto = get("producto");
		    $tipoProducto = get("tipoProducto");
			
			if($tipoProducto == 4 ){
				$tipoProducto = "Curso";
			}elseif($tipoProducto == 1 ){
				$tipoProducto = "Ebook";				
			}
			
						// WE($entidad);
		    $Query=" SELECT Codigo, KeySuscripcionProEducative, UsuarioProEducative FROM entidades WHERE SubDominio = :SubDominio  ";
			$rg = OwlPDO::fetchObj($Query, ["SubDominio" => $entidad] ,$cnPDO);
		    $KeySuscripcionProEducative = $rg->KeySuscripcionProEducative;
		    $UsuarioProEducative = $rg->UsuarioProEducative;


			$cur = conexionCurPost(PLATAFORMA_EDUCATIVA."_librerias/php/Services/SrvUsuario.php",[
			'CodArticuloVenta'=> $producto,
			'KeyEntityProEducative'=>$KeySuscripcionProEducative,
			'Email'=> $Cod_Email,
			'Nombres'=> $Nombre,
			'Apellidos'=> $Descripcion,
			'Secure_password'=> $Clave,
			'TipoProducto'=> $tipoProducto,
			'EntidadCreadoraProEducative'=> $UsuarioProEducative,
			'SesionIdCliente'=> session_id()
			],true,"POST");

	        echo json_encode($cur);
			WE("");
        break;
    }
}



?>
