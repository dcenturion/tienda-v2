<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
$enlace = "_vistas/se_usuario.php";
$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
// W($Entidad. " Entidad ");
if (get('Site') != '') {Site(get('Site'));}

if (get("metodo") != "") {
        
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario, $Entidad;
        
        if (get("metodo") == "articulos") {
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
			if (get("metodo") == "articulos") {		
				 IngresaAlmacen($codigo);
			}
        }		
    }

    if (get("TipoDato") == "texto") {

        if (get("transaccion") == "UPDATE") {

            if (get("metodo") == "articulos") {
                p_gf_udp("articulos", $cnPDO, get("cod_articulo"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Tienda("EditaArticulos");
				WE("");
            }
            if (get("metodo") == "articulos_presentacion") {
                p_gf_udp("articulos_presentacion", $cnPDO, get("cod_articulo"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Tienda("EditaArticulosPresentacion");
				WE("");
            }			
        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "articulos") {
				
                p_gf_udp("articulos",$cnPDO,'','Codigo');
				
				W(" <script> \$popupEC.close();</script>");
                Tienda("Site");
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


function Site($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup,$Entidad;
	
    switch ($Arg) {

        case 'Site':
			$html = "	
                        <script> openPopupURI('".$enlace."?Site=SiteDet', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);        	
            break;  
			
		case 'SiteDet':
		
			$menu .= "Articulos]_vistas/se_tienda.php?Site=Site]panelB]Marca}";
			$menu .= "Sector]_vistas/se_tienda_sector.php?SectorProducto=Site]panelB}";
			$menu .= "Tipo]_vistas/se_tienda_tipo_producto.php?TipoProducto=Site]panelB}";
			$menu .= "Linea]_vistas/se_tienda_linea_producto.php?LineaProducto=Site]panelB}";
			$menu .= "Familia]_vistas/se_tienda_familia_producto.php?FamiliaProducto=Site]panelB}";
			$pestanas = menuHorizontal($menu, 'menuV1');
	
            $btn = "<i class='icon-pencil'></i>  Crear ]" .$enlace."?Tienda=CreaArticulosRD]panelOculto]]}";
            $btn .= "<i class='icon-search'></i>  Buscar ]" .$enlace."?Tienda=CreaArticulos]PanelA1-B]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
			
            $titulo = "<p>CREA, ACTUALIZA Y ELIMINA</p><span>Listado de articulos</span>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "auto", "TituloALM");

            $sql = "SELECT AR.Nombre
			, CONCAT('
                <div style=color:#7f7d7d >',AR.Descripcion ,' </div>
                <div style=color:#0ebb0e >',LA.Descripcion,' | ', FA.Descripcion,' | ', AR.FechaHoraCreacion,' </div>
            ')AS 'Detallle'
			,  CONCAT('
                <div class=Btn-reporte onclick=enviaReg(''EDI',MA.Codigo,''',''{$enlace}?Tienda=EliminaArticulosRD&cod_mov_almacen=',MA.Codigo,''',''panelOculto'',''''); ><div class=botIcRep><i class=icon-trash ></i></div></div>
                <div class=Btn-reporte onclick=enviaReg(''DEL',AR.Codigo,''',''{$enlace}?Tienda=EditarArticulosRD&cod_mov_almacen=',MA.Codigo,''',''panelOculto'',''''); ><div class=botIcRep><i class=icon-edit ></i></div></div>
            ')AS 'Acción' FROM articulos AR
			INNER JOIN movimiento_almacen MA ON AR.Codigo = MA.Articulo
			INNER JOIN lineaarticulo LA ON AR.Lineaarticulo = LA.Codigo
			INNER JOIN familiaarticulo FA ON AR.Familiaarticulo = FA.Codigo
			WHERE MA.Entidad = {$Entidad}
			ORDER BY AR.FechaHoraCreacion DESC
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
			
			
							
		    $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
			$panel = "<div class='panelContenedorF1' style='float:left;width:98%;' ><div style='float:left;width:600px;' >" . $s . "</div>";
            WE($Close . $panel);
            break;
			
        case 'EliminaArticulosRD':
            $cod_mov_almacen = get("cod_mov_almacen");
			$html = "	
                        <script> openPopupURI('".$enlace."?Tienda=EliminaArticulos&cod_mov_almacen={$cod_mov_almacen}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;  
			
        case 'EliminaArticulos':
            $cod_mov_almacen = get("cod_mov_almacen");	
            $btn = "Confirmar ]" .$enlace."?Tienda=EliminaArticulosAccion&cod_mov_almacen={$cod_mov_almacen}]panelB]]}";
            $btn .= "<div onclick=\$popupEC.close(); >Cancelar</div> ]" .$enlace."?Tienda=CreaArticulos]PanelA1-B]]plomo}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR </p><span>El artículo seleccionado</span>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "auto", "TituloALM");
			
			WE($btn_titulo);
            break;  		
        case 'EliminaArticulosAccion':
		
            $cod_mov_almacen = get("cod_mov_almacen");
			
			$Q_U2 = " SELECT Articulo FROM movimiento_almacen WHERE Codigo = ".$cod_mov_almacen." ";
			$rg = fetch($Q_U2, $cnPDO);
			$codigoArticulo = $rg['Articulo'];			
			
			
	        DReg("movimiento_almacen", "Codigo", $cod_mov_almacen, $cnOld);	
	        DReg("articulos", "Codigo",$codigoArticulo, $cnOld);	
			
			W(" <script> \$popupEC.close();</script>");
			Tienda("Site");
			WE("");
            break; 			
        case 'CreaArticulosRD':
        
			$html = "	
                        <script> openPopupURI('".$enlace."?Tienda=CreaArticulos', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      

        case 'CreaArticulos':
        
                $titulo = "Crear Articulo";
                $path = "";				
                $uRLForm = "Guardar]" . $enlace . "?metodo=articulos&transaccion=INSERT]panelB]F]}";
				
                $tSelectD = array(
				'TipoArticulos' => 'SELECT Codigo,Descripcion FROM tipoarticulos'
				,'SectorArticulo' => 'SELECT Codigo,Descripcion FROM sectorarticulos'
				,'Lineaarticulo' => 'SELECT Codigo,Descripcion FROM lineaarticulo'
				,'Familiaarticulo' => 'SELECT FA.Codigo, CONCAT(FA.Descripcion," - ",LA.Descripcion) AS Familia FROM 
				                       familiaarticulo FA
									   INNER JOIN lineaarticulo LA ON FA.Lineaarticulo = LA.Codigo
									   '
				);
                $form = c_form_adp($titulo, $cnPDO, "articulos", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
				
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$html = "<div style='float:left;width:600px;' >" . $Close . $form . "</div>";
                WE($html);
				
            break;              
        case 'EditarArticulosRD':
		
		    $cod_mov_almacen = get("cod_mov_almacen");
			
			$html = "	
                        <script> openPopupURI('".$enlace."?Tienda=EditaArticulos&cod_mov_almacen={$cod_mov_almacen}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      			
        case 'EditaArticulos':

			$cod_mov_almacen = get("cod_mov_almacen");
			
			$sql = "SELECT AR.Codigo 
			FROM  articulos AR
			INNER JOIN movimiento_almacen MA ON AR.Codigo = MA.Articulo
			WHERE MA.Codigo = {$cod_mov_almacen}
			GROUP BY MA.Codigo
			";    
			$rg = fetch($sql,$cnPDO);
			$cod_articulo = $rg["Codigo"];				
			
		    $segmentoUrl =  "&cod_articulo=".$cod_articulo."&cod_mov_almacen=".$cod_mov_almacen;
			
			$menu .= "General]" . $enlace . "?Tienda=EditaArticulos{$segmentoUrl}]panelForm]Marca}";
			$menu .= "Presentación]" . $enlace . "?Tienda=EditaArticulosPresentacion{$segmentoUrl}]panelForm}";
			$menu .= "Curricula]_vistas/se_curricula.php?Curricula=Site{$segmentoUrl}]panelForm}";
			$menu .= "Ventas]_vistas/se_ventas.php?Ventas=Site{$segmentoUrl}]panelForm}";
			// $menu .= "SEO]" . $enlace . "?Importacion=Site]cuerpo}";
			$pestanas = menuHorizontal($menu, 'menuV1');	
			
			$path = array('Banner' => '../_imagenes/productos/');
            $tSelectD = array(
				'TipoArticulos' => 'SELECT Codigo,Descripcion FROM tipoarticulos'
				,'SectorArticulo' => 'SELECT Codigo,Descripcion FROM sectorarticulos'
				,'Lineaarticulo' => 'SELECT Codigo,Descripcion FROM lineaarticulo'
				,'Familiaarticulo' => 'SELECT Codigo,Descripcion FROM familiaarticulo'
				);
			
            $uRLForm = "Actualizar]" . $enlace . "?metodo=articulos&transaccion=UPDATE&{$segmentoUrl}]panelForm]F]}";
			$form = c_form_adp("", $cnPDO, "articulos", "CuadroA", $path, $uRLForm,$cod_articulo, $tSelectD, 'Codigo');
					
		    $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
		    $form = "<div style='padding:15px 0px 0px 0px;width:100%;float:left;' >".$form."</div>";
			
		    $html = "<div style='float:left;width:600px;' id='panelForm' >" . $Close . $pestanas . $form . "</div>";	
            WE( $html);
            break;
						
        case 'EditaArticulosPresentacion':

            $cod_articulo = get("cod_articulo");
            $cod_mov_almacen = get("cod_mov_almacen");
			
		    $segmentoUrl =  "&cod_articulo=".$cod_articulo."&cod_mov_almacen=".$cod_mov_almacen;
			
			
			$menu .= "General]" . $enlace . "?Tienda=EditaArticulos{$segmentoUrl}]panelForm}";
			$menu .= "Presentación]" . $enlace . "?Tienda=EditaArticulosPresentacion{$segmentoUrl}]panelForm]Marca}";
			$menu .= "Curricula]_vistas/se_curricula.php?Curricula=Site{$segmentoUrl}]panelForm}";
			$menu .= "Ventas]_vistas/se_ventas.php?Ventas=Site{$segmentoUrl}]panelForm}";
			$pestanas = menuHorizontal($menu, 'menuV1');	
			
			$path = array('ImagenPresentacionA' => '/system/_articulos/','ImagenPresentacionB' => '/system/_articulos/' );
			$tSelectD = array('TipoArticulos' => 'SELECT Codigo,Descripcion FROM tipoarticulos'
			,'SectorArticulo' => 'SELECT Codigo,Descripcion FROM sectorarticulos');
			
            $uRLForm = "Actualizar]" . $enlace . "?metodo=articulos_presentacion&transaccion=UPDATE&{$segmentoUrl}]panelForm]F]}";
			$form = c_form_adp("", $cnPDO, "articulos_presentacion", "CuadroA", $path, $uRLForm,$cod_articulo, $tSelectD, 'Codigo');
					
		    $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
		    $form = "<div style='padding:15px 0px 0px 0px;width:100%;float:left;' >".$form."</div>";
			
		    $html = "<div style='float:left;width:600px;' id='panelForm' >" . $Close . $pestanas . $form . "</div>";	
            WE( $html);
			
            break;
								
        case 'EditaArticulosVentas':

            $codigoAlmacen = get("codigoAlmacen");
			
            $sql = "SELECT ";
            $sql .=" AL.Codigo   ";
            $sql .=" ,AR.Nombre   ";
            $sql .=" , AR.Codigo as Articulo  ";
            $sql .=" FROM articulos AS AR ";        
            $sql .=" INNER JOIN almacen AS AL ON AR.Codigo = AL.Articulo ";   
            $sql .=" WHERE AL.Codigo = ".$codigoAlmacen."  ";   
			$rega = rGT($vConex, $sql);						
			$nombreArticulo= $rega["Nombre"];
			$CodigoArticulos= $rega["Articulo"];

			$menu = "GENERAL]" . $enlace . "?Articulos=EditaArticulos&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]}";
			$menu .= "PRESENTACIÓN]" . $enlace . "?Articulos=EditaArticulosPresentacion&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]}";
			$menu .= "VENTAS]" . $enlace . "?Articulos=EditaArticulosVentas&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]Marca}";
			// $menu .= "REDES SOCIALES]" . $enlace . "?Articulos=EditaArticulosRS&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			$menu .= "  ELIMINAR]" . $enlace . "?Articulos=EliminarConfirmar&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			$mv = menuVertical($menu,'menuDP');
		
            $tSelectD = array('Vendedor' => 'SELECT Codigo,Nombre FROM vendedores ');
            $uRLForm = "Actualizar]" . $enlace . "?metodo=almacen&transaccion=UPDATE&codigoAlmacen=" . $codigoAlmacen . "]panelB-R]F]}";
            // $uRLForm .="Eliminar]" . $enlace . "?Almacen=EliminaAlmacen&=DELETE&codigoAlmacen=" . $codigoAlmacen . "]panelB-R]F]}";

			$form = c_form_adp("", $vConex, "almacen", "CuadroA", $path, $uRLForm,$codigoAlmacen, $tSelectD, 'Codigo');		
           			
            $titulo = "<span>CÓDIGO PRODUCTO # ".$CodigoArticulos." |  CÓDIGO ALMACEN # ".$codigoAlmacen."     </span><p>VENTAS</p>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "150px", "TituloALM");	
			
            $style= '
					<style type="text/css">
					 #panelB-R{      width: 78%; float: left; padding: 1em 1.5em; border: 1px solid #ccc; min-height: 450px;}
					</style>	
			';			
	        $html = "<div style='float:left;width:600px;' >" .  $style . $btn_titulo . $form . "</div>";			
			$result = layoutLH($mHrz,$tituloBtn.$mv, $html);
		    $nombreArticulo = "<div class='nombre_producto'>" . $nombreArticulo . "</div>";				
			
            WE( $nombreArticulo . $result);	
            break;

        case 'EliminarConfirmar':

            $codigoAlmacen = get("codigoAlmacen");
			
            $sql = "SELECT ";
            $sql .=" AL.Codigo   ";
            $sql .=" ,AR.Nombre   ";
            $sql .=" , AR.Codigo as Articulo  ";
            $sql .=" FROM articulos AS AR ";        
            $sql .=" INNER JOIN almacen AS AL ON AR.Codigo = AL.Articulo ";   
            $sql .=" WHERE AL.Codigo = ".$codigoAlmacen."  ";   
			$rega = rGT($vConex, $sql);						
			$nombreArticulo= $rega["Nombre"];
			$CodigoArticulos= $rega["Articulo"];

			$menu = "GENERAL]" . $enlace . "?Articulos=EditaArticulos&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]}";
			$menu .= "PRESENTACIÓN]" . $enlace . "?Articulos=EditaArticulosPresentacion&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]}";
			$menu .= "VENTAS]" . $enlace . "?Articulos=EditaArticulosVentas&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]}";
			// $menu .= "REDES SOCIALES]" . $enlace . "?Articulos=EditaArticulosRS&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			$menu .= "  ELIMINAR]" . $enlace . "?Articulos=EliminarConfirmar&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]Marca}";
			$mv = menuVertical($menu,'menuDP');
		
            $tSelectD = array('Vendedor' => 'SELECT Codigo,Nombre FROM vendedores ');
            $uRLForm = "Actualizar]" . $enlace . "?metodo=almacen&transaccion=UPDATE&codigoAlmacen=" . $codigoAlmacen . "]panelB-R]F]}";
            $uRLForm .="Eliminar]" . $enlace . "?Almacen=EliminaAlmacen&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=" . $codigoAlmacen . "]cuerpo]F]}";

			$form = c_form_adp("", $vConex, "almacen", "CuadroA", $path, $uRLForm,$codigoAlmacen, $tSelectD, 'Codigo');		
           			
            $titulo = "<span>CÓDIGO PRODUCTO # ".$CodigoArticulos." |  CÓDIGO ALMACEN # ".$codigoAlmacen."     </span><p>VENTAS</p>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "150px", "TituloALM");	
			
            $style= '
					<style type="text/css">
					 #panelB-R{      width: 78%; float: left; padding: 1em 1.5em; border: 1px solid #ccc; min-height: 450px;}
					</style>	
			';			
	        $html = "<div style='float:left;width:600px;' >" .  $style . $btn_titulo . $form . "</div>";			
			$result = layoutLH($mHrz,$tituloBtn.$mv, $html);
		    $nombreArticulo = "<div class='nombre_producto'>" . $nombreArticulo . "</div>";				
			
            WE( $nombreArticulo . $result);	
            break;			
			
        case 'EliminaItem':
            $CodigoArticulos = get("CodigoArticulos");
            DReg("articulos", "Codigo", $CodigoArticulos, $vConex);
            Articulos("Site");
            break;

        case 'IngresarAlmacenForm':
            $CodigoArticulos = get("CodigoArticulos");
            $uRLForm = "Confirmar Ingreso en el Almacen]" . $enlace . "?Articulos=IngresarAlmacen&CodigoArticulos=" . $CodigoArticulos . "]layoutV]F]}";
            $titulo = "Establecer Cantidad ";
            $form = c_form("PROGRAMA #".$CodigoArticulos, $vConex, "almacen_ingreso", "CuadroA", "", $uRLForm,"", "");
            WE($form);
            break;

        case 'IngresarAlmacen':
            $CodigoArticulos = get("CodigoArticulos");
            $CodPlataforma = post("CodPlataforma");

            $sql = 'INSERT INTO almacen (Articulo,CodPlataforma,FechaHoraCreacion,FechaHoraActualizacion,Estado) 
                                   VALUES ('.$CodigoArticulos.','.$CodPlataforma.',"' . $FechaHora . '","' . $FechaHora . '","Activo")';
       
	        xSQL($sql, $vConex);
			
            Almacen("Site");
            break;

        default:
            exit;
            break;
    }
}

function IngresaAlmacen($codigo){
    global $cnPDO,$FechaHora, $Usuario, $Entidad;
	
	$tableValue 	=	array();
	$tableValue["Articulo"] =   $codigo;
	$tableValue["Cantidad"] =   1;
	$tableValue["Precio"] =   post("Precio");
	$tableValue["Estado"] =   "Activo";
	$tableValue["FechaHoraActualizacion"] =   $FechaHora;
	$tableValue["FechaHoraCreacion"] =   $FechaHora;
	$tableValue["UsuarioCreacion"] =   $Usuario;
	$tableValue["UsuarioActualizacion"] =   $Usuario;
	$tableValue["Entidad"] =   $Entidad;
	
	$return 			= 	insertPDO("movimiento_almacen",$tableValue,$cnPDO);
			
}

?>
