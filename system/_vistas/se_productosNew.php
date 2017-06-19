<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');

$enlace = "_vistas/se_productosNew.php";
$cnPDO = PDOConnection();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];

if (get('Almacen') != '') {Almacen(get('Almacen'));}
if (get('Articulos') != '') {Articulos(get('Articulos'));}
if (get('Importacion') != '') { Importacion(get('Importacion'));}

if (get("metodo") != "") {
    
    if (get("TipoDato") == "archivo") {

        if (get("metodo") == "articulos") {
            $filedata = upload($usuarioEntidad, $entidadCreadora, $vConex);
            echo json_encode($filedata);
        }
		if (get("metodo") == "articulos_banner") {
            $filedata = upload($usuarioEntidad, $entidadCreadora, $vConex);
            echo json_encode($filedata);
        }
    }
    
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario;
        
        if (get("metodo") == "almacen") {
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

        if (get("metodo") == "articulos") {
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
              
    }

    function p_before($codigo) {
	
        if (get("metodo") == "articulos") {		
		     IngresaAlmacen($codigo);
		}	 
    }

    if (get("TipoDato") == "texto") {

        if (get("transaccion") == "UPDATE") {

            if (get("metodo") == "almacen") {
                p_gf("almacen", $vConex, get("codigoAlmacen"));
				$msg =Msg("El proceso fue cerrado correctamente","C");
				WE($msg);
            }
            if (get("metodo") == "articulos") {
                p_gf("articulos", $vConex, get("CodigoArticulos"));
				$msg =Msg("El proceso fue cerrado correctamente","C");
				WE($msg);
            }
			 if (get("metodo") == "articulos_banner") {
                p_gf("articulos_banner", $vConex, get("CodigoArticulos"));
				$msg =Msg("El proceso fue cerrado correctamente","C");
				WE($msg);
            }

        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "articulos") {
                p_gf("articulos", $vConex, '');
				W(" <script> \$popupEC.close();</script>");
                Articulos("Site");
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


function IngresaAlmacen($CodigoArticulos) {
    global $vConex, $enlace, $FechaHora, $enlacePopup, $Usuario ;
	
	$sql = 'INSERT INTO almacen (Articulo,FechaHoraCreacion,FechaHoraActualizacion,Estado,UsuarioCreacion,UsuarioActualizacion) 
						   VALUES ('.$CodigoArticulos.',"' . $FechaHora . '","' . $FechaHora . '","Activo",'.$Usuario.','.$Usuario.')';
	xSQL($sql, $vConex);
	return;   
}


function Almacen($Arg) {
    global $vConex, $enlace;

    $menu  = "Almacen]" . $enlace . "?Almacen=Site]cuerpo]Marca}";
    $menu .= "Programas]" . $enlace . "?Articulos=Site]cuerpo}";
    $menu .= "Importación de Articulos]" . $enlace . "?Importacion=Site]cuerpo}";

    $pestanas = menuHorizontal($menu, 'menuV1');

    switch ($Arg) {
        case 'EliminaAlmacen':
		
            $codigoAlmacen = get("codigoAlmacen");
            $CodigoArticulos = get("CodigoArticulos");
            DReg("almacen", "Codigo", $codigoAlmacen, $vConex);
            DReg("articulos", "Codigo", $CodigoArticulos, $vConex);
            Articulos("Site");
            break;
        default:
            exit;
            break;
    }
}

function Articulos($Arg) {
    global $cnPDO, $enlace, $FechaHora, $enlacePopup;
	
    switch ($Arg) {

        case 'Site':
            
			$menu .= "Productos]" . $enlace . "?Articulos=Site]cuerpo]Marca}";
			$menu .= "Importación de Articulos]" . $enlace . "?Importacion=Site]cuerpo}";

			$pestanas = menuHorizontal($menu, 'menuV1');
	
            $btn = "<i class='icon-pencil'></i>  Crear ]" .$enlace."?Articulos=CreaArticulosRD]panelOculto]]}";
            $btn .= "<i class='icon-search'></i>  Buscar ]" .$enlace."?Articulos=CreaArticulos]PanelA1-B]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<span>Productos</span><p>PROGRAMAS</p><div class='bicel'></div>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "auto", "TituloA");

            $sql = "SELECT ";
			$sql .="  CONCAT(
			'<div> ', AR.Nombre, ' </div>
			<div> ', AR.Banner, ' </div>
			<div style=color:green;> ', SC.Nombre, ' </div>'
			) AS 'Descripción del producto' ";
			$sql .=" , AR.DescripcionFace AS 'Descripción de Facebook' ";
			$sql .="  , CONCAT(
			'<div> ', AR.FechaHoraCreacion, ' </div>
			<div> ', US.Nombres,'  ',US.Apellidos , ' </div>'
			) AS 'Datos de edición' ";			
		
            $sql .=" , AL.Codigo AS CodigoAjax  ";
            $sql .=" FROM articulos AS AR ";       
            $sql .=" LEFT JOIN sector AS SC ON AR.Sector = SC.Codigo ";   
            $sql .=" INNER JOIN usuarios AS US ON AR.UsuarioCreacion = US.Codigo ";   
            $sql .=" INNER JOIN almacen AS AL ON AR.Codigo = AL.Articulo ";   
			$sql .=" ORDER BY AR.FechaHoraCreacion DESC ";    
            $clase = 'reporteA';
            $enlaceCod = 'codigoAlmacen';
            $url = $enlace."?Articulos=EditaArticulos";
            $panel = 'layoutV';
            // $reporte = ListR2('', $sql, $vConex, $clase, '', $url, $enlaceCod, $panel, 'sys_form', '', '');
            $html .= "<div id = 'panelOculto' style='float:left;width:100%;' ></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;' >" . $reporte . "</div>";


            // $panel = array(array('PanelA1-A', '60%', $CuerpoIni), array('PanelA1-B', '35%', ''));
            $panel = array(array('PanelA1-A', '100%', $html));
            $Cuerpo = LayoutPage($panel);
            $s = layoutV2($pestanas . $divFloat , $btn_titulo . $Cuerpo);      
            
            WE($s);
            break;
			
        case 'CreaArticulosRD':
        
			$html = "	
                        <script> openPopupURI('".$enlace."?Articulos=CreaArticulos', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      

        case 'CreaArticulos':
        
                $titulo = "CREAR ARTICULO";
                $path = array('Banner' => '../_imagenes/productos/banner/');
                $uRLForm = "Guardar]" . $enlace . "?metodo=articulos&transaccion=INSERT]cuerpo]F]PanelA1-A}";
                $tSelectD = array('Sector' => 'SELECT Codigo,Nombre FROM sector ORDER BY Posicion DESC');
                $form = c_form_adp($titulo, $cnPDO, "articulos", "CuadroA", $path, $uRLForm, '', $tSelectD, 'codigo');
				
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$html = "<div style='float:left;width:600px;' >" . $Close . $form . "</div>";
                WE($html);
				
            break;              
			
        case 'EditaArticulos':

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

			$menu = "GENERAL]" . $enlace . "?Articulos=EditaArticulos&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]Marca}";
			$menu .= "PRESENTACIÓN]" . $enlace . "?Articulos=EditaArticulosPresentacion&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			$menu .= "VENTAS]" . $enlace . "?Articulos=EditaArticulosVentas&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			// $menu .= "REDES SOCIALES]" . $enlace . "?Articulos=EditaArticulosRS&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			$menu .= "  ELIMINAR]" . $enlace . "?Articulos=EliminarConfirmar&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			
			$mv = menuVertical($menu,'menuDP');
		    $nombreArticulo = "<div class='nombre_producto'>" . $nombreArticulo . "</div>";	
			
			
            $titulo = "<span>CÓDIGO PRODUCTO # ".$CodigoArticulos."</span><p>GENERAL</p>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "150px", "TituloA");	
			
			$path = array('Banner' => '../_imagenes/productos/banner/');
			$tSelectD = array('Sector' => 'SELECT Codigo,Nombre FROM sector ORDER BY Posicion DESC');
            $uRLForm = "Actualizar]" . $enlace . "?metodo=articulos&transaccion=UPDATE&CodigoArticulos=" . $CodigoArticulos . "]panelB-R]F]}";
           
			$sqla='select Articulo from almacen WHERE Articulo ='.$CodigoArticulos.' ';
			$rega = rGT($vConex, $sqla);						
			$Articulo= $rega["Articulo"] ;
		   // $uRLForm .="Eliminar]" . $enlace . "?Articulos=EliminaItem&CodigoArticulos=" . $CodigoArticulos . "]panelB-R]F]layoutV}";
         
			$form = c_form_adp("", $vConex, "articulos", "CuadroA", $path, $uRLForm,$CodigoArticulos, $tSelectD, 'Codigo');
			
			
            $style= '
					<style type="text/css">
					 #panelB-R{      width: 78%; float: left; padding: 1em 1.5em; border: 1px solid #ccc; min-height: 450px;}
					</style>	
			';						
		    $html = "<div style='float:left;width:600px;' >" .  $style . $btn_titulo . $form . "</div>";			
			$result = layoutLH($mHrz,$tituloBtn.$mv, $html);
            WE(  $nombreArticulo . $result);
            break;
						
        case 'EditaArticulosPresentacion':

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
			$menu .= "PRESENTACIÓN]" . $enlace . "?Articulos=EditaArticulosPresentacion&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]Marca}";
			$menu .= "VENTAS]" . $enlace . "?Articulos=EditaArticulosVentas&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			// $menu .= "REDES SOCIALES]" . $enlace . "?Articulos=EditaArticulosRS&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			$menu .= "  ELIMINAR]" . $enlace . "?Articulos=EliminarConfirmar&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			
			$mv = menuVertical($menu,'menuDP');
		
            $path = array('Imagen' => '../_imagenes/productos/');
            $uRLForm = "Actualizar]" . $enlace . "?metodo=articulos_banner&transaccion=UPDATE&CodigoArticulos=" . $CodigoArticulos . "]panelB-R]F]}";
            $titulo = "";
			$form = c_form_adp($titulo, $vConex, "articulos_banner", "CuadroA", $path, $uRLForm,$CodigoArticulos, "", 'Codigo');			
           			
            $titulo = "<span>CÓDIGO PRODUCTO # ".$CodigoArticulos."</span><p>PRESENTACIÓN</p>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "150px", "TituloA");	
			
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
            $btn_titulo = tituloBtnPn($titulo, $btn, "150px", "TituloA");	
			
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
            $btn_titulo = tituloBtnPn($titulo, $btn, "150px", "TituloA");	
			
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
			
			// W($sql."    ---- ".$CodigoArticulos);
            Almacen("Site");
            break;

        default:
            exit;
            break;
    }
}

function Importacion($Arg) {
    global $vConex, $enlace, $FechaHora,$Usuario;
	

    switch ($Arg) {

        case 'Site':
            
			$menu  = "Almacen]" . $enlace . "?Almacen=Site]cuerpo}";
			$menu .= "Programas]" . $enlace . "?Articulos=Site]cuerpo}";
			$menu .= "Importación de Articulos]" . $enlace . "?Importacion=Site]cuerpo]Marca}";

			$pestanas = menuHorizontal($menu, 'menuV1');
	
            $btn = "<div class='botIconS'>Importar</div>]" .$enlace."?Importacion=Importar]PanelA1-B]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<span>Articulos para Importar</span><p>PROGRAMAS ABIERTOS FRI</p><div class='bicel'></div>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "150px", "TituloA");

			$conexPlataforma = conexPlataforma();
			
			$sql = " SELECT ";    
			$sql .="  AL.AlmacenCod ";
			$sql .="  ,PR.Titulo ";
			// $sql .="  ,SUBSTRING(PR.Presentacion,1,100) as Presentacion ";
			$sql .="  ,AL.Estado ";
			$sql .="  ,AL.Entidad ";
			$sql .=" FROM almacen AS AL  ";
			$sql .=" LEFT JOIN articulos as AR on AL.Producto = AR.Producto ";
			$sql .=" LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma ";
			$sql .=" WHERE AL.Estado = 'Abierto' ";
			$sql .=" AND AL.TipoProducto like '%programa%' ";
			$sql .=" AND AL.Entidad = 'fri.com.pe' ";
			// $sql = " SELECT * FROM almacen ";    
            $clase = 'reporteA';
            $enlaceCod = 'CodigoArticulos';
            $url = $enlace."?Importacion=EditaArticulos";
            $panel = 'PanelA1-B';
            $reporte = ListR2('', $sql, $conexPlataforma, $clase, '', $url, $enlaceCod, $panel, 'sys_form', 'checks', '');
            $reporte = "<div id = 'PanelInferior' style='float:left;width:100%;' >" . $reporte . "</div>";
            $CuerpoIni = $reporte;

            $panel = array(array('PanelA1-A', '60%', $CuerpoIni), array('PanelA1-B', '35%', ''));
            $Cuerpo = LayoutPage($panel);
            $s = layoutV2($pestanas . $divFloat . $btn_titulo, $Cuerpo);      
            
            WE($s);
            break;


        case 'Importar':
            $CodigoArticulos = get("CodigoArticulos");
            $CodPlataforma = post("CodPlataforma");
			
			$conexPlataforma = conexPlataforma();
			
			$sql = " SELECT ";    
			$sql .="  AL.AlmacenCod ";
			$sql .="  ,PR.Titulo ";
			$sql .="  ,PR.Presentacion ";
			$sql .="  ,AL.Estado ";
			$sql .="  ,AL.Entidad ";
			$sql .="  ,PR.Sector ";
			$sql .="  ,AL.DiaInicio ";
			$sql .="  ,AL.DiaFinal ";
			$sql .="  ,AL.DiaFinalInscripcion ";
			$sql .=" FROM almacen AS AL  ";
			$sql .=" LEFT JOIN articulos as AR on AL.Producto = AR.Producto ";
			$sql .=" LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma ";
			$sql .=" WHERE AL.Estado = 'Abierto' ";
			$sql .=" AND AL.TipoProducto like '%programa%' ";
			$sql .=" AND AL.Entidad = 'fri.com.pe' ";
			// $sql .=" Limit 5";
            $sql = " SELECT
                     AL.AlmacenCod
                    ,PR.Titulo
                    ,PR.Presentacion
                    ,AL.Estado
                    ,AL.Entidad
                    ,PR.Sector
                    ,AL.DiaInicio
                    ,AL.DiaFinal
                    ,AL.DiaFinalInscripcion
                    FROM lista_trabajo_det AS LTD
                    INNER JOIN lista_trabajo AS LT ON LT.Codigo=LTD.Lista
                    INNER JOIN almacen AS AL ON LTD.CodigoAlmacen=AL.AlmacenCod
                    INNER JOIN programas AS PR ON LTD.CodigoProducto=PR.CodPrograma
                    INNER JOIN lista_trabajo_det_coordinacion AS LTDC ON LTDC.lista_trabajo_det= LTD.Codigo
                    WHERE LT.Empresa='fri.com.pe'
                    AND (PR.vista = '' OR PR.vista = 'Activado')
                    AND LTDC.Estado = 'Activo'
                    AND LTD.Estado = 'Abierto'
                    AND LTD.TipoProducto like 'Programa%'
                    AND (AL.HerenciaOrigen = 'Original'  OR AL.HerenciaOrigen = '')
                    AND LTD.CodigoProducto IS NOT NULL
                    ORDER BY LTD.Estado ASC ";

			$Cantidad=0;
			$Existe=0;
			$consulta2 = Matris_Datos($sql, $conexPlataforma);
			while ($rg = mysql_fetch_array($consulta2)) {

			$CodigoAlmacen = $rg["AlmacenCod"];
            $Titulo = str_replace('"','',$rg["Titulo"]);
            $Titulo = str_replace("'","",$Titulo);
			$Descripcion = strip_tags($rg["Presentacion"]);
			$Sector = $rg["Sector"];
			$DiaInicio = $rg["DiaInicio"];
			$DiaFinal = $rg["DiaFinal"];
			$DiaFinalInscripcion = $rg["DiaFinalInscripcion"];
		
			$vConex = conexSys();
			$sql3 = 'SELECT  CodPlataforma  FROM almacen WHERE CodPlataforma = '.$CodigoAlmacen.'  ';
			$reg3 = rGT($vConex, $sql3);						
			$Codigo= $reg3["CodPlataforma"] ;	
		
				if($Codigo == ""){//No existe el codigo-->entra

				$Cantidad=$Cantidad+1;
										
					
					//SECTOR
					
					$sqlsec='SELECT Codigo from sector WHERE SectorPlataforma = '.$Sector.'';
					$regsec= rGT($vConex, $sqlsec);
					$Sector2= $regsec["Codigo"] ;
					
					if($Sector2==""){//SI NO ESXITE EL SECTOR SE CREA...
					
					$sqlcatSECT='SELECT Descripcion from sectores WHERE IdSectores = '.$Sector.'';
					$regcatSECT= rGT($conexPlataforma, $sqlcatSECT);
					$DescripcionSECT= $regcatSECT["Descripcion"] ;
					
					
					$sqlsec2 = 'INSERT INTO sector (Nombre,SectorPlataforma) 
								VALUES ("'.$DescripcionSECT.'","'.$Sector.'")';
								xSQL($sqlsec2, $vConex);
					}
					

					$sql4 = 'INSERT INTO articulos (Nombre,Sector,FechaHoraCreacion,FechaHoraActualizacion,UsuarioCreacion,UsuarioActualizacion) 
							VALUES ("'.$Titulo.'","'.$Sector2.'","'.$FechaHora.'","'.$FechaHora.'",'.$Usuario.','.$Usuario.')';
					xSQL($sql4, $vConex);


					$sqla='select max(Codigo) as Codigo from articulos';
					$rega = rGT($vConex, $sqla);						
					$Articulo= $rega["Codigo"] ;

					$sql5 = 'INSERT INTO almacen (CodPlataforma,Articulo,Estado,Vendedor,FechaInicio,FechaFinalInscripcion,FechaFin,FechaHoraCreacion,FechaHoraActualizacion,UsuarioCreacion,UsuarioActualizacion) 
							VALUES ('.$CodigoAlmacen.','.$Articulo.',"Desactivo","2","'.$DiaInicio.'","'.$DiaFinal.'","'.$DiaFinalInscripcion.'","'.$FechaHora.'","'.$FechaHora.'",'.$Usuario.','.$Usuario.')';
					xSQL($sql5, $vConex);
				
				}else{
				$Existe=$Existe+1;
				}

			}
			$titulo = "<p>PROGRAMAS IMPORTADOS ".$Cantidad."</p><span>Programas Existentes ".$Existe."</span>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "150px", "TituloA");
			WE($btn_titulo);
            break;

        default:
            exit;
            break;
    }
}

?>
