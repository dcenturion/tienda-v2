<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_sys_tablas.php');

if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/sys_tablas.php";
$enlacePopup = "sys_tablas.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['master_access'];
$administradorUser = $_SESSION['master_access'];
$_SESSION['menuVertical']= "0";

if (get('tablas') != '') {tablas(get('tablas'));}
if (get('Busqueda') != '') {Busqueda(get('Busqueda'));}

if (get("metodo") != "") {
        
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario;


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
		
        if (get("metodo") == "sys_tabla1") {
            if ($campo == "FechaRegistro") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = "'".$Usuario."'";
            } elseif ($campo == "UsuarioActualizacion") {
                $valor = "'".$Usuario."'";
            } elseif ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";
            } else {
                $valor = "";
            }
            return $valor;
        }	
		
        if (get("metodo") == "sysTabletDet") {
            if ($campo == "sys_tabla") {
                $valor = "'" . get("codigo") . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = "'".$Usuario."'";
            } elseif ($campo == "UsuarioActualizacion") {
                $valor = "'".$Usuario."'";
            } elseif ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";
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

            if (get("metodo") == "sysTabletDet") {
             
                #ADMIN ACCIONES

                $data = array(
                'Usuario' => $administradorUser,
                'Fecha' => $FechaHora,
                'Tabla' => 'sys_tabla_det',
                'CRUD' => 'UPDATE',
                'Descripcion' => 'Actualización del campo '.post("Descripcion").' de la tabla '.get("codigoSysTabla").'.'
                );
				$rg = OwlPDO::insert('sys_admin_acciones', $data, $cnPDO);	

                actualizaCampo();
			}			
        }

        if (get("transaccion") == "INSERT") {

            if (get("metodo") == "sys_tabla1") {
                pro_systabla();
				W(" <script> \$popupEC.close();</script>");
				Msg("El proceso fue ejecutado correctamaente","C");
				tablas("Site");
            }

            if (get("metodo") == "sysTabletDet") {
				
                #ADMIN ACCIONES
                $data = array(
                'Usuario' => $administradorUser,
                'Fecha' => $FechaHora,
                'Tabla' => 'sys_tabla_det',
                'CRUD' => 'INSERT',
                'Descripcion' => 'Creación del campo '.post("Descripcion").' de la tabla '.get("codigoSysTabla").'.'
                );
				$rg = OwlPDO::insert('sys_admin_acciones', $data, $cnPDO);	
                pro_sysTabletDet();
				// vistaCT("FormDet");
            }
            if (get("metodo") == "sys_export") {

                $Archivo = post("Archivo");
                if($Archivo){
                    $Path = '../../Importar/'.$Archivo.'';
                    $Data = LeerExcelImport($Path);
                    $Json = json_decode($Data);
                    foreach($Json as $objeto){
                        $Tabla = $objeto->Tabla;
                        $Campo = $objeto->Campo;
                        $TipoCampo = $objeto->TipoCampo;
                        $Tamano = $objeto->Tamano;
                        if($Tabla && $Campo && $TipoCampo){
                            CrearCampos($Tabla,$Campo,$TipoCampo,$Tamano);
                        }
                    }


                }else{
                    W(Msg("Adjunte un archivo tipo: *.xls, *.xlsx <i class='icon-remove'></i>",'E')) ;
                }

                closePopupB();
                tablas('Site');
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


function tablas($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup ,$Usuario;
	$codigoBd = $_SESSION['CodigoBD'];
    switch ($Arg) {

        case 'Site':
            //brianvalverde
			$pestanas = funcion_local_pestanas_A(array("&parm=new]Marca",""));

            $listMn = "<i class='icon-chevron-right'></i> Seleccionar Base de Datos[" .$enlace."?tablas=RDBaseDatos[panelOculto[[{";
            // $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 1 [" .$enlace."?tablas=CreaArticulos[PanelA1-B[[{";
            // $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 2 [" .$enlace."?tablas=CreaArticulos[PanelA1-B[[{";



		    if(!empty($codigoBd)){
				
				// $Query=" SELECT Nombre FROM sys_base_datos WHERE Codigo = :CodigoT ";
				// $rg = OwlPDO::fetchObj($Query, ["CodigoT" => $codigoBd] ,$cnPDO);
				// vd($rg);
				// $nombreBD = $rg->Nombre;
			    $string = " | Base se Datos: <b>".$codigoBd."</b>";
            }
			
            $btn = "<i class='icon-search'></i> Búsqueda Avanzada ]" .$enlace."?tablas=CreaTablaRD]panelOculto]]}";
            $btn .= "<i class='icon-pencil'></i>  ]" .$enlace."?tablas=CreaTablaRD]panelOculto]]}";
            $btn .= "<i class='icon-cog'></i>  ]SubMenu]{$listMn}]CONFIGURACIÓN]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
			
			$titulo = "<p>TABLAS  </p><span>Administración y gestión de tablas ".$string." </span>";
            $btn_tituloP = panelST2017($titulo, $btn, "auto", "TituloAMSG");

            $listMn .= "<i class='icon-chevron-right'></i> Importar Tablas[" .$enlace."?tablas=ImportarTablaRD[panelOculto[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Exportar Tablas[" .$enlacePopup."?tablas=ExportarTabla[panelB[CHECK[sys_tabla_form{";
            $listMn .= "<i class='icon-chevron-right'></i> Eliminar Tablas[." .$enlace."?tablas=EliminarTablas[panelB[CHECK[sys_tabla_form{";

			
			
            $btn = "<i class='icon-reorder'></i> ]SubMenu]{$listMn}]Opciones múltiples]}";
            $btn = Botones($btn, 'botones1', 'sys_form');	
            $titulo = "<p>LISTADO DE TABLAS</p><span></span>";
            $btn_titulo = panelST2017($titulo,$btn, "auto", "TituloALMB");
			

            $sql = " SELECT
				Codigo AS CodigoAjax
				,ST.Descripcion
				,AD.Nombres AS 'User Creación'
				,AD2.Nombres AS 'User Actualización'
				,DATE_FORMAT(FechaRegistro, '%d %M %Y') AS FechaCreacion
				,  CONCAT('
                <div  >
				<div class=botIcRepC ><i class=icon-chevron-down ></i> 
				     <ul class=sub_boton >
					    <li onclick=enviaReg(''EDI',Codigo,''',''{$enlace}?tablas=EditarRD&codigo=',Codigo,''',''panelOculto'','''');  >Editar</li>
					    <li onclick=enviaReg(''EDI',Codigo,''',''{$enlace}?tablas=Elimina&codigo=',Codigo,''',''panelOculto'','''');>Eliminar</li>
				     </ul>
				</div>
				</div>
            ')AS 'Acción' 
				FROM sys_tabla ST
				LEFT JOIN administradores AD ON ST.UsuarioCreacion = AD.AdminCod
				LEFT JOIN administradores AD2 ON ST.UsuarioActualizacion = AD2.AdminCod
				WHERE ST.Descripcion LIKE :Descripcion
				ORDER BY  FechaRegistro Desc "; 
			$clase = 'reporteA';
            $queryd  =get("queryd");
			$where = [
			"Descripcion" =>'%'.$queryd.'%'
			];
			
			$panel = 'panelB';
            $url = "";
			$enlaceCod = 'AlmacenCurso,AlmacenPrograma';
            $urlPaginacion = $enlace."?tablas=Site";
			
            $reporte = ListR2('', $sql, $where , $cnPDO, $clase, '', $url, $enlaceCod, $panel, 'sys_tabla_form', 'checks', '0,10','',$urlPaginacion);

            $html .= "<div id = 'panelOculto' style='float:left;width:100%;' ></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;' >" . $reporte . "</div>";
            $panel = array(array('PanelA1-A', '100%', $html));
            $Cuerpo = LayoutPage($panel);
			
            $s = layoutV2($btn_tituloP , $pestanas . $btn_titulo . $Cuerpo);
			
			WE(html($s));	
		
            break;
        case 'ImportarTablaRD':
            $html = "	
						<script> openPopupURI('".$enlacePopup."?tablas=ImportarTabla', {modal:true, closeContent:null}); </script>
					";
            WE($html);
            break;
        case 'ImportarTabla':
            $btn = "<i class='icon-download-alt'></i> Descargar Plantilla]".$IPArchivos."/TUTORIAL/Importar.xlsx]]LINK]Verde}";
            $btn .= "<div class='botIconS'><i class='icon-arrow-left'></i></div>]" . $enlace . "?accionCT=tablas]cuerpo}";
            $btn = Botones($btn, 'botones1', '');
            $titulo = "<span></span><p>Importar Tablas</p><div class='bicel'></div>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "400px", "TituloA");

            $Path = '../../Importar/';
            $path = array('Archivo' => $Path);
            $uRLForm = "Guardar ]" . $enlace . "?metodo=sys_export&transaccion=INSERT]panelB]F]}";
            $form = PrintForm('', $cnOld, 'sys_export', '', $path, $uRLForm, '', '', '');

            $form = "<div> <label>Archivo fuente </label> {$form}  <div class='titulo_boton_form'> <i class='fa fa-cloud-upload'> </i> Subir archivo</div> </div>";

            $panelGeneral = "<div id='panel_form'> {$form} </div>" ;
            $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
            $contenido = "<div class='sub_panelInterfas'>". $panelGeneral."</div>";
            $cn = "<div class='panelInterfas' style='padding:2%'>".$contenido."</div>";
            $style = "<style>
						#panel_form .titulo_boton_form {
							position: absolute;
							color: #FFF;
							font-size: 0.8em;
							margin-top: 30px;
							margin-left: 28px;
							cursor: pointer;
						}						
			</style>";
            WE(html($Close. $style . $cn));
            break;
        case 'ExportarTabla':

            $campos = post("ky");
            if($campos){

                $FILTER = implode("','", $campos);

                $sql = "SELECT sys_tabla Tabla,
                            Descripcion Campo,
                            TipoCampo,
                            Size Tamano
                            FROM sys_tabla_det
                            WHERE sys_tabla IN ('".$FILTER."')
                            ORDER BY sys_tabla ASC ";
                $rg = fetch($sql,$cnPDO);
                if($rg) {
                    ExportExcelTabla($sql,$cnOld,'Exportar');
                }
            }else{
                W(Msg("Seleccione como mínimo una Tabla <i class='icon-remove'></i>",'E')) ;
            }
            //vistaCT('tablas');
            tablas('Site');
            break;
        case 'EliminarTablas':
		
            $ky = post("ky");
		    foreach($ky as $selected) {

				$where = array('Codigo' => $selected );
				$rg = OwlPDO::delet('sys_tabla', $where, $cnPDO);
				
				$where = array('sys_tabla' => $selected );
				$rg = OwlPDO::delet('sys_tabla_det', $where, $cnPDO);	

				$rg = OwlPDO::drop($selected,$cnPDO);	

			}
			
			closePopup();
			
			Msg("El proceso fue ejecutado correctamaente","C");
			tablas("Site");
			
			WE("");
			
            break;	
			
        case 'EliminarTablaONE':
		
            $tabla = get("codigo");

				$where = array('Codigo' => $tabla );
				$rg = OwlPDO::delet('sys_tabla', $where, $cnPDO);
				
				$where = array('sys_tabla' => $tabla );
				$rg = OwlPDO::delet('sys_tabla_det', $where, $cnPDO);	

				$rg = OwlPDO::drop($tabla,$cnPDO);	
			
			
			Msg("El proceso fue ejecutado correctamaente","C");

			W(" <script> \$popupEC.close();</script>");
			tablas("Site");
			WE("");
			
            break;	
			
        case 'EliminarCampos':

            $ky = post("ky");
            $tabla = get("codigo");

		    foreach($ky as $selected) {


				$sql = "SELECT  Descripcion FROM sys_tabla_det WHERE  Codigo =:CodigoT ";
				$rg = OwlPDO::fetchObj($sql, ["CodigoT" => $selected] ,$cnPDO);
				$nombre_campT = $rg->Descripcion;
				
	
				$where = array('Codigo' => $selected );
				$rg = OwlPDO::delet('sys_tabla_det', $where, $cnPDO);	
		
				$sql = " ALTER TABLE " . $tabla . " DROP " . $nombre_campT . "";
				$rg = OwlPDO::ex($sql,$cnPDO);	

			}
	        
			closePopup();
			Msg("El proceso fue ejecutado correctamaente","C");	
		    tablas("Editar");	
			
		    WE("");
		    break;

        case 'EliminarCampo':

            $ky = post("ky");
            $tabla = get("codigo");

            foreach($ky as $selected) {


                $sql = "SELECT  Descripcion FROM sys_tabla_det WHERE  Codigo =:CodigoT ";
                $rg = OwlPDO::fetchObj($sql, ["CodigoT" => $selected] ,$cnPDO);
                $nombre_campT = $rg->Descripcion;


                $where = array('Codigo' => $selected );
                $rg = OwlPDO::delet('sys_tabla_det', $where, $cnPDO);

                $sql = " ALTER TABLE " . $tabla . " DROP " . $nombre_campT . "";
                $rg = OwlPDO::ex($sql,$cnPDO);

            }

            closePopup();
            Msg("El proceso fue ejecutado correctamaente","C");
            tablas("Editar");

            WE("");
            break;
			
        case 'CreaTablaRD':
            $codigoSysTabla = get("codigo");	
			$html = "	
						<script> openPopupURI('".$enlacePopup."?tablas=CreaTabla&codigo=".$codigoSysTabla."', {modal:true, closeContent:null}); </script>
					";
			WE($html);
			
            break;			
        case 'CreaTabla':
		    
			$codigoSysTabla = get("codigo");	
			
            $titulo = "<p>CREAR TABLA".$codigoSysTabla."</p><span>Estas trabajando en la BD '".$codigoBd."'</span>";
            $btn_titulo = panelST2017($titulo, "", "auto", "TituloALM");
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=sys_tabla1&transaccion=INSERT]panelB]F]}";
			// $uRLForm .= "Cancelar]" . $enlace . "?metodo=articulos&transaccion=INSERT]\$popupEC.close()]JSB]]Plomo}";
			
			$tSelectD = "";
			$form = c_form_adp($titulo, $cnPDO, "sys_tabla1", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
			
			
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";				
	        $html = "<div style='float:left;width:500px;' >" . $Close.$btn_titulo.$form . "</div>";
			WE($html);
			
            break;  	
			
        case 'EditarRD':
            $codigoSysTabla = get("codigo");	
			$html = "	
						<script> openPopupURI('".$enlacePopup."?tablas=Editar&codigo=".$codigoSysTabla."', {modal:true, closeContent:null}); </script>
					";
			WE($html);
			
            break;			
        case 'Editar':
		    
			$codigoSysTabla = get("codigo");


            $listMn = "<i class='icon-chevron-right'></i> Añadir Campo [" .$enlace."?tablas=AnadirCampo&codigo=".$codigoSysTabla."[panel_popup[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Eliminar Campos[." .$enlace."?tablas=EliminarCampo&codigo=".$codigoSysTabla."&[panel_popup[CHECK[sys_tabla_det_form{";
            $listMn .= "<i class='icon-chevron-right'></i> Eliminar Tabla [" .$enlace."?tablas=EliminarTablaONE&codigo=".$codigoSysTabla."[panelB[[{";

            $btn = "<i class='icon-reorder'></i> ]SubMenu]{$listMn}]Opciones]}";
            $btn = Botones($btn, 'botones1', 'sys_form');

            $titulo = "<p>ADMINISTRAR TABLA: ".$codigoSysTabla."</p><span></span>";
            $btn_titulo = panelST2017($titulo,$btn, "auto", "TituloALM");



            $sql = 'SELECT Codigo AS CodigoAjax, Descripcion,TipoCampo  FROM sys_tabla_det ';
			$sql .= ' WHERE sys_tabla = :sys_tabla ';
            $clase = 'reporteA';
			$where = [":sys_tabla"=>$codigoSysTabla];
			
            $url = $enlace."?tablas=EditarCampo&codigo=".$codigoSysTabla."";
            $panel = 'panel_popup';
			$enlaceCod = "CodigoSD";
			
            $reporte = ListR2('', $sql, $where , $cnPDO, $clase, '', $url, $enlaceCod, $panel, 'sys_tabla_det_form', 'checks', '','');


			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";		
	        $reporte = "<div class='panelReport' >" . $reporte . "</div>";			
	        $html = "<div style='float:left;width:500px;' id='panel_popup'>" . $Close.$btn_titulo .$reporte . "</div>";

			WE($html);
			
            break; 
			
        case 'EditarCampo':
		    
			$CodigoSD = get("CodigoSD");	
			$codigoSysTabla = get("codigo");	
			
			$Query=" SELECT sys_tabla,Descripcion,TipoCampo ,Codigo AS CodigoAjax FROM sys_tabla_det
             			WHERE Codigo = :CodigoT ";
			$rg = OwlPDO::fetchObj($Query, ["CodigoT" => $CodigoSD] ,$cnPDO);
			$sys_tabla = $rg->sys_tabla;
			$Descripcion = $rg->Descripcion;
				
            $btn = "<i class='icon-arrow-left'></i>  ]" .$enlace."?tablas=Editar&codigo=".$codigoSysTabla."]panel_popup]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');		

            $titulo = "<span>ACTUALIZAR EL CAMPO: " . $Descripcion . "</span><p>Tabla: " . $sys_tabla . "</p>";
            $btn_titulo = panelST2017($titulo,$btn, "auto", "TituloALM");
			
			
			
            $uRLForm = "Actualizar]" . $enlace . "?metodo=sysTabletDet&transaccion=UPDATE&codigo=".$codigoSysTabla."&cod_std=".$CodigoSD."]panel_popup]F]}";
			$uRLForm .="Eliminar]" . $enlace . "?metodo=sysTabletDet&transaccion=DELETE&codigo=".$codigoSysTabla."&cod_std=".$CodigoSD."]panel_popup]F]}";
			
			$tSelectD = array('TipoCampo' => 'SELECT Codigo,Descripcion FROM sys_tipo_input');
			// $form = c_form($titulo, $vConex, "sysTabletDet", "CuadroA", $path, $uRLForm, $codigo_sys_tabla_det, $tSelectD);
			$form = c_form_adp("", $cnPDO, "sysTabletDet", "CuadroA", $path, $uRLForm,$CodigoSD, $tSelectD, 'Codigo');
			
			
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";				
	        $html = "<div style='float:left;width:500px;' id='panel_popup'>" . $Close.$btn_titulo. $form . "</div>";
			WE($html);
			
            break;  

			
        case 'AnadirCampo':
		    
			$CodigoSD = get("CodigoSD");	
			$codigoSysTabla = get("codigo");	
			
			$Query=" SELECT sys_tabla,Descripcion,TipoCampo ,Codigo AS CodigoAjax FROM sys_tabla_det
             			WHERE Codigo = :CodigoT ";
			$rg = OwlPDO::fetchObj($Query, ["CodigoT" => $CodigoSD] ,$cnPDO);
			$sys_tabla = $rg->sys_tabla;
			$Descripcion = $rg->Descripcion;
				
            $btn = "<i class='icon-arrow-left'></i>  ]" .$enlace."?tablas=Editar&codigo=".$codigoSysTabla."]panel_popup]]}";
            // $btn .= "<i class='icon-cog'></i>  ]SubMenu]{$listMn}]CONFIGURACIÓN]}";
            $btn = Botones($btn, 'botones1', 'sys_form');		

			
            $titulo = "<span>AÑADIR CAMPO </span><p>Tabla: " . $codigoSysTabla . "</p>";
            $btn_titulo = panelST2017($titulo,$btn, "auto", "TituloALM");
			
            $uRLForm = "Guardar]" . $enlace . "?metodo=sysTabletDet&transaccion=INSERT&codigo=" . $codigoSysTabla . "]panel_popup]F]}";

			$tSelectD = array('TipoCampo' => 'SELECT Codigo,Descripcion FROM sys_tipo_input');
			// $form = c_form($titulo, $vConex, "sysTabletDet", "CuadroA", $path, $uRLForm, $codigo_sys_tabla_det, $tSelectD);
			$form = c_form_adp("", $cnPDO, "sysTabletDet", "CuadroA", $path, $uRLForm,"", $tSelectD, 'Codigo');
			
			
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";				
	        $html = "<div style='float:left;width:500px;' id='panel_popup'>" . $Close.$btn_titulo. $form . "</div>";
			WE($html);
			
            break;  
						
			
        case 'RDBaseDatos':
            
			$html = "	
						<script> openPopupURI('".$enlacePopup."?tablas=BaseDatos', {modal:true, closeContent:null}); </script>
					";
			WE($html);
			
            break;			
        case 'BaseDatos':
		
            $titulo = "<p>SELECCION LA BASE DE DATOS</p><span>En la que deseas trabajar</span>";
            $btn_titulo = panelST2017($titulo, "", "auto", "TituloALM");
			
            $tSelectD = array('Nombre' => 'SELECT Nombre,Nombre  FROM sys_base_datos  ');
            $uRLForm = "Cambiar]" . $enlace . "?tablas=CambiarBD&transaccion=UPDATE]panelB]F]}";
    
			$form = c_form_adp("", $cnPDO, "select_bdatos", "CuadroA", $path, $uRLForm,"", $tSelectD, 'Codigo');	
	        
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";				
	        $html = "<div style='float:left;width:500px;' >" . $Close.$btn_titulo.$form . "</div>";
			WE($html);
			
            break;  
			
        case 'CambiarBD':
		
            $Nombre = post("Nombre");	
	        $_SESSION['CodigoBD'] = $Nombre;
			W(" <script> \$popupEC.close();</script>");
			tablas("Site");
			WE("");
            break;  		
     

        default:
            exit;
            break;
    }
}

function pro_systabla() {
    global $cnOld, $administradorUser, $FechaHora,$cnPDO;
	
    $Codigo=post("Codigo");
    // $Codigo = strtolower($Codigo);
	
    $sql = 'SELECT Codigo,Descripcion FROM sys_tabla WHERE  Codigo = "' . $Codigo . '" ';
    $rg = fetchOld($sql);
    $codigo = $rg["Codigo"];
    if ($codigo != "") {

        W("La tabla ya existe");
        vistaCT("tablas");
    } else {

		p_gf_udp("sys_tabla1",$cnPDO,'','Codigo');
        crea_tabla($Codigo, $cnOld);

        #ADMIN ACCIONES
        $data = array(
        'Usuario' => $administradorUser,
        'Fecha' => $FechaHora,
        'Tabla' => 'sys_tabla',
        'CRUD' => 'INSERT',
        'Descripcion' => 'Creación de la tabla '.$Codigo.'.'
        );
        insert("sys_admin_acciones", $data);
    }
}

function crea_tabla($tabla, $conexion) {
    global $administradorUser, $FechaHora;

    $entero = post("Entero");
    $Log = post("Log");
    $size = post("Size");
    $sql = " CREATE TABLE " . $tabla . " (";
    if ($entero == "SI") {
        if ($size > 0) {
            $sql .= " Codigo INT(" . $size . ") NOT NULL AUTO_INCREMENT, ";
        } else {
            $sql .= " Codigo INT NOT NULL AUTO_INCREMENT, ";
        }
        $tipo = "INT";
    } else {
        if ($size > 0) {
            $sql .= " Codigo VARCHAR(" . $size . ") NOT NULL, ";
        } else {
            $sql .= " Codigo VARCHAR NOT NULL, ";
        }

        $tipo = "VARCHAR";
    }
    $sql .= " PRIMARY KEY (Codigo)";
    $sql .= " ); ";
    xSQL($sql, $conexion);

    $cod_sys_tabla_det = numerador("sys_tabla_det", 0, "");
	
    $sql = 'INSERT  INTO sys_tabla_det (Codigo,Descripcion,TipoCampo,sys_tabla,UsuarioCreacion,UsuarioActualizacion,FechaHoraCreacion,FechaHoraActualizacion) VALUES (' . $cod_sys_tabla_det . ',"Codigo","' . cmn($tipo) . '","' . $tabla . '","' . $administradorUser . '","' . $administradorUser . '","' . $FechaHora . '","' . $FechaHora . '")';
    xSQL($sql, $conexion);

    if ($Log != "") {
        $sql = " CREATE TABLE log_" . $tabla . " (";
        $sql .= " Codigo INT NOT NULL auto_increment, ";
        $sql .= " Usuario VARCHAR(100) NOT NULL ,";
        $sql .= " Empresa VARCHAR(100) NOT NULL ,";
        $sql .= " Operacion VARCHAR(50) NOT NULL ,";
        if ($entero == "SI") {
            $sql .= " " . $tabla . " INT(30) NOT NULL, ";
        } else {
            $sql .= " " . $tabla . " VARCHAR(20) NOT NULL ,";
        }
        $sql .= " Fecha_Hora DATETIME NOT NULL ,";
        $sql .= " PRIMARY KEY (Codigo)";
        $sql .= " ); ";
        xSQL($sql, $conexion);
    }
}

function actualizaCampo() {

    global $cnOld;
    $sys_tabla = get("codigo");

    $campoActual = post("Descripcion");
    $tipoCampo = post("TipoCampo");
    $size = post("Size");

    $sql = 'SELECT Descripcion FROM sys_tabla_det WHERE  Codigo = ' . get("cod_std") . '  ';
    $rg = fetchOld($sql);
    $campoAntiguo = $rg["Descripcion"];
    // W($campoAntiguo);

    $sql = " ALTER TABLE " . $sys_tabla . " ";
    $sql .= " CHANGE " . $campoAntiguo . "  " . $campoActual . " " . $tipoCampo . " ";
    if ($tipoCampo == "int" || $tipoCampo == "decimal") {
        if ($size > 0) {
            $sql .= " (" . $size . ")";
        } else {
            $sql .= "";
        }
    }

    if ($tipoCampo == "varchar") {
        $sql .= " (" . $size . ") CHARACTER SET utf8 ";
    }
    if ($tipoCampo == "char") {
        $sql .= " (" . $size . ") CHARACTER SET utf8 ";
    }
    if ($tipoCampo == "datetime" || $tipoCampo == "date") {
        
    }
    if ($tipoCampo == "text") {
        $sql .= " CHARACTER SET utf8 NOT NULL";
    }

    $sql .= " ; ";
	
    W(xSQL($sql, $cnOld));

    p_gf("sysTabletDet", $cnOld, get("cod_std"));
	Msg("El proceso fue ejecutado correctamaente","C");	
    tablas("Editar");
}


function pro_sysTabletDet() {

    global $cnOld;
	
    $tabla = get("codigo");
	
    $descripcion = post("Descripcion");
    $tipoCampo = post("TipoCampo");
    $size = post("Size");

    if ($tipoCampo == "varchar" || $tipoCampo == "char") {
        $sql = "ALTER TABLE " . $tabla . " ADD " . $descripcion . " " . $tipoCampo . "(" . $size . ") CHARACTER SET utf8  NOT NULL";
        // WE(" FF ".$sql);
		xSQL($sql, $cnOld);
    }

    if ($tipoCampo == "int" || $tipoCampo == "decimal") {
        $sql = "ALTER TABLE " . $tabla . " ADD " . $descripcion . " " . $tipoCampo . "(" . $size . ") NOT NULL ";
        xSQL($sql, $cnOld);
    }

    if ($tipoCampo == "text") {
        $sql = "ALTER TABLE " . $tabla . " ADD COLUMN  " . $descripcion . " " . $tipoCampo . "  CHARACTER SET utf8 NOT NULL";
        xSQL($sql, $cnOld);
    }

    if ($tipoCampo == "datetime" || $tipoCampo == "date" || $tipoCampo == "time") {
        $sql = "ALTER TABLE " . $tabla . " ADD " . $descripcion . " " . $tipoCampo . " NOT NULL ";
        xSQL($sql, $cnOld);
    }
    if ($tipoCampo == "double") {
        $sql = "ALTER TABLE " . $tabla . " ADD " . $descripcion . " " . $tipoCampo . " ";
        xSQL($sql, $cnOld);
    }
	
    p_gf("sysTabletDet", $cnOld, "");
	
	Msg("El proceso fue ejecutado correctamaente","C");	
    tablas("Editar");
	
}


function Busqueda($arg){
    global $vConex,$cnPDO,$enlace,$urlEmpresa,$idEmpresa,$UsuarioEntidad, $EntidadPersona,$Codigo_Entidad_Usuario,$enterprise_user,$FechaHora;

    switch ($arg) {
     
        case 'Busqueda':	
			
            $queryd = get("queryd");
			
            $sql = "SELECT
				Codigo AS CodigoAjax
				,Descripcion
				FROM sys_tabla ST
				WHERE Descripcion LIKE :Descripcion
				ORDER BY  FechaRegistro Desc "; 
            
			$where = [
			"Descripcion" =>'%'.$queryd.'%'
			];
			$html ="";	
			$countcolumn = OwlPDO::fetchAllArr($sql,$where,$cnPDO);
			$cont = 0;
			foreach ($countcolumn as $reg) {
				$con +=1;	
				// $viewdata = array();
				// $viewdata['Alias'] = $reg["Alias"];
				$html .= "<div id='p-".$con."' class=item onclick=buscadorAccionItem('p-".$con."'); >".$reg["Descripcion"]."</div>";
			}
            // vd($countcolumn);			
		    WE($html);
			
		break;
    }		
}

?>
