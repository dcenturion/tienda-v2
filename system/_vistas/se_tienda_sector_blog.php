<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');

$enlace = "_vistas/se_tienda_sector_blog.php";
$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
// W($Entidad. " Entidad ");
if (get('SectorProducto') != '') {SectorProducto(get('SectorProducto'));}

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

            if (get("metodo") == "sectorarticulos") {
                p_gf_udp("sectorarticulos", $cnPDO, get("codigo"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				W(" <script> \$popupEC.close();</script>");
				SectorProducto("Site");
            }
		
        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "sectorarticulos") {
				
                p_gf_udp("sectorarticulos",$cnPDO,'','Codigo');
				
				W(" <script> \$popupEC.close();</script>");
                SectorProducto("Site");
            }

        }
    }


    exit();
}


function SectorProducto($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup,$Entidad;
	
    switch ($Arg) {

        case 'Site':
            
		    $menu .= "Publicaciones]_vistas/se_blog.php?Blog=Site]panelB}";
			$menu .= "Sector]_vistas/se_tienda_sector.php?SectorProducto=Site]panelB]Marca}";
			
			$pestanas = menuHorizontal($menu, 'menuV1');
	
            $btn = "<i class='icon-pencil'></i>  Crear ]" .$enlace."?SectorProducto=CreaRD]panelOculto]]}";
            $btn .= "<i class='icon-search'></i>  Buscar ]" .$enlace."?SectorProducto=CreaArticulos]PanelA1-B]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CREA, ACTUALIZA Y ELIMINA</p><span>Familia producto</span>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "auto", "TituloALM");

            $sql = "SELECT Codigo
			, Descripcion
			,  CONCAT('
                <div class=Btn-reporte onclick=enviaReg(''EDI',Codigo,''',''{$enlace}?SectorProducto=EliminaRD&codigo=',Codigo,''',''panelOculto'',''''); ><div class=botIcRep><i class=icon-trash ></i></div></div>
                <div class=Btn-reporte onclick=enviaReg(''DEL',Codigo,''',''{$enlace}?SectorProducto=EditarRD&codigo=',Codigo,''',''panelOculto'',''''); ><div class=botIcRep><i class=icon-edit ></i></div></div>
            ')AS 'Acción' FROM sectorarticulos
			";    
            $clase = 'reporteA';
            $enlaceCod = 'codigoAlmacen';
            $url = $enlace."?Articulos=EditaArticulos";
            $panel = 'layoutV';
            $reporte = ListR2('', $sql, $cnOld, $clase, '', $url, $enlaceCod, $panel, 'sys_form', '', '');
			
            $html .= "<div id = 'panelOculto' style='float:left;width:100%;' ></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;' >" . $reporte . "</div>";

            $panel = array(array('PanelA1-A', '100%', $html));
            $Cuerpo = LayoutPage($panel);
            $s = layoutV2($pestanas . $divFloat , $btn_titulo . $Cuerpo);      
            
            WE($s);
            break;
			
        case 'CreaRD':
        
			$html = "	
                        <script> openPopupURI('".$enlace."?SectorProducto=Crear', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;   			

        case 'Crear':
        
                $titulo = "Crear sector del producto";
                $path = "";				
                $uRLForm = "Guardar]" . $enlace . "?metodo=sectorarticulos&transaccion=INSERT]panelB]F]}";
				
                $tSelectD = "";
                $form = c_form_adp($titulo, $cnPDO, "sectorarticulos", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
				
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$html = "<div style='float:left;width:600px;' >" . $Close . $form . "</div>";
                WE($html);
				
            break;              
        case 'EditarRD':
		
		    $codigo = get("codigo");
			$html = "	
                        <script> openPopupURI('".$enlace."?SectorProducto=Editar&codigo={$codigo}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      			
        case 'Editar':

            $codigo = get("codigo");
			$titulo = "Editar sector del productos";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=sectorarticulos&transaccion=UPDATE&codigo={$codigo}]panelB]F]}";
			
			$tSelectD = "";
			$form = c_form_adp($titulo, $cnPDO, "sectorarticulos", "CuadroA", $path, $uRLForm,$codigo, $tSelectD, 'Codigo');
			
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
	   
			$html = "<div style='float:left;width:600px;' >" . $Close . $form . "</div>";
			WE($html);
			
            break;
        case 'EliminaRD':
		
		    $codigo = get("codigo");
			$html = "	
                        <script> openPopupURI('".$enlace."?SectorProducto=Eliminar&codigo={$codigo}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      			
        case 'Eliminar':

            $codigo = get("codigo");	
            $btn = "Confirmar ]" .$enlace."?SectorProducto=EliminaAccion&codigo={$codigo}]panelB]]}";
            $btn .= "<div onclick=\$popupEC.close(); >Cancelar</div> ]" .$enlace."?Tienda=CreaArticulos]PanelA1-B]]plomo}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR </p><span>El artículo seleccionado</span>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "auto", "TituloALM");
			
			WE($btn_titulo);
			
            break;						
        case 'EliminaAccion':
		
            $codigo = get("codigo");
		
	        DReg("sectorarticulos", "Codigo", $codigo, $cnOld);	
			
			W(" <script> \$popupEC.close();</script>");
			SectorProducto("Site");
			WE("");
            break; 		
        default:
            exit;
            break;
    }
}


?>
