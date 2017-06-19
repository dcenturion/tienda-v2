<?php

	$IPArchivos = CONS_IPArchivos;	
	
    $vConex = conexSys();
    $cnOwl = conexSys();
    
    function menuSiteEmpresa($valor) {

    $html = '<!DOCTYPE html> ';
    $html = $html.'<html lang="es">';
    $html = $html.'<head>';
    $html = $html.'<title>Owl</title>';
    $html = $html.' <meta charset="utf-8">';
    $html = $html.' <meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $html = $html.'<meta name="description" content="">';
    $html = $html.'<meta name="keywords" content="">';
    $html = $html.' <meta name="author" content="">';
    $html = $html.'<script type="text/javascript" src="_librerias/js/global.js"></script>';
$html = $html.'<script type="text/javascript" src="_librerias/js/ajaxglobal.js"></script>';
    $html = $html.'<link href="./_estilos/estiloCuadro.css" rel="stylesheet" type="text/css" />';
    $html = $html. '</head>';
    $html = $html.'<body>';
    $html = $html.'<div class="site">';	

$html = $html.'<div id="menu" class="mHSinSubElementosA001 tamano">';

    $sClassA ="font-weight:bold;font-size:2.2em;line-height:40px;";
    $sClassB ="font-weight:lighter;font-size:0.75em;margin:8px 0px 0px 2px;";

    $sUrlPanelesA = "PanelA[PanelA[./_vistas/carrusel.html?vista=PanelA[1000|";
    $sUrlPanelesA = $sUrlPanelesA."PanelB[PanelB[system/_vistas/se_publicidad.php.php?empresa=".$tIdUsuario."[2000|";	
    // $sUrlPanelesA = $sUrlPanelesA."PanelC[PanelC[./vistas/site.php?vista=PanelC[4000|";	

    $sBotMatris = "<div style='".$sClassA."'>OWL</div><div style='".$sClassB."'>HERRAMIENTAS</div>]".$sUrlPanelesA."]cuerpo]RZ}";
    $sBotMatris = $sBotMatris."Home]index.php?TipoConsulta=juan&usuario=daniel&estado=Abierto]cuerpo]C}";
    $sBotMatris = $sBotMatris."Login]index.php?TipoConsulta=Felipe&usuario=daniel&estado=Abierto]cuerpo]C}";
    $sBotMatris = $sBotMatris."Form]./_vistas/form.php?vista=Felipe]cuerpo]C}";
    $sBotMatris = $sBotMatris."Form Inpt]./_vistas/formAj.html?vista=Felipe]cuerpo]C}";
    $sBotMatris = $sBotMatris."Listado]./_vistas/listadoReporte.php?vista=Felipe]cuerpo]C}";
    $sBotMatris = $sBotMatris."Check]./_vistas/listadoReporte2.php?vista=Felipe]cuerpo]C}";	
    $sTipoAjax = "true";
    $sClase  = "menuHorz001";
$sBot = Boton001($sBotMatris,$sClase,$sTipoAjax);
$html = $html.$sBot.' </div>';
     return  $html;
}

    function menuAdmin($valor) {
    $html = '<!DOCTYPE html> ';
    $html = $html.'<html lang="es">';
    $html = $html.'<head>';
    $html = $html.'<title>Owl</title>';
    $html = $html.' <meta charset="utf-8">';
    $html = $html.' <meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $html = $html.'<meta name="description" content="">';
    $html = $html.'<meta name="keywords" content="">';
    $html = $html.' <meta name="author" content="">';
    $html = $html.'<script type="text/javascript" src="_librerias/js/global.js"></script>';
$html = $html.'<script type="text/javascript" src="_librerias/js/ajaxglobal.js"></script>';
    $html = $html.'<link href="./_estilos/estiloCuadro.css" rel="stylesheet" type="text/css" />';
    $html = $html. '</head>';
    $html = $html.'<body>';
    $html = $html.'<div class="site">';	
$html = $html.'<div id="menu" class="mHSinSubElementosA001 tamano">';
    $sClassA ="font-weight:bold;font-size:2.2em;line-height:40px;";
    $sClassB ="font-weight:lighter;font-size:0.75em;margin:8px 0px 0px 2px;";
    $sUrlPanelesA = "PanelA[PanelA[./_vistas/carrusel.html?vista=PanelA[1000|";
    $sUrlPanelesA = $sUrlPanelesA."PanelB[PanelB[./_vistas/site.php?vista=PanelB[2000|";	
    // $sUrlPanelesA = $sUrlPanelesA."PanelC[PanelC[./vistas/site.php?vista=PanelC[4000|";	
    $sBotMatris = "<div style='".$sClassA."'>FRI</div><div style='".$sClassB."'>GESTIÓN</div>]".$sUrlPanelesA."]cuerpo]RZ}";
    $sBotMatris = $sBotMatris."Site]index.php?TipoConsulta=juan&usuario=daniel&estado=Abierto]cuerpo]C}";
    $sBotMatris = $sBotMatris."Gestión]index.php?TipoConsulta=Felipe&usuario=daniel&estado=Abierto]cuerpo]C}";
    $sBotMatris = $sBotMatris."Configuración]./_vistas/form.php?vista=Felipe]cuerpo]C}";
    // $sBotMatris = $sBotMatris."Form Inpt]./_vistas/formAj.html?vista=Felipe]cuerpo]C}";
    // $sBotMatris = $sBotMatris."Listado]./_vistas/listadoReporte.php?vista=Felipe]cuerpo]C}";
    // $sBotMatris = $sBotMatris."Check]./_vistas/listadoReporte2.php?vista=Felipe]cuerpo]C}";	
    $sTipoAjax = "true";
    $sClase  = "menuHorz001";
$sBot = Boton001($sBotMatris,$sClase,$sTipoAjax);
$html = $html.$sBot.' </div>';
    return  $html;
}

    function menuMaster($UsuarioAdmin) {

            $html = '<!DOCTYPE html> ';
            $html = $html.'<html lang="es">';
            $html = $html.'<head>';
            $html = $html.'<title>Owl</title>';
            $html = $html.' <meta charset="utf-8">';
            $html = $html.' <meta name="viewport" content="width=device-width, initial-scale=1.0">';
            $html = $html.'<meta name="description" content="">';
            $html = $html.'<meta name="keywords" content="">';
            $html = $html.' <meta name="author" content="">';
            $html = $html.'<script type="text/javascript" src="_librerias/js/global.js"></script>';
            $html = $html.'<script type="text/javascript" src="_librerias/js/ajaxglobal.js"></script>';
            $html = $html.'<link href="system/_estilos/estiloCuadro4.css" rel="stylesheet" type="text/css" />';
            $html = $html. '</head>';
            $html = $html.'<body>';
            $html = $html.'<div class="site" id="site">';			
            $html = $html.'<div id="menu" class="mHSinSubElementosA002 tamano">';

                    $sClassA ="font-weight:bold;font-size:1.4em;";
                    $sClassB ="font-weight:lighter;font-size:0.9em;margin:0px;";

                    $sUrlPanelesA = "PanelA[PanelA[./_vistas/carrusel.html?vista=PanelA[1000|";
                    $sUrlPanelesA = $sUrlPanelesA."PanelB[PanelB[./_vistas/site.php?vista=PanelB[2000|";	
                    $sBotMatris = "<div style='".$sClassA."'>OWL</div><div style='".$sClassB."'>HERRAMIENTAS</div>]".$sUrlPanelesA."]cuerpo]RZ}";
                    if(!empty($UsuarioAdmin)){

                            $sBotMatris = $sBotMatris."Salir]./master.php?CierraSesion=Yess]site]C}";
                            $sBotMatris = $sBotMatris."Gestión]./_vistas/sys_gestion.php?vista=Felipe]cuerpo]C}";
                            $sBotMatris = $sBotMatris."Migra DATA]./_vistas/sys_migraciones.php?vista=Felipe]cuerpo]C}";
                            $sBotMatris = $sBotMatris."H Interna]./_vistas/adminTablasFormsB.php?site=yes]cuerpo]C}";
                            $sBotMatris = $sBotMatris."H Inicio]./_vistas/adminTablasForms.php?site=yes]cuerpo]C}";				
                    }

                    $sBotMatris = $sBotMatris."Ayuda]./_vistas/adminTablasForms.php?site=yes]cuerpo]C}";
                    $sTipoAjax = "true";
                    $sClase  = "menuHorz002";
                    $sBot = Boton001($sBotMatris,$sClase,$sTipoAjax);

            $html = $html.$sBot.' </div>';
            return  $html;

}

    function menuEmpresaSite($url) {
    global $cnOwl, $IPArchivos;


    $sql = 'SELECT U.Usuario,U.IdUsuario,U.UrlId,U.Carpeta,U.Perfil,U.Estado
    ,E.RazonSocial, E.Logo,T.Archivo
    FROM ((usuarios AS U
    LEFT JOIN empresa E ON E.PaginaWeb = U.IdUsuario)
    LEFT JOIN temasgraf T ON E.IdTemaGraf = T.IdTemasGraf)
    WHERE  UrlId = "'.$url.'" ';
    $rg = fetch($sql);
    $tUrlId = $rg["UrlId"];	
    $tRazonSocial = $rg["RazonSocial"];
    $tLogo = $rg["Logo"];
    $tIdUsuario = $rg["IdUsuario"];
    $tCarpeta = $rg["Carpeta"];		
    $color = $rg["Archivo"];		

    $tUrlLogo = $IPArchivos."/ArchivosEmpresa/".$tCarpeta."/".$tLogo."";	

    $html = '<!DOCTYPE html> ';
    $html = $html.'<html lang="es">';
    $html = $html.'<head>';
    $html = $html.'<title>Owl</title>';
    $html = $html.' <meta charset="utf-8">';
    $html = $html.' <meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $html = $html.'<meta name="description" content="">';
    $html = $html.'<meta name="keywords" content="">';
    $html = $html.' <meta name="author" content="">';
    $html = $html.'<script type="text/javascript" src="../system/_librerias/js/global.js"></script>';
    $html = $html.'<script type="text/javascript" src="../system/_librerias/js/ajaxglobal.js"></script>';
    $html = $html.'<link href="../system/_estilos/estiloCuadroA.css" rel="stylesheet" type="text/css" />';
    $html = $html.'<script type="text/javascript" src="../system/_librerias/js/defsei_ajax.js"></script>';
   //$html = $html.'<script type="text/javascript" src="../system/_librerias/js/fb/fbauthentication.js"></script>';
    $html = $html. '</head>';
    $html = $html.'<body>';
    $html = $html.'<style type="text/css">';
    $html = $html.'.mHSinSubElementosA001.tamano{background-color:'.$color.' !important;}';
    $html = $html.'.CuadroA2 button{background-color:'.$color.' !important;}';
    $html = $html.'</style>';	
    $html = $html.'<div class="site">';	

$html = $html.'<div id="menu" class="mHSinSubElementosA001 tamano">';

      $browser = $_SERVER['HTTP_USER_AGENT']; 
      $navegador = verNavegador($browser); 

      if($navegador != 'Google Chrome'){
            $html = $html.'<div class="navegador">';
            $html = $html.'<span class="alert-navegador">¡Su navegador ('.$navegador.'), no esta permitido para este sitio! </span>';
            $html = $html.'<span class="link-navegador">Porfavor, descargue Google Chrome  </span><a href="https://www.google.com/intl/es/chrome/browser/" target="_blank">aqui</a><img class="imagen-crome" src="../_imagenes/crome.png">';
            $html = $html.'</div>';
      }

    $sClassA ="font-weight:bold;font-size:2.2em;line-height:40px;float:left";
    $sClassB ="margin:8px 20px 0px 50px;color:#e7e7e7;float:left;width:130px;line-height:20px;";

    $sUrlPanelesA = "PanelA[PanelA[../system/_vistas/se_publicidad.php?empresa=".$tIdUsuario."[1000|";
    $sUrlPanelesA = $sUrlPanelesA."PanelB[PanelB[../system/_vistas/se_login_inscripcion.php?muestra=Login&empresa=".$tIdUsuario."[2000|";	
$sUrlPanelesA = $sUrlPanelesA."PanelD[PanelD[../system/_vistas/se_productos.php?empresa=".$tIdUsuario."&panel=PanelD[4000[true|";

    $sUrlPanelesB = "PanelA[PanelA[../system/_vistas/se_productos.php?vista=PanelA&empresa=".$tIdUsuario."&panel=PanelA[1000|";
    $sUrlPanelesB = $sUrlPanelesB."PanelB[PanelB[../system/_vistas/se_login_inscripcion.php?muestra=Login&empresa=".$tIdUsuario."[2000|";	

    $sUrlPanelesC = "PanelA[PanelA[../system/_vistas/se_somos.php?vista=PanelA&empresa=".$tIdUsuario."[1000|";
    $sUrlPanelesC = $sUrlPanelesC."PanelB[PanelB[../system/_vistas/se_login_inscripcion.php?muestra=Login&empresa=".$tIdUsuario."[2000|";	

    $sUrlPanelesD = "PanelA[PanelA[../system/_vistas/se_noticias.php?vista=PanelA&empresa=".$tIdUsuario."[1000|";
    $sUrlPanelesD = $sUrlPanelesD."PanelB[PanelB[../system/_vistas/se_login_inscripcion.php?muestra=Login&empresa=".$tIdUsuario."[2000|";

    if($tLogo !=""){
            $sBotoL = "
            <div style='".$sClassA."'><img src='".$tUrlLogo."'></div>
            <div style='".$sClassB."'>
            <div class='tituloA2' >PLATAFORMA </div>
            <div class='lineaRZ' ></div>
            <div class='sub_tituloA2'>EDUCATIVA</div>
            </div>";
       $sBotMatris = "".$sBotoL."]".$sUrlPanelesA."]cuerpo]RZ}";	
    }else{
       $sBotMatris = "<div style='".$sClassA."'>".$tRazonSocial."</div><div style='".$sClassB."'>HERRAMIENTAS</div>]".$sUrlPanelesA."]cuerpo]RZ}";
    }


    $sql = " SELECT tab1.TipoEmpresa  FROM empresa  AS tab1";
    $sql .= " INNER JOIN tipoempresa  AS tab2 ON tab1.TipoEmpresa = tab2.IDTipoEmpresa";	
    $sql .= " WHERE tab1.PaginaWeb = '".$tIdUsuario."' ";
    $rg = fetch($sql);
$iDTipoEmpresa = $rg["TipoEmpresa"];
if ($iDTipoEmpresa==6) {
    $BntProductos = "<div class='Fnd_Men_Btn'><i class='icon-th-large'></i><div class='fnt-text'>Cursos</div></div> ";
 }else{
        $BntProductos = "<div class='Fnd_Men_Btn'><i class='icon-th-large'></i><div class='fnt-text'>Programas</div></div> ";
 }	
    $Somos = "<div class='Fnd_Men_Btn'><i class='icon-user'></i><div class='fnt-text'>Somos</div></div> ";
    $Noticias = "<div class='Fnd_Men_Btn'><i class='icon-list-alt'></i><div class='fnt-text'>Noticias</div></div> ";
    $Registrate = "<div class='Fnd_Men_Btn'><i class='icon-pencil' id='icon-pencil' ></i><div class='fnt-text'>Registrate</div></div> ";
    $Login = "<div class='Fnd_Men_Btn'><i class='icon-signout'></i><div class='fnt-text'>Login</div></div>";

    $sBotMatris = $sBotMatris."".$BntProductos."]".$sUrlPanelesB."]cuerpo]C}";
    $sBotMatris = $sBotMatris."".$Somos ."]".$sUrlPanelesC."]cuerpo]C}";
    $sBotMatris = $sBotMatris."".$Noticias."]".$sUrlPanelesD."]cuerpo]C}";
    $sBotMatris = $sBotMatris."".$Registrate."]../system/_vistas/se_login_inscripcion.php?muestra=Registro&empresa=".$tIdUsuario."]PanelB]C}";
    $sBotMatris = $sBotMatris."".$Login."]../system/_vistas/se_login_inscripcion.php?muestra=Login&empresa=".$tIdUsuario."]PanelB]C}";	
    $sTipoAjax = "true";
    $sClase  = "menuHorz001";
$sBot = Boton001($sBotMatris,$sClase,$sTipoAjax);
$html = $html.$sBot.' </div>';
    return  $html;
}


function menuEmpresaSiteMod($url) {
    global $cnOwl, $num_rand, $IPArchivos;

 
    $sql = 'SELECT U.Usuario,U.IdUsuario,U.UrlId,U.Carpeta,U.Perfil,U.Estado
    ,E.RazonSocial, E.Logo,T.Archivo
    FROM ((usuarios AS U
    LEFT JOIN empresa E ON E.PaginaWeb = U.IdUsuario)
    LEFT JOIN temasgraf T ON E.IdTemaGraf = T.IdTemasGraf)
    WHERE  UrlId = "'.$url.'" ';
    $rg = fetch($sql);
    $tUrlId = $rg["UrlId"];	
    $tRazonSocial = $rg["RazonSocial"];
    $tLogo = $rg["Logo"];
    $tIdUsuario = $rg["IdUsuario"];
    $tCarpeta = $rg["Carpeta"];		
    $color = $rg["Archivo"];		


    $tUrlLogo = $IPArchivos."/ArchivosEmpresa/".$tCarpeta."/".$tLogo."";	
	
    $html = '<!DOCTYPE html> ';
    $html = $html.'<html lang="es">';
    $html = $html.'<head>';
    $html = $html.'<title>Owl</title>';
    $html = $html.' <meta charset="utf-8">';
    $html = $html.' <meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $html = $html.'<meta name="description" content="">';
    $html = $html.'<meta name="keywords" content="">';
    $html = $html.' <meta name="author" content="">';
    $html = $html.'<script type="text/javascript" src="../system/_librerias/js/global.js"></script>';
    $html = $html.'<script type="text/javascript" src="../system/_librerias/js/ajaxglobal.js"></script>';
    $html = $html.'<link href="../system/_estilos/estiloCuadroA.css?vr='.$num_rand.'" rel="stylesheet" type="text/css" />';
    $html = $html.'<script type="text/javascript" src="../system/_librerias/js/defsei_ajax.js"></script>';	
    $html = $html. '</head>';
    $html = $html.'<body>';
    $html = $html.'<style type="text/css">';
    $html = $html.'.mHSinSubElementosA001.tamano{background-color:'.$color.' !important;}';
    $html = $html.'.CuadroA2 button{background-color:'.$color.' !important;}';
    $html = $html.'</style>';	
    $html = $html.'<div class="site">';	

    $html = $html.'<div id="menu" class="mHSinSubElementosA001 tamano">';

      $browser = $_SERVER['HTTP_USER_AGENT']; 
      $navegador = verNavegador($browser);
      $siteUrl = siteUrl();	  

      if($navegador != 'Google Chrome'){
            $html = $html.'<div class="navegador">';
            $html = $html.'<span class="alert-navegador">¡Su navegador ('.$navegador.'), no esta permitido para este sitio! </span>';
            $html = $html.'<span class="link-navegador">Porfavor, descargue Google Chrome  </span><a href="https://www.google.com/intl/es/chrome/browser/" target="_blank">aqui</a><img class="imagen-crome" src="../_imagenes/crome.png">';
            $html = $html.'</div>';
      }

    $sClassA ="font-weight:bold;font-size:2.2em;line-height:40px;float:left";
    $sClassB ="margin:8px 20px 0px 50px;color:#e7e7e7;float:left;width:130px;line-height:20px;";

    if($tLogo !=""){
            $sBotoL = "
            <div style='".$sClassA."'>
			<a href='".$siteUrl.$tUrlId."' style='text-decoration:none;border:none;'>
			<img src='".$tUrlLogo."'></div>
            <div style='".$sClassB."'>
            <div class='tituloA2' >PLATAFORMA </div>
            <div class='lineaRZ' ></div>
            <div class='sub_tituloA2'>EDUCATIVA</div>
			</a>
            </div>";
       $sBotMatris = "".$sBotoL."]".$sUrlPanelesA."]cuerpo]RZ}";	
    }else{
       $sBotMatris = "<div style='".$sClassA."'>".$tRazonSocial."</div><div style='".$sClassB."'>HERRAMIENTAS</div>]".$sUrlPanelesA."]cuerpo]RZ}";
    }


    $sql = " SELECT tab1.TipoEmpresa  FROM empresa  AS tab1";
    $sql .= " INNER JOIN tipoempresa  AS tab2 ON tab1.TipoEmpresa = tab2.IDTipoEmpresa";	
    $sql .= " WHERE tab1.PaginaWeb = '".$tIdUsuario."' ";
    $rg = fetch($sql);
	$iDTipoEmpresa = $rg["TipoEmpresa"];
	if ($iDTipoEmpresa==6) {
		$BntProductos = "<div class='Fnd_Men_Btn'><i class='icon-th-large'></i><div class='fnt-text'>Cursos</div></div> ";
	 }else{
			$BntProductos = "<div class='Fnd_Men_Btn'><i class='icon-th-large'></i><div class='fnt-text'>Programas</div></div> ";
	 }	
    $sTipoAjax = "true";
    $sClase  = "menuHorz001";
	$sBot = Boton001($sBotMatris,$sClase,$sTipoAjax);
	$html = $html.$sBot.' </div>';
    return  $html;
}



function menuPie($url) {
global $cnOwl;  
    $sql = 'SELECT U.Usuario,U.IdUsuario,U.UrlId,U.Carpeta,U.Perfil,U.Estado
    ,E.RazonSocial, E.Logo,T.Archivo
    FROM ((usuarios AS U
    LEFT JOIN empresa E ON E.PaginaWeb = U.IdUsuario)
    LEFT JOIN temasgraf T ON E.IdTemaGraf = T.IdTemasGraf)
    WHERE  UrlId = "'.$url.'" ';
    $rg = fetch($sql);
    $tUrlId = $rg["UrlId"];	
    $tRazonSocial = $rg["RazonSocial"];
    $tLogo = $rg["Logo"];
    $tIdUsuario = $rg["IdUsuario"];
    $tCarpeta = $rg["Carpeta"];		
    $color = $rg["Archivo"];		
    $enlace = "http://{$_SERVER["HTTP_HOST"]}";	

    $html = '<div class="footerA_P" style="background-color:'.$color.';">';
    $html .= '<div class="footerA">';
    $html .= ' <ul>';	
    $html .= ' <li>';
    $html .= ' <p class="text-right">© 2013 owlgroup.org </p>';	
    $html .= ' </li>';
    $html .= ' <li>';
    $html .= ' </li>';		
    $html .= ' <li style="float:right !important;width:140px;">';	
    // $html .= ' <a href="#" title="owlgroup.org"><img src="'.$enlace.'/img/icono-owl.png" height="27" width="27" /></a>';
    $html .= ' <a href="#" title="facebook"><div class="bot_icon_RD"><i class="icon-facebook-sign"></i></div></a>';
    $html .= ' <a href="#" title="twiter"><div class="bot_icon_RD"><i class="icon-twitter-sign"></i></div></a>';
    $html .= ' <a href="#" title="in"><div class="bot_icon_RD"><i class="icon-linkedin-sign"></i></div></a>';
    $html .= ' </li>';
    $html .= ' </ul>';	
    $html .= ' </div>';
    $html .= ' </div>';
    $html .=' </body>';
    $html .='</html>';
    return  $html;
}  