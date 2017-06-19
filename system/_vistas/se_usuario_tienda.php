<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_usuarios.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_usuario_tienda.php";
$enlacePopup = "se_usuario_tienda.php";

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

	// $_SESSION['usuario_entidad']['string'] = "90";
	// var_dump($_SESSION);
    // WE("");
	

			
    switch ($Arg) {

        case 'registro':
		
			// ini_set('session.use_strict_mode', 1);
			// $sid = md5('wuxiancheng.cn');
			$sesionId = session_id();
					
				
			//Creación del cliente
			$entidad = get("entidad");	
			
			
		    $Query=" SELECT Codigo FROM entidades WHERE SubDominio = :SubDominio  ";
			$rg = OwlPDO::fetchObj($Query, ["SubDominio" => $entidad] ,$cnPDO);
		    $CodigoEntidad = $rg->Codigo;
			
		    $Query=" SELECT Codigo FROM clientes WHERE 	Email = :Email AND Entidad = :Entidad  ";
			$rg = OwlPDO::fetchObj($Query, ["Email" => post("email"),"Entidad"=>$CodigoEntidad] ,$cnPDO);
		    $CodigoCliente = $rg->Codigo;
			
			if(empty($CodigoCliente)){
				
				$info=detectClient();
					
				$ipvisitante = $_SERVER["REMOTE_ADDR"];
				$tipoDispositivo = tipoDispositivo();
				$data = array(
				'SesionId' => $sesionId
				, 'IpRegistro' => $ipvisitante
				, 'Nombre' => post("name")
				, 'Descripcion' =>  post("lastname")
				, 'Email' =>  post("email")
				, 'IpRegistro' => $ipvisitante
				, 'DispositivoRegistro' => $tipoDispositivo
				, 'Navegador' => $info["browser"]
				, 'VersionNavegador' => $info["version"]
				, 'SistemaOperativo' => $info["os"]
				, 'TipoCliente' => "Inscrito"
				, 'Entidad' => $CodigoEntidad
				, 'TerminosSuscripcion' =>  post("terminos")
				, 'FechaHoraCreacion' => $FechaHora
				, 'FechaHoraActualizacion' => $FechaHora
				, 'UsuarioCreacion' => $CodigoEntidad
				, 'UsuarioActualizacion' => $CodigoEntidad				
				);
				$rg = OwlPDO::insert('clientes', $data, $cnPDO);
				$CodigoCliente = $rg['lastInsertId']; 
					
				///Crear Usuario
				crear_usuario($CodigoCliente, $cnPDO, $FechaHora);	
				
                $mensaje = "true";				
			}else{
                $mensaje =  "false";						
			}
		    WE($mensaje);
			
		break;	
		
        case 'IniciarSesion':
		
			$entidad = get("entidad");	
			
		    $rgDatosEmpresa = datosEntidad($entidad,$cnPDO);
			$CodEntidad = $rgDatosEmpresa->CodEntidad;	
			
			$username = post("Usuario");
			$password = post("Contrasena");

            $rg = login($username,$password,$CodEntidad,$cnPDO);
			$UsuarioCod = $rg->Codigo; 	
			// W("UsuarioCod:: ".$UsuarioCod);
			if(empty($UsuarioCod)){
			   $mensaje = "false"; 
			}else{
			   $mensaje = "true"; 		
			}
			
		    WE($mensaje);
			
		break;	
		
        case 'RecuperarContrasena':
		
			$email = post("email_user");	
			$entidad = get("entidad");
			
		    $Query=" SELECT Codigo FROM entidades WHERE SubDominio = :SubDominio  ";
			$rg = OwlPDO::fetchObj($Query, ["SubDominio" => $entidad] ,$cnPDO);
		    $CodigoEntidad = $rg->Codigo;			
					
			// $Clientes
			$Query=" SELECT Nombre, Descripcion, Email, Codigo
			FROM Clientes WHERE Email = :Email  AND Entidad = :Entidad ";
			$rg = OwlPDO::fetchObj($Query, ["Email" => $email, "Entidad" => $CodigoEntidad ] ,$cnPDO);
			$Nombre = $rg->Nombre;	
			$Descripcion = $rg->Descripcion;	
			$Email = $rg->Email;	
			$CodigoCliente = $rg->Codigo;	
			
			if(empty($Email)){
				
				$mensaje = '    <div class="row">
									<div class="col-xs-12 text-center">
										<h3 class="text-uppercase" id="modal-recover-account-confirm-title">
											<strong>Error</strong>
										</h3>
										<p>
											El correo ingresado no se encuentra registrado. '.$email.' '.$CodigoEntidad.'
										</p>
										<br />
									</div>
								</div>';
				WE($mensaje);
			}
			
			$Query=" SELECT 
			Codigo, ImagenLogo, ColorCabeceraEmail
			, ColorCuerpoEmail, ColorFondoEmail, TextoEmailInscripcion, TextoEmailCompra
			, EmailSoporteCliente
			, NroTelefonoSoporteCliente
			, SubDominio
			, ColorMenuHorizontal
			FROM entidades WHERE Codigo = :Codigo  ";
			$rg = OwlPDO::fetchObj($Query, ["Codigo" => $Entidad ] ,$cnPDO);
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
			
			$dominio = siteUrl();
			
			if( $ColorCabeceraEmail == "Ninguno"){  
				$ColorCabeceraEmail = "#fff";
			}

			$Cabecera = "
				<div style='background-color:".$ColorCabeceraEmail.";padding:50px;'>
					<img src='http://fri.com.pe/_imagenes/iconos/logo.png' >
					<br>
					<p style='text-align:right;color:".$ColorMenuHorizontal.";font-weight:bold;'>SISTEMA DE VENTAS ONLINE</p>
				</div>

			";
			
			$Cuerpo = "
				
				<div style='background-color:".$ColorCabeceraEmail.";padding:50px;'>
					<p>Estimado(a) ".$Nombre ."  ". $Descripcion .",</p>
					<br>
					<p> Hemos recibido tu solicitud para el cambio de tu contraseña, has clic para aplicar el cambio.</p>	
					<br>
				</div>
				
				<div style='background-color:".$ColorCabeceraEmail.";padding:30px 30px;text-align: center;'>
					<a href='".$dominio."cambiar-clave/entidad/".$SubDominio."/cliente/".$CodigoCliente."'  style='text-decoration: none; background-color: ".$ColorMenuHorizontal."; padding: 10px 20px; color: #fff;'>CAMBIAR CLAVE</a>
					<br>
				</div>				
			
			";
			
			$Footer = "
				<div style='background-color:".$ColorCabeceraEmail.";padding:50px;'>
					<br>
					<p style='color:#8e8d8d;'>Email de Soporte: ".$EmailSoporteCliente."</p>
					<p style='color:#8e8d8d;'>Teléfono de Soporte: ".$NroTelefonoSoporteCliente."</p>
				</div>
				
			";				

			
			$NombreReceptor = "COMPRAS";				
			$Asunto = "CAMBIO DE CLAVE";	
			
			$data = array('Cabecera' => $Cabecera ,'Cuerpo'=> $Cuerpo
						   , 'ColorFondo' => $ColorFondoEmail, 'Footer' => $Footer
						   , 'NombreReceptor' => $NombreReceptor, 'Asunto' => $Asunto
						   );			
			emailInscripcion($data,$Email,$cnPDO);
			
			$mensaje = '    <div class="row">
								<div class="col-xs-12 text-center">
									<h3 class="text-uppercase" id="modal-recover-account-confirm-title">
										<strong>Revisa tu correo electrónico</strong>
									</h3>
									<p>
										Hemos enviado un mensaje con las
										instrucciones necesarias para que puedas
										reestablecer tu contraseña.
									</p>
									<br />
								</div>
							</div>';
		    WE($mensaje);
			
		break;			
		
        case 'CambiarContrasena':
		
			$clienteGet = get("clienteGet");	
			$entidad = get("entidad");
			
		    $Query=" SELECT Email FROM Clientes WHERE Codigo = :Codigo  ";
			$rg = OwlPDO::fetchObj($Query, ["Codigo" => $clienteGet] ,$cnPDO);
		    $Email = $rg->Email;	
			
			$Clave =  _crypt(post("password1"));
		
			$reg = array(
			'Clave' => $Clave
			);
			$where = array('Email' => $Email );
			$rg = OwlPDO::update('entidades', $reg , $where, $cnPDO);
			
	        WE($Clave);
        break;			
		
    }
}


?>
