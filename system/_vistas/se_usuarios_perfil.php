<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_usuarios.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_usuarios_perfil.php";
$enlacePopup = "se_usuarios_perfil.php";

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
        
        if (get("metodo") == "FUsuarios_Perfiles") {
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "Entidad") {
                $valor = $Entidad;				
            } else {
                $valor = "";
            }
            return $valor;
        }
		
  
              
    }

    function p_before($codigo) {
        if (get("transaccion") == "INSERT") {	
			if (get("metodo") == "FEntidades") {		
				// VinculacionUsuario($codigo);
			}
        }		
    }

    if (get("TipoDato") == "texto") {

        if (get("transaccion") == "UPDATE") {

            if (get("metodo") == "FUsuarios_Perfiles") {
                p_gf_udp("FUsuarios_Perfiles", $cnPDO, get("CodRegistro"),'Codigo');
				
				W(" <script> \$popupEC.close();</script>");				
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Main("Principal");
				WE("");
            }
		
        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "FUsuarios_Perfiles") {
				
                p_gf_udp("FUsuarios_Perfiles",$cnPDO,'','Codigo');
				
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
     
			$pestanas = pestanasLocal(array("&parm=new]","]Marca","","",""));
			
            $listMn = "<i class='icon-chevron-right'></i> Búsqueda Avanzada  [" .$enlace."?Tienda=CreaArticulosRD[panelOculto[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 1 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 2 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";

            $btn = "<i class='icon-pencil'></i> Crear  ]" .$enlace."?Main=CrearRegistroRD]panelOculto]]}";
            $btn .= "<i class='icon-align-justify'></i>  ]SubMenu]{$listMn}]OPCIONES DEL PERFIL]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
			
			$titulo = "<p>USUARIOS </p><span></span>";
            $btn_tituloP = panelST2017($titulo, $btn, "auto", "TituloAMSG");
			
	
            $titulo = "<p>LISTADO DE PERFILES</p><span>Administración de datos</span>";
            $btn_titulo = panelST2017($titulo,"", "auto", "TituloALMB");
			
			$queryd = get("queryd");
            $queryd = explode(" ", $queryd);;

            $parm1 = $queryd[0];
            $parm2 = $queryd[1];
			
			if(empty($queryd)){
			    $operadorA = "<>";
				$queryd = "77777777777777777777777777";
			}else{
				$operadorA = "LIKE";
			}
				
				
            $sql = "SELECT 
								
				USP.Nombre
				,US.Nombre AS 'Usuario Creacion'
				,USP.FechaHoraCreacion AS 'Fecha Hora Creacion'
				,US2.Nombre AS 'Usuario Actualizacion'
				,USP.FechaHoraActualizacion AS 'Fecha Hora Actualizacion'
				,  CONCAT('
					<div  >
					<div class=botIcRepC ><i class=icon-chevron-down ></i> 
						 <ul class=sub_boton >
							<li onclick=enviaReg(''EDI',USP.Codigo,''',''{$enlace}?Main=EditarRegistroRD&CodRegistro=',USP.Codigo,''',''panelOculto'','''');  >Editar</li>
							<li onclick=enviaReg(''EDI',USP.Codigo,''',''{$enlace}?Main=EliminaRegistroRD&CodRegistro',USP.Codigo,''',''panelOculto'','''');>Eliminar</li>
						 </ul>
					</div>
					</div>
				')AS 'Acción' FROM usuarios_perfiles USP
				LEFT JOIN usuarios US ON USP.UsuarioCreacion = US.Codigo
				LEFT JOIN usuarios US2 ON USP.UsuarioActualizacion = US2.Codigo				
			";    
            $clase = 'reporteA';
            $enlaceCod = 'codigoAlmacen';
            $url = $enlace."?Articulos=EditaArticulos";
            $panel = 'layoutV';		
			
		    $where = [];
     
            $reporte = ListR2('', $sql, $where ,$cnPDO, $clase, '', $url, $enlaceCod, $panel, 'sys_tabla_det_form', '', '','');
            $html .= "<div id = 'panelOculto' style='float:left;width:100%;' ></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;' >" . $reporte . "</div>";
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
        

				$titulo = "<p>CREAR PERFIL</p><span>Completa los datos del formulario</span>";
				$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALM");
			
                $titulo = "";
     
	 
                $uRLForm = "Guardar]" . $enlace . "?metodo=FUsuarios_Perfiles&transaccion=INSERT]panelB]F]}";
                $uRLForm .= "Cancelar]" . $enlace . "?metodo=FUsuarios_Perfiles&transaccion=INSERT]\$popupEC.close()]JSB]]Plomo}";
				
				$path = array('Foto' => '/_imagenes/usuarios/');
				
                $tSelectD = array(
				'TipoArticulos' => 'SELECT Codigo,Descripcion FROM tipoarticulos'
				,'SectorArticulo' => 'SELECT Codigo,Descripcion FROM sectorarticulos'
				,'Lineaarticulo' => 'SELECT Codigo,Descripcion FROM lineaarticulo'
				,'Familiaarticulo' => 'SELECT FA.Codigo, CONCAT(FA.Descripcion," - ",LA.Descripcion) AS Familia FROM 
				                       familiaarticulo FA
									   INNER JOIN lineaarticulo LA ON FA.Lineaarticulo = LA.Codigo
									   '
				);
                $form = c_form_adp($titulo, $cnPDO, "FUsuarios_Perfiles", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
				
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
        
                $CodRegistro = get("CodRegistro");
				$titulo = "<p>EDITAR PERFIL</p><span>Completa los datos del formulario</span>";
				$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALM");
			
                $titulo = "";
     
	 
                $uRLForm = "Actualizar]" . $enlace . "?metodo=FUsuarios_Perfiles&transaccion=UPDATE&CodRegistro={$CodRegistro}]panelB]F]}";
                $uRLForm .= "Cancelar]" . $enlace . "?metodo=FUsuarios_Perfiles&transaccion=INSERT]\$popupEC.close()]JSB]]Plomo}";
				
				$path = array('Foto' => '/_imagenes/usuarios/');
				
                $tSelectD = array(
				'TipoArticulos' => 'SELECT Codigo,Descripcion FROM tipoarticulos'
				,'SectorArticulo' => 'SELECT Codigo,Descripcion FROM sectorarticulos'
				,'Lineaarticulo' => 'SELECT Codigo,Descripcion FROM lineaarticulo'
				,'Familiaarticulo' => 'SELECT FA.Codigo, CONCAT(FA.Descripcion," - ",LA.Descripcion) AS Familia FROM 
				                       familiaarticulo FA
									   INNER JOIN lineaarticulo LA ON FA.Lineaarticulo = LA.Codigo
									   '
				);
                $form = c_form_adp($titulo, $cnPDO, "FUsuarios_Perfiles", "CuadroA", $path, $uRLForm, $CodRegistro, $tSelectD, 'Codigo');
				
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$form = "<div style='float:left;width:100%;padding:0px;' >" . $form . "</div>";
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

function Busqueda($arg){
    global $vConex,$cnPDO,$enlace,$urlEmpresa,$idEmpresa,$Entidad, $EntidadPersona,$Codigo_Entidad_Usuario,$enterprise_user,$FechaHora;

    switch ($arg) {
     
        case 'Busqueda':	
			
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

function VinculacionUsuario($codigo){
	global $cnPDO, $FechaHora,$Usuario, $Entidad;
	
	#ADMIN ACCIONES
	$data = array(
	'Nombre' => post('Nombre'),
	'Descripcion' => post('Descripcion'),
	'Email' => post('Email'),
	'Entidades_Suscriptor' => $Entidad,
	'Entidades' => $codigo,
	'Telefono' => post('Telefono'),
	'Perfiles' => post('Tipo'),
	'Foto' => post('Foto'),
	'FechaHoraCreacion' => $FechaHora,
	'FechaHoraActualizacion' => $FechaHora,
	'UsuarioCreacion' => $Usuario,
	'UsuarioActualizacion' => $Usuario,
	);
	$rg = OwlPDO::insert('usuarios', $data, $cnPDO);	
	
}

?>
