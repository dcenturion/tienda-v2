<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/funcionesAdd.php');
require_once('../_librerias/php/conexiones.php');
error_reporting(E_ERROR);

$enlace = "_vistas/se_login.php";
$cnPDO = PDOConnection();

if( get('site') !=''){ 
site(get('site'));
}
if(protect(get("metodo")) != ""){// esta condicion inicia cuando se procesa la info de un formulario

    if(protect(get("TipoDato")) == "archivo"){
    }
    function p_interno($codigo,$campo){
        if(protect(get("metodo")) == "SysFomr1"){
        if ($campo == "Descripcion"){
            $vcamp = post($campo);
            $valor = " 'Form_".$vcamp." ' ";
        }else
            
        return $valor;
        }
    }
    function p_before($codigo){
        global $vConex,$ConexSisEmp;
        if ( protect(get('metodo')) == 'regsolicitud'){
            InsertarCuenta();
        }
    }
    if(protect(get("TipoDato")) == "texto"){
		
        if(protect(get("transaccion")) == "UPDATE"){
        }
        if(protect(get("transaccion")) == "INSERT"){
      
        }
        if(protect(get("transaccion")) == "OTRO"){
            if(protect(get("metodo")) == "login"){P_Login();}
        }
    }
	if(protect(get("transaccion")) == "DELETE"){
	}
   exit();
}


function site($arg,$msg=null){
    global $cnPDO,$enlace,$ConexSisEmp;
	
	$EntidadGet = get("sitio");
	if(empty($EntidadGet)){
	    $EntidadGet = $_SESSION['EntidadUrl']['string'];	
	}else{
	    $EntidadGet = get("sitio");		
	}	

    switch ($arg){
        case "Login":
		    
			$rgDatosEmpresa = datosEntidad($EntidadGet,$cnPDO);
			
			$ImagenLogo = $rgDatosEmpresa->ImagenLogo;	
			$ColorMenuHorizontal = $rgDatosEmpresa->ColorMenuHorizontal;	
			$ColorBotonesInternos = $rgDatosEmpresa->ColorBotonesInternos;	
			$CodEntidad = $rgDatosEmpresa->CodEntidad;	
	
			$uRLForm = "Iniciar sesión ]".$enlace."?site=iniciar_sesion&sitio=".$EntidadGet."]mensajePN]F]]procesaFormularioLogin}";
			$form = c_form_L("",$cnPDO,"login", "CuadroA", $path, $uRLForm, "");
			
            $btn = Botones($btn, 'botones1', 'usuarios_entidad_matricula');
            $titulo = tituloBtnPn('<span>INICIAR SESIÓN PARA INGRESAR </span><p>Administrador de datos</p>', $btn, 'auto', 'TituloALM');
			
        	$s  .= "
			<div class='panelCentralL' style='width:338px;'>
			    <div style='float:left;width:91%;height:100%;padding: 0% 22% 6% 25%;'><img src='/_imagenes/usuarios/".$ImagenLogo."' width=50% ></div>			    
				<div id='mensajePN' style='position: absolute;width:100%;' ></div>
				<div class='panelCentralEstilo' >
				".$titulo. $form."
				</div>
			</div>
			";

			WE($s);						
            break;
			
        case "iniciar_sesion":

		    $rgDatosEmpresa = datosEntidad($EntidadGet,$cnPDO);
			$CodEntidad = $rgDatosEmpresa->CodEntidad;	
			
			$username = post("Usuario");
			$password = post("contrasena");

			$Q_U2 = " SELECT USU.Codigo,USU.Entidades_Suscriptor, ENT.Usuario, ENT.KeySuscripcionProEducative FROM usuarios USU 
			INNER JOIN entidades ENT ON USU.Entidades = ENT.Codigo
			WHERE ENT.Usuario = '{$username}' AND ENT.Clave = '{$password}' AND USU.Entidades_Suscriptor = '{$CodEntidad}' ";
			$rg = fetch($Q_U2, $cnPDO);
			$Entidades_Suscriptor = $rg['Entidades_Suscriptor']; 
			$UsuarioCod = $rg['Codigo']; 
			$KeySuscripcionProEducative = $rg['KeySuscripcionProEducative']; 
			
			if(empty($UsuarioCod)){
				
					$tituloMsg = tituloBtnPn('<span></span>
					<p>VERFICA LOS DATOS</p>', $btn, 'auto', 'TituloALMB');

					$result = '  
					<div class="panelMsj" id="panelMsj" >
					<div class="panelMsj panelCentralMsj alert-success">
					<a href="#" class="btn_cerrar" onclick=panelAdm(\'panelMsj\',\'Cierra\');>x</a>
					'.$tituloMsg.'
					<div>
					<p>El usuarios y contraseña que has ingresado no son válidos.
					<BR> !! VUELVE A INTENTAR!! <img src="./_imagenes/jaguar.jpg">
					<p>

					</div>
					</div>
					</div>';
					 
			}else{
			         $result = "True";
					 $_SESSION['Usuario']['string'] = $UsuarioCod;
					 $_SESSION['Entidad']['string'] = $Entidades_Suscriptor;
					 $_SESSION['KeySuscripcionProEducative']['string'] = $KeySuscripcionProEducative;
					 
			}
		    WE($result);
            break;
	
	exit;
    break;
    }
    WE($s);
}



function pAnimado2( $cont )
{
    $s = "<div class='PanelAnimado-001' >";
    $s = $s . "<div class='PanelAnimado-001-animate' style='width:100%;'>";
    $s = $s . $cont;
    $s = $s . "</div>";
    $s = $s . "</div>";
    return $s;
}
function log_msg($msg){
    $return="<div class='MensajeB Error' style='float:left;width:90%;'>
                <div style='display:block;'>{$msg}</div>
            </div>";
    return $return;
}
?>
