<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_tienda.php');

if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_user_d_personales.php";
$enlacePopup = "se_user_d_personales.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
$_SESSION['menuVertical']= "2";

if (get('userPerfil') != '') {userPerfil(get('userPerfil'));}

if (get("metodo") != "") {
        
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario, $Entidad;
        
        if (get("metodo") == "FUsuarioEdit") {
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
		
        if (get("metodo") == "FFotoUsuario") {
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

            if (get("metodo") == "FUsuarioEdit") {
                p_gf_udp("FUsuarioEdit", $cnPDO, $Usuario,'Codigo');
				
			
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				userPerfil("EditaPerfil");
				WE("");
            }
			
            if (get("metodo") == "FFotoUsuario") {
                p_gf_udp("FFotoUsuario", $cnPDO, get("CodRegistro"),'Codigo');
				
				ActualizaFoto();
					
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				userPerfil("EditarFoto");
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


function userPerfil($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup ,$Entidad,$Usuario;
	
    switch ($Arg) {

        case 'Site':
		
            $cod_mov_almacen = get("cod_mov_almacen");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?userPerfil=EditaPerfil&cod_mov_almacen={$cod_mov_almacen}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;  		
        case 'EditaPerfil':

			$cod_mov_almacen = 1222;
			
			$sql = "SELECT AR.Codigo 
			FROM  articulos AR
			INNER JOIN movimiento_almacen MA ON AR.Codigo = MA.Articulo
			WHERE MA.Codigo = {$cod_mov_almacen}
			GROUP BY MA.Codigo
			";    
			$rg = fetch($sql,$cnPDO);
			$cod_articulo = $rg["Codigo"];				
			
		    $segmentoUrl =  "&cod_articulo=".$cod_articulo."&cod_mov_almacen=".$cod_mov_almacen;
			
			$pestanas = pestanasPerfilLocal(array("".$segmentoUrl."]Marca","".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]"));
			
			$path = array('Banner' => '../_imagenes/productos/');
            $tSelectD = "";
			
            $uRLForm = "Actualizar]" . $enlace . "?metodo=FUsuarioEdit&transaccion=UPDATE&{$segmentoUrl}]panelFormA1]F]}";
			
			$form = c_form_adp("", $cnPDO, "FUsuarioEdit", "CuadroA", $path, $uRLForm,$Usuario, $tSelectD, 'Codigo');
					
		    $form = "<div style='padding:0% 1%;width:98%;float:left;' >".$pestanas . $form."</div>";
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";		
		    $html = "<div style='width:35rem;' id='panelFormA1' >" . $Close. $form . "</div>";	
		    $html .= "<script>resiSizePopup();</script>";	
	
            WE($html);
            break;
						
        case 'EditarFoto':

            $cod_articulo = get("cod_articulo");
            $cod_mov_almacen = get("cod_mov_almacen");
			
		    $segmentoUrl =  "&cod_articulo=".$cod_articulo."&cod_mov_almacen=".$cod_mov_almacen;
			
	        $pestanas = pestanasPerfilLocal(array("".$segmentoUrl."]","".$segmentoUrl."]Marca","".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]"));
				
			
            $path = array('Foto' => '/_imagenes/usuarios/');
			$tSelectD = array('TipoArticulos' => 'SELECT Codigo,Descripcion FROM tipoarticulos'
			,'SectorArticulo' => 'SELECT Codigo,Descripcion FROM sectorarticulos');
			
            $uRLForm = "Actualizar]" . $enlace . "?metodo=FFotoUsuario&transaccion=UPDATE&CodRegistro={$Usuario}{$segmentoUrl}]panelFormA1]F]}";
			
			$form = c_form_adp("", $cnPDO, "FFotoUsuario", "CuadroA", $path, $uRLForm,$Usuario, $tSelectD, 'Codigo');
					
		    $form = "<div style='padding:0% 1%;width:98%;float:left;' >".$pestanas .$form."</div>";
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";		
		    $html = "<div style='width:35rem;' id='panelFormA1' >" . $Close . $form . "</div>";	
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

function ActualizaFoto(){
	global $cnPDO, $FechaHora,$Usuario, $Entidad;	
	$reg = array('Foto' => post('Foto'));
	$where = array('Codigo' =>  get('CodRegistro'));
	$rg = OwlPDO::update('entidades', $reg , $where, $cnPDO);			
}

?>
