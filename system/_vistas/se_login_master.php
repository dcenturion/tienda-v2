<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');

error_reporting(E_ERROR);

$enlace = "_vistas/se_login_master.php";
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

    switch ($arg){
        case "Login":
		    
            
			$uRLForm = "Iniciar sesión]".$enlace."?site=iniciar_sesion]mensajePN]F]]procesaFormularioLogin}";
			$form = c_form_L("",$cnPDO,"login", "CuadroA", $path, $uRLForm, "");
			
            $btn = Botones($btn, 'botones1', 'usuarios_entidad_matricula');
            $titulo = tituloBtnPn('<span>INICIAR SESIÓN PARA INGRESAR </span><p> Herramienta de desarrollo</p>', $btn, 'auto', 'TituloALM');
			
        	$s  .= "
			
			<div class='panelCentralL' style='width:300px;'>
			    
				<div id='mensajePN' style='position: absolute;width:100%;' ></div>
				<div class='panelCentralEstilo' >
				".$titulo. $form."
				</div>
			</div>
			";

			WE($s);						
            break;
			
        case "iniciar_sesion":

			$username = post("Usuario");
			$password = post("contrasena");
			$password = _crypt($password);

			$Q_U = "
			SELECT 
			AdminCod, 
			Nombres 
			FROM administradores
			WHERE Usuario = '{$username}' 
			AND Contrasena = '{$password}'";
			$rg = fetch($Q_U, $cnPDO);
			$AdminCod = $rg['AdminCod']; 
			if(empty($AdminCod)){
				
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
					 $_SESSION['master_access'] = $AdminCod;
					 
			}
		    WE($result);
            break;
			
        case "cambiar_contrasena":

			$uRLForm = "Iniciar sesión]".$enlace."?site=cambiar_contrasena_reset]mensajePN]F]]}";
			// $form = c_form_adp("",$cnPDO,"login", "CuadroA", $path, $uRLForm, "");
			$form = c_form_adp($titulo, $cnPDO, "login", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
				
            $btn = Botones($btn, 'botones1', 'usuarios_entidad_matricula');
            $titulo = tituloBtnPn('<span>INICIAR SESIÓN PARA INGRESAR </span><p> Inserte su usuario y contraseña</p>', $btn, 'auto', 'TituloALM');
			
        	$s  .= "
			
			<div id='mensajePN' style='width:300px;'>
			</div>
			<div class='panelCentralL' style='width:300px;'>
			    
				<div id='mensajePN' style='position: absolute;width:100%;' ></div>
				<div class='panelCentralEstilo' >
				".$titulo. $form."
				</div>
			</div>
			";

			WE($s);	
            break;	
			
        case "cambiar_contrasena_reset":

			$username = post("Usuario");
			$password = post("contrasena");
			$password = _crypt($password);
			
			$reg = array('Contrasena' => $password);
			$where = array('Usuario' => $username);
			$rg = OwlPDO::update('administradores', $reg , $where, $cnPDO);
			
			// $data = array('ApellidoPat' => 'Centurion'
			// , 'ApellidoMat' => 'Chinchay'
			// , 'Usuario' => $username
			// , 'Contrasena' => $password
			// );
			// $rg = OwlPDO::insert('administradores', $data, $cnPDO);
			
			// $where = array('AdminCod' => 19);
			// $rg = OwlPDO::delet('administradores', $where, $cnPDO);

			WE($s);	
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
