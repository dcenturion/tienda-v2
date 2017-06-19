<?php


function Proc_Actuliza_ProgEsp($dataInsert, $vConex) {
	$Estado		   = $dataInsert["Estado"];
	$CodigoProgEsp = $dataInsert["Cod_Curso"];
	$sqlupdateprogresp = "UPDATE programaespecial SET Estado='{$Estado}' WHERE Codigo={$CodigoProgEsp}";
	
	XSQL($sqlupdateprogresp,$vConex);

}

function Proc_Inpacto_TabAlmacen_Publicacion($dataInsert, $vConex) {
    $CodigoArticulo = $dataInsert["CodigoArticulo"];
    $CodigoAlmacen = $dataInsert["CodigoAlmacen"];
    $Estado = $dataInsert["Estado"];
    $TipoProducto = $dataInsert["TipoProducto"];
    $TipoIngreso = $dataInsert["TipoIngreso"];
    $FechaHora = $dataInsert["FechaHora"];
    // $FechaHoraVersion = $dataInsert["FechaHoraVersion"];
    $entidadCreadora = $dataInsert["entidadCreadora"];
    $UnidadNegocio_ID = $dataInsert["UnidadNegocio_ID"];
    $Codigo_Entidad_Usuario = $dataInsert["Codigo_Entidad_Usuario"];
    $Escuela_ID = $dataInsert["Escuela_ID"];
    $Descripcion = $dataInsert["Descripcion"];

    $Q_AR = "SELECT  
            IdArticulo,
            Fabricante,
            Entidad, 
            TipoProducto,
            Producto,
            ProductoFab,
            Categoria,
            DiaFinalInscripcion,
            DiaInicio,
            DiaFinal,
            Cantidad
            FROM articulos  
            WHERE IdArticulo={$CodigoArticulo}";

    $ObjAR = fetchOne($Q_AR, $vConex);
    $IdArticulo = $ObjAR->IdArticulo;
    $Entidad = $ObjAR->Entidad;
    $TipoProducto = $ObjAR->TipoProducto;
    $Producto = $ObjAR->Producto;
    $Categoria = $ObjAR->Categoria;
    $DiaFinalInscripcion = $ObjAR->DiaFinalInscripcion;
    $DiaInicio = $ObjAR->DiaInicio;
    $DiaFinal = $ObjAR->DiaFinal;
    $Cantidad = $ObjAR->Cantidad;

    if(empty($CodigoAlmacen) || $CodigoAlmacen==0 || !$CodigoAlmacen){
        //Genera Codigo Unico de almacen
        $Sql = "INSERT INTO almacen(Origen,Entidad,Producto,TipoProducto,Estado,DiaFinalInscripcion,DiaInicio,DiaFinal,FechReg,FechaHoraVersion,cantidad, stock ,Descripcion,Ingreso,NivelCoordinacion ,Coordinador  )
                VALUES('{$Entidad}','{$entidadCreadora}','{$Producto}','{$TipoProducto}','{$Estado}','{$DiaFinalInscripcion}','{$DiaInicio}','{$DiaFinal}','{$FechaHora}','{$FechaHora}',{$Cantidad},{$Cantidad},'{$Descripcion}',1,'Programa',{$Codigo_Entidad_Usuario} )";
        xSQL($Sql, $vConex);
        $CodAlmacen = mysql_insert_id($vConex);
    } else {

        //Genera Codigo Unico de almacen
        $Sql = "UPDATE almacen 
                SET 
                Estado='{$Estado}' 
                ,DiaInicio ='{$DiaInicio}' 
                ,DiaFinal ='{$DiaFinal}'                 
                ,DiaFinalInscripcion ='{$DiaFinalInscripcion}'                 
                WHERE AlmacenCod={$CodigoAlmacen}";
				// W($Sql);
        xSQL($Sql, $vConex);
        $CodAlmacen = $CodigoAlmacen;
    }
    //Llama a las transacciones y almacenes
    $sql = "SELECT codigo FROM almacen_transaccion 
        WHERE entidad='{$entidadCreadora}' AND nombre='ingreso-produccion' ";
    $rg = fetch($sql);
    $TransaccionIngProduccion = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_nombre 
        WHERE entidad='{$entidadCreadora}' AND nombre='productos-desarrollo' ";
    $rg = fetch($sql);
    $AlmacenDesarrollo = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_transaccion 
        WHERE entidad='{$entidadCreadora}' AND nombre='transferencia-productos-terminados' ";
    $rg = fetch($sql);
    $TransaccionIngSalidaProduccionAPT = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_transaccion 
        WHERE entidad='{$entidadCreadora}' AND nombre='ingreso-produccion-productos-terminados' ";
    $rg = fetch($sql);
    $TransaccionIngAPT = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_nombre 
        WHERE entidad='{$entidadCreadora}' AND nombre='productos-terminados' ";
    $rg = fetch($sql);
    $AlmacenAPT = $rg["codigo"];


    if ($TipoIngreso == "Desarrollo") {
        //Genera Movimiento de Ingreso a produccion
        $Sql = "INSERT INTO almacen_movimiento(almacen_id,almacen_transaccion_id ,documento,usuario, cantidad, almacen_nombre_id,descripcion, fecha_registro ,UNegocio,Escuela,Usuario_Entidad) 
            VALUES({$CodAlmacen},{$TransaccionIngProduccion},'','{$entidadCreadora}',{$Cantidad},{$AlmacenDesarrollo},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario});";

        xSQL($Sql, $vConex);
    }

    if ($TipoIngreso == "Abrir") {
        $Sql = "INSERT INTO almacen_movimiento (almacen_id,almacen_transaccion_id,documento,usuario,cantidad,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela,Usuario_Entidad) 
            VALUES ({$CodAlmacen},{$TransaccionIngSalidaProduccionAPT},'','{$entidadCreadora}',{$Cantidad},{$AlmacenDesarrollo},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario})";

        xSQL($Sql, $vConex);

        $Sql = "INSERT INTO almacen_movimiento(almacen_id,almacen_transaccion_id,documento,usuario,cantidad,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela,Usuario_Entidad)
            VALUES({$CodAlmacen},{$TransaccionIngAPT},'','{$entidadCreadora}',{$Cantidad},{$AlmacenAPT},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario})";
        xSQL($Sql, $vConex);
    }

    if ($TipoIngreso == "Desarrollo-Abierto") {
        //Genera Movimiento de Ingreso a produccion  y a productos terminados
        $Sql = " INSERT INTO almacen_movimiento ( 
                            almacen_id, almacen_transaccion_id ,documento, usuario,cantidad
                           ,almacen_nombre_id, descripcion ,fecha_registro ,UNegocio ,Escuela
                           ,Usuario_Entidad
                           ) VALUES (

                                   " . $CodAlmacen . "," . $TransaccionIngProduccion . ",'','" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenDesarrollo . ",'','" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . ",
                                   " . $Codigo_Entidad_Usuario . "

                           ) ";
        xSQL($Sql, $vConex);

        $Sql = " INSERT INTO almacen_movimiento ( 
                            almacen_id,almacen_transaccion_id,documento,usuario,cantidad
                           ,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela
                           ,Usuario_Entidad
                           ) VALUES (

                                   " . $CodAlmacen . "," . $TransaccionIngSalidaProduccionAPT . ",'','" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenDesarrollo . ",'','" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . "
                                   ," . $Codigo_Entidad_Usuario . "
                                                            ) ";
        xSQL($Sql, $vConex);

        $Sql = " INSERT INTO almacen_movimiento ( 
                            almacen_id, almacen_transaccion_id, documento, usuario, cantidad
                           ,almacen_nombre_id ,descripcion ,fecha_registro ,UNegocio ,Escuela
                           ,Usuario_Entidad
                           ) VALUES (

                                   " . $CodAlmacen . " ," . $TransaccionIngAPT . " ,''	,'" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenAPT . ",'' ,'" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . "
                                   ," . $Codigo_Entidad_Usuario . "

                           ) ";
        xSQL($Sql, $vConex);
    }
    return $CodAlmacen;
}

function Proc_Inpacto_TabAlmacen_Publicacion_canal($dataInsert, $vConex) {
    $CodigoArticulo = $dataInsert["CodigoArticulo"];
    $CodigoAlmacen = $dataInsert["CodigoAlmacen"];
    $Estado = $dataInsert["Estado"];
    $TipoProducto = $dataInsert["TipoProducto"];
    $TipoIngreso = $dataInsert["TipoIngreso"];
    $FechaHora = $dataInsert["FechaHora"];
    $entidadCreadora = $dataInsert["entidadCreadora"];
    $UnidadNegocio_ID = $dataInsert["UnidadNegocio_ID"];
    $Codigo_Entidad_Usuario = $dataInsert["Codigo_Entidad_Usuario"];
    $Escuela_ID = $dataInsert["Escuela_ID"];
    $Descripcion = $dataInsert["Descripcion"];

    $Q_AR = "SELECT
            IdArticulo,
            Fabricante,
            Entidad,
            TipoProducto,
            Producto,
            ProductoFab,
            Categoria,
            DiaFinalInscripcion,
            DiaInicio,
            DiaFinal,
            Cantidad
            FROM articulos
            WHERE IdArticulo={$CodigoArticulo}";

    $ObjAR = fetchOne($Q_AR, $vConex);
    $IdArticulo = $ObjAR->IdArticulo;
    $Entidad = $ObjAR->Entidad;
    $TipoProducto = $ObjAR->TipoProducto;
    $Producto = $ObjAR->Producto;
    $Categoria = $ObjAR->Categoria;
    $DiaFinalInscripcion = $ObjAR->DiaFinalInscripcion;
    $DiaInicio = $ObjAR->DiaInicio;
    $DiaFinal = $ObjAR->DiaFinal;
    $Cantidad = $ObjAR->Cantidad;

    if(empty($CodigoAlmacen) || $CodigoAlmacen==0 || !$CodigoAlmacen){

        //Genera Codigo Unico de almacen
        $Sql = "INSERT INTO almacen(Origen,Entidad,Producto,TipoProducto,Estado,DiaFinalInscripcion,DiaInicio,DiaFinal,FechReg,cantidad, stock ,Descripcion)
                VALUES('{$Entidad}','{$entidadCreadora}','{$Producto}','{$TipoProducto}','{$Estado}','{$DiaFinalInscripcion}','{$DiaInicio}','{$DiaFinal}','{$FechaHora}',{$Cantidad},{$Cantidad},'{$Descripcion}')";
        xSQL($Sql, $vConex);
        $CodAlmacen = mysql_insert_id($vConex);
    } else {

        //Genera Codigo Unico de almacen
        $Sql = "UPDATE almacen
                SET Estado='{$Estado}'
                WHERE AlmacenCod={$CodigoAlmacen}";
        xSQL($Sql, $vConex);
        $CodAlmacen = $CodigoAlmacen;
    }

    //Llama a las transacciones y almacenes
    $sql = "SELECT codigo FROM almacen_transaccion
        WHERE entidad='{$entidadCreadora}' AND nombre='ingreso-produccion' ";
    $rg = fetch($sql);
    $TransaccionIngProduccion = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_nombre
        WHERE entidad='{$entidadCreadora}' AND nombre='productos-desarrollo' ";
    $rg = fetch($sql);
    $AlmacenDesarrollo = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_transaccion
        WHERE entidad='{$entidadCreadora}' AND nombre='transferencia-productos-terminados' ";
    $rg = fetch($sql);
    $TransaccionIngSalidaProduccionAPT = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_transaccion
        WHERE entidad='{$entidadCreadora}' AND nombre='ingreso-produccion-productos-terminados' ";
    $rg = fetch($sql);
    $TransaccionIngAPT = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_nombre
        WHERE entidad='{$entidadCreadora}' AND nombre='productos-terminados' ";
    $rg = fetch($sql);
    $AlmacenAPT = $rg["codigo"];


    if ($TipoIngreso == "Desarrollo") {

        //Genera Movimiento de Ingreso a produccion
        $Sql = "INSERT INTO almacen_movimiento(almacen_id,almacen_transaccion_id ,documento,usuario, cantidad, almacen_nombre_id,descripcion, fecha_registro ,UNegocio,Escuela,Usuario_Entidad)
            VALUES({$CodAlmacen},{$TransaccionIngProduccion},'','{$entidadCreadora}',{$Cantidad},{$AlmacenDesarrollo},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario});";
        xSQL($Sql, $vConex);

    }

    if ($TipoIngreso == "Abrir") {

        $Sql = "INSERT INTO almacen_movimiento (almacen_id,almacen_transaccion_id,documento,usuario,cantidad,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela,Usuario_Entidad)
            VALUES ({$CodAlmacen},{$TransaccionIngSalidaProduccionAPT},'','{$entidadCreadora}',{$Cantidad},{$AlmacenDesarrollo},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario})";

        xSQL($Sql, $vConex);

        $Sql = "INSERT INTO almacen_movimiento(almacen_id,almacen_transaccion_id,documento,usuario,cantidad,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela,Usuario_Entidad)
            VALUES({$CodAlmacen},{$TransaccionIngAPT},'','{$entidadCreadora}',{$Cantidad},{$AlmacenAPT},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario})";

        xSQL($Sql, $vConex);

    }

    if ($TipoIngreso == "Desarrollo-Abierto") {

        //Genera Movimiento de Ingreso a produccion  y a productos terminados
        $Sql = " INSERT INTO almacen_movimiento (
                            almacen_id, almacen_transaccion_id ,documento, usuario,cantidad
                           ,almacen_nombre_id, descripcion ,fecha_registro ,UNegocio ,Escuela
                           ,Usuario_Entidad
                           ) VALUES (
                                   " . $CodAlmacen . "," . $TransaccionIngProduccion . ",'','" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenDesarrollo . ",'','" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . ",
                                   " . $Codigo_Entidad_Usuario . "
                           ) ";


        xSQL($Sql, $vConex);

        $Sql = " INSERT INTO almacen_movimiento (
                            almacen_id,almacen_transaccion_id,documento,usuario,cantidad
                           ,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela
                           ,Usuario_Entidad
                           ) VALUES (
                                   " . $CodAlmacen . "," . $TransaccionIngSalidaProduccionAPT . ",'','" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenDesarrollo . ",'','" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . "
                                   ," . $Codigo_Entidad_Usuario . "
                                                            ) ";
        #W($Sql."<BR>");
        xSQL($Sql, $vConex);

        $Sql = " INSERT INTO almacen_movimiento (
                            almacen_id, almacen_transaccion_id, documento, usuario, cantidad
                           ,almacen_nombre_id ,descripcion ,fecha_registro ,UNegocio ,Escuela
                           ,Usuario_Entidad
                           ) VALUES (
                                   " . $CodAlmacen . " ," . $TransaccionIngAPT . " ,''	,'" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenAPT . ",'' ,'" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . "
                                   ," . $Codigo_Entidad_Usuario . "
                           ) ";
        #W($Sql."<BR>");
        xSQL($Sql, $vConex);

    }
    return $CodAlmacen;
}

function Proc_Inpacto_TabAlmacen_Publicacion_Entrevista($dataInsert, $vConex) {
    $CodigoArticulo = $dataInsert["CodigoArticulo"];
    $CodigoAlmacen = $dataInsert["CodigoAlmacen"];
    $Estado = $dataInsert["Estado"];
    $TipoProducto = $dataInsert["TipoProducto"];
    $TipoIngreso = $dataInsert["TipoIngreso"];
    $FechaHora = $dataInsert["FechaHora"];
    $entidadCreadora = $dataInsert["entidadCreadora"];
    $UnidadNegocio_ID = $dataInsert["UnidadNegocio_ID"];
    $Codigo_Entidad_Usuario = $dataInsert["Codigo_Entidad_Usuario"];
    $Escuela_ID = $dataInsert["Escuela_ID"];
    $Descripcion = $dataInsert["Descripcion"];

    $Q_AR = "SELECT
            IdArticulo,
            Fabricante,
            Entidad,
            TipoProducto,
            Producto,
            ProductoFab,
            Categoria,
            DiaFinalInscripcion,
            DiaInicio,
            DiaFinal,
            Cantidad
            FROM articulos
            WHERE IdArticulo={$CodigoArticulo}";

    $ObjAR = fetchOne($Q_AR, $vConex);
    $IdArticulo = $ObjAR->IdArticulo;
    $Entidad = $ObjAR->Entidad;
    $TipoProducto = $ObjAR->TipoProducto;
    $Producto = $ObjAR->Producto;
    $Categoria = $ObjAR->Categoria;
    $DiaFinalInscripcion = $ObjAR->DiaFinalInscripcion;
    $DiaInicio = $ObjAR->DiaInicio;
    $DiaFinal = $ObjAR->DiaFinal;
    $Cantidad = $ObjAR->Cantidad;

    if(empty($CodigoAlmacen) || $CodigoAlmacen==0 || !$CodigoAlmacen){

        //Genera Codigo Unico de almacen
        $Sql = "INSERT INTO almacen(Origen,Entidad,Producto,TipoProducto,Estado,DiaFinalInscripcion,DiaInicio,DiaFinal,FechReg,cantidad, stock ,Descripcion)
                VALUES('{$Entidad}','{$entidadCreadora}','{$Producto}','{$TipoProducto}','{$Estado}','{$DiaFinalInscripcion}','{$DiaInicio}','{$DiaFinal}','{$FechaHora}',{$Cantidad},{$Cantidad},'{$Descripcion}')";
        xSQL($Sql, $vConex);
        $CodAlmacen = mysql_insert_id($vConex);
    } else {

        //Genera Codigo Unico de almacen
        $Sql = "UPDATE almacen
                SET Estado='{$Estado}'
                WHERE AlmacenCod={$CodigoAlmacen}";
        xSQL($Sql, $vConex);
        $CodAlmacen = $CodigoAlmacen;
    }

    //Llama a las transacciones y almacenes
    $sql = "SELECT codigo FROM almacen_transaccion
        WHERE entidad='{$entidadCreadora}' AND nombre='ingreso-produccion' ";
    $rg = fetch($sql);
    $TransaccionIngProduccion = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_nombre
        WHERE entidad='{$entidadCreadora}' AND nombre='productos-desarrollo' ";
    $rg = fetch($sql);
    $AlmacenDesarrollo = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_transaccion
        WHERE entidad='{$entidadCreadora}' AND nombre='transferencia-productos-terminados' ";
    $rg = fetch($sql);
    $TransaccionIngSalidaProduccionAPT = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_transaccion
        WHERE entidad='{$entidadCreadora}' AND nombre='ingreso-produccion-productos-terminados' ";
    $rg = fetch($sql);
    $TransaccionIngAPT = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_nombre
        WHERE entidad='{$entidadCreadora}' AND nombre='productos-terminados' ";
    $rg = fetch($sql);
    $AlmacenAPT = $rg["codigo"];


    if ($TipoIngreso == "Desarrollo") {

        //Genera Movimiento de Ingreso a produccion
        $Sql = "INSERT INTO almacen_movimiento(almacen_id,almacen_transaccion_id ,documento,usuario, cantidad, almacen_nombre_id,descripcion, fecha_registro ,UNegocio,Escuela,Usuario_Entidad)
            VALUES({$CodAlmacen},{$TransaccionIngProduccion},'','{$entidadCreadora}',{$Cantidad},{$AlmacenDesarrollo},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario});";
        xSQL($Sql, $vConex);

    }

    if ($TipoIngreso == "Abrir") {

        $Sql = "INSERT INTO almacen_movimiento (almacen_id,almacen_transaccion_id,documento,usuario,cantidad,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela,Usuario_Entidad)
            VALUES ({$CodAlmacen},{$TransaccionIngSalidaProduccionAPT},'','{$entidadCreadora}',{$Cantidad},{$AlmacenDesarrollo},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario})";

        xSQL($Sql, $vConex);

        $Sql = "INSERT INTO almacen_movimiento(almacen_id,almacen_transaccion_id,documento,usuario,cantidad,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela,Usuario_Entidad)
            VALUES({$CodAlmacen},{$TransaccionIngAPT},'','{$entidadCreadora}',{$Cantidad},{$AlmacenAPT},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario})";

        xSQL($Sql, $vConex);

    }

    if ($TipoIngreso == "Desarrollo-Abierto") {

        //Genera Movimiento de Ingreso a produccion  y a productos terminados
        $Sql = " INSERT INTO almacen_movimiento (
                            almacen_id, almacen_transaccion_id ,documento, usuario,cantidad
                           ,almacen_nombre_id, descripcion ,fecha_registro ,UNegocio ,Escuela
                           ,Usuario_Entidad
                           ) VALUES (
                                   " . $CodAlmacen . "," . $TransaccionIngProduccion . ",'','" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenDesarrollo . ",'','" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . ",
                                   " . $Codigo_Entidad_Usuario . "
                           ) ";


        xSQL($Sql, $vConex);

        $Sql = " INSERT INTO almacen_movimiento (
                            almacen_id,almacen_transaccion_id,documento,usuario,cantidad
                           ,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela
                           ,Usuario_Entidad
                           ) VALUES (
                                   " . $CodAlmacen . "," . $TransaccionIngSalidaProduccionAPT . ",'','" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenDesarrollo . ",'','" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . "
                                   ," . $Codigo_Entidad_Usuario . "
                                                            ) ";
        #W($Sql."<BR>");
        xSQL($Sql, $vConex);

        $Sql = " INSERT INTO almacen_movimiento (
                            almacen_id, almacen_transaccion_id, documento, usuario, cantidad
                           ,almacen_nombre_id ,descripcion ,fecha_registro ,UNegocio ,Escuela
                           ,Usuario_Entidad
                           ) VALUES (
                                   " . $CodAlmacen . " ," . $TransaccionIngAPT . " ,''	,'" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenAPT . ",'' ,'" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . "
                                   ," . $Codigo_Entidad_Usuario . "
                           ) ";
        #W($Sql."<BR>");
        xSQL($Sql, $vConex);

    }
    return $CodAlmacen;
}
function Proc_Inpacto_TabAlmacen_Publicacion_reunion($dataInsert, $vConex) {
    $CodigoArticulo = $dataInsert["CodigoArticulo"];
    $CodigoAlmacen = $dataInsert["CodigoAlmacen"];
    $Estado = $dataInsert["Estado"];
    $TipoProducto = $dataInsert["TipoProducto"];
    $TipoIngreso = $dataInsert["TipoIngreso"];
    $FechaHora = $dataInsert["FechaHora"];
    $entidadCreadora = $dataInsert["entidadCreadora"];
    $UnidadNegocio_ID = $dataInsert["UnidadNegocio_ID"];
    $Codigo_Entidad_Usuario = $dataInsert["Codigo_Entidad_Usuario"];
    $Escuela_ID = $dataInsert["Escuela_ID"];
    $Descripcion = $dataInsert["Descripcion"];

    $Q_AR = "SELECT
            IdArticulo,
            Fabricante,
            Entidad,
            TipoProducto,
            Producto,
            ProductoFab,
            Categoria,
            DiaFinalInscripcion,
            DiaInicio,
            DiaFinal,
            Cantidad
            FROM articulos
            WHERE IdArticulo={$CodigoArticulo}";

    $ObjAR = fetchOne($Q_AR, $vConex);
    $IdArticulo = $ObjAR->IdArticulo;
    $Entidad = $ObjAR->Entidad;
    $TipoProducto = $ObjAR->TipoProducto;
    $Producto = $ObjAR->Producto;
    $Categoria = $ObjAR->Categoria;
    $DiaFinalInscripcion = $ObjAR->DiaFinalInscripcion;
    $DiaInicio = $ObjAR->DiaInicio;
    $DiaFinal = $ObjAR->DiaFinal;
    $Cantidad = $ObjAR->Cantidad;

    if(empty($CodigoAlmacen) || $CodigoAlmacen==0 || !$CodigoAlmacen){

        //Genera Codigo Unico de almacen
        $Sql = "INSERT INTO almacen(Origen,Entidad,Producto,TipoProducto,Estado,DiaFinalInscripcion,DiaInicio,DiaFinal,FechReg,cantidad, stock ,Descripcion)
                VALUES('{$Entidad}','{$entidadCreadora}','{$Producto}','{$TipoProducto}','{$Estado}','{$DiaFinalInscripcion}','{$DiaInicio}','{$DiaFinal}','{$FechaHora}',{$Cantidad},{$Cantidad},'{$Descripcion}')";
        xSQL($Sql, $vConex);
        $CodAlmacen = mysql_insert_id($vConex);
    } else {

        //Genera Codigo Unico de almacen
        $Sql = "UPDATE almacen
                SET Estado='{$Estado}'
                WHERE AlmacenCod={$CodigoAlmacen}";
        xSQL($Sql, $vConex);
        $CodAlmacen = $CodigoAlmacen;
    }

    //Llama a las transacciones y almacenes
    $sql = "SELECT codigo FROM almacen_transaccion
        WHERE entidad='{$entidadCreadora}' AND nombre='ingreso-produccion' ";
    $rg = fetch($sql);
    $TransaccionIngProduccion = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_nombre
        WHERE entidad='{$entidadCreadora}' AND nombre='productos-desarrollo' ";
    $rg = fetch($sql);
    $AlmacenDesarrollo = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_transaccion
        WHERE entidad='{$entidadCreadora}' AND nombre='transferencia-productos-terminados' ";
    $rg = fetch($sql);
    $TransaccionIngSalidaProduccionAPT = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_transaccion
        WHERE entidad='{$entidadCreadora}' AND nombre='ingreso-produccion-productos-terminados' ";
    $rg = fetch($sql);
    $TransaccionIngAPT = $rg["codigo"];

    $sql = "SELECT codigo FROM almacen_nombre
        WHERE entidad='{$entidadCreadora}' AND nombre='productos-terminados' ";
    $rg = fetch($sql);
    $AlmacenAPT = $rg["codigo"];


    if ($TipoIngreso == "Desarrollo") {

        //Genera Movimiento de Ingreso a produccion
        $Sql = "INSERT INTO almacen_movimiento(almacen_id,almacen_transaccion_id ,documento,usuario, cantidad, almacen_nombre_id,descripcion, fecha_registro ,UNegocio,Escuela,Usuario_Entidad)
            VALUES({$CodAlmacen},{$TransaccionIngProduccion},'','{$entidadCreadora}',{$Cantidad},{$AlmacenDesarrollo},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario});";
        xSQL($Sql, $vConex);

    }

    if ($TipoIngreso == "Abrir") {

        $Sql = "INSERT INTO almacen_movimiento (almacen_id,almacen_transaccion_id,documento,usuario,cantidad,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela,Usuario_Entidad)
            VALUES ({$CodAlmacen},{$TransaccionIngSalidaProduccionAPT},'','{$entidadCreadora}',{$Cantidad},{$AlmacenDesarrollo},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario})";

        xSQL($Sql, $vConex);

        $Sql = "INSERT INTO almacen_movimiento(almacen_id,almacen_transaccion_id,documento,usuario,cantidad,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela,Usuario_Entidad)
            VALUES({$CodAlmacen},{$TransaccionIngAPT},'','{$entidadCreadora}',{$Cantidad},{$AlmacenAPT},'','{$FechaHora}',{$UnidadNegocio_ID},{$Escuela_ID},{$Codigo_Entidad_Usuario})";

        xSQL($Sql, $vConex);

    }

    if ($TipoIngreso == "Desarrollo-Abierto") {

        //Genera Movimiento de Ingreso a produccion  y a productos terminados
        $Sql = " INSERT INTO almacen_movimiento (
                            almacen_id, almacen_transaccion_id ,documento, usuario,cantidad
                           ,almacen_nombre_id, descripcion ,fecha_registro ,UNegocio ,Escuela
                           ,Usuario_Entidad
                           ) VALUES (
                                   " . $CodAlmacen . "," . $TransaccionIngProduccion . ",'','" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenDesarrollo . ",'','" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . ",
                                   " . $Codigo_Entidad_Usuario . "
                           ) ";


        xSQL($Sql, $vConex);

        $Sql = " INSERT INTO almacen_movimiento (
                            almacen_id,almacen_transaccion_id,documento,usuario,cantidad
                           ,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela
                           ,Usuario_Entidad
                           ) VALUES (
                                   " . $CodAlmacen . "," . $TransaccionIngSalidaProduccionAPT . ",'','" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenDesarrollo . ",'','" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . "
                                   ," . $Codigo_Entidad_Usuario . "
                                                            ) ";
        #W($Sql."<BR>");
        xSQL($Sql, $vConex);

        $Sql = " INSERT INTO almacen_movimiento (
                            almacen_id, almacen_transaccion_id, documento, usuario, cantidad
                           ,almacen_nombre_id ,descripcion ,fecha_registro ,UNegocio ,Escuela
                           ,Usuario_Entidad
                           ) VALUES (
                                   " . $CodAlmacen . " ," . $TransaccionIngAPT . " ,''	,'" . $entidadCreadora . "'," . $Cantidad . "
                                   ," . $AlmacenAPT . ",'' ,'" . $FechaHora . "'," . $UnidadNegocio_ID . "," . $Escuela_ID . "
                                   ," . $Codigo_Entidad_Usuario . "
                           ) ";
        #W($Sql."<BR>");
        xSQL($Sql, $vConex);

    }
    return $CodAlmacen;
}


function Proc_Actuliza_Lista($dataInsert, $vConex) {
    $CodigoAlmacen=$dataInsert["CodigoAlmacen"];
    $CodigoLDT=$dataInsert["CodigoLDT"];
    $Estado=$dataInsert["Estado"];

    $Sql="UPDATE lista_trabajo_det 
        SET CodigoAlmacen={$CodigoAlmacen},
        Estado='{$Estado}'
        WHERE Codigo={$CodigoLDT}";
    // W($Sql);
    xSQL($Sql,$vConex);
}

function Process_Lista($dataInsert, $vConex) {
    //#// Proceso que genera la lista de trabajo
    $Email_Entidad_Usuario = $dataInsert['Email_Entidad_Usuario'];
    $entidadCreadora 	   = $dataInsert['entidadCreadora'];
    $UnidadNegocio_ID 	   = $dataInsert['UnidadNegocio_ID'];
    $Escuela_ID            = $dataInsert['Escuela_ID'];
    $codigo                = $dataInsert['codigo'];
    $TipoProducto          = $dataInsert['TipoProducto'];
    $FechaHora             = $dataInsert['FechaHora'];
    $Cod_responsable       = $dataInsert['Cod_responsable'];

    $sql = "SELECT Codigo    FROM lista_trabajo 
			WHERE  Responsable =  '{$Cod_responsable}' 
			AND    Empresa     =  '{$entidadCreadora}'  
			AND    Estado      =  'Trabajando'";

    $rg = fetch($sql);
    $CodigoLista = $rg["Codigo"];

    #insertamos el detalle en la lista de trabajo
    $sql4 = " INSERT INTO lista_trabajo_det(Fecha,Lista,TipoProducto,CodigoProducto, Estado)
		VALUES('" . date('Y-m-d H:i:s') . "'," . $CodigoLista . ",'" . $TipoProducto . "','" . $codigo . "','Activo')";

    xSQL($sql4, $vConex);
}

function LogProductos($dataInsert, $vConex) {
    $codigo = $dataInsert['codigo'];
    $TipoProducto = $dataInsert['TipoProducto'];
    $FechaHora = $dataInsert['FechaHora'];
    $Tipo = $dataInsert['Tipo'];

    $sql2 = "INSERT INTO log_cursos(FechaReg,Curso,UsuarioEntidad,Empresa,Tipo)
		VALUES('" . $FechaHora . "','" . $codigo . "','" . $Email_Entidad_Usuario . "','" . $entidadCreadora . "','" . $Tipo . "')";
    xSQL($sql2, $vConex);
}

function Log_programas($dataInsert, $vConex) {
    $codigo = $dataInsert['codigo'];
    $TipoProducto = $dataInsert['TipoProducto'];
    $FechaHora = $dataInsert['FechaHora'];
    $Tipo = $dataInsert['Tipo'];

    $sql2 = "INSERT INTO log_programas(FechaReg,Programa,UsuarioEntidad,Empresa,Tipo)
		VALUES('" . $FechaHora . "','" . $codigo . "','" . $Email_Entidad_Usuario . "','" . $entidadCreadora . "','" . $Tipo . "')";
    xSQL($sql2, $vConex);
}

//Tipo Nivel Programa : retorna Tipo de Programa y Nivel de Matricula
function TN_Programa($AlmacenPrograma) {
    $Q_P = "SELECT 
            AR.ProductoFab,
            AL.NivelMatricula,
            PR.GrupoProgrId,
            TP.Descripcion AS 'TipoPrograma'
            FROM almacen AL 
            INNER  JOIN articulos AR ON AL.Producto=AR.Producto  
            INNER  JOIN programas PR ON AR.ProductoFab=PR.CodPrograma  
            INNER JOIN tipoprograma TP ON PR.TipoPrograma=TP.IDTipoPrograma
            WHERE  AL.AlmacenCod='{$AlmacenPrograma}'";

  
    # ProductoFab : El codigo de Programa de la tabla PROGRAMAS
    # NivelMatricula : EL nivel de matricula definido para el Programa en la tabla ALMACEN [Curso,Programa,Modulo]
    # GrupoProgId : El alcance del Programa
    # TipoPrograma :Tipo de Programa (Curso,Diplomado,Seminario,Extendido)
    $ObjP = fetchOne($Q_P);
    $TP = $ObjP->TipoPrograma;
    $NM = $ObjP->NivelMatricula;
    if (!$NM) {
//        W(Msg("ERROR: El Nivel de Matricula no existe en este Programa","E"));
        return false;
    } else {
        $return_Obj = new stdClass();
        $return_Obj->TP = $TP;
        $return_Obj->NM = $NM;

        return $return_Obj;
    }
}

function T_Programa($AlmacenPrograma) {
    $Q_P = "SELECT 
            AR.ProductoFab,
            AL.NivelMatricula,
            PR.GrupoProgrId,
            TP.Descripcion AS 'TipoPrograma'
            FROM almacen AL 
            INNER  JOIN articulos AR ON AL.Producto=AR.Producto  
            INNER  JOIN programas PR ON AR.ProductoFab=PR.CodPrograma  
            INNER JOIN tipoprograma TP ON PR.TipoPrograma=TP.IDTipoPrograma
            WHERE  AL.AlmacenCod='{$AlmacenPrograma}'";
    # ProductoFab : El codigo de Programa de la tabla PROGRAMAS
    # NivelMatricula : EL nivel de matricula definido para el Programa en la tabla ALMACEN [Curso,Programa,Modulo]
    # GrupoProgId : El alcance del Programa
    # TipoPrograma :Tipo de Programa (Curso,Diplomado,Seminario,Extendido)
    $ObjP = fetchOne($Q_P);
    $TP = $ObjP->TipoPrograma;

    if (!$TP) {
//        W(Msg("ERROR: El Nivel de Matricula no existe en este Programa","E"));
        return false;
    } else {
        $return_Obj = new stdClass();
        $return_Obj->TP = $TP;

        return $return_Obj;
    }
}
