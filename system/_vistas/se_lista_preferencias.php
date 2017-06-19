<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_usuarios.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_lista_preferencias.php";
$enlacePopup = "se_lista_preferencias.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
$_SESSION['menuVertical']= "3";

if (get('Main') != '') {Main(get('Main'));}
if (get('Busqueda') != '') {Busqueda(get('Busqueda'));}

if (get("metodo") != "") {
        
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario, $Entidad;
        
        if (get("metodo") == "FEntidades") {
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsusriosAlterno") {
                $valor = "'" .get("Usuario"). "'";
            } elseif ($campo == "Entidad") {
                $valor = $Entidad;	
            } elseif ($campo == "Tipo") {
                $valor = 2;					
            } else {
                $valor = "";
            }
            return $valor;
        }
		
        if (get("metodo") == "FUsuarios") {
			
            if ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";
            } else {
                $valor = "";
            }
            return $valor;
        }		
		
              
    }

    function p_before($codigo) {
        if (get("transaccion") == "INSERT") {	
			if (get("metodo") == "FEntidades") {		
				VinculacionUsuario($codigo);
			}
        }		
    }

    if (get("TipoDato") == "texto") {

        if (get("transaccion") == "UPDATE") {

            if (get("metodo") == "FUsuarios") {
                p_gf_udp("FUsuarios", $cnPDO, get("CodRegistro"),'Codigo');
				ActualizaFoto();
				
				W(" <script> \$popupEC.close();</script>");				
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Main("Principal");
				WE("");
            }
		
        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "FEntidades") {
				
                p_gf_udp("FEntidades",$cnPDO,'','Codigo');
				
				// $arr_clientes = array('nombre'=> 'Jose', 'edad'=> '20', 'genero'=> 'masculino',
				// 'email'=> 'correodejose@dominio.com', 'localidad'=> 'Madrid', 'telefono'=> '91000000');


				//Creamos el JSON
				// $json_string = json_encode($arr_clientes);
				// $file = '/clientes.json';
				// file_put_contents($file, $json_string);
				
				W(" <script> \$popupEC.close();</script>");
                Main("Principal");
            }

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

	ini_set('session.use_strict_mode', 1);
	$sid = md5('wuxiancheng.cn');
	$sesionId = session_id($sid);
			
    switch ($Arg) {

        case 'capturaArticulo':
 
		    $idProducto = get("idProducto");
		    $cod_cliente = get("cod_cliente");
		    $entidad = get("entidad");
			
		    $Query=" SELECT Codigo FROM entidades WHERE SubDominio = :SubDominio  ";
			$rg = OwlPDO::fetchObj($Query, ["SubDominio" => $entidad] ,$cnPDO);
		    $CodigoEntidad = $rg->Codigo;

		    $Query=" SELECT Codigo FROM movimiento_almacen WHERE AliasId = :AliasId  ";
			$rg = OwlPDO::fetchObj($Query, ["AliasId" => $idProducto] ,$cnPDO);
		    $Codigo_movimiento_almacen = $rg->Codigo;			


		    $Query=" SELECT Codigo FROM lista_producto_preferido 
			WHERE Movimiento_Almacen = :Movimiento_Almacen  AND Cliente = :Cliente ";
			$rg = OwlPDO::fetchObj($Query, ["Movimiento_Almacen" => $Codigo_movimiento_almacen, "Cliente" => $cod_cliente] ,$cnPDO);
		    $CodigoLP = $rg->Codigo;
			if(empty($CodigoLP)){
				
				$data = array(
				'Cliente' => $cod_cliente
				, 'Movimiento_Almacen' => $Codigo_movimiento_almacen
				, 'FechaHoraActualizacion' => $FechaHora
				, 'FechaHoraCreacion' => $FechaHora
				, 'Entidad' => $CodigoEntidad
				, 'UsuarioActualizacion' => $CodigoEntidad
				, 'UsuarioCreacion' => $CodigoEntidad
				);
				$rg = OwlPDO::insert('lista_producto_preferido', $data, $cnPDO);
				$CodigoCliente = $rg['lastInsertId']; 
                $msj ="El producto fue guardado en la lista de favoritos";				
			}else{
                $msj ="El producto ya existe en la lista de favoritos";				
			}
		    WE($msj);
			
		break;

        
    }
}


?>
