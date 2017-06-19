<?php

function PresentacionPrograma($CodigoAlmacen,$alumnos){

    $sql = "SELECT ProductoFab FROM almacen  AL
            INNER  JOIN articulos AR ON AL.Producto = AR.Producto
            WHERE AL.AlmacenCod = {$CodigoAlmacen} ";
    $rg = fetch($sql);
    $CodPrograma = $rg["ProductoFab"];

    foreach($alumnos as $Alumno) {

        $dataInsert = array(
            'CodigoPrograma'        => $CodPrograma,
            'CodigoProgramaAlmacen' => $CodigoAlmacen,
            'Estado'                => 'Activo',
            'Alumno'                => $Alumno);
        insert("programapresentaciones", $dataInsert);

        $alumno = str_replace("Alumno","",$Alumno);
        update('usuario_entidad',[ "GuiaEstudio" => "Si" ],[ "Usuario" => $alumno ]);

    }

}