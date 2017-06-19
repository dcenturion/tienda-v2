<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');

$enlace = "_vistas/se_tienda_familia_producto.php";
$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
// W($Entidad. " Entidad ");
if (get('FamiliaProducto') != '') {FamiliaProducto(get('FamiliaProducto'));}

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

            if (get("metodo") == "familiaarticulo") {
                p_gf_udp("familiaarticulo", $cnPDO, get("codigo"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				W(" <script> \$popupEC.close();</script>");
				FamiliaProducto("Site");
            }
		
        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "familiaarticulo") {
				
                p_gf_udp("familiaarticulo",$cnPDO,'','Codigo');
				
				W(" <script> \$popupEC.close();</script>");
                FamiliaProducto("Site");
            }

        }
    }


    exit();
}


function FamiliaProducto($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup,$Entidad;
	
    switch ($Arg) {

        case 'Site':
            
		    $menu .= "Articulos]_vistas/se_tienda.php?Tienda=Site]panelB}";
			$menu .= "Sector]_vistas/se_tienda_sector.php?SectorProducto=Site]panelB}";
			$menu .= "Tipo]_vistas/se_tienda_tipo_producto.php?TipoProducto=Site]panelB}";
			$menu .= "Linea]_vistas/se_tienda_linea_producto.php?LineaProducto=Site]panelB}";
			$menu .= "Familia]_vistas/se_tienda_familia_producto.php?FamiliaProducto=Site]panelB]Marca}";	
			
			$pestanas = menuHorizontal($menu, 'menuV1');
	
            $btn = "<i class='icon-pencil'></i>  Crear ]" .$enlace."?FamiliaProducto=CreaRD]panelOculto]]}";
            $btn .= "<i class='icon-search'></i>  Buscar ]" .$enlace."?LineaProducto=CreaArticulos]PanelA1-B]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CREA, ACTUALIZA Y ELIMINA</p><span>Familia producto</span>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "auto", "TituloALM");

            $sql = "SELECT FA.Codigo
			, FA.Descripcion
			, LA.Descripcion AS Linea
			,  CONCAT('
                <div class=Btn-reporte onclick=enviaReg(''EDI',FA.Codigo,''',''{$enlace}?FamiliaProducto=EliminaRD&codigo=',FA.Codigo,''',''panelOculto'',''''); ><div class=botIcRep><i class=icon-trash ></i></div></div>
                <div class=Btn-reporte onclick=enviaReg(''DEL',FA.Codigo,''',''{$enlace}?FamiliaProducto=EditarRD&codigo=',FA.Codigo,''',''panelOculto'',''''); ><div class=botIcRep><i class=icon-edit ></i></div></div>
            ')AS 'Acción' FROM familiaarticulo FA
			LEFT JOIN lineaarticulo LA ON FA.Lineaarticulo = LA.Codigo
			
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
                        <script> openPopupURI('".$enlace."?FamiliaProducto=Crear', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;   			

        case 'Crear':
        
                $titulo = "Crear familia de productos";
                $path = "";				
                $uRLForm = "Guardar]" . $enlace . "?metodo=familiaarticulo&transaccion=INSERT]panelB]F]}";

				$tSelectD = array(
				'Lineaarticulo' => 'SELECT Codigo,Descripcion FROM lineaarticulo'
				);
                $form = c_form_adp($titulo, $cnPDO, "familiaarticulo", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
				
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$html = "<div style='float:left;width:600px;' >" . $Close . $form . "</div>";
                WE($html);
				
            break;              
        case 'EditarRD':
		
		    $codigo = get("codigo");
			$html = "	
                        <script> openPopupURI('".$enlace."?FamiliaProducto=Editar&codigo={$codigo}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      			
        case 'Editar':

            $codigo = get("codigo");
			$titulo = "Editar línea de productos";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=familiaarticulo&transaccion=UPDATE&codigo={$codigo}]panelB]F]}";
			
			$tSelectD = array(
			'Lineaarticulo' => 'SELECT Codigo,Descripcion FROM lineaarticulo'
			);
			$form = c_form_adp($titulo, $cnPDO, "familiaarticulo", "CuadroA", $path, $uRLForm,$codigo, $tSelectD, 'Codigo');
			
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
	   
			$html = "<div style='float:left;width:600px;' >" . $Close . $form . "</div>";
			WE($html);
			
            break;
        case 'EliminaRD':
		
		    $codigo = get("codigo");
			$html = "	
                        <script> openPopupURI('".$enlace."?FamiliaProducto=Eliminar&codigo={$codigo}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      			
        case 'Eliminar':

            $codigo = get("codigo");	
            $btn = "Confirmar ]" .$enlace."?FamiliaProducto=EliminaAccion&codigo={$codigo}]panelB]]}";
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
