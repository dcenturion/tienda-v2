<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_tienda.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_ventas.php";
$enlacePopup = "se_curricula.php";
$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
// W($Entidad. " Entidad ");
if (get('Ventas') != '') {Ventas(get('Ventas'));}

if (get("metodo") != "") {
        
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario, $Entidad;
		
        $cod_articulo = get("cod_articulo");
        $cod_mov_almacen = get("cod_mov_almacen");
        $CurriculaCod = get("CurriculaCod");
		
        if (get("metodo") == "curricula") {
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;
            } elseif ($campo == "Articulo") {
                $valor = $cod_articulo;
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";				
            } elseif ($campo == "Entidad") {
                $valor = $Entidad;				
            } else {
                $valor = "";
            }
            return $valor;
        }
		
        if (get("metodo") == "curricula_docentes") {
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;
            } elseif ($campo == "Almacen") {
                $valor = $cod_mov_almacen;
            } elseif ($campo == "Curricula") {
                $valor = $CurriculaCod;				
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";				
            } elseif ($campo == "Entidad") {
                $valor = $Entidad;				
            } else {
                $valor = "";
            }
            return $valor;
        }              
			  
			  
        if (get("metodo") == "docente") {
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
		
		
        if (get("metodo") == "FCodigoPlataforma") {
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
			if (get("metodo") == "docente") {		
				infresarCurriculaDocente($codigo);
			}
        }		
    }

    if (get("TipoDato") == "texto") {

        if (get("transaccion") == "UPDATE") {

            if (get("metodo") == "movimiento_almacen_ventas") {
                p_gf_udp("movimiento_almacen_ventas", $cnPDO, get("cod_mov_almacen"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Ventas("Site");
            }
            if (get("metodo") == "FCodigoPlataforma") {
                p_gf_udp("FCodigoPlataforma", $cnPDO, get("cod_mov_almacen"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Ventas("Site");
            }				
		
        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "curricula") {
                p_gf_udp("curricula",$cnPDO,'','Codigo');
                Curricula("Site");
            }		
			
        }
    }


    exit();
}


function Ventas($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup,$Entidad;
	$enlaceTienda = "_vistas/se_tienda.php";
	
	$cod_articulo = get("cod_articulo");
	$cod_mov_almacen = get("cod_mov_almacen");

	$segmentoUrl =  "&cod_articulo=".$cod_articulo."&cod_mov_almacen=".$cod_mov_almacen;
	
	$rg = datosAlmacenMovimientos($cod_mov_almacen,$cnPDO);
	$Horarios = $rg->Horarios;
	$Lugar = $rg->Lugar;
	$Requisitos = $rg->Requisitos;
	$FechaInicio = $rg->FechaInicio;
	$CodigoPlataformaEducativa = $rg->CodigoPlataformaEducativa;
			
    switch ($Arg) {

        case 'Site':
           
			$pestanas = pestanasBLocal(array("".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]Marca","".$segmentoUrl."]"));
			
            $btn = "<i class='icon-pencil'></i>  Editar ]" .$enlace."?Ventas=EditarDatosPrograma{$segmentoUrl}]panelFormA1]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>Datos del programa </p><span>ADMINISTRAR DATOS</span>";
            $btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
       
			
			$cuerpoHmtl .= "<h1>Requisitos</h1>";
			$cuerpoHmtl .= "<p>{$Requisitos}</p>";			
			$cuerpoHmtl .= "<h1>Lugar</h1>";
			$cuerpoHmtl .= "<p>{$Lugar}</p>";
			$cuerpoHmtl .= "<h1>Horarios</h1>";
			$cuerpoHmtl .= "<p>{$Horarios}</p>";			
			
			$linea = "<div class='LineaIT' ></div>";
            $html = "<div id = 'panelOcultoB2' style='float:left;width:100%;' ></div>";
            $html .= "<div class = 'PanelInferior2' > ". $cuerpoHmtl." </div>";	
			
        
			$cuerpoHmtl2 .= "<h1>Código de Plataforma Educativa</h1>";
			$cuerpoHmtl2 .= "<p>{$CodigoPlataformaEducativa}</p>";			

            $btn = "<i class='icon-pencil'></i>  Editar ]" .$enlace."?Ventas=EditarDatosPlataformaEducativa{$segmentoUrl}]panelFormA1]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>Configuración </p><span>PLATAFORMA EDUCATIVA</span>";
            $btn_titulo2 = panelST2017($titulo, $btn, "auto", "TituloALMBBG");		
			
            $html2 = "<div id = 'panelOcultoB2' style='float:left;width:100%;' ></div>";
            $html2 .= "<div class = 'PanelInferior2'  > ".$cuerpoHmtl2." </div>";	

			
			
            $linea = "<div class='LineaIT' ></div>";			
	
				
			$panel = array(array('PanelA1-A', '100%',  $btn_titulo . $linea  . $html . $btn_titulo2 .  $html2));
			$Cuerpo = LayoutPage($panel);
			$s = layoutV2($pestanas ,$Cuerpo ,"layoutV3","body-lv3");    
			
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
			$panel = "<div class='panelContenedorF1' style='float:left;width:98%;padding:1% 1% 5% 1%;overflow: auto;height: 461px;' >" . $s . "</div>";
			WE($Close .$panel);
	
			
            break;
			
        case 'EditarDatosPrograma':
		
            $btn = "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Ventas=Site{$segmentoUrl}]panelFormA1]]plomo}";
			
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>Datos del programa</p><span>ACTUALIZAR</span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$linea = "<div class='LineaIT'></div>";
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=movimiento_almacen_ventas&transaccion=UPDATE{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = "";
			$form = c_form_adp($titulo, $cnPDO, "movimiento_almacen_ventas", "CuadroA", $path, $uRLForm,$cod_mov_almacen, $tSelectD, 'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:98%;padding: 0px 1%;' >" . $btn_titulo . $linea . $form . "</div>";
			WE($html);
			
            break; 
			
        case 'EditarDatosPlataformaEducativa':
		
            $btn = "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Ventas=Site{$segmentoUrl}]panelFormA1]]plomo}";
			
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>Configuración de Plataforma Educativa</p><span>ACTUALIZAR</span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$linea = "<div class='LineaIT'></div>";
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=FCodigoPlataforma&transaccion=UPDATE{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = "";
			$form = c_form_adp($titulo, $cnPDO, "FCodigoPlataforma", "CuadroA", $path, $uRLForm,$cod_mov_almacen, $tSelectD, 'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:98%;padding: 0px 1%;' >" . $btn_titulo . $linea . $form . "</div>";
			WE($html);
			
            break; 
						
			
        default:		
            exit;
            break;
    }
}


function infresarCurriculaDocente($codigo){
    global $cnPDO,$FechaHora, $Usuario, $Entidad;
	
	$cod_mov_almacen = get("cod_mov_almacen");
	$CurriculaCod = get("CurriculaCod");	
	$tableValue 	=	array();
	$tableValue["Docente"] =   $codigo;
	$tableValue["Almacen"] =   $cod_mov_almacen;
	$tableValue["Curricula"] =   $CurriculaCod;
	$tableValue["FechaHoraActualizacion"] =   $FechaHora;
	$tableValue["FechaHoraCreacion"] =   $FechaHora;
	$tableValue["UsuarioCreacion"] =   $Usuario;
	$tableValue["UsuarioActualizacion"] =   $Usuario;
	$tableValue["Entidad"] =   $Entidad;
	
	$return 			= 	insertPDO("curricula_docentes",$tableValue,$cnPDO);
			
}
?>
