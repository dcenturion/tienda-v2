<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_configuracion.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_configuracion_imagenes.php";
$enlacePopup = "se_configuracion_imagenes.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
$_SESSION['menuVertical']= "4";

if (get('Main') != '') {Main(get('Main'));}
if (get('Busqueda') != '') {Busqueda(get('Busqueda'));}

if (get("metodo") != "") {
        
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario, $Entidad;
        
        if (get("metodo") == "FEntidadesLogo") {
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";		
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

            if (get("metodo") == "FEntidadesLogo") {
				
				p_gf_udp("FEntidadesLogo",$cnPDO,$Entidad,'Codigo');
				
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
	
    switch ($Arg) {

        case 'Principal':
     
			$pestanas = funcion_local_pestanas_A(array("","","","&parm=new]Marca",""));
			
            $listMn = "<i class='icon-chevron-right'></i> Búsqueda Avanzada  [" .$enlace."?Tienda=CreaArticulosRD[panelOculto[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 1 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 2 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";

            $btn = "<i class='icon-pencil'></i> Crear  ]" .$enlace."?Main=CrearRegistroRD]panelOculto]]}";
            $btn .= "<i class='icon-align-justify'></i>  ]SubMenu]{$listMn}]OPCIONES DEL PERFIL]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
			
			$titulo = "<p>CONFIGURACIÓN</p><span></span>";
            $btn_tituloP = panelST2017($titulo, $btn, "auto", "TituloAMSG");
			
	
            $titulo = "<p>IMÁGENES DEL SITIO </p><span></span>";
            $btn_titulo = panelST2017($titulo,"", "auto", "TituloALMB");
			
 
			$uRLForm = "Guardar]" . $enlace . "?metodo=FEntidadesLogo&transaccion=UPDATE]panelB]F]}";
			// $uRLForm .= "Cancelar]" . $enlace . "?metodo=FEntidadesLogo&transaccion=INSERT]\$popupEC.close()]JSB]]Plomo}";
			
			$path = array('ImagenLogo' => '/_imagenes/usuarios/','ImagenLogoColorNegativo' => '/_imagenes/usuarios/');
			
			$tSelectD = array(
			'Tipo' => 'SELECT Codigo,Nombre FROM usuarios_perfiles'
			);
			$form = c_form_adp("", $cnPDO, "FEntidadesLogo", "CuadroA", $path, $uRLForm,$Entidad, $tSelectD, 'Codigo');
			
			
            $html .= "<div id = 'panelOculto' style='float:left;width:100%;' ></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;background:#fff;padding-bottom:29px;' >" . $form . "</div>";
            $panel = array(array('PanelA1-A', '100%', $html));
            $Cuerpo = LayoutPage($panel);
			
            $s = layoutV2($btn_tituloP , $pestanas . $btn_titulo . $Cuerpo); 
			  
			WE(htmlApp( $s));	

            break;
		
        case 'CrearRegistroRD':
		
            $CodRegistro = get("CodRegistro");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?Main=CrearRegistro&', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      

        case 'CrearRegistro':
        

				$titulo = "<p>CREAR USUARIOS</p><span>Completa los datos del formulario</span>";
				$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALM");
			
                $titulo = "";
     
	 
                $uRLForm = "Guardar]" . $enlace . "?metodo=FEntidades&transaccion=INSERT]panelB]F]}";
                $uRLForm .= "Cancelar]" . $enlace . "?metodo=FEntidades&transaccion=INSERT]\$popupEC.close()]JSB]]Plomo}";
				
				$path = array('Foto' => '/_imagenes/usuarios/');
				
                $tSelectD = array(
				'Tipo' => 'SELECT Codigo,Nombre FROM usuarios_perfiles'
				);
                $form = c_form_adp($titulo, $cnPDO, "FEntidades", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
				
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$form = "<div style='float:left;width:100%;padding:0px;' >" . $form . "</div>";
				$html = "<div style='float:left;width:600px;' >" . $btn_titulo . $form . "</div>";
                WE($html);
				
            break;   

        case 'EditarRegistroRD':
		
            $CodRegistro = get("CodRegistro");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?Main=EditarRegistro&CodRegistro={$CodRegistro}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      

        case 'EditarRegistro':
                WE("DDD");
                $CodRegistro = get("CodRegistro");
				$titulo = "<p>EDITAR COLORES </p><span>Selecciona el color favorito</span>";
				$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALM");
			
                $titulo = "";
				
 		        $Query=" SELECT Nombre FROM entidades_color WHERE Entidad = :Entidad AND Estado = :Estado ";
				$rg = OwlPDO::fetchAllObj($Query, ["Entidad" => $Entidad ,  "Estado" => "Aprobado" ] ,$cnPDO);				

				$s = "<form class='CuadroA' id='formColor' name='formColor' method='post' action='javascript:void(null);' enctype='multipart/form-data'>";
				
					$s .= "<ul>";
					
						$s .= "<li>";
								$s .= "<div id='inputForm' > <input type='text' value='Selecciona' name='inputColor' id='inputColor' ></span>";				
								$s .= "<ul >";	
									foreach ($rg as $key) {
										
										$s .= "<li class='item' style='background-color:".$key->Nombre."' 
										onclick=seleccionaComboBox('inputColor','".$key->Nombre."');
										> ".$key->Nombre."</li>";
										
									}
									$s .= "<li class='item' style='background-color:#fff' 
										onclick=seleccionaComboBox('inputColor','Ninguno');
										>Ninguno</li>";
								$s .= "</ul>";	
								$s .= "</div>";		
						$s .= "</li>";	
					$s .= "</ul>";	
					
					$viewdata = array();
					$viewdata['sUrl'] =  $enlace."?metodo=formColor&transaccion=UPDATE&CodRegistro={$CodRegistro}";
					$viewdata['formid'] = "formColor";
					$viewdata['sDivCon'] = "panelB";
					$viewdata['sIdCierra'] = "";
				
					$s .= '<ul class="botonera">';
						$s .= '<li id="PanelBtn-FEntidades">';
					    $s .= "<button onclick=enviaFormS2('" . json_encode($viewdata) . "'); class='" . $atributoBoton[5] . "'  >Guardar</button>";
						$s .= '<button onclick="$popupEC.close()" class="Plomo">Cancelar</button>';
						$s .= '</li>';
					$s .= '</ul>';
					
					
					
				$s .= "</form>";					
				
                $form = $s;
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$form = "<div style='float:left;width:100%;' >" . $form . "</div>";
				$html = "<div style='float:left;width:600px;' >" . $btn_titulo . $form . "</div>";
                WE($html);
				
            break; 			

        case 'EliminaRegistroRD':
            $CodRegistro = get("CodRegistro");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?Tienda=EliminaRegistro&CodRegistro={$CodRegistro}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;  
			
        case 'EliminaRegistro':
            
			$CodRegistro = get("CodRegistro");	
			
            $btn = "Confirmar ]" .$enlace."?Tienda=EliminaRegistroAccion&CodRegistro={$CodRegistro}]panelB]]}";
            $btn .= "<div onclick=\$popupEC.close(); >Cancelar</div> ]" .$enlace."?Tienda=CreaArticulos]PanelA1-B]]plomo}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR </p><span>El artículo seleccionado</span>";
            $btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALM");
			
			WE($btn_titulo);
            break;  		
        case 'EliminaRegistroAccion':
		
            $CodRegistro = get("CodRegistro");
			
		    $Query=" SELECT Entidades FROM usuarios WHERE Codigo = :CodRegistro ";
			$rg = OwlPDO::fetchObj($Query, ["CodRegistro" => $CodRegistro] ,$cnPDO);
		    $Entidades = $rg->Entidades;			
					
	        DReg("usuarios", "Codigo", $CodRegistro, $cnOld);	
	        DReg("entidades", "Codigo",$Entidades, $cnOld);	
			
			W(" <script> \$popupEC.close();</script>");
			Tienda("Site");
			WE("");
            break; 	

        	
        default:
            exit;
            break;
    }
}


function htmlPantone(){
    global $Entidad, $cnPDO, $enlace;
	
	$Query=" SELECT ColorMenuHorizontal,ColorMenuHorizontal_Boton,ColorMenuVertical,ColorMenuVerticalBoton,ColorBotonesInternos FROM entidades WHERE Codigo = :Entidad ";
	$rg = OwlPDO::fetchObj($Query, ["Entidad" => $Entidad ] ,$cnPDO);
	$ColorMenuHorizontal = $rg->ColorMenuHorizontal;			
	$ColorMenuHorizontal_Boton = $rg->ColorMenuHorizontal_Boton;			
	$ColorMenuVertical = $rg->ColorMenuVertical;			
	$ColorMenuVerticalBoton = $rg->ColorMenuVerticalBoton;			
	$ColorBotonesInternos = $rg->ColorBotonesInternos;			
	
	$s= " <div class='Pantone'>
	
	        <div class='row'>
				<div class='Titulo'>
				    COLOR DEL MENÚ HORIZONTAL
				</div>	
				<div 
				     class='Color'
					 style='background-color:".$ColorMenuHorizontal."'
					 onclick=enviaReg('','{$enlace}?Main=EditarRegistroRD&CodRegistro=ColorMenuHorizontal','panelOculto','');
					 >
					 
				</div>					
			</div>
			
	        <div class='row'>
				<div class='Titulo'>
				    COLOR DE BOTONES EN EL MENÚ HORIZONTAL
				</div>	
				<div 
				     class='Color'
					 style='background-color:".$ColorMenuHorizontal_Boton."'
					 onclick=enviaReg('','{$enlace}?Main=EditarRegistroRD&CodRegistro=ColorMenuHorizontal_Boton','panelOculto','');
					 >
					
				</div>					
			</div>	
			
	    
	        <div class='row'>
				<div class='Titulo'>
				    COLOR DEL MENÚ VERTICAL
				</div>	
				<div 
				     class='Color'
					 style='background-color:".$ColorMenuVertical."'
					 onclick=enviaReg('','{$enlace}?Main=EditarRegistroRD&CodRegistro=ColorMenuVertical','panelOculto','');
					 >
					
				</div>					
			</div>
			
	        <div class='row'>
				<div class='Titulo'>
				    COLOR DE BOTONES EN EL MENÚ VERTICAL
				</div>	
				<div 
				     class='Color'
					 style='background-color:".$ColorMenuVerticalBoton."'
					 onclick=enviaReg('','{$enlace}?Main=EditarRegistroRD&CodRegistro=ColorMenuVerticalBoton','panelOculto','');
					 >
					
				</div>					
			</div>	
			
	        <div class='row'>
				<div class='Titulo'>
				    COLOR DE BOTONES INTERNOS 
				</div>	
				<div 
				     class='Color'
					 style='background-color:".$ColorBotonesInternos."'
					 onclick=enviaReg('','{$enlace}?Main=EditarRegistroRD&CodRegistro=ColorBotonesInternos','panelOculto','');
					 >
				</div>					
			</div>	
								
			
	    </div>";
    return $s;		
}	

function Busqueda($arg){
    global $vConex,$cnPDO,$enlace,$urlEmpresa,$idEmpresa,$Entidad, $EntidadPersona,$Codigo_Entidad_Usuario,$enterprise_user,$FechaHora;

    switch ($arg) {
     
        case 'Busqueda':	

		    WE("Busqueda Desactivada");
            $queryd = get("queryd");
			
			
			if(empty($queryd)){
			    $operadorA = "<>";
				$queryd = "77777777777777777777777777";
			}else{
				$operadorA = "LIKE";
			}
			
			
            $sql = " SELECT
				    US.Nombre, 
				    US.Descripcion
					FROM usuarios US
					INNER JOIN entidades ENB ON US.Entidades = ENB.Codigo
					WHERE 
					US.Entidades_Suscriptor = :Entidades_Suscriptor	
					AND ( US.Nombre ".$operadorA." :Nombre OR US.Descripcion ".$operadorA." :Descripcion )
					
			"; 
            
			$where = [
			"Nombre" =>'%'.$queryd.'%',
			"Descripcion" =>'%'.$queryd.'%',
			"Entidades_Suscriptor" => $Entidad
			];
			
			$html ="";	
			$countcolumn = OwlPDO::fetchAllArr($sql,$where,$cnPDO);
			$cont = 0;
			foreach ($countcolumn as $reg) {
				$con +=1;	
				// $viewdata = array();
				// $viewdata['Alias'] = $reg["Alias"];
				$html .= "<div id='p-".$con."' class=item onclick=buscadorAccionItem('p-".$con."'); > ".$reg["Nombre"]." ".$reg["Descripcion"]."</div>";
			}
            // vd($countcolumn);			
		    WE($html);
			
		break;
    }		
}


?>
