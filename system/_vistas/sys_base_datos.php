<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_sys_usuarios.php');

if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}

require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/sys_base_datos.php";
$enlacePopup = "sys_base_datos.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
$_SESSION['menuVertical']= "2";

if (get('baseDatos') != '') {baseDatos(get('baseDatos'));}

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


function baseDatos($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup ,$Entidad;
	
    switch ($Arg) {

        case 'Site':
            
			$pestanas = funcion_local_pestanas_A(array("&parm=new]Marca",""));
			
            $listMn = "<i class='icon-chevron-right'></i> Búsqueda Avanzada  [" .$enlace."?Tienda=CreaArticulosRD[panelOculto[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 1 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 2 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";

            $btn = "<i class='icon-pencil'></i>  ]" .$enlace."?Tienda=CreaArticulosRD]panelOculto]]}";
            $btn .= "<i class='icon-align-justify'></i>  ]SubMenu]{$listMn}]OPCIONES DEL PERFIL]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
			
			$titulo = "<p>BASE DE DATOS  </p><span>Administración y gestión de base de datos</span>";
            $btn_tituloP = panelST2017($titulo, $btn, "auto", "TituloAMSG");
			
	
            $titulo = "<p>LISTADO DE USUARIOS</p><span></span>";
            $btn_titulo = panelST2017($titulo,"", "auto", "TituloALMB");
			
        
            $sql = "SELECT AR.Nombre
			, CONCAT('
                <div  >',AR.Descripcion ,' </div>
                <div style=color:#999999;font-size:11px; >',LA.Descripcion,' | ', FA.Descripcion,' | ', AR.FechaHoraCreacion,' </div>
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
			
            $s = layoutV2($btn_tituloP , $pestanas . $btn_titulo . $Cuerpo); 
			
			WE(html($s));	
			// WE($s);	
            
            break;
			
        case 'Site_Perfiles':
            
			$pestanas = funcion_local_pestanas_A(array("","&parm=new]Marca"));
			
            $listMn = "<i class='icon-chevron-right'></i> Búsqueda Avanzada  [" .$enlace."?Tienda=CreaArticulosRD[panelOculto[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 1 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 2 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";

            $btn = "<i class='icon-pencil'></i>  ]" .$enlace."?Tienda=CreaArticulosRD]panelOculto]]}";
            $btn .= "<i class='icon-align-justify'></i>  ]SubMenu]{$listMn}]OPCIONES DEL PERFIL]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
			
			$titulo = "<p>USUARIOS  </p><span>Administración y gestión de usuarios</span>";
            $btn_tituloP = panelST2017($titulo, $btn, "auto", "TituloAMSG");
			
	
            $titulo = "<p>LISTADO DE PRODUCTOS</p><span>Administración de datos</span>";
            $btn_titulo = panelST2017($titulo,"", "auto", "TituloALMB");
			
        
            $sql = "SELECT AR.Nombre
			, CONCAT('
                <div  >',AR.Descripcion ,' </div>
                <div style=color:#999999;font-size:11px; >',LA.Descripcion,' | ', FA.Descripcion,' | ', AR.FechaHoraCreacion,' </div>
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
			
            $s = layoutV2($btn_tituloP , $pestanas . $btn_titulo . $Cuerpo); 
			
			WE(html($s));	
			// WE($s);	
            
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
     

        default:
            exit;
            break;
    }
}



?>
