<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_sys_tablas.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/sys_form.php";
$enlacePopup = "sys_form.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['master_access'];
$administradorUser = $_SESSION['master_access'];
$_SESSION['menuVertical']= "1";



if (get('formulario') != '') {formulario(get('formulario'));}
if (get('Busqueda') != '') {Busqueda(get('Busqueda'));}



if (get("metodo") != "") {

    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario,$administradorUser;

        if (get("metodo") == "sysformdet2") {
            if ($campo == "Form") {
                $valor = "'" . get("codigo") . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = "'".$administradorUser."'";
            } elseif ($campo == "UsuarioActualizacion") {
                $valor = "'".$administradorUser."'";
            } elseif ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";
            } else {
                $valor = "";
            }
            return $valor;
        }

        if (get("metodo") == "SysFomr1") {

            if ($campo == "Descripcion") {
                $vcamp = post($campo);
                $valor = "'Form_" . $vcamp . "' ";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = "'".$administradorUser."'";
            } elseif ($campo == "UsuarioActualizacion") {
                $valor = "'".$administradorUser."'";
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


            if (get("metodo") == "sysformdet2") {
                p_gf("sysformdet2", $cnOld, get("codformdet"));

                #ADMIN ACCIONES
                $data = array(
                'Usuario' => $administradorUser,
                'Fecha' => $FechaHora,
                'Tabla' => 'sys_form_det',
                'CRUD' => 'UPDATE',
                'Descripcion' => 'Actualizacion del campo '.post("NombreCampo").' del formulario '.get("codigo").'.'
                );
                insert("sys_admin_acciones", $data);

				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);

                formulario('Editar');
            }

        }

        if (get("transaccion") == "INSERT") {

            if (get("metodo") == "sys_tabla1") {
                pro_systabla();
				W(" <script> \$popupEC.close();</script>");
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
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
            }

            if (get("metodo") == "SysFomr1") {
				pro_sysform2();
            }


            if (get("metodo") == "sysformdet2") {

                p_gf("sysformdet2", $cnOld, "");

                #ADMIN ACCIONES
                $data = array(
                'Usuario' => $administradorUser,
                'Fecha' => $FechaHora,
                'Tabla' => 'sys_form_det',
                'CRUD' => 'INSERT',
                'Descripcion' => 'Actualizacion del campo '.post("NombreCampo").' del formulario '.get("codigo").'.'
                );
                insert("sys_admin_acciones", $data);

				$msg =Msg("El proceso fue cerrado correctamente","C");

				W($msg);
                formulario('Editar');
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


function formulario($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup ,$Usuario;
	$codigoBd = $_SESSION['CodigoBD'];
	$vConex=$cnOld;
    switch ($Arg) {

        case 'Site':

            #autor: Daniel Centurion

			$pestanas = funcion_local_pestanas_form(array("&parm=new]Marca","")); #pestanaña principal
            $listMn = "<i class='icon-chevron-right'></i> Seleccionar Base de Datos[" .$enlace."?tablas=RDBaseDatos[panelOculto[[{";

		    if(!empty($codigoBd)){

				// $Query=" SELECT Nombre FROM sys_base_datos WHERE Codigo = :CodigoT ";
				// $rg = OwlPDO::fetchObj($Query, ["CodigoT" => $codigoBd] ,$cnPDO);
				// vd($rg);
				// $nombreBD = $rg->Nombre;
			    $string = " | Base se Datos: <b>".$codigoBd."</b>";
            }

            $btn = "<i class='icon-pencil'></i> Crear ]" .$enlace."?formulario=CreaFormularioRD]panelOculto]]}";
            $btn .= "<i class='icon-search'></i> Búsqueda ]" .$enlace."?formulario=BuscarFormRD]panelOculto]]}";
            $btn .= "<i class='icon-cog'></i>  ]SubMenu]{$listMn}]CONFIGURACIÓN]}";
            $btn = Botones($btn, 'botones1', 'sys_form');

			$titulo = "<p>FORMULARIO  </p><span>Administración y gestión de formularios ".$string." </span>";
            $btn_tituloP = panelST2017($titulo, $btn, "auto", "TituloAMSG");




            $listMn  = "<i class='icon-chevron-right'></i> Importar Formularios[{$enlacePopup}?formulario=ImportarFormularioRD[panelOculto[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Exportar Formularios[." .$enlace."?formulario=ExportarForm[panelB[CHECK[sys_tabla_form{";
            $listMn .= "<i class='icon-chevron-right'></i> Eliminar Formularios[." .$enlace."?formulario=EliminarFormularios[panelB[CHECK[sys_tabla_form{";

            $btn = "<i class='icon-reorder'></i> ]SubMenu]{$listMn}]Opciones múltiples]}";
            $btn = Botones($btn, 'botones1', 'sys_form');

            $titulo = "<p>LISTADO DE FORMULARIOS ".post("Nombre")."</p><span></span>";
            $btn_titulo = panelST2017($titulo,$btn, "auto", "TituloALMB");

            if(post("Nombre")!=="" ){ $nombre = post("Nombre"); $operadorEntidadPublica= "LIKE"; }else{ $nombre = "7777777777777"; $operadorEntidadPublica = "<>";  }

			$sql = "
				SELECT 
				Codigo AS Formulario 
				, Tabla 
				, DATE_FORMAT(FechaHoraCreacion, '%d %M %Y')  AS FechaCreacion
				, Codigo AS CodigoAjax 
				, CONCAT('
				<div  >
				<div class=botIcRepC ><i class=icon-chevron-down ></i> 
					 <ul class=sub_boton >
						<li onclick=enviaReg(''EDI',Codigo,''',''{$enlace}?formulario=EditarRD&codigo=',Codigo,''',''panelOculto'','''');  >Editar</li>
						<li onclick=enviaReg(''EDI',Codigo,''',''{$enlace}?formulario=Elimina&codigo=',Codigo,''',''panelOculto'','''');>Eliminar</li>
					 </ul>
				</div>
				</div>
				')AS 'Acción' 					
				FROM sys_form 
				WHERE Codigo ".$operadorEntidadPublica." :Codigo
				ORDER BY  FechaHoraCreacion DESC 
			";

            $clase = 'reporteA';
			$where = [
			"Codigo" => '%'.$nombre.'%'
			];

			$clase = 'class_reporte1';
			$panel = 'panelB';
            $url = "./_vistas/gpro_ficha.php?Ficha=Principal&Filtro=".$filtro."&indicador=".$indicador."";
			$enlaceCod = 'AlmacenCurso,AlmacenPrograma';
            $urlPaginacion = $enlace."?formulario=Site";

            $reporte = ListR2('', $sql, $where , $cnPDO, $clase, '', $url, $enlaceCod, $panel, 'sys_tabla_form','checks', '0,10','',$urlPaginacion);
//            $reporte = ListR2('', $sql, $where , $cnPDO, $clase, '', $url, $enlaceCod, $panel, 'sys_tabla_form', 'checks', '0,10','');

            $html .= "<div id = 'panelOculto'  style='float:left;width:100%;' ></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;' >" . $reporte . "</div>";
            $panel = array(array('PanelA1-A', '100%', $html));
            $Cuerpo = LayoutPage($panel);

            $s = layoutV2($btn_tituloP , $pestanas . $btn_titulo . $Cuerpo);

			WE(html($s));

            break;

        case 'ExportarForm':
            $campos = post("ky");

            if($campos){
                $columnas='';
                for ($j = 0; $j < count($campos); $j++) {
                    $columnas.=" ,'".$campos[$j]."' ";
                }
                $columnas=substr($columnas,2);

                $Q_fromulario="
					SELECT
					SF.Codigo  AS 'Formulario'
					,SF.Tabla
					,SFD.NombreCampo AS 'Campo'
					,SFD.Alias
					,SFD.TipoOuput
					,SFD.TipoInput
					,SFD.TamanoCampo
					,SFD.Visible
					,SFD.TablaReferencia
					,SFD.OpcionesValue
					,SFD.MaximoPeso
					,SFD.AliasB
					,SFD.CtdaCartCorrelativo
					,SFD.Validacion
					,SFD.InsertP
					,SFD.UpdateP
					,SFD.CadenaCorrelativo
					,SFD.CadenaCorrelativo
					,SFD.Form
					,SFD.Visible
					,SFD.Correlativo
					,SFD.AutoIncrementador
					,SFD.Posicion
					,SFD.read_only
					,SFD.TipoValor
					,SFD.PlaceHolder
					,SFD.Edicion
					,SFD.Event_hidden_field
					,SFD.destiny_upload
					,SFD.video_control
					,SFD.video_destiny_platform
					,SFD.UsuarioCreacion
					,SFD.UsuarioActualizacion
					,SFD.FechaHoraActualizacion
					,SFD.FechaHoraCreacion
					,SFD.Codigo AS CodigoAjax
					FROM sys_form_det  AS SFD
					INNER JOIN  sys_form AS SF ON SFD.Form=SF.Codigo
					WHERE SFD.Form IN ({$columnas})
					ORDER BY Posicion;  ";

                ExportExcelSimple($Q_fromulario,$vConex,'Reporte Formulario',$datos);
                $Mensaje = "<div style='display:block;font-family: Open Sans;background-color:#34a853;font-size: 1em;color:#FFFFFF; padding:4px;text-align:center;'>La exportación  del formulario fue realizada con ÉXITO..!!! </div>";
                W($Mensaje);


            }else{
                W(Msg("No seleccionó nigún formulario.","E"));
            }
            formulario('Site');
            break;
        case "ImportarFormularioRD":
//            $codigoSysTabla = get("codigo");
            $html = "	
						<script> openPopupURI('".$enlacePopup."?formulario=ImportarFormulario', {modal:true, closeContent:null}); </script>
					";
            WE($html);
            break;
        case "ImportarFormulario":

            $enlaceExterno = "https://owlgroup.s3.amazonaws.com/PlaintillasGlobal/plantilla_ficha_proyecto_v7.xlsx";
            $btn = "<i class='icon-download-alt'></i> Plantilla ]" .$enlaceExterno."]panelOculto]LINK]}";
            $btn = BotonesB($btn, 'botones1', 'sys_form');

            $titulo = "<p>CONFIGURACIÓN</p><span> Importación de la Ficha de Proyectos</span>";
            $cabecera = panelSTB($titulo, $btn, "auto", "TituloAMSG");
            $path = array('DescripcionExtendida' => "../../system/_export");
            $path2=$path['DescripcionExtendida'];

            //$uRLForm = "Procesar]" . $enlace . "?Importacion=ImportacionProceso&transaccion=UPDATE&{$parametros}]panel_form]F]}";
            $uRLForm = "<i class='icon-refresh'></i> Procesar Archivo]" . $enlace . "?formulario=ImportarF&Path={$path2}]panelB]F]}";

            $form = PrintForm('', $vConex, 'ImportarFormulario', '', $path, $uRLForm, '', '', '');
//            $form = c_form_adpb('', $vConex, 'ImportarFormulario', 'CuadroA',$path, $uRLForm, '', "",'');

            $form = "<div> <label>Archivo fuente </label> {$form}  <div class='titulo_boton_form'> <i class='fa fa-cloud-upload'> </i> Subir archivo</div> </div>";

            $panelGeneral = "<div id='panel_form'> {$form} </div>" ;
            $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
            $contenido = "<div class='sub_panelInterfas'>". $cabecera.$panelGeneral."</div>";
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
        case "ImportarF":
            $Archivo=post('DescripcionExtendida');
//            vd($_POST);we();
            if(empty($Archivo)){
                $Mensaje = "<div style='display:block;font-family: Open Sans;background-color:#f44336;font-size: 1em;color:#FFFFFF; padding:4px;text-align:center;'> No se ha encontrado ningún archivo Excel que procesar..!!! </div>";
                WE($Mensaje);
            }
            $Path=get('Path');
            $NombreArchivo=$Path."/{$Archivo}";
            $excel=LeerExcelImport($NombreArchivo);
            $VarJson = json_decode($excel);
            $name_temp = "";

            foreach($VarJson as $key ){
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                if($name_temp!= $key->Formulario){
                    // codigo consulta nombre tabla
                    $name_temp=$key->Formulario;

                    $Q_ExisteTable="SELECT  Codigo FROM  sys_tabla  WHERE Codigo='{$key->Tabla}' ";
                    $RgT=fetchOld($Q_ExisteTable);

                    if(!$RgT){// preguntar existencia tabla
                        // WE("TABLA NO EXISTE NO SE PUEDE IMPORTAR");
                        $Mensaje = "<div style='display:block;font-family: Open Sans;background-color:#f44336;font-size: 1em;color:#FFFFFF; padding:4px;text-align:center;'> La tabla especificada no existe, No se puede realizar la Importación.!!! </div>";
                        WE($Mensaje);
                    }else{
                        // consulta nombre del formulario
                        $Q_Existe="SELECT  Codigo FROM  sys_form  WHERE Codigo='{$key->Formulario}' ";
                        $RgF=fetchOld($Q_Existe);

                        if($RgF){// preguntar existencia formulario
                            // WE("FORMULARIO YA EXISTE  NO SE PUEDE IMPORTAR");
                            $Mensaje = "<div style='display:block;font-family: Open Sans;background-color:#f44336;font-size: 1em;color:#FFFFFF; padding:4px;text-align:center;'> El Formulario ya exíste, No se puede realizar la Importación..!!! </div>";
                            WE($Mensaje);
                        }else{
                            insert_row_form($key);
                        }
                    }

                }else{
                    insert_row_form($key);
                    $name_temp=$key->Formulario;
                }

            }
            // WE("IMPORTACION EXITOSA");
            $Mensaje = "<div style='display:block;font-family: Open Sans;background-color:#34a853;font-size: 1em;color:#FFFFFF; padding:4px;text-align:center;'>La Importación fue realizada con ÉXITO..!!! </div>";
            W($Mensaje);
            formulario('Site');

            break;
        case 'CreaFormularioRD':

            $codigoSysTabla = get("codigo");
			$html = "	
						<script> openPopupURI('".$enlacePopup."?formulario=CreaFormulario&codigo=".$codigoSysTabla."', {modal:true, closeContent:null}); </script>
					";
			WE($html);

            break;

        case 'CreaFormulario':


			$codigoSysTabla = get("codigo");

            $titulo = "<p>CREAR FORMULARIO ".$codigoSysTabla."</p><span>Estas trabajando en la BD '".$codigoBd."'</span>";
            $btn_titulo = panelST2017($titulo, "", "auto", "TituloALM");

			$titulo = "";
			$path = "";

            $uRLForm = "Guardar]" . $enlace . "?metodo=SysFomr1&transaccion=INSERT]panelB]F]}";
			$tSelectD = "";
			$form = c_form($titulo,$cnOld,"SysFomr1","CuadroA",$path,$uRLForm,'','');
	        $form = "<div style='float:left;width:96%;padding:2%' >" .$form . "</div>";
			
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
	        $html = "<div style='float:left;width:500px;' >" . $Close.$btn_titulo.$form . "</div>";
			WE($html);

            break;

        case 'EditarRD':
            $codigoSysTabla = get("codigo");
			$html = "	
						<script> openPopupURI('".$enlacePopup."?formulario=Editar&codigo=".$codigoSysTabla."', {modal:true, closeContent:null}); </script>
					";
			WE($html);

            break;

        case 'Editar':

			$codigoForm = get("codigo");

			$listMn = "<i class='icon-chevron-right'></i> Añadir Campo [" .$enlace."?formulario=AnadirCampo&codigo=".$codigoForm."[panel_popup[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Editar Campos[." .$enlace."?formulario=EditarCampo&codigo=".$codigoForm."[panel_popup[CHECK[sys_tabla_det_form{";
            $listMn .= "<i class='icon-chevron-right'></i> Eliminar Campos[." .$enlace."?formulario=EliminarCampos&codigo=".$codigoForm."[panel_popup[CHECK[sys_tabla_det_form{";
            $listMn .= "<i class='icon-chevron-right'></i> Eliminar Formulario [" .$enlace."?formulario=EliminarTablaONE&codigo=".$codigoForm."[panelB[[{";

            $btn = "<i class='icon-reorder'></i> ]SubMenu]{$listMn}]Opciones]}";
            $btn = Botones($btn, 'botones1', 'sys_form');

            $titulo = "<p>ADMINISTRAR FORMULARIO: ".$codigoForm."</p><span></span>";
            $btn_titulo = panelST2017($titulo,$btn, "auto", "TituloALM");

			$sql = 'SELECT Codigo AS CodigoAjax, NombreCampo, Alias,TipoInput, TipoOuput, Visible, TamanoCampo AS "Size", Posicion FROM sys_form_det ';
			$sql .= ' WHERE Form = :Codigo ORDER BY Posicion ASC';
            $clase = 'reporteA';
			$where = ["Codigo"=>$codigoForm];

            $url = $enlace."?formulario=EditarCampo&codigo=".$codigoForm."";
            $panel = 'panel_popup';
			$enlaceCod = "CodigoSD";
			
            $reporte = ListR2('', $sql, $where , $cnPDO, $clase, '', $url, $enlaceCod, $panel, 'sys_tabla_det_form', 'checks', '','');

			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
	        $reporte = "<div class='panelReport' >" . $reporte . "</div>";
	        $html = "<div style='float:left;width:900px;' id='panel_popup'>" . $Close.$btn_titulo .$reporte . "</div>";

			WE($html);

            break;

        case 'EditarCampo':


			$CodDet = get("CodigoSD");
			$codigoForm = get("codigo");
			$ky=$_POST["ky"][0];

			$Query=" SELECT Form,NombreCampo ,Codigo AS CodigoAjax FROM sys_form_det
             			WHERE Codigo = :CodigoT ";
			$rg = OwlPDO::fetchObj($Query, ["CodigoT" => $CodDet] ,$cnPDO);
			$sForm = $rg->Form;
			$Descripcion = $rg->NombreCampo;

            $btn = "<i class='icon-arrow-left'></i>  ]" .$enlace."?formulario=Editar&codigo=".$codigoForm."]panel_popup]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');

            $titulo = "<span>ACTUALIZAR EL CAMPO: " . $Descripcion . "</span><p>Tabla: " . $codigoForm . "</p>";
            $btn_titulo = panelST2017($titulo,$btn, "auto", "TituloALM");


			$sql = 'SELECT Form FROM sys_form_det WHERE  Codigo = "' . $CodDet . '" ';
			$rg = fetchOld($sql);
			$form = $rg["Form"];

			$sql = 'SELECT Tabla FROM sys_form WHERE  Codigo = "' . $form . '" ';
			$rg = fetchOld($sql);
			$tabla = $rg["Tabla"];

			$uRLForm = "Actualizar]" . $enlace . "?metodo=sysformdet2&transaccion=UPDATE&codformdet=" . $CodDet . "&codigoForm=" . $form . "&CodigoSD=".$CodDet."&codigo=".$codigoForm."]panel_popup]F]}";

			$tSelectD = array('NombreCampo' => 'SELECT Descripcion as Cod,Descripcion FROM sys_tabla_det WHERE sys_tabla = "' . $tabla . '" ');

			$form = c_form("", $cnOld, "sysformdet2", "CuadroA", "", $uRLForm, $CodDet, $tSelectD);

			$form = "<div class='panelForm' style='padding:2%;width:96%;'>".$form."</div>";

			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
	        $html = "<div style='float:left;width:900px;' id='panel_popup'>" . $Close.$btn_titulo. $form . "</div>";
			WE($html);

            break;


        case 'AnadirCampo':


			$CodDet = get("CodigoSD");
			$codigoForm = get("codigo");


            $btn = "<i class='icon-arrow-left'></i>  ]" .$enlace."?formulario=Editar&codigo=".$codigoForm."]panel_popup]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');

            $titulo = "<span>AÑADIR CAMPO </span><p> FORMULARIO: " . $codigoForm . "</p>";
            $btn_titulo = panelST2017($titulo,$btn, "auto", "TituloALM");

			$sql = 'SELECT Tabla FROM sys_form WHERE  Codigo = "' . $codigoForm . '" ';
			$rg = fetchOld($sql);
			$tabla = $rg["Tabla"];

			$uRLForm = "Añadir]" . $enlace . "?metodo=sysformdet2&transaccion=INSERT&codigo=".$codigoForm."]panel_popup]F]}";

			$tSelectD = array('NombreCampo' => 'SELECT Descripcion as Cod,Descripcion FROM sys_tabla_det WHERE sys_tabla = "' . $tabla . '" ');

			$form = c_form("", $cnOld, "sysformdet2", "CuadroA", "", $uRLForm, $CodDet, $tSelectD);

			$form = "<div class='panelForm' style='padding:2%;width:96%;'>".$form."</div>";

			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
	        $html = "<div style='float:left;width:900px;' id='panel_popup'>" . $Close.$btn_titulo. $form . "</div>";
			WE($html);

            break;

        case 'EliminarCampos':

            $ky = post("ky");
            $tabla = get("codigo");

		    foreach($ky as $selected) {
				// W("selected: ".$selected."<BR>");

				$sql = "SELECT  NombreCampo FROM sys_form_det WHERE  Codigo =:CodigoT ";
				$rg = OwlPDO::fetchObj($sql, ["CodigoT" => $selected] ,$cnPDO);
				$nombre_campT = $rg->NombreCampo;

				$where = array('Codigo' => $selected );
				$rg = OwlPDO::delet('sys_form_det', $where, $cnPDO);

			}

			closePopup();
			Msg("El proceso fue ejecutado correctamaente","C");
		    formulario("Editar");

		    WE("");
		    break;

        case 'EliminarTablaONE':

            $formCod = get("codigo");

				$where = array('Codigo' => $formCod );
				$rg = OwlPDO::delet('sys_form', $where, $cnPDO);

				$where = array('Form' => $formCod );
				$rg = OwlPDO::delet('sys_form_det', $where, $cnPDO);


			Msg("El proceso fue ejecutado correctamaente","C");

			W(" <script> \$popupEC.close();</script>");
			formulario("Site");
			WE("");

            break;

        case 'BuscarFormRD':
            $codigoSysTabla = get("codigo");
			$html = "	
						<script> openPopupURI('".$enlacePopup."?formulario=BuscarForm&codigo=".$codigoSysTabla."', {modal:true, closeContent:null}); </script>
					";
			WE($html);

            break;
        case 'BuscarForm':


			$codigoSysTabla = get("codigo");

            $titulo = "<p>Buscar Formulario </p><span></span>";
            $btn_titulo = panelST2017($titulo, "", "auto", "TituloALM");

			$titulo = "";
			$path = "";

            $uRLForm = "Buscar]" . $enlace . "?formulario=Site]panelB]F]}";
			$tSelectD = "";
			$form = c_form_adp($titulo, $cnPDO, "FormBusquedaA", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');

			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
	        $html = "<div style='float:left;width:500px;' >" . $Close.$btn_titulo.$form . "</div>";
			WE($html);

            break;

			case 'EliminarFormularios':

            $ky = post("ky");
		    foreach($ky as $selected) {

				$where = array('Codigo' => $selected );
				$rg = OwlPDO::delet('sys_form', $where, $cnPDO);

				$where = array('Form' => $selected );
				$rg = OwlPDO::delet('sys_form_det', $where, $cnPDO);

			}


			closePopup();

			Msg("El proceso fue ejecutado correctamaente","C");
			tablas("Site");

			WE("");

            break;


    }
}


function pro_sysform2() {
    global $cnOld, $administradorUser, $FechaHora;

    $sql = 'SELECT Codigo,Descripcion FROM sys_tabla WHERE  Codigo = "' . post("Tabla") . '" ';
    $rg = fetchOld($sql);
    $codigo = $rg["Codigo"];

    if ($codigo != "") {

        // WE(post("Tabla"));
        $vSQL = 'SELECT Codigo,Descripcion,TipoCampo,sys_tabla  FROM  sys_tabla_det WHERE  sys_tabla = "' . post("Tabla") . '" ';
        $consulta = mysql_query($vSQL, $cnOld);

        while ($r = mysql_fetch_array($consulta)) {
			$cod_sys_form_det = numerador("sys_form_det", 5, "");
			$sql = 'INSERT  INTO sys_form_det (Codigo,NombreCampo,Alias,TipoInput,TipoOuput,Form,Visible,TamanoCampo,UsuarioCreacion,UsuarioActualizacion,FechaHoraCreacion,FechaHoraActualizacion)
			VALUES ("' . $cod_sys_form_det . '","' . $r["Descripcion"] . '","' . $r["Descripcion"] . '","' . cmn($r["TipoCampo"]) . '","text","' . post("Codigo") . '","SI",130,"' . $administradorUser . '","' . $administradorUser . '","' . $FechaHora . '","' . $FechaHora . '")';
			xSQL($sql, $cnOld);
        }

        p_gf("SysFomr1", $cnOld, "");

        #ADMIN ACCIONES
        $data = array(
        'Usuario' => $administradorUser,
        'Fecha' => $FechaHora,
        'Tabla' => 'sys_form_det',
        'CRUD' => 'INSERT',
        'Descripcion' => 'Creación del formulario ' . post("Codigo") . ' de la tabla ' . post("Tabla") . '.'
        );
        insert("sys_admin_acciones", $data);

		W(" <script> \$popupEC.close();</script>");
		$msg =Msg("El proceso fue cerrado correctamente","C");
		W($msg);

        W(formulario("Site"));



    } else {

        WE("La Tabla No existe" . post("Tabla"));
    }
}

function insert_row_form($key){
    global $cnOld,$enlace, $enlacePopup,$FechaHora;
    $vConex=conexSys();
    $FormularioName=$key->Formulario;
    $TablaName=$key->Tabla;

    $Campo=$key->Campo;
    $Alias=$key->Alias;
    $TipoOuput=$key->TipoOuput;
    $TipoInput=$key->TipoInput;
    $Visible=$key->Visible;
    $Correlativo=$key->Correlativo;
    $AutoIncrementador=$key->AutoIncrementador;
    $Posicion=$key->Posicion;
    $TamanoCampo=$key->TamanoCampo;
    $Form=$key->Form;
    $OpcionesValue=$key->OpcionesValue;
    $TablaReferencia=$key->TablaReferencia;
    $MaximoPeso=$key->MaximoPeso;
    $AliasB=$key->AliasB;
    $CtdaCartCorrelativo=$key->CtdaCartCorrelativo;
    $CadenaCorrelativo=$key->CadenaCorrelativo;
    $InsertP=$key->InsertP;
    $UpdateP=$key->UpdateP;
    $Validacion=$key->Validacion;
    $read_only=$key->read_only;
    $TipoValor=$key->TipoValor;
    $PlaceHolder=$key->PlaceHolder;
    $Edicion=$key->Edicion;
    $Event_hidden_field=$key->Event_hidden_field;
    $destiny_upload=$key->destiny_upload;
    $video_control=$key->video_control;
    $video_destiny_platform=$key->video_destiny_platform;
    $UsuarioCreacion=$key->UsuarioCreacion;
    $UsuarioActualizacion=$key->UsuarioActualizacion;
    $FechaHoraActualizacion=$key->FechaHoraActualizacion;
    $FechaHoraCreacion=$key->FechaHoraCreacion;

    $CodigoAjax=$key->CodigoAjax;

    $Q_Existe="SELECT  Codigo FROM  sys_form  WHERE Codigo='{$FormularioName}' ";
    $Rg=fetchOld($Q_Existe);
    $exist=$Rg['Codigo'];

    if($exist<>$FormularioName){

        $Q_Formulario1=" INSERT  INTO sys_form 
				SET Codigo='{$FormularioName}' 
				, Descripcion='Form_{$FormularioName}'
				, DescripcionExtendida= '{$FormularioName}' 
				, Tabla= '{$TablaName}'
				, Estado= 'Activo'
				, FechaHoraCreacion='{$FechaHora}'
				, UsuarioCreacion= '{$UsuarioCreacion}'
				, UsuarioActualizacion='{$UsuarioActualizacion}'
				 
			";


        xSQL($Q_Formulario1, $vConex);

    }
    $cod_sys_form_det = numerador("sys_form_det", 5, "");
    /// INSERTAR EL DETALLE DEL FORMULARIO
    $Q_DetalleFormulario=" INSERT INTO  sys_form_det 
						SET
						Codigo={$cod_sys_form_det}
						,NombreCampo='{$Campo}' 
						,Alias= '{$Alias}'
						,TipoInput= '{$TipoInput}'
						,TipoOuput= '{$TipoOuput}'
						,TamanoCampo={$TamanoCampo}
						,Form= '{$FormularioName}'
						,Visible= '{$Visible}'
						,TablaReferencia=  '{$TablaReferencia}'
						,OpcionesValue=  '{$OpcionesValue}'
						,MaximoPeso=  '{$MaximoPeso}'
						,AliasB=  '{$AliasB}'
						,CtdaCartCorrelativo=  '{$CtdaCartCorrelativo}'
						,CadenaCorrelativo=  '{$CadenaCorrelativo}'
						,Validacion=  '{$Validacion}'
						,InsertP=  '{$InsertP}'
						,UpdateP=  '{$UpdateP}'
						,Correlativo='{$Correlativo}'
						,Posicion= '{$Posicion}' 
						,AutoIncrementador= '{$AutoIncrementador}'
						,read_only= '{$read_only}'
						,TipoValor= '{$TipoValor}'
						,PlaceHolder='{$PlaceHolder}'
						,Edicion= '{$Edicion}'
						,Event_hidden_field='{$Event_hidden_field}'
						,destiny_upload= '{$destiny_upload}'
						,video_control='{$video_control}'
						,video_destiny_platform='{$video_destiny_platform}'
						,FechaHoraCreacion='{$FechaHora}'

				                 ";

    xSQL($Q_DetalleFormulario, $vConex);

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
