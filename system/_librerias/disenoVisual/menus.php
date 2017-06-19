<?php

// session_start();
require_once('_librerias/php/funciones.php');
require_once('_librerias/php/conexiones.php');

$IPArchivos = CONS_IPArchivos;
$num_rand = rand() * 1000;
$vConex = conexSys();

function menuSiteEmpresa() {
    global $num_rand;
    $html = "<!DOCTYPE html>
            <html lang='es'>
            <head>
                <title>Plataforma</title>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0'>
                <meta name='description' content=''>
                <meta name='keywords' content=''>
                <meta name='author' content=''>
                <script type='text/javascript' src='_librerias/js/global.js'></script>
                <script type='text/javascript' src='_librerias/js/ajaxglobal.js'></script>
                <link href='./_estilos/OwlStyles.css?vr=" . $num_rand . "' rel='stylesheet' type='text/css' />
            </head>
            <body>
            <div class='site'>
            <div id='menu' class='mHSinSubElementosA001 tamano'>";

    $sClassA = 'font-weight:bold;font-size:2.2em;line-height:40px;';
    $sClassB = 'font-weight:lighter;font-size:0.75em;margin:8px 0px 0px 2px;';

    $sUrlPanelesA = 'PanelA[PanelA[./_vistas/carrusel.html?vista=PanelA[1000|';
    $sUrlPanelesA = $sUrlPanelesA . 'PanelB[PanelB[./_vistas/site.php?vista=PanelB[2000|';
    // $sUrlPanelesA = $sUrlPanelesA.'PanelC[PanelC[./vistas/site.php?vista=PanelC[4000|';

    $sBotMatris = "<div style='{$sClassA}'>Plataforma </div><div style='{$sClassB}'>HERRAMIENTAS</div>]{$sUrlPanelesA}]cuerpo]RZ}";
    $sBotMatris .= "Home]index.php?TipoConsulta=juan&usuario=daniel&estado=Abierto]cuerpo]C}";
    $sBotMatris .= "Login]index.php?TipoConsulta=Felipe&usuario=daniel&estado=Abierto]cuerpo]C}";
    $sBotMatris .= "Form]./_vistas/form.php?vista=Felipe]cuerpo]C}";
    $sBotMatris .= "Form Inpt]./_vistas/formAj.html?vista=Felipe]cuerpo]C}";
    $sBotMatris .= "Listado]./_vistas/listadoReporte.php?vista=Felipe]cuerpo]C}";
    $sBotMatris .= "Check]./_vistas/listadoReporte2.php?vista=Felipe]cuerpo]C}";

    $sTipoAjax = "true";
    $sClase = "menuHorz001";
    $sBot = Boton001($sBotMatris, $sClase, $sTipoAjax);
    $html = $html . $sBot . ' </div>';
    return $html;
}

function menuPanelControlEmpresa($entidadEmpresa) {
    global $vConex, $num_rand, $IPArchivos;

    $Q_ = " SELECT USU.UrlId, 
            USU.Carpeta, 
            EMP.Logo, 
            EMP.NombreEmpresa, 
            TEM.Archivo AS Color,
            USU.IdUsuario
            FROM usuarios USU
            INNER JOIN empresa EMP ON USU.IdUsuario = EMP.PaginaWeb 
            INNER JOIN temasgraf TEM ON EMP.IdTemaGraf = TEM.IdTemasGraf
            WHERE USU.IdUsuario = '{$entidadEmpresa}'";
    $Obj = fetchOne($Q_, $vConex);

    $color = $Obj->Color;
		$Logo = (string) $Obj->Logo;
		$Carpeta = $Obj->Carpeta;
		$IdUsuario = $Obj->IdUsuario;
		$UrlId = $Obj->UrlId;
		$NombreEmpresa = $Obj->NombreEmpresa;

		$sqlUsuario="SELECT IdUsuario FROM usuarios WHERE UrlId='{$NombreEmpresa}'  ";
		$rg2 = fetch($sqlUsuario);
		$IdUsuario= $rg2["IdUsuario"];
		
		$SqlTP= "  SELECT  TipoProyecto  FROM empresa  WHERE  PaginaWeb='{$IdUsuario}'   ";
		$rg2 = fetch($SqlTP);
		$TipoProyecto= $rg2["TipoProyecto"];
	
	
    if (!$Logo) {
        $src_img_logo = "/system/_imagenes/icon_user.png";
    } else {
        $src_img_logo = "{$IPArchivos}/ArchivosEmpresa/{$Carpeta}/{$Logo}";
    }

    	//$DominioAppIni = SesionVL('DominioAppIni');

    	$DominioAppIni = $_SERVER['HTTP_HOST'];
    
    $rand = "?p" . rand(0, 1); 

    $html = "
    <!DOCTYPE html> 
    <html lang='es'>
    <head>
        <title>Plataforma</title>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0'>
        <meta name='description' content=''>
        <meta name='keywords' content=''>
        <meta name='author' content=''>
        <script type='text/javascript' src='_librerias/js/amcharts/amcharts.js'></script>
        <script type='text/javascript' src='_librerias/js/amcharts/serial.js'></script>
        <script type='text/javascript' src='_librerias/js/amcharts/pie.js'></script>
        <script type='text/javascript' src='_librerias/js/global.js{$rand}'></script>
        <script type='text/javascript' src='_librerias/js/ajaxglobal.js{$rand}'></script>
        <script type='text/javascript' src='/system/_librerias/js/jquery-2.1.1.min.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/lib/moment.min.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/jquery-ui.min.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/fullcalendar/fullcalendar.min.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/fullcalendar/es.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/owlchat/zilli.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/owlchat/AjaxZilli.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/popup/jquery.popup.js'></script>        
        <script type='text/javascript' src='/system/_librerias/js/simply-toast.js'></script>        
        <script type='text/javascript' src='/system/_librerias/js/sweetalert2.min.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/jquery.qtip.js'></script>        
        <script type='text/javascript' src='/system/_librerias/js/jquery.dataTables.1.9.0.js'></script>          
        <script type='text/javascript' src='/system/_librerias/js/task.js'></script>
        <script type='text/javascript' src='https://chat.owlgroup.org/socket.io/socket.io.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/temporary.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/print.js'></script>

        <link href='./_estilos/OwlStyles.css?vr={$num_rand}' rel='stylesheet' type='text/css' />
        <link href='./_estilos/owl_room_responsive.css?vr={$num_rand}' rel='stylesheet' type='text/css' />
        <link href='/system/_estilos/popup/popup.css' rel='stylesheet' type='text/css'/>
        <link href='/system/_estilos/temporary.css' rel='stylesheet' type='text/css'/>
        <link href='/system/_estilos/simply-toast.css' rel='stylesheet' type='text/css'/>
        <link href='/system/_estilos/sweetalert2.css' rel='stylesheet' type='text/css'/>
        <link href='/system/_estilos/jquery.qtip.css' rel='stylesheet' type='text/css'/>
        <link href='/system/_estilos/owl_desktop.php' rel='stylesheet' type='text/css'/>
        <link href='/system/_estilos/fullcalendar/fullcalendar.css?vr={$num_rand}' rel='stylesheet'/>
        <link href='/system/_estilos/fullcalendar/fullcalendar.print.css?vr={$num_rand}' rel='stylesheet' media='print'/>
    </head>
    <body>
        <script>

            var UserEmail = new _AppUserEmail();

             function tabla(){
                var nro = document.getElementById('cursos-alumnos-T').rows.length
                console.log(nro);
                var table = document.getElementById('cursos-alumnos-T');                
                var row = table.rows[1];
                console.log(row);    
                console.log(row.id);    
            }

            function addUser(){
                UserEmail.user = [];
                var nro = document.getElementById('cursos-alumnos-T').rows.length
                var table = document.getElementById('cursos-alumnos-T');                                
                for(var i=1;i<nro;i++){
                    var row = table.rows[i];
                    UserEmail.addUser(row.id);    
                }                            
            }

            function viewbox(id){
                document.getElementById(id).innerHTML = UserEmail.viewUser();
            }

            function deleteuser(id,box){
                UserEmail.deleteUser(id);
                document.getElementById(box).innerHTML = UserEmail.viewUser();
            }
        
        </script>
        <div class='menuCabezera'>
            <div class='barra_menu_001' style='background-color:{$color}'>
                <div class='barra_menu_001 atb_contenido_position_001'>

                    <div class='atb_contenido_elementos_002'> 
                        <div id='dl-menu' class='dl-menuwrapper'>
                            <div class='icon_config'></div>
                            <button id='Configuracion' style='padding: 0 3em;'>Configurar</button>
                            <ul class='dl-menu-l'>
                                <li>
                                    <div class='PanelUsuarioA' style='border-color:{$color}'>
                                        <div class='PanelInt_Menu_a'>
                                            <img src='{$src_img_logo}' width='80'>
                                        </div>
                                        <div class='PanelInt_Menu_b'>";

			                if($TipoProyecto==1 || $TipoProyecto=='' ){
                                               $html.=" <a href='/system/PanelControlEmpresa.php'>Gestión</a>
                                                        <a href='/system/desktop.php'> Aula </a>";
                            }else{
                                               $html.=" <a href='/system/PanelControlProyecto.php'>Gestión</a>
                                                        <a href='/system/desktopProyectos.php'>Modo Participante</a>";
			                }
				                            
	                            									
											
                             	  $html .= "	 <a href='/system/cierra_session_empresa.php?CierraSesion=Cierra'>Cerrar Sesión</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class='atb_contenido_elementos_002'>
                        <div class='icon_site'></div>
                        <a  href='http://{$DominioAppIni}/{$UrlId}' target='_blank' style='padding: 5px 2em;'>" . substr($NombreEmpresa, 0, 22) . "</a>
                    </div>

                    <div class='atb_contenido_elementos_002' onclick=AddEventCollapseDiv(this,'/system/_vistas/gad_alertas_cursos.php?AlertasSoporte=SoporteListAdmin','PanelMensajeAlerta');>
                        <a title='Soporte'><i class='icon-user-md'></i></a>
                        <div class='indicator' id='support-indicator'></div>
                        <div id='PanelMensajeAlerta' style='position:absolute;top:1.5em;font-size:1.2em;'></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            addTimerEvent('support-indicator', '/system/_vistas/gad_alertas_cursos.php?AlertasSoporte=SoporteIndicatorAdmin');
        </script>
        ";
        
    return $html;
}

function Menu_Escritorio($idEmpresa, $UsuarioEntidad) {
    global $vConex, $num_rand, $IPArchivos;

    $Q_ = " SELECT 
            TEM.Archivo AS Color
            FROM usuarios USU
            INNER JOIN empresa EMP ON EMP.PaginaWeb = USU.IdUsuario
            INNER JOIN temasgraf TEM ON EMP.IdTemaGraf = TEM.IdTemasGraf
            WHERE USU.IdUsuario = '{$idEmpresa}'";

    $Obj = fetchOne($Q_, $vConex);
    $color = (string) $Obj->Color;

    $Q_Usuario = "SELECT 
        ALU.Nombres, 
        ALU.ApellidosPat, 
        ALU.ApellidoMat, 
        ALU.Image, 
        USU.Carpeta,
        USU.UrlId 
        FROM usuarios USU
        INNER JOIN alumnos ALU ON USU.IdUsuario = ALU.Usuario 
        WHERE USU.IdUsuario = '{$UsuarioEntidad}'";
    $Usuario = fetchOne($Q_Usuario, $vConex);

    $idValida = $Usuario->Carpeta;
    $UrlId = $Usuario->UrlId;
    $Nombres = $Usuario->Nombres;
    $ApellidosPat = $Usuario->ApellidosPat;
    $Image = $Usuario->Image;

    if (!$Image) {
        $src_img = "/system/_imagenes/icon_user.png";
    } else {
        $src_img = "{$IPArchivos}/ArchivosAlumnos/{$idValida}/{$Image}";
    }

    $valor = "
    <!DOCTYPE html> 
    <html lang='es'>
        <head>
            <title>Plataforma</title>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0'>
            <meta name='description' content=''>
            <meta name='keywords' content=''>
            <meta name='author' content=''>
            <script type='text/javascript' src='http://group.owlcrm.info/socket.io/socket.io.js'></script>
            <script type='text/javascript' src='http://underscorejs.org/underscore-min.js'></script>
            <script type='text/javascript' src='_librerias/js/calendar.js'></script>
            <script type='text/javascript' src='_librerias/js/calendar-es.js'></script>
            <script type='text/javascript' src='_librerias/js/calendar-setup.js'></script>
            <link href='./_estilos/calendario.css' rel='stylesheet' type='text/css' />
            <script type='text/javascript' src='_librerias/js/global.js'></script>
            <script type='text/javascript' src='_librerias/js/ajaxglobal.js'></script>
            <link href='./_estilos/OwlStyles.css?vr={$num_rand}' rel='stylesheet' type='text/css'/>
        </head>
        <body>
            <div class='fondoTransparente' id='fondoTransparente' style='display:none'></div>
                <div class='menuCabezera'>
                    <div class='barra_menu_001' style='background-color:{$color}'>
                        <div class='barra_menu_001 atb_contenido_position_001'>
                            <div class='atb_contenido_elementos_002'> 
                                <div id='dl-menu' class='dl-menuwrapper'>
                                    <button id='Configuracion'><div class='Ocultar1'>Configurar</div><div class=botIconF2B ><i class=icon-cog ></i></div></button>
                                    <ul class='dl-menu-l'>
                                        <li>
                                            <div class='PanelUsuarioA' style='border-color:{$color}'>
                                                <div class='PanelInt_Menu_a'>
                                                    <img src='{$src_img}' width='80'>
                                                </div>
                                                <div class='PanelInt_Menu_b'>
                                                    <a href='#' onclick=enviaVista('./_vistas/gad_usuario_config.php?Persona=Listado','PanelA',''); >Modificar Perfil</a>
                                                    <a href='/system/cierra_session.php?CierraSesion=Cierra'>Cerrar Sesión</a>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div> 
                            <div class='atb_contenido_elementos_002'>
                                <a href='/system/tutoriales.php'><div class='Ocultar1' >Ayuda</div><div class='botIconF2B'><i class='icon-question-sign'></i></div></a>
                            </div> 
                            <div class='atb_contenido_elementos_002'>
                                <a href='/system/Comunidad.php'><div class='Ocultar1' >Comunidad</div><div class='botIconF2B'><i class='icon-group'></i></div></a>
                            </div> 
                            <div class='atb_contenido_elementos_002'  style='Position:relative;'>
                                <a href='/system/miscursos.php?idUsuario={$UrlId}' >
                                    <div class='Ocultar1'>Escritorio</div>
                                    <div class='botIconF2B'><i class='icon-th'></i></div>
                                </a>
                                <div style='padding:8px 0px 0px 0px;float:left;'>
                                    <div class='botIconF2BO' id='IconoAlerta' style='position:relative;' onclick=AddEventCollapseDiv(this,'/system/_vistas/gad_alertas_cursos.php?AlertasCursos=Listado','PanelMensajeAlerta');>
                                        <div id='PanelMensajeAlerta'></div>
                                        <div class='F2BAlerta' id='F2BAlerta' style='display:none;'>0</div>
                                        <i class='icon-comments'></i>
                                        <div class='ha_indicator' id='ha_ind_count_ha'></div>
                                    </div>
                                </div>
                            </div>
                            <div class='atb_contenido_elementos_002'>
                                <a href='#' target='_blank' ><div class='Ocultar1'>" . substr($Nombres . " " . $ApellidosPat, 0, 22) . "</div>
                                    <div class=botIconF2B ><i class='icon-user'></i></div>
                                </a>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
            <script>
                addTimerEvent('ha_ind_count_ha','/system/_vistas/gad_alertas_cursos.php?AlertasCursosTotal=AlertasCursosTotal');
            </script>";
                
    return $valor;
}

function Menu_Escritorio_tutoriales($idEmpresa) {
    global $vConex, $num_rand;

    $Q_ = " SELECT 
            TEM.Archivo AS Color
            FROM usuarios USU
            INNER JOIN empresa EMP ON EMP.PaginaWeb = USU.IdUsuario
            INNER JOIN temasgraf TEM ON EMP.IdTemaGraf = TEM.IdTemasGraf
            WHERE USU.IdUsuario = '{$idEmpresa}'";

    $Obj = fetchOne($Q_, $vConex);
    $color = (string) $Obj->Color;
    
    $html = "
        <!DOCTYPE html> 
        <html lang='es'>
        <head>
            <title>Plataforma</title>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0'>
            <meta name='description' content=''>
            <meta name='keywords' content=''>
            <meta name='author' content=''>
            <script type='text/javascript' src='http://group.owlcrm.info/socket.io/socket.io.js'></script>
            <script type='text/javascript' src='http://underscorejs.org/underscore-min.js'></script>
            <script type='text/javascript' src='_librerias/js/calendar.js'></script>
            <script type='text/javascript' src='_librerias/js/calendar-es.js'></script>
            <script type='text/javascript' src='_librerias/js/calendar-setup.js'></script>
            <link href='./_estilos/calendario.css' rel='stylesheet' type='text/css' />
            <script type='text/javascript' src='_librerias/js/global.js'></script>
            <script type='text/javascript' src='_librerias/js/ajaxglobal.js'></script>
            <link href='./_estilos/OwlStyles.css?vr={$num_rand}' rel='stylesheet' type='text/css' />
        </head>
        <body>
            <div class='fondoTransparente' id='fondoTransparente' style='display:none'></div>
            <div class='menuCabezera'>
                <div class='barra_menu_001' style='background-color:{$color}'>
                    <div class='barra_menu_001 atb_contenido_position_001'>
                        <div class='atb_contenido_elementos_002'>
                            <a href='#'><div class='Ocultar1' >PLATAFORMA - TUTORIALES </div><div class=botIconF2B ><i class=icon-list-alt ></i></div></a>
                        </div> 
                    </div>
                </div>
            </div>";

    return $html;
}

function menuAdmin() {
    global $num_rand;
    
    $html = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <title>Plataforma</title>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0'>
            <meta name='description'>
            <meta name='keywords'>
            <meta name='author' content=''>
            <script type='text/javascript' src='_librerias/js/global.js'></script>
            <script type='text/javascript' src='_librerias/js/ajaxglobal.js'></script>
            <link href='./_estilos/OwlStyles.css?vr={$num_rand}' rel='stylesheet' type='text/css'/>
        </head>
        <body>
            <div class='site'>
            <div id='menu' class='mHSinSubElementosA001 tamano'>";
    
    $sUrlPanelesA = "PanelA[PanelA[./_vistas/carrusel.html?vista=PanelA[1000|";
    $sUrlPanelesA .= "PanelB[PanelB[./_vistas/site.php?vista=PanelB[2000|";
    
    $sClassA = "font-weight:bold;font-size:2.2em;line-height:40px;";
    $sClassB = "font-weight:lighter;font-size:0.75em;margin:8px 0px 0px 2px;";
    
    $sBotMatris = "<div style='{$sClassA}'>FRI</div><div style='{$sClassB}'>GESTIÓN</div>]{$sUrlPanelesA}]cuerpo]RZ}";
    $sBotMatris .= "Site]index.php?TipoConsulta=juan&usuario=daniel&estado=Abierto]cuerpo]C}";
    $sBotMatris .= "Gestión]index.php?TipoConsulta=Felipe&usuario=daniel&estado=Abierto]cuerpo]C}";
    $sBotMatris .= "Configuración]./_vistas/form.php?vista=Felipe]cuerpo]C}";
    
    $sTipoAjax = "true";
    $sClase = "menuHorz001";
    $sBot = Boton001($sBotMatris, $sClase, $sTipoAjax);
    
    $html = "{$html}{$sBot}</div>";
    
    return $html;
}

function menuMaster() {
    global $num_rand,$vConex;

    $html = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <title>Plataforma</title>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0'>
            <meta name='description'>
            <meta name='keywords'>
            <meta name='author' content=''>
            <script type='text/javascript' src='_librerias/js/global.js'></script>
            <script type='text/javascript' src='_librerias/js/ajaxglobal.js'></script>
            <script type='text/javascript' src='/system/_librerias/js/jquery-2.1.1.min.js'></script>
            <script type='text/javascript' src='/system/_librerias/js/jquery-ui.min.js'></script>
            <script type='text/javascript' src='/system/_librerias/js/owlchat/zilli.js'></script>
            <script type='text/javascript' src='/system/_librerias/js/owlchat/AjaxZilli.js'></script>
            <script type='text/javascript' src='/system/_librerias/js/popup/jquery.popup.js'></script>

            <link href='./_estilos/OwlStyles.css' rel='stylesheet' type='text/css'/>
            <link href='./_estilos/popup/popup.css' rel='stylesheet' type='text/css'/>
        </head>
        <body>
            <div class='site'>
            <div id='menu' class='mHSinSubElementosA001 tamano'>";

    $sUrlPanelesA = "PanelA[PanelA[./_vistas/carrusel.html?vista=PanelA[1000|";
    $sUrlPanelesA .= "PanelB[PanelB[./_vistas/site.php?vista=PanelB[2000|";

    $sClassA = "font-weight:bold;font-size:1em;line-height:20px;";
    $sClassB = "font-weight:lighter;font-size:0.9em;margin:2px 0px 0px 2px;";

    $sBotMatris = "<div style='{$sClassA}'  style='float: left;width: 30%;'>DEFSEI</div><div style='{$sClassB}'>Herramientas</div>]{$sUrlPanelesA}]cuerpo]RZ}";
    $sBotMatris .= "Herramienta ]./_vistas/adminTablasForms.php?vista=Felipe]cuerpo]C}";
    $sBotMatris .= "Site ]./_vistas/adminTablasForms.php?vista=Felipe]cuerpobody]C}";
    $sBotMatris .= "Productos ]./_vistas/adminTablasForms.php?vista=Felipe]cuerpo]C}";
    $sBotMatris .= "Análisis ]./_vistas/adminTablasForms.php?vista=Felipe]cuerpobody]C}";
    $sBotMatris .= "Salir]salir_master.php]cuerpo]C}";

    $sTipoAjax = "true";
    $sClase = "menuHorz001";
    $sBot = Boton001($sBotMatris, $sClase, $sTipoAjax);

    $master_access = $_SESSION["master_access"];

    $Q_U = " SELECT * FROM administradores WHERE AdminCod = {$master_access}";
    $Obj = fetchOne($Q_U, $vConex);
    $Nombres=$Obj->Nombres;
    $ApellidoPat=$Obj->ApellidoPat;
    $Usuario=$Obj->Usuario;
    $Tipo=$Obj->Tipo;
	
	if($Tipo == 1){
		$Tipo = "MASTER";	
	}elseif($Tipo == 2){
		$Tipo = "DESARROLLADOR";	
	}elseif($Tipo == 3){
		$Tipo = "COORDINADOR";	
	}elseif($Tipo == 4){
		$Tipo = "VISITANTE";	
	}
	
    $sBot .=' <div class="menuHorz001" style="float: right;width: 30%;">
                <ul><li class="razonSocial" style="float: right;text-align: center;color: #fff;">
                <div style="font-weight:bold;font-size:1em;line-height:20px;">'.$Nombres.' '.$ApellidoPat.'</div>
                <div style="font-weight:lighter;font-size:0.9em;margin:2px 0px 0px 2px;">'.$Tipo.'</div>
                </li></ul>
              </div>';

    $html = "{$html}{$sBot}</div>";
    return $html;
}

function menuLogin() {
    global $num_rand;
    
    $html = "
        <!DOCTYPE html> 
        <html lang='es'>
        <head>
            <link rel='shortcut icon'  href='/system/_imagenes/icono.ico' >
            <title>Login</title>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0'>
            <meta name='description' content=''>
            <meta name='keywords' content=''>
            <meta name='author' content=''>
            <script type='text/javascript' src='_librerias/js/global.js'></script>
            <script type='text/javascript' src='_librerias/js/ajaxglobal.js'></script>
            <link href='./_estilos/OwlStyles.css?vr={$num_rand}' rel='stylesheet' type='text/css' />
        </head>
        <body>
            <div class='site'>";
    
    return $html;
}

function menuPie($entidadEmpresa) {
    global $vConex;
    
    $Q_Empresa = "SELECT 
        USU.Usuario,
        USU.IdUsuario,
        USU.UrlId,
        USU.Carpeta,
        USU.Perfil,
        USU.Estado,
        EMP.RazonSocial,
        EMP.Logo,
        TEM.Archivo AS color
	FROM usuarios AS USU
	INNER JOIN empresa EMP ON EMP.PaginaWeb = USU.IdUsuario
	INNER JOIN temasgraf TEM ON EMP.IdTemaGraf = TEM.IdTemasGraf
	WHERE USU.IdUsuario = '{$entidadEmpresa}'";
        
    $Empresa = fetchOne($Q_Empresa, $vConex);
    
    $color = $Empresa->color;
    $Current_year = date("Y");
    $html = "";
    /*
    $html = "
        <div class='footerA_P' style='background-color:{$color}'>
            <div class='footerA'>
                <ul>
                    <li>
                    <p class='text-right' style='font-size: 0.8em;'>© {$Current_year} owlgroup.org - Todos los derechos reservados</p>
                    </li>
                    <li style='float:right !important;padding: 0.5em 2em 1em 0;'>
                        <a href='#' title='facebook'><div class='bot_icon_RD'><i class='icon-facebook-sign'></i></div></a>
                        <a href='#' title='twiter'><div class='bot_icon_RD'><i class='icon-twitter-sign'></i></div></a>
                        <a href='#' title='in'><div class='bot_icon_RD'><i class='icon-linkedin-sign'></i></div></a>
                    </li>
                </ul>
            </div>
        </div>
        </body>
    </html>";
      */              
    return $html;
}
