<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
// require_once('../_librerias/php/funcion_email.php');
require_once('funciones_se_usuarios.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_consulta_tienda.php";
$enlacePopup = "se_consulta_tienda.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
$_SESSION['menuVertical']= "3";



if (get('Main') != '') {Main(get('Main'));}

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
	$sesionId = session_id();
	
    switch ($Arg) {

        case 'Enviar':
		
			//Creación del cliente
			$empresa = get("empresa");	
			$codPedido = get("codPedido");	
			$idProducto = get("idProducto");	
			$cod_cliente = get("cod_cliente");	
			$nombre = post("nombre");	
			$email = post("email");	
			$comment = post("comment");	
			
		    $Query=" SELECT 
			Codigo, ImagenLogo, ColorCabeceraEmail
			, ColorCuerpoEmail, ColorFondoEmail, TextoEmailInscripcion, TextoEmailCompra
			, EmailSoporteCliente
			, NroTelefonoSoporteCliente
			, SubDominio
			, ColorMenuHorizontal
			FROM entidades WHERE SubDominio = :SubDominio  ";
			$rg = OwlPDO::fetchObj($Query, ["SubDominio" => $empresa] ,$cnPDO);
		    $CodigoEntidad = $rg->Codigo;			
		    $ColorCabeceraEmail = $rg->ColorCabeceraEmail;			
		    $ColorCuerpoEmail = $rg->ColorCuerpoEmail;			
		    $ColorFondoEmail = $rg->ColorFondoEmail;			
		    $ImagenLogo = $rg->ImagenLogo;			
		    $TextoEmailInscripcion = $rg->TextoEmailInscripcion;			
		    $TextoEmailCompra = $rg->TextoEmailCompra;			
		    $EmailSoporteCliente = $rg->EmailSoporteCliente;			
		    $NroTelefonoSoporteCliente = $rg->NroTelefonoSoporteCliente;			
		    $SubDominio = $rg->SubDominio;			
		    $ColorMenuHorizontal = $rg->ColorMenuHorizontal;			
			
		    $Query=" SELECT Codigo FROM movimiento_almacen WHERE AliasId = :AliasId AND Entidad = :Entidad ";
			$rg = OwlPDO::fetchObj($Query, ["AliasId" => $idProducto,"Entidad"=>$CodigoEntidad] ,$cnPDO);
		    $Codigo_Movimiento_almacen = $rg->Codigo;		
			
			if($cod_cliente == ""){$cod_cliente = 0; }
			
		    $Query=" SELECT SesionId FROM clientes WHERE Codigo = :Codigo ";
			$rg = OwlPDO::fetchObj($Query, ["Codigo"=>$cod_cliente] ,$cnPDO);
		    $SesionId = $rg->SesionId;
			
			
			if(!empty($cod_cliente)){

					$Query=" SELECT Email, Codigo FROM clientes WHERE Email = :Email AND Entidad = :Entidad ";
					$rg = OwlPDO::fetchObj($Query, ["Email"=>post("email"),"Entidad"=>$CodigoEntidad] ,$cnPDO);
					$Email = $rg->Email;
					$Codigo = $rg->Codigo;
					if(!empty($Email)){ //Si el email ya existe debo reemplazar el código del cliente existente en la proforma
					    
						if(!empty($codPedido)){
							
							$data = array(
							'Clientes' => $Codigo		
							);
							$where = array('Codigo' => $codPedido );
							$rg = OwlPDO::update('proformas_cab', $data , $where, $cnPDO);	

							$where = array('Codigo' => $cod_cliente);
							$rg = OwlPDO::delet('clientes', $where, $cnPDO); 
							
						}					     
						
					}else{
			
						$data = array(
						'SesionId' => ""
						, 'Nombre' => post("nombre")
						, 'Email' =>  post("email")
						, 'Telefono' =>  post("telefono")			
						);
						
						$where = array('Codigo' => $cod_cliente );
						$rg = OwlPDO::update('clientes', $data , $where, $cnPDO);
						
						if(!empty($SesionId)){
							///Crear Usuario
							crear_usuario($cod_cliente, $cnPDO, $FechaHora);					

						}
					}
					
			}else{
				    
					$info=detectClient();						
					$ipvisitante = $_SERVER["REMOTE_ADDR"];
					$tipoDispositivo = tipoDispositivo();
					$data = array(
					'SesionId' => ""
					, 'IpRegistro' => $ipvisitante
					, 'Nombre' => post("nombre")
					, 'Email' =>  post("email")
					, 'Telefono' =>  post("telefono")
					, 'IpRegistro' => $ipvisitante
					, 'DispositivoRegistro' => $tipoDispositivo
					, 'Navegador' => $info["browser"]
					, 'VersionNavegador' => $info["version"]
					, 'SistemaOperativo' => $info["os"]
					, 'TipoCliente' => "Inscrito"
					, 'Entidad' => $CodigoEntidad
					, 'FechaHoraCreacion' => $FechaHora
					, 'FechaHoraActualizacion' => $FechaHora
					, 'UsuarioCreacion' => $CodigoEntidad
					, 'UsuarioActualizacion' => $CodigoEntidad				
					);
					$rg = OwlPDO::insert('clientes', $data, $cnPDO);
					$cod_cliente = $rg['lastInsertId']; 

					
				///Crear Usuario
				crear_usuario($cod_cliente, $cnPDO, $FechaHora);					              				
			}
			
			
			$data = array(
			'Nombre' => post("nombre")
			, 'Descripcion' => post("comment")
			, 'Movimiento_Almacen' => $Codigo_Movimiento_almacen
			, 'Cliente' => $cod_cliente
			, 'Entidad' => $CodigoEntidad
			, 'FechaHoraCreacion' => $FechaHora
			, 'FechaHoraActualizacion' => $FechaHora
			, 'UsuarioCreacion' => $CodigoEntidad
			, 'UsuarioActualizacion' => $CodigoEntidad				
			);
			$rg = OwlPDO::insert('consulta', $data, $cnPDO);	
			$ColorCabeceraEmail = $rg->ColorCabeceraEmail;			
			$ColorCuerpoEmail = $rg->ColorCuerpoEmail;			
			$ColorFondoEmail = $rg->ColorFondoEmail;		
			
	        WE("Mensaje Enviado");
			
		break;	


		
  
    }
}


?>
