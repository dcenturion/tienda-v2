<?php
require_once('funciones.php');
require_once('conexiones.php');
$ConexionEmpresa = conexSys();

if(post('Usuario')!=""){
    $data='[{"Usuario":"Creado"}]';
    W($data);
}

$key=get("KeyWebService");
$AlmacenCod=get("AlmacenCod");

$CodPlataforma=get("CodPlataforma");
$CodPlataformaAlmacen=get("CodPlataformaAlmacen");
$CodPlataformaSocio=get("CodPlataformaSocio");
$Codigo = get("Codigo");
$codigoAlmacen = get("codigoAlmacen");
$CodigoProducto = get("CodigoProducto");

if (get('PeticionCliente') =='Data'){ PeticionClientes(get('PeticionCliente'));}
if (get('ConsultaCliente') =='Consulta'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta1'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta2'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta3'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta4'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta5'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta6'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta7'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta8'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta9'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta10'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta11'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta12'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta13'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta14'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta15'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta16'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta17'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta18'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta19'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta20'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta21'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta22'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta23'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta24'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta25'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta26'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta27'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta28'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta29'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta30'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta31'){ certificaciones(get('ConsultaCliente'));}

if (get('ConsultaCliente') =='Consulta32'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta33'){ certificaciones(get('ConsultaCliente'));}
if (get('ConsultaCliente') =='Consulta34'){ certificaciones(get('ConsultaCliente'));}

function PeticionClientes($Arg)
{
    global $key,$ConexionEmpresa;
    switch($Arg)
    {
        case "Data":
            $SqlUsuairo="SELECT cte.Codigo as 'CodigoEmpresa',cte.RazonSocial,cte.RUC,cte.Direccion,
            su.Codigo as 'CodigoUsuario',su.email,su.Nombres,su.Apellidos,su.Usuario,su.Contrasena
            FROM ct_empresa cte
            INNER JOIN ct_miembro_empresa cme on cte.Codigo=cme.ct_Empresa
            INNER JOIN sys_usuarios su on cme.ct_suscriptor=su.ct_suscriptor
            WHERE cte.keysuscripcion='".$key."'
            GROUP BY Usuario ";
            $Json= Listado( $SqlUsuairo, $ConexionEmpresa);
            W("$Json");
            break;
    }
}

function certificaciones($Arg)
{
    global $key,$ConexionEmpresa,$nombre,$email,$contrasena,$AlmacenCod, $CodPlataforma, $CodPlataformaAlmacen, $CodPlataformaSocio, $Codigo, $codigoAlmacen, $CodigoProducto;

    $sql = "select IdUsuario from usuarios where KeySuscripcion = '".$key."'";

    $reg = fetch($sql);

    $IdUsuario = $reg["IdUsuario"];

    if ($IdUsuario){
        //switch
        switch($Arg)
        {
            case "Consulta":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' DATE_FORMAT( DiaInicio,  "%d de %M del %Y" ) AS Fecha'
                    . ' FROM almacen'
                    . ' WHERE AlmacenCod ='.$AlmacenCod. '  ';
                $Json= Listado( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta1"://HCC
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.banner_presentacion'
                    . ' ,AR.ProductoFab'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= Listado( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta2":
                mysql_query( "SET lc_time_names = 'es_ES'" );

                $sql = 'SELECT  '
                    . ' PR.InformeInscripciones '
                    . ' ,PR.FolletoVentas '
                    . ' ,AR.ProductoFab '
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';

                $Json= ListadoHtml( $sql, $ConexionEmpresa);

                W("$Json");
                break;
            case "Consulta3":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Titulo'
                    . ', PR.Presentacion'
                    . ', PR.Objetivo'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';
                $Json= Listado( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta4":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . '  PR.FechaInicial'
                    . ', PR.FechaFinal'
                    . ', Desc_Duracion_Horario'
                    .', Desc_Horario'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta5":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Diplomas'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta6":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Facultad'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta7":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Admision'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';
                $Json= Listado( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta8":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Titulo'
                    . ', PR.Presentacion'
                    . ', PR.Objetivo'
                    . ', PR.PublicoDirigido'
                    . ', PR.TipoPrograma'
                    . ', PR.CategoriaCod'
                    . ', PR.Sector'
                    . ', PR.Sede'
                    . ', PR.TipoElearning'
                    . ', PR.DiaFinaInscripcion'
                    . ', PR.FechaInicial'
                    . ', PR.FechaFinal'
                    . ', PR.Diplomas'
                    . ', PR.inversion_flexible'
                    . ', PR.Moneda'
                    . ', PR.CantidadVacantes'
                    . ', PR.DetFinanciamiento'
                    . ', PR.DetDescuento'
                    . ', PR.DetDeposito'
                    . ',PR.InformeInscripciones'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';
                $Json= Listado( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta9":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' DATE_FORMAT( DiaInicio,  "%d de %M" ) AS Fecha'
                    . ' FROM almacen'
                    . ' WHERE AlmacenCod ='.$CodPlataforma. '  ';
                $Json= Listado( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta10":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' replace(PR.EstrucuraCurricular,"owlgroup.org","owlgroup.s3-website-us-west-2.amazonaws.com") AS EstrucuraCurricular '
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta11":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Admision'
                    . ', Desc_Duracion_Horario '
                    . ', Desc_Horario '
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta12":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Titulo'
                    . ', PR.Presentacion'
                    . ', PR.Objetivo'
                    . ', PR.PublicoDirigido'
                    . ', PR.TipoPrograma'
                    . ', PR.CategoriaCod'
                    . ', PR.Sector'
                    . ', PR.Sede'
                    . ', PR.TipoElearning'
                    . ', PR.DiaFinaInscripcion'
                    . ', PR.FechaInicial'
                    . ', PR.FechaFinal'
                    . ', PR.Diplomas'
                    . ',   replace(PR.inversion_flexible,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS inversion_flexible '
                    . ', PR.Moneda'
                    . ', PR.CantidadVacantes'
                    . ',   replace(PR.DetFinanciamiento,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS DetFinanciamiento '
                    . ', PR.DetDescuento'
                    . ',   replace(PR.DetDeposito,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS DetDeposito '
                    . ',PR.InformeInscripciones'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta13":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Presentacion'
                    . ', PR.Objetivo'
                    . ', PR.PublicoDirigido'
                    . ', PR.EstrucuraCurricular'
                    . ', PR.Diplomas'
                    . ', PR.Admision'
                    . ', DetDeposito'
                    . ', replace(PR.DetFinanciamiento,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS Financiamiento '
                    . ', replace(PR.inversion_flexible,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS inversion '
                    . ', Desc_Duracion_Horario'
                    . ', InformeInscripciones'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta14":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Facultad'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta15":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' replace(PR.EstrucuraCurricular,"owlgroup.org","owlgroup.s3-website-us-west-2.amazonaws.com") AS EstrucuraCurricular '
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta16":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql_plat = 'SELECT  Estado  FROM lista_trabajo_det WHERE CodigoAlmacen =  ' . $CodPlataforma . '  ';
                $Json= Listado( $sql_plat, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta17":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . '  replace(PR.Presentacion,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS Presentacion '
                    . ', PR.Objetivo'
                    . ', PR.PublicoDirigido'
                    . ', PR.EstructuraCurricularPrograma'
                    . ', PR.Diplomas'
                    . ', PR.Admision,DetDeposito,DetFinanciamiento,Desc_Duracion_Horario'
                    . ', PR.inversion_flexible'
                    . ' ,PR.FolletoVentas '
                    . ' ,AR.ProductoFab '
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta18":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sqlP1A = 'SELECT  '
                    . '  replace(PR.Presentacion,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS Presentacion '
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaSocio. '  ';
                $Json= ListadoHtml( $sqlP1A, $ConexionEmpresa);
                W($Json);
                break;
            case "Consulta19":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sqlP1 = 'SELECT  '
                    . '  replace(PR.Presentacion,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS Presentacion '
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sqlP1, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta20":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . '  replace(PR.Presentacion,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS Presentacion '
                    . ', PR.Objetivo'
                    . ', PR.PublicoDirigido'
                    . ',  replace(PR.EstructuraCurricularPrograma,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS EstructuraCurricularPrograma '
                    // . ', PR.EstructuraCurricularPrograma'
                    . ', PR.Diplomas'
                    . ', PR.Admision'
                    . ' ,DetDeposito'
                    . ',  replace(PR.DetFinanciamiento,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS DetFinanciamiento '
                    . ' ,Desc_Duracion_Horario'
                    . ',  replace(PR.inversion_flexible,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS inversion_flexible '
                    . ' ,PR.FolletoVentas '
                    . ' ,AR.ProductoFab '
                    . ' ,PR.InformeInscripciones'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta21":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sqlP1 = 'SELECT  '
                    . '  replace(PR.Presentacion,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS Presentacion '
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sqlP1, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta22":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Titulo'
                    . ', PR.Presentacion'
                    . ', PR.Objetivo'
                    . ', PR.PublicoDirigido'
                    . ', PR.TipoPrograma'
                    . ', PR.CategoriaCod'
                    . ', PR.Sector'
                    . ', PR.Sede'
                    . ', PR.TipoElearning'
                    . ', PR.DiaFinaInscripcion'
                    . ', PR.FechaInicial'
                    . ', PR.FechaFinal'
                    . ', PR.Diplomas'
                    . ',   replace(PR.inversion_flexible,"owlgroup.org","owlgroup.s3-website-us-west-2.amazonaws.com") AS inversion_flexible '
                    . ', PR.Moneda'
                    . ', PR.CantidadVacantes'
                    . ',   replace(PR.DetFinanciamiento,"owlgroup.org","owlgroup.s3-website-us-west-2.amazonaws.com") AS DetFinanciamiento '
                    . ', PR.DetDescuento'
                    . ',   replace(PR.DetDeposito,"owlgroup.org","owlgroup.s3-website-us-west-2.amazonaws.com") AS DetDeposito '
                    . ',PR.InformeInscripciones'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';
                $Json= Listado( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta23":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Titulo'
                    . ', PR.Presentacion'
                    . ', PR.Objetivo'
                    . ', PR.PublicoDirigido'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataformaAlmacen . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta24":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' DATE_FORMAT( DiaInicio,  "%d de %M del %Y" ) AS Fecha'
                    . ' FROM almacen'
                    . ' WHERE AlmacenCod ='.$CodPlataforma . '  ';
                $Json= Listado( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta25":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql='select Codigo,CodigoProducto,CodigoAlmacen'
                    .' from lista_trabajo_det where codigoAlmacen='.$codigoAlmacen
                    .' and CodigoProducto ='.$CodigoProducto;
                $Json= Listado( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta26"://FRI
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.InformeInscripciones '
                    . ' ,PR.FolletoVentas '
                    . ' ,AR.ProductoFab '
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta27"://FRI
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Presentacion'
                    . ', PR.Objetivo'
                    . ', PR.PublicoDirigido'
                    . ', PR.EstrucuraCurricular'
                    . ', PR.Diplomas'
                    . ', PR.Admision'
                    . ', DetDeposito'
                    . ', PR.DetFinanciamiento'
                    . ', Desc_Duracion_Horario'
                    . ', PR.inversion_flexible'
                    . ', replace(PR.inversion_flexible,"owlgroup.org","owlgroup.s3-website-us-west-2.amazonaws.com") AS inversion '
                    . ', InformeInscripciones'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta28":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Facultad'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta29":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.EstrucuraCurricular'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta30":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql_plat = 'SELECT  Estado  FROM almacen WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= Listado( $sql_plat, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta31":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql_plat = 'SELECT  Estado,DATE_FORMAT( DiaInicio,  "%d de %M" ) AS Fecha  FROM almacen WHERE AlmacenCod =  ' . $CodPlataforma . ' ORDER BY Fecha Desc ';
                $Json= Listado( $sql_plat, $ConexionEmpresa);
                W("$Json");
                break;
            //*****3ig
            case "Consulta32":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Titulo'
                    . ', PR.Presentacion'
                    . ', PR.Objetivo'
                    . ', PR.PublicoDirigido'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta33":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Titulo'
                    . ', PR.Presentacion'
                    . ', PR.Objetivo'
                    . ', PR.PublicoDirigido'
                    . ', PR.TipoPrograma'
                    . ', PR.CategoriaCod'
                    . ', PR.Sector'
                    . ', PR.Sede'
                    . ', PR.TipoElearning'
                    . ', PR.DiaFinaInscripcion'
                    . ', PR.FechaInicial'
                    . ', PR.FechaFinal'
                    . ', PR.Diplomas'
                    . ',   replace(PR.inversion_flexible,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS inversion_flexible '
                    . ', PR.Moneda'
                    . ', PR.CantidadVacantes'
                    . ',   replace(PR.DetFinanciamiento,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS DetFinanciamiento '
                    . ', PR.DetDescuento'
                    . ',   replace(PR.DetDeposito,"app.owlcrm.info","owlgroup.s3-website-us-west-2.amazonaws.com") AS DetDeposito '
                    . ',PR.InformeInscripciones'
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';

                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;
            case "Consulta34":
                mysql_query( "SET lc_time_names = 'es_ES'" );
                $sql = 'SELECT  '
                    . ' PR.Admision'
                    . ', Desc_Duracion_Horario '
                    . ', Desc_Horario '
                    . ' FROM almacen as AL'
                    . ' LEFT JOIN articulos as AR on AL.Producto = AR.Producto'
                    . ' LEFT JOIN programas as PR on AR.ProductoFab =PR.CodPrograma'
                    . ' WHERE AlmacenCod =  ' . $CodPlataforma . '  ';
                $Json= ListadoHtml( $sql, $ConexionEmpresa);
                W("$Json");
                break;

        }
    } else{
        $Json = '[{ "ERROR": "error" }]';

        W("$Json");
    }
}

function Listado( $sql, $conexion)
{
    $consulta = mysql_query($sql, $conexion);
    $resultado = $consulta or die(mysql_error());
    $num = mysql_num_fields($consulta);
    $filas = mysql_num_rows($consulta);
    $cont=0;
    $clientes ="[";
    while ($reg = mysql_fetch_array($resultado)) {
        for ($i = 0; $i < mysql_num_fields($consulta); ++$i) {
            if($i<1){
                $clientes.="{";
                for ($j = 0; $j < mysql_num_fields($consulta); ++$j) {
                    $campo = mysql_field_name($consulta, $j);
                    if ($j == $num-1 ){
                        $clientes .= '"'.$campo.'":"'. $reg[$j].'"';
                    }else{
                        $clientes .= '"'.$campo.'":"'. $reg[$j].'",';
                    }
                    #array_push($cmp,$campo, $reg[$j]);
                }
            }
        }
        if ($cont == $filas-1 ){
            $clientes.="}";
        }else{
            $clientes.="},";
        }
        $cont++;
    }
    $clientes.="]";
    return $clientes;
}

function ListadoHtml( $sql, $conexion)
{
    $consulta = mysql_query($sql, $conexion);
    $resultado = $consulta or die(mysql_error());
    $num = mysql_num_fields($consulta);
    $filas = mysql_num_rows($consulta);
    $cont=0;
    $clientes ="[";
    while ($reg = mysql_fetch_array($resultado)) {
        for ($i = 0; $i < mysql_num_fields($consulta); ++$i) {
            if($i<1){
                $clientes.="{";
                for ($j = 0; $j < mysql_num_fields($consulta); ++$j) {
                    $campo = mysql_field_name($consulta, $j);
                    if ($j == $num-1 ){
                        $clientes .= '"'.$campo.'":"'. urlencode($reg[$j]).'"';
                    }else{
                        $clientes .= '"'.$campo.'":"'. urlencode($reg[$j]).'",';
                    }
                    #array_push($cmp,$campo, $reg[$j]);
                }
            }
        }
        if ($cont == $filas-1 ){
            $clientes.="}";
        }else{
            $clientes.="},";
        }
        $cont++;
    }
    $clientes.="]";
    return $clientes;

    #Encripta
    //$e= urlencode("HOLA MUNDO!");

    #Desencripta
    //$a = urldecode($e);
}