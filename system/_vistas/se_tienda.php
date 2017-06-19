<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_tienda.php');

if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_tienda.php";
$enlacePopup = "se_tienda.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
$_SESSION['menuVertical']= "2";

if (get('Tienda') != '') {Tienda(get('Tienda'));}
if (get('Busqueda') != '') {Busqueda(get('Busqueda'));}
if (get('CategoriasSearch') != '') {CategoriasSearch(get('CategoriasSearch'));}

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
				 // GeneraAlias($codigo);
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
            if (get("metodo") == "articulos_presentacion_ebook") {
                p_gf_udp("articulos_presentacion_ebook", $cnPDO, get("cod_articulo"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Tienda("EditaArticulosPresentacion");
				WE("");
            }	
			
        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "articulos") {
				
                p_gf_udp("articulos",$cnPDO,'','Codigo');
				
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);				
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


function Tienda($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup ,$Entidad;
	
    switch ($Arg) {

        case 'Site':
     
			$pestanas = pestanasLocal(array("&parm=new]Marca","","","",""));
			
            // $listMn = "<i class='icon-chevron-right'></i> Búsqueda Avanzada  [" .$enlace."?Tienda=CreaArticulosRD[panelOculto[[{";
            // $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 1 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";
            // $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 2 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";

            $btn = "<i class='icon-pencil'></i> Crear Artículo]" .$enlace."?Tienda=CreaArticulosRD]panelOculto]]}";
            // $btn .= "<i class='icon-align-justify'></i>  Crear Artículo]SubMenu]{$listMn}]OPCIONES DEL PERFIL]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
			
			$titulo = "<p>TIENDA </p><span>Administración de artículos</span>";
            $btn_tituloP = panelST2017($titulo, $btn, "auto", "TituloAMSG");
			
	
            $titulo = "<p>LISTADO DE ARTÍCULOS</p><span></span>";
            $btn_titulo = panelST2017($titulo,"", "auto", "TituloALMB");
			

            $queryd = get("queryd");
			
			if(empty($queryd)){
			    $operadorA = "<>";
				$queryd = "77777777777777777777777777";
			}else{
				$operadorA = "LIKE";
			}
			
            $sql = "SELECT AR.Nombre
			, CONCAT('
                <div  >',AR.Descripcion ,' </div>
                <div style=color:#999999;font-size:11px; >',TA.Descripcion,' | ', SA.Descripcion,' | ', AR.FechaHoraCreacion,' </div>
                <div style=color:#999999;font-size:11px; >ALIAS: ', MA.AliasId,' </div>
            ')AS 'Detalles'
			,  CONCAT('
                <div  >
				<div class=botIcRepC ><i class=icon-chevron-down ></i> 
				     <ul class=sub_boton >
					    <li onclick=enviaReg(''EDI',MA.Codigo,''',''{$enlace}?Tienda=EditarArticulosRD&cod_mov_almacen=',MA.Codigo,''',''panelOculto'','''');  >Editar</li>
					    <li onclick=enviaReg(''EDI',MA.Codigo,''',''{$enlace}?Tienda=EliminaArticulosRD&cod_mov_almacen=',MA.Codigo,''',''panelOculto'','''');>Eliminar</li>
				     </ul>
				</div>
				</div>
            ')AS 'Acción'
			,MA.Codigo AS CodigoAjax
			FROM articulos AR
			INNER JOIN movimiento_almacen MA ON AR.Codigo = MA.Articulo
			INNER JOIN lineaarticulo LA ON AR.Lineaarticulo = LA.Codigo
			INNER JOIN sectorarticulos SA ON AR.SectorArticulo = SA.Codigo
			INNER JOIN tipoarticulos TA ON AR.TipoArticulos = TA.Codigo
			
			WHERE 
			MA.Entidad = :Entidad
			AND ( AR.Nombre ".$operadorA." :Nombre OR AR.Descripcion ".$operadorA." :Descripcion )
			ORDER BY AR.FechaHoraCreacion DESC
			";    
            $clase = 'reporteA';
            $enlaceCod = 'cod_mov_almacen';
            $url = $enlace."?Tienda=EditarArticulosRD";
            $panel = 'panelOculto';
			
		    $where = [
			"Nombre" =>'%'.$queryd.'%',
			"Descripcion" =>'%'.$queryd.'%',			
			"Entidad"=>$Entidad
			];
     
            $reporte = ListR2('', $sql, $where ,$cnPDO, $clase, '', $url, $enlaceCod, $panel, 'sys_tabla_det_form', '', '','');
			
            $html .= "<div id = 'panelOculto' style='float:left;width:100%;' ></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;' >" . $reporte . "</div>";
            $panel = array(array('PanelA1-A', '100%', $html));
            $Cuerpo = LayoutPage($panel);
			
            $s = layoutV2($btn_tituloP , $pestanas . $btn_titulo . $Cuerpo); 
			  
			WE(htmlApp($s));	
            break;
			
        case 'EliminaArticulosRD':
            $cod_mov_almacen = get("cod_mov_almacen");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?Tienda=EliminaArticulos&cod_mov_almacen={$cod_mov_almacen}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;  
			
        case 'EliminaArticulos':
            $cod_mov_almacen = get("cod_mov_almacen");	
            $btn = "Confirmar ]" .$enlace."?Tienda=EliminaArticulosAccion&cod_mov_almacen={$cod_mov_almacen}]panelB]]}";
            $btn .= "<div onclick=\$popupEC.close(); >Cancelar</div> ]" .$enlace."?Tienda=CreaArticulos]PanelA1-B]]plomo}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR </p><span>El artículo seleccionado</span>";
            $btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALM");
			
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
                        <script> openPopupURI('".$enlacePopup."?Tienda=CreaArticulos', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      

        case 'CreaArticulos':
        
				$titulo = "<p>CREAR ARTÍCULO</p><span>Completa los datos del formulario</span>";
				$btn_titulo = panelST2017($titulo,"", "auto", "TituloALM");
			
                $titulo = "";
                $path = "";				
                $uRLForm = "Guardar]" . $enlace . "?metodo=articulos&transaccion=INSERT]panelB]F]}";
                $uRLForm .= "Cancelar]" . $enlace . "?metodo=articulos&transaccion=INSERT]\$popupEC.close()]JSB]]Plomo}";
				
                $tSelectD = array(
				'TipoArticulos' => 'SELECT Codigo,Descripcion FROM tipoarticulos'
				,'SectorArticulo' => 'SELECT Codigo,Descripcion FROM sectorarticulos WHERE Entidad = "'.$Entidad.'" '
				,'Lineaarticulo' => 'SELECT Codigo,Descripcion FROM lineaarticulo WHERE Entidad = "'.$Entidad.'" '
				);
			
                $form = c_form_adp($titulo, $cnPDO, "articulos", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
				
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$form = "<div style='float:left;width:100%;padding:0px;' >" . $form . "</div>";
				$html = "<div style='float:left;width:600px;' >" . $btn_titulo . $form . "</div>";
			    $script = jsCategorias();
                WE($script . $html);
				
            break;              
        case 'EditarArticulosRD':
		
		    $cod_mov_almacen = get("cod_mov_almacen");
			$html = "<script> openPopupURI('".$enlacePopup."?Tienda=EditaArticulos&cod_mov_almacen={$cod_mov_almacen}', {modal:true, closeContent:null}); </script>";
            WE($html);
			
            break;    
  			
        case 'EditaArticulos':

			$cod_mov_almacen = get("cod_mov_almacen");
			
			$sql = "SELECT 
			AR.Codigo 
			,AR.TipoArticulos
			FROM  articulos AR
			INNER JOIN movimiento_almacen MA ON AR.Codigo = MA.Articulo
			WHERE MA.Codigo = {$cod_mov_almacen}
			GROUP BY MA.Codigo
			";    
			$rg = fetch($sql,$cnPDO);
			$cod_articulo = $rg["Codigo"];				
			$CodigoTipoArticulo = $rg["TipoArticulos"];		
									
		    $segmentoUrl =  "&cod_articulo=".$cod_articulo."&cod_mov_almacen=".$cod_mov_almacen;
			if($CodigoTipoArticulo == 2 || $CodigoTipoArticulo == 4 ){//Curso
			
	            $pestanas = pestanasBLocal(array("".$segmentoUrl."]Marca","".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]"));
				
            }elseif($CodigoTipoArticulo == 1){// Ebook
			
	            $pestanas = pestanasBLocalEbook(array("".$segmentoUrl."]Marca","".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]"));
			
            }elseif($CodigoTipoArticulo == 3){// Estándar
			
			}	
			
			$path = array('Banner' => '../_imagenes/productos/');
			
            $tSelectD = array(
				'TipoArticulos' => 'SELECT Codigo,Descripcion FROM tipoarticulos'
				,'SectorArticulo' => 'SELECT Codigo,Descripcion FROM sectorarticulos WHERE Entidad = '.$Entidad.' '
				,'Lineaarticulo' => 'SELECT Codigo,Descripcion FROM lineaarticulo WHERE Entidad = '.$Entidad.' '
				,'Familiaarticulo' => 'SELECT Codigo,Descripcion FROM familiaarticulo'
			);
			
            $uRLForm = "Actualizar]" . $enlace . "?metodo=articulos&transaccion=UPDATE&{$segmentoUrl}]panelFormA1]F]}";
			$uRLForm .= "Cancelar]" . $enlace . "?metodo=articulos&transaccion=INSERT]\$popupEC.close()]JSB]]Plomo}";
			
			$form = c_form_adp("", $cnPDO, "articulos", "CuadroA", $path, $uRLForm,$cod_articulo, $tSelectD, 'Codigo');
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";							
		    $form = "<div style='padding:1% 1%;width:98%;float:left;' >".$pestanas . $form."</div>";
		    $html = "<div style='width:40rem;' id='panelFormA1' >" .$Close. $form . "</div>";	
		    $html .= "<script>resiSizePopup();</script>";	
	        $script = jsCategorias();
            WE($script . $html);
			
            break;
						
        case 'EditaArticulosPresentacion':

            $cod_articulo = get("cod_articulo");
            $cod_mov_almacen = get("cod_mov_almacen");
			
		    $segmentoUrl =  "&cod_articulo=".$cod_articulo."&cod_mov_almacen=".$cod_mov_almacen;
			
			$Query=" SELECT TipoArticulos FROM articulos  WHERE Codigo = :Codigo ";
			$rg = OwlPDO::fetchObj($Query, ["Codigo" => $cod_articulo] ,$cnPDO);
			$CodigoTipoArticulo = $rg->TipoArticulos;	
			
            if($CodigoTipoArticulo == 2){//Curso
			
	            $pestanas = pestanasBLocal(array("".$segmentoUrl."]","".$segmentoUrl."]Marca","".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]"));
				
				$path = array('ImagenPresentacionA' => '/system/_articulos/','ImagenPresentacionB' => '/system/_articulos/' );
				$tSelectD = array('TipoArticulos' => 'SELECT Codigo,Descripcion FROM tipoarticulos'
				,'SectorArticulo' => 'SELECT Codigo,Descripcion FROM sectorarticulos');
				$uRLForm = "Actualizar ]" . $enlace . "?metodo=articulos_presentacion&transaccion=UPDATE&{$segmentoUrl}]panelFormA1]F]}";
				$form = c_form_adp("", $cnPDO, "articulos_presentacion", "CuadroA", $path, $uRLForm,$cod_articulo, $tSelectD, 'Codigo');
				
            }elseif($CodigoTipoArticulo == 1){// Ebook
			
	            $pestanas = pestanasBLocalEbook(array("".$segmentoUrl."]","".$segmentoUrl."]Marca","".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]"));
			
				$path = array('ImagenPresentacionA' => '/system/_articulos/','DocumentoPresentacion' => '/system/_articulos/' );
				$tSelectD = "";
				
				$uRLForm = "Actualizar ]" . $enlace . "?metodo=articulos_presentacion_ebook&transaccion=UPDATE&{$segmentoUrl}]panelFormA1]F]}";
				$form = c_form_adp("", $cnPDO, "articulos_presentacion_ebook", "CuadroA", $path, $uRLForm,$cod_articulo, $tSelectD, 'Codigo');
				
			
            }elseif($CodigoTipoArticulo == 3){// Estándar
			
				
			}			
			
		    $form = "<div style='padding:1% 1%;width:98%;float:left;' >".$pestanas .$form."</div>";
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";		
		    $html = "<div style='width:40rem;' id='panelFormA1' >" . $Close . $form . "</div>";	
		    $html .= "<script>resiSizePopup();</script>";	
		
            WE($html);
			
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

			$menu = "GENERAL ]" . $enlace . "?Articulos=EditaArticulos&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]}";
			$menu .= "PRESENTACIÓN]" . $enlace . "?Articulos=EditaArticulosPresentacion&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]}";
			$menu .= "VENTAS]" . $enlace . "?Articulos=EditaArticulosVentas&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV]Marca}";
			// $menu .= "REDES SOCIALES]" . $enlace . "?Articulos=EditaArticulosRS&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			$menu .= "  ELIMINAR]" . $enlace . "?Articulos=EliminarConfirmar&CodigoArticulos=".$CodigoArticulos."&codigoAlmacen=".$codigoAlmacen."]layoutV}";
			$mv = menuVertical($menu,'menuDP');
		
            $tSelectD = array('Vendedor' => 'SELECT Codigo,Nombre FROM vendedores ');
            
			$uRLForm = "Actualizar]" . $enlace . "?metodo=almacen&transaccion=UPDATE&codigoAlmacen=" . $codigoAlmacen . "]panelB-R]F]}";
            $uRLForm .= "Cancelar]" . $enlace . "?metodo=articulos&transaccion=INSERT]\$popupEC.close()]JSB]]Plomo}";
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
				     AR.Nombre, 
				     AR.Descripcion
					 FROM articulos AR
					 WHERE 
					 AR.Entidad = :Entidad	
					 AND ( AR.Nombre ".$operadorA." :Nombre OR AR.Descripcion ".$operadorA." :Descripcion )
			"; 
            
			$where = [
			"Nombre" =>'%'.$queryd.'%',
			"Descripcion" =>'%'.$queryd.'%',
			"Entidad" => $Entidad
			];
			
			$html ="";	
			$countcolumn = OwlPDO::fetchAllArr($sql,$where,$cnPDO);
			$cont = 0;
			foreach ($countcolumn as $reg) {
				$con +=1;	
				// $viewdata = array();
				// $viewdata['Alias'] = $reg["Alias"];
				$html .= "<div id='p-".$con."' class=item onclick=buscadorAccionItem('p-".$con."'); > ".$reg["Nombre"]."</div>";
			}
            // vd($countcolumn);			
		    WE($html);
			
		break;
    }		
}


function CategoriasSearch($arg){

    switch ($arg) {
        case 'Categoria':

            $codigo_categoria = get('codigo_categoria');
            
            $sql = "SELECT Codigo,Descripcion FROM lineaarticulo WHERE SectorArticulos = :SectorArticulos ";
			$where = [
			"SectorArticulos" => $codigo_categoria
			];			
		    $countcolumn = OwlPDO::fetchAllArr($sql,$where,$cnPDO);

            foreach ($countcolumn as $value) {
                $html .= "<option value='".$value["Codigo"]."'>".$value["Descripcion"]."</option>";
            }

            WE($html);

        break;
    }
    
    
}

function IngresaAlmacen($codigo){
    global $cnPDO,$FechaHora, $Usuario, $Entidad;
	
    
	$Nombre = post("Nombre");
	$search = array(" ");
	$fileNameModified = remp_caracter(str_replace($search, "_", $Nombre));
	
	$Query=" SELECT COUNT(*) TotReg FROM movimiento_almacen  WHERE AliasId = :AliasId ";
	$rg = OwlPDO::fetchObj($Query, ["AliasId" => $fileNameModified] ,$cnPDO);
	$TotReg = $rg->TotReg;		

	if($TotReg == 0){
		$fileNameModified = $fileNameModified;	
	}else{
		$TotReg = $TotReg + 1;
		$fileNameModified = $fileNameModified."_".$TotReg;			
	}
	
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
	$tableValue["AliasId"] =   $fileNameModified;
	
	$return 			= 	insertPDO("movimiento_almacen",$tableValue,$cnPDO);
	

		
}

// function GeneraAlias($codigo){
    // global $cnPDO,$FechaHora, $Usuario, $Entidad;

	// $Nombre = post("Nombre");
	// $search = array(" ");
	// $fileNameModified = remp_caracter(str_replace($search, "_", $Nombre));
	
	// $Query=" SELECT COUNT(*) TotReg FROM movimiento_almacen  WHERE AliasId = :AliasId ";
	// $rg = OwlPDO::fetchObj($Query, ["AliasId" => $fileNameModified] ,$cnPDO);
	// $TotReg = $rg->TotReg;		

	// if($TotReg == 0){
		// $fileNameModified = $fileNameModified;	
	// }else{
		// $TotReg = $TotReg + 1;
		// $fileNameModified = $fileNameModified."_".$TotReg;			
	// }

	// $reg = array('AliasId' => $fileNameModified);
	// $where = array('Codigo' => $codigo);
	// $rg = OwlPDO::update('movimiento_almacen', $reg , $where, $cnPDO);		
// }


?>
