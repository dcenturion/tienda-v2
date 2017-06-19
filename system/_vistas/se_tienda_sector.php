<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_tienda.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_tienda_sector.php";
$enlacePopup = "se_tienda_sector.php";

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
        
        if (get("metodo") == "sectorarticulos") {
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

        if (get("metodo") == "lineaarticulo") {
			
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
            } elseif ($campo == "SectorArticulos") {
                $valor = get("codigoSubCategoria");					
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
			
            if (get("metodo") == "lineaarticulo") {
                p_gf_udp("lineaarticulo", $cnPDO, get("codigo"),'Codigo');
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

            if (get("metodo") == "lineaarticulo") {
				
                p_gf_udp("lineaarticulo",$cnPDO,'','Codigo');
				
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
	        
			$pestanas = pestanasLocal(array("","&parm=new]Marca","","",""));
	
            // $listMn = "<i class='icon-chevron-right'></i> Búsqueda Avanzada  [" .$enlace."?Tienda=CreaArticulosRD[panelOculto[[{";
            // $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 1 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";
            // $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 2 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";

            $btn = "<i class='icon-pencil'></i> Crear Categoría  ]" .$enlace."?SectorProducto=CreaRD]panelOculto]]}";
            // $btn .= "<i class='icon-align-justify'></i>  ]SubMenu]{$listMn}]OPCIONES DEL PERFIL]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
			
			$titulo = "<p>TIENDA</p><span>Administración de categorías</span>";
            $btn_tituloP = panelST2017($titulo, $btn, "auto", "TituloAMSG");
			
	
            $titulo = "<p>LISTADO</p><span>Administración de datos</span>";
            $btn_titulo = panelST2017($titulo,"", "auto", "TituloALMB");

            $sql = "SELECT Codigo
			, Descripcion
			, Orden
			FROM sectorarticulos
			WHERE 
			Entidad = :Entidad
			ORDER BY Orden ASC
			";    

            
			$where = [
			"Entidad" => $Entidad
			];
			
			$table ="<table style='width:100%' class='ReportDC1'>";	
			$countcolumn = OwlPDO::fetchAllArr($sql,$where,$cnPDO);
			$cont = 0;
			foreach ($countcolumn as $reg) {
				$con +=1;	
				
				$btn = " <i class='icon-pencil'></i>]" .$enlace."?SectorProducto=EditarRD&codigo=".$reg["Codigo"]."]panelOculto]]}";
				$btn .= " <i class='icon-trash'></i>]" .$enlace."?SectorProducto=EliminaRD&codigo=".$reg["Codigo"]."]panelOculto]]}";
				$btn .= " Sub Categoría  ]" .$enlace."?SectorProducto=CreaRDSub&codigoSubCategoria=".$reg["Codigo"]."]panelOculto]]}";
				$btn = Botones($btn, 'botones1', 'sys_form');
				$tituloItem = "<span style='padding:0px 0px 0px 30px;font-size:0.8em;'>".$reg["Descripcion"]." </span><span style='font-size:0.7em;'>| Orden: (".$reg["Orden"].")</span>";
				$btn_tituloItem = panelST2017($tituloItem, $btn, "auto", "TituloAMSG");
				$table .= "<tr><th>".$btn_tituloItem."</th></tr>";
				
					$sql = "SELECT Codigo
					, Descripcion
					, Orden
					FROM lineaarticulo
					WHERE 
					Entidad = :Entidad AND
					SectorArticulos = :SectorArticulos
					ORDER BY Orden ASC
					";    
					$where = [
					"Entidad" => $Entidad,
					"SectorArticulos" => $reg["Codigo"]
					];
					
					$countcolumn = OwlPDO::fetchAllArr($sql,$where,$cnPDO);
					foreach ($countcolumn as $regB) {
						
						$btn = " <i class='icon-pencil'></i>]" .$enlace."?SectorProducto=EditarRDSC&codigo=".$regB["Codigo"]."]panelOculto]]}";
						$btn .= " <i class='icon-trash'></i>]" .$enlace."?SectorProducto=EliminaRDSC&codigo=".$regB["Codigo"]."]panelOculto]]}";
						$btn = Botones($btn, 'botones1', 'sys_form');
						$tituloItem = "<span style='padding:0px 0px 0px 30px;font-size:0.8em;'>".$regB["Descripcion"]." </span><span style='font-size:0.7em;'>| Orden: (".$regB["Orden"].")</span>";
						$btn_tituloItem = panelST2017($tituloItem, $btn, "auto", "TituloAMSG");
						$table .= "<tr><td>".$btn_tituloItem."</td></tr>";
				
					}				
				
			}
			$table .="</table>";				
			$reporte = $table;
			
            $html .= "<div id = 'panelOculto' style='float:left;width:100%;' ></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;' >" . $reporte . "</div>";

            $panel = array(array('PanelA1-A', '100%', $html));
            $Cuerpo = LayoutPage($panel);
			
            $s = layoutV2($btn_tituloP , $pestanas . $btn_titulo . $Cuerpo); 
			
			WE(htmlApp($s));
            break;
			
        case 'CreaRD':
        
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?SectorProducto=Crear', {modal:true, closeContent:null}); </script>
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
                        <script> openPopupURI('".$enlacePopup."?SectorProducto=Editar&codigo={$codigo}', {modal:true, closeContent:null}); </script>
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
                        <script> openPopupURI('".$enlacePopup."?SectorProducto=Eliminar&codigo={$codigo}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      			
        case 'Eliminar':

            $codigo = get("codigo");	
            $btn = "Confirmar ]" .$enlace."?SectorProducto=EliminaAccion&codigo={$codigo}]panelB]]}";
            $btn .= "<div onclick=\$popupEC.close(); >Cancelar</div> ]" .$enlace."?Tienda=CreaArticulos]PanelA1-B]]plomo}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR </p><span>En el item seleccionado</span>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "auto", "TituloALM");
			
			WE($btn_titulo);
			
            break;						
        case 'EliminaAccion':
		
            $codigo = get("codigo");
			
	        DReg("sectorarticulos", "Codigo", $codigo, $cnOld);	
			     
	        DReg("lineaarticulo", "	SectorArticulos", $codigo, $cnOld);	
			
			W(" <script> \$popupEC.close();</script>");
			
			$msg =Msg("El proceso fue cerrado correctamente","C");
			W($msg);
				
			SectorProducto("Site");
			WE("");
            break; 		
			
        case 'CreaRDSub':
            $codigoSubCategoria = get("codigoSubCategoria");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?SectorProducto=CrearSub&codigoSubCategoria=".$codigoSubCategoria."', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;   			

        case 'CrearSub':
		
                $codigoSubCategoria = get("codigoSubCategoria");       
                $titulo = "Crear línea de productos";
                $path = "";				
                $uRLForm = "Guardar]" . $enlace . "?metodo=lineaarticulo&transaccion=INSERT&codigoSubCategoria=".$codigoSubCategoria."]panelB]F]}";
				
                $tSelectD = "";
                $form = c_form_adp($titulo, $cnPDO, "lineaarticulo", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
				
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$html = "<div style='float:left;width:600px;' >" . $Close . $form . "</div>";
                WE($html);
				
            break;   


        case 'EditarRDSC':
		
		    $codigo = get("codigo");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?SectorProducto=EditarSC&codigo={$codigo}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      			
        case 'EditarSC':

            $codigo = get("codigo");
			$titulo = "Editar Sub Categoría";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=lineaarticulo&transaccion=UPDATE&codigo={$codigo}]panelB]F]}";
			
			$tSelectD = "";
			$form = c_form_adp($titulo, $cnPDO, "lineaarticulo", "CuadroA", $path, $uRLForm,$codigo, $tSelectD, 'Codigo');
			
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
	   
			$html = "<div style='float:left;width:600px;' >" . $Close . $form . "</div>";
			WE($html);
			
            break;
        case 'EliminaRDSC':
		
		    $codigo = get("codigo");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?SectorProducto=EliminarSC&codigo={$codigo}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      			
        case 'EliminarSC':

            $codigo = get("codigo");	
            $btn = "Confirmar ]" .$enlace."?SectorProducto=EliminaAccionSC&codigo={$codigo}]panelB]]}";
            $btn .= "<div onclick=\$popupEC.close(); >Cancelar</div> ]" .$enlace."?Tienda=CreaArticulos]PanelA1-B]]plomo}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR </p><span>El artículo seleccionado</span>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "auto", "TituloALM");
			
			WE($btn_titulo);
			
            break;						
        case 'EliminaAccionSC':
		
            $codigo = get("codigo");
		
	        DReg("lineaarticulo", "Codigo", $codigo, $cnOld);	
			
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
