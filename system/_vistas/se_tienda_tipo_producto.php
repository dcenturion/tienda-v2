<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_tienda.php');

if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');


$enlace = "./_vistas/se_tienda_tipo_producto.php";
$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
// W($Entidad. " Entidad ");
if (get('TipoProducto') != '') {TipoProducto(get('TipoProducto'));}

if (get("metodo") != "") {
        
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario, $Entidad;
        
        if (get("metodo") == "tipoarticulos") {
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
			// if (get("metodo") == "tipoarticulos") {		
				 // IngresaAlmacen($codigo);
			// }
        }		
    }

    if (get("TipoDato") == "texto") {

        if (get("transaccion") == "UPDATE") {

            if (get("metodo") == "tipoarticulos") {
                p_gf_udp("tipoarticulos", $cnPDO, get("codigo"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				W(" <script> \$popupEC.close();</script>");
				TipoProducto("Site");
            }
		
        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "tipoarticulos") {
				
                p_gf_udp("tipoarticulos",$cnPDO,'','Codigo');
				
				W(" <script> \$popupEC.close();</script>");
                TipoProducto("Site");
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


function TipoProducto($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup,$Entidad;
	
    switch ($Arg) {

        case 'Site':
   
	        
			$pestanas = pestanasLocal(array("","","&parm=new]Marca","",""));
			
			$titulo = "<p>TIENDA</p><span>Administración de categorías</span>";
            $btn_tituloP = panelST2017($titulo, $btn, "auto", "TituloAMSG");			
	
            $btn = "<i class='icon-pencil'></i>  Crear ]" .$enlace."?TipoProducto=CreaRD]panelOculto]]}";
            // $btn .= "<i class='icon-search'></i>  Buscar ]" .$enlace."?TipoProducto=CreaArticulos]PanelA1-B]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
			
            $titulo = "<p>CREA, ACTUALIZA Y ELIMINA</p><span>Tipo producto</span>";
            $btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMB");

            $sql = "SELECT Codigo
			, Descripcion
			,  CONCAT('
                <div class=Btn-reporte onclick=enviaReg(''EDI',Codigo,''',''{$enlace}?TipoProducto=EliminaRD&codigo=',Codigo,''',''panelOculto'',''''); ><div class=botIcRep><i class=icon-trash ></i></div></div>
                <div class=Btn-reporte onclick=enviaReg(''DEL',Codigo,''',''{$enlace}?TipoProducto=EditarRD&codigo=',Codigo,''',''panelOculto'',''''); ><div class=botIcRep><i class=icon-edit ></i></div></div>
            ')AS 'Acción' FROM tipoarticulos
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
            
			WE(htmlApp($s));
            break;
			
        case 'CreaRD':
        
			$html = "	
                        <script> openPopupURI('".$enlace."?TipoProducto=Crear', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;   			

        case 'Crear':
        
                $titulo = "Crear tipos de productos";
                $path = "";				
                $uRLForm = "Guardar]" . $enlace . "?metodo=tipoarticulos&transaccion=INSERT]panelB]F]}";
				
                $tSelectD = "";
                $form = c_form_adp($titulo, $cnPDO, "tipoarticulos", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
				
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$html = "<div style='float:left;width:600px;' >" . $Close . $form . "</div>";
                WE($html);
				
            break;              
        case 'EditarRD':
		
		    $codigo = get("codigo");
			$html = "	
                        <script> openPopupURI('".$enlace."?TipoProducto=Editar&codigo={$codigo}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      			
        case 'Editar':

            $codigo = get("codigo");
		    $segmentoUrl =  "&codigo=".$codigo;
			
			$titulo = "Editar tipos de productos";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=tipoarticulos&transaccion=UPDATE&codigo={$codigo}]panelB]F]}";
			
			$tSelectD = "";
			$form = c_form_adp($titulo, $cnPDO, "tipoarticulos", "CuadroA", $path, $uRLForm,$codigo, $tSelectD, 'Codigo');
			
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
	   
			$html = "<div style='float:left;width:600px;' >" . $Close . $form . "</div>";
			WE($html);
			
            break;
        case 'EliminaRD':
		
		    $codigo = get("codigo");
			$html = "	
                        <script> openPopupURI('".$enlace."?TipoProducto=Eliminar&codigo={$codigo}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      			
        case 'Eliminar':

            $codigo = get("codigo");	
            $btn = "Confirmar ]" .$enlace."?TipoProducto=EliminaAccion&codigo={$codigo}]panelB]]}";
            $btn .= "<div onclick=\$popupEC.close(); >Cancelar</div> ]" .$enlace."?Tienda=CreaArticulos]PanelA1-B]]plomo}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR </p><span>El artículo seleccionado</span>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "auto", "TituloALM");
			
			WE($btn_titulo);
			
            break;						
        case 'EliminaAccion':
		
            $codigo = get("codigo");
		
	        DReg("tipoarticulos", "Codigo", $codigo, $cnOld);	
			
			W(" <script> \$popupEC.close();</script>");
			TipoProducto("Site");
			WE("");
            break; 		
        default:
            exit;
            break;
    }
}


?>
