<?php
define("PREGUNTA_SELECTIVA", 1);
define("PREGUNTA_MULTI_SELECTIVA", 2);
define("PREGUNTA_ABIERTA", 3);
define("PREGUNTA_SELECTIVA_RELACIONAL", 4);

//TIPO DE RECURSOS DE EVALUACION
define("RE_EXAMEN", 1);
define("RE_CUESTIONARIO", 2);
define("RE_VALOTARIO", 3);
define("RE_CONTROL_LECTURA", 4);
define("RE_ARCHIVO_ADJUNTO", 5);
define("RE_TEST", 6);
define("RE_CHAT", 7);
define("RE_FORO", 8);
define("RE_VCONFERENCIA", 9);

$entidadCreadora = isset($_SESSION['UserEmpresa']['string']) ? $_SESSION['UserEmpresa']['string'] : '';
$ipHost = siteUrl();

function MatriculaAlumno($vConex,$data){
    global $ipHost;

    $resp=GeneraMatricula($data,$vConex);

    if($resp["success"]){
        updtIntroTransac($data['productoId'],$data['alumno'], $vConex);

        //updtAnuncioTransac($data['productoId'],$data['alumno'],$vConex);
        // $rec_eval=update_recurso_eval($data['productoId'],$data['alumno'],$vConex);
        if( $data['EnviaCorreo'] == 1 ){
            enviar_mail($data,$vConex);
		}else{
            $auxMsg="no envia mail";
            $style="background-color: #00B06C;
                        padding: 0.8em;
                        border: 1px solid #fff;
                        font-family: segoeuil;
                        font-size: 0.7em;
                        color: #fff;
                    ";
		}
    }else{
        if( $data['EnviaCorreo'] == 1 ){
            enviar_mail($data,$vConex);
        }else{
            $auxMsg="no envia mail";
            $style="background-color: #00B06C;
                        padding: 0.8em;
                        border: 1px solid #fff;
                        font-family: segoeuil;
                        font-size: 0.7em;
                        color: #fff;
                    ";
        }
    }
    return $resp;
}


function MatriculaEntrevista($vConex,$data){
    #W("genera matricula");
    $resp=GeneraMatriculaEntrevista($data,$vConex);
    if($resp["success"]){
        // updtIntroTransac($data['productoId'],$data['alumno'], $vConex);
        // updtAnuncioTransac($data['productoId'],$data['alumno'],$vConex);
        // $rec_eval = update_recurso_eval($data['productoId'], $data['alumno'], $vConex);
        $ema = enviar_mail_entrevista($data, $vConex);
    }

    return $resp;
}

function MatriculaReunion($vConex, $data) {
    $resp = GeneraMatriculaReunion($data, $vConex);
    if ($resp["success"]) {
        updtIntroTransac($data['productoId'], $data['alumno'], $vConex);
        updtAnuncioTransac($data['productoId'], $data['alumno'], $vConex);
        // $rec_eval = update_recurso_eval($data['productoId'], $data['alumno'], $vConex);
        $ema = enviar_mail($data, $vConex);
    }
    return $resp;
}

function GeneraMatriculaEntrevista($data, $link_identifier) {

    $AuxEM = false;
    $return = array(
        "success" => false,
        "msg" => "Variables CodAlmacenPN ó CodAlmacenSN ó CodAlmacenTN no estan definidas correctamente"
    );
    /*
      AuxEM : Resultado auxiliar MYSQL para saber el ESTADO MATRICULA ( si no entra en las condiciones es false )
      PN : Campo CodAlmacenPN de la tabla matriculas
      SN : Campo CodAlmacenSN de la tabla matriculas
      TN : Campo CodAlmacenTN de la tabla matriculas
     */

    $Q_P = "SELECT AR.ProductoFab, AL.NivelMatricula, AL.AlmacenCod
                 FROM almacen AL
                 INNER JOIN articulos AR ON AL.Producto = AR.Producto
                 WHERE  AL.AlmacenCod='{$data['productoId']}'";

    # ProductoFab : El codigo de Programa de la tabla PROGRAMAS
    # NivelMatricula : EL nivel de matricula definido para el Programa en la tabla ALMACEN [Curso,Programa,Modulo]
    # GrupoProgId : El alcance del Programa
    # TipoPrograma :Tipo de Programa (Curso,Diplomado,Seminario,Extendido)
    #W($Q_P."<br>");
    $ObjP = fetchOne($Q_P, $link_identifier);
    $TP = "Entrevista"; # $ObjP->TipoPrograma;
    $NM = "Entrevista"; #$ObjP->NivelMatricula;

    $data = (array) $data;
    $Proveedor = $data['proveedor'];
    $CodigoProducto = $data['productoId'];
    $alumno = $data['alumno'];
    $FacturasCab = $data['FacturasCab'];
    $FacturasDet = $data['FacturasDet'];
    $CodAlmacenPN = $data['productoId']; #$data["CodAlmacenPN"];
    $CodAlmacenSN = $data["CodAlmacenSN"];
    $CodAlmacenTN = $data["CodAlmacenTN"];
    $TipoAccesoMatricula = $NM;
    $usuarioId = $alumno;

    /*
      TP : Tipo Programa

      Curso -> NM: Programa
      Diplomado -> NM: Curso,Programa
      Seminario -> NM: Curso,Programa
      Extendido -> NM: Modulo,Programa
     */

    if ($NM) {

        switch ($NM) {
            case "Entrevista":
                # W($TP.'   '.$CodAlmacenSN);
                if ($TP == "Entrevista" || $TP == "Diplomado" || $TP == "Seminario") {
                    if (!$CodAlmacenPN) {
                        return $return;
                    }
                    $sqlSb = "SELECT Estado
                    FROM matriculas
                    WHERE Cliente='{$usuarioId}'
                    AND Producto='{$CodigoProducto}'
                    AND CodAlmacenPN={$CodAlmacenPN};";
                    #W($sqlSb);

                    $Q_PRG = "SELECT PRG.Titulo
                        FROM almacen AL
                        INNER JOIN articulos AR ON AL.Producto=AR.Producto
                        INNER JOIN programas PRG ON AR.ProductoFab=PRG.CodPrograma
                        AND AL.AlmacenCod={$CodAlmacenPN};";

                    $auxEstructura = 2;
                    $AuxEM = fetchOne($sqlSb, $link_identifier);
                    $ObjPRG = fetchOne($Q_PRG, $link_identifier);
                    if ($AuxEM) {
                        $auxMsg = "El alumno " . array_shift(explode("Alumno", $usuarioId)) . " ya esta matriculado en el Programa {$ObjPRG->Titulo} <br>";
                    } else {
                        $auxMsg = "El alumno " . array_shift(explode("Alumno", $usuarioId)) . " fue matriculado en el Programa {$ObjPRG->Titulo} &check; <br>";
                    }
                }
                break;
        }
    } else {
        W(Msg("ERROR: Falta definir un Nivel de Matricula al Programa", "E"));
    }

    $EstadoMatriculado = $AuxEM;
    if (!$EstadoMatriculado) {
        # REGISTRO EN ALMACEN_ENTIDAD
        $CodAlmacenPNivel = $CodAlmacenPN;
        $CodAlmacenSNivel = $CodAlmacenSN;
        $CodAlmacenTNivel = $CodAlmacenTN;
        $Entidad = $alumno;
        $FechaHoraRegistro = date("Y-m-d");
        $EstadoSesionAPP = "Modificado";
        $EstadoSesionData = "Modificado";
        $AlcanceEstructura = "1";
        $auxEstructura;
        $CodAlmacenContenedor = $CodigoProducto;

        # data_AE : data Almacen Entidad
        $data_AE = array(
            "CodAlmacenPNivel" => $CodAlmacenPNivel,
            "CodAlmacenSNivel" => $CodAlmacenSNivel,
            "CodAlmacenTNivel" => $CodAlmacenTNivel,
            "Entidad" => $Entidad,
            "FechaHoraRegistro" => $FechaHoraRegistro,
            "EstadoSesionAPP" => $EstadoSesionAPP,
            "EstadoSesionData" => $EstadoSesionData,
            "AlcanceEstructura" => $AlcanceEstructura,
            "CodAlmacenContenedor" => $CodAlmacenContenedor
        );
        # data_Matricula
        $tabla = array(
            'name' => 'matriculas',
            'alias' => 'Matricula'
        );
        $data = array(
            'NumeroOperacion' => '',
            'IdFacturasCab' => $FacturasCab,
            'IdFacturasDet' => $FacturasDet,
            'Producto' => $CodigoProducto,
            'Cliente' => $alumno,
            'FechaInscripcion' => date("Y-m-d"),
            'Estado' => 'Matriculado',
            'Entidad' => $Proveedor,
            'FechReg' => date("Y-m-d"),
            'Proveedor' => $Proveedor,
            "CodAlmacenPN" => $CodigoProducto,
            "CodAlmacenSN" => $CodAlmacenSN,
            "CodAlmacenTN" => $CodAlmacenTN,
            "TipoAccesoMatricula" => $TipoAccesoMatricula
        );

        $codigo = array(
            'name' => 'IdMatricula',
            'prefijo' => '',
        );

        # REGISTRO PARA almacen_entidad
        insert("almacen_entidad", $data_AE);

        # REGISTRO PARA matriculas
        insertCorrelativo($tabla, $data, $codigo, $link_identifier);

        # FIN REGISTRO EN ALMACEN_ENTIDAD
        # REGISTRANDO MOVIMIENTOS DE ALMACEN
        $sqlTransaccion = " SELECT codigo,descripcion,tipo
                            FROM almacen_transaccion
                            WHERE entidad='{$Proveedor}'
                            AND nombre='matricula-venta'
                            LIMIT 1";

        $sqlAlmacenNombre = "SELECT codigo,descripcion
                            FROM almacen_nombre
                            WHERE entidad='{$Proveedor}'
                            AND nombre='productos-terminados'
                            LIMIT 1";

        $almacenTransaccion = fetchOne($sqlTransaccion);
        $almacenNombre = fetchOne($sqlAlmacenNombre);

        $insertData = array();
        $insertData['almacen_id'] = $CodigoProducto;
        $insertData['almacen_transaccion_id'] = $almacenTransaccion->codigo;
        $insertData['usuario'] = $Proveedor;
        $insertData['cantidad'] = 1;
        $insertData['almacen_nombre_id'] = $almacenNombre->codigo;
        $insertData['descripcion'] = '';
        $almacenMovimientoId = insert('almacen_movimiento', $insertData);

        $sqlAlmacen = "SELECT AlmacenCod, stock, cantidad
                    FROM almacen
                    WHERE AlmacenCod={$CodigoProducto} AND Entidad = '{$Proveedor}'
                    LIMIT 1";
        $almacen = fetchOne($sqlAlmacen);

        if (!empty($almacen)) {
            $updateData['stock'] = $almacen->stock - 1;
            update('almacen', $updateData, array('AlmacenCod' => $almacen->AlmacenCod));
        }

        $return["success"] = true;
    }

    $return["msg"] = $auxMsg;

    return $return;
}

function GeneraMatriculaReunion($data, $link_identifier) {

    $AuxEM = false;
    $return = array(
        "success" => false,
        "msg" => "Variables CodAlmacenPN ó CodAlmacenSN ó CodAlmacenTN no estan definidas correctamente"
    );
    /*
      AuxEM : Resultado auxiliar MYSQL para saber el ESTADO MATRICULA ( si no entra en las condiciones es false )
      PN : Campo CodAlmacenPN de la tabla matriculas
      SN : Campo CodAlmacenSN de la tabla matriculas
      TN : Campo CodAlmacenTN de la tabla matriculas
     */

    $Q_P = "SELECT AR.ProductoFab, AL.NivelMatricula, AL.AlmacenCod
                 FROM almacen AL
                 INNER JOIN articulos AR ON AL.Producto = AR.Producto
                 WHERE  AL.AlmacenCod='{$data['productoId']}'";

    # ProductoFab : El codigo de Programa de la tabla PROGRAMAS
    # NivelMatricula : EL nivel de matricula definido para el Programa en la tabla ALMACEN [Curso,Programa,Modulo]
    # GrupoProgId : El alcance del Programa
    # TipoPrograma :Tipo de Programa (Curso,Diplomado,Seminario,Extendido)
    #W($Q_P."<br>");
    $ObjP = fetchOne($Q_P, $link_identifier);
    $TP = "Reunion"; # $ObjP->TipoPrograma;
    $NM = "Reunion"; #$ObjP->NivelMatricula;

    $data = (array) $data;
    $Proveedor = $data['proveedor'];
    $CodigoProducto = $data['productoId'];
    $alumno = $data['alumno'];
    $FacturasCab = $data['FacturasCab'];
    $FacturasDet = $data['FacturasDet'];
    $CodAlmacenPN = $data['productoId']; #$data["CodAlmacenPN"];
    $CodAlmacenSN = $data["CodAlmacenSN"];
    $CodAlmacenTN = $data["CodAlmacenTN"];
    $TipoAccesoMatricula = $NM;
    $usuarioId = $alumno;

    /*
      TP : Tipo Programa

      Curso -> NM: Programa
      Diplomado -> NM: Curso,Programa
      Seminario -> NM: Curso,Programa
      Extendido -> NM: Modulo,Programa
     */

    if ($NM) {

        switch ($NM) {
            case "Reunion":
                # W($TP.'   '.$CodAlmacenSN);
                if ($TP == "Reunion" || $TP == "Diplomado" || $TP == "Seminario") {
                    if (!$CodAlmacenPN) {
                        return $return;
                    }
                    $sqlSb = "SELECT Estado
                    FROM matriculas
                    WHERE Cliente='{$usuarioId}'
                    AND Producto='{$CodigoProducto}'
                    AND CodAlmacenPN={$CodAlmacenPN};";
                    #W($sqlSb);

                    $Q_PRG = "SELECT PRG.Titulo
                        FROM almacen AL
                        INNER JOIN articulos AR ON AL.Producto=AR.Producto
                        INNER JOIN programas PRG ON AR.ProductoFab=PRG.CodPrograma
                        AND AL.AlmacenCod={$CodAlmacenPN};";

                    $auxEstructura = 2;
                    $AuxEM = fetchOne($sqlSb, $link_identifier);
                    $ObjPRG = fetchOne($Q_PRG, $link_identifier);
                    if ($AuxEM) {
                        $auxMsg = "El alumno " . array_shift(explode("Alumno", $usuarioId)) . " ya esta matriculado en el Programa {$ObjPRG->Titulo}<br>";
                    } else {
                        $auxMsg = "El alumno " . array_shift(explode("Alumno", $usuarioId)) . " fue matriculado en el Programa {$ObjPRG->Titulo} &check;<br>";
                    }
                }
                break;
        }
    } else {
        W(Msg("ERROR: Falta definir un Nivel de Matricula al Programa", "E"));
    }

    $EstadoMatriculado = $AuxEM;
    if (!$EstadoMatriculado) {
        # REGISTRO EN ALMACEN_ENTIDAD
        $CodAlmacenPNivel = $CodAlmacenPN;
        $CodAlmacenSNivel = $CodAlmacenSN;
        $CodAlmacenTNivel = $CodAlmacenTN;
        $Entidad = $alumno;
        $FechaHoraRegistro = date("Y-m-d");
        $EstadoSesionAPP = "Modificado";
        $EstadoSesionData = "Modificado";
        $AlcanceEstructura = "1";
        $auxEstructura;
        $CodAlmacenContenedor = $CodigoProducto;

        # data_AE : data Almacen Entidad
        $data_AE = array(
            "CodAlmacenPNivel" => $CodAlmacenPNivel,
            "CodAlmacenSNivel" => $CodAlmacenSNivel,
            "CodAlmacenTNivel" => $CodAlmacenTNivel,
            "Entidad" => $Entidad,
            "FechaHoraRegistro" => $FechaHoraRegistro,
            "EstadoSesionAPP" => $EstadoSesionAPP,
            "EstadoSesionData" => $EstadoSesionData,
            "AlcanceEstructura" => $AlcanceEstructura,
            "CodAlmacenContenedor" => $CodAlmacenContenedor
        );
        # data_Matricula
        $tabla = array(
            'name' => 'matriculas',
            'alias' => 'Matricula'
        );
        $data = array(
            'NumeroOperacion' => '',
            'IdFacturasCab' => $FacturasCab,
            'IdFacturasDet' => $FacturasDet,
            'Producto' => $CodigoProducto,
            'Cliente' => $alumno,
            'FechaInscripcion' => date("Y-m-d"),
            'Estado' => 'Matriculado',
            'Entidad' => $Proveedor,
            'FechReg' => date("Y-m-d"),
            'Proveedor' => $Proveedor,
            "CodAlmacenPN" => $CodigoProducto,
            "CodAlmacenSN" => $CodAlmacenSN,
            "CodAlmacenTN" => $CodAlmacenTN,
            "TipoAccesoMatricula" => $TipoAccesoMatricula
        );

        $codigo = array(
            'name' => 'IdMatricula',
            'prefijo' => '',
        );

        # REGISTRO PARA almacen_entidad
        insert("almacen_entidad", $data_AE);

        # REGISTRO PARA matriculas
        insertCorrelativo($tabla, $data, $codigo, $link_identifier);

        # FIN REGISTRO EN ALMACEN_ENTIDAD
        # REGISTRANDO MOVIMIENTOS DE ALMACEN
        $sqlTransaccion = " SELECT codigo,descripcion,tipo
                            FROM almacen_transaccion
                            WHERE entidad='{$Proveedor}'
                            AND nombre='matricula-venta'
                            LIMIT 1";

        $sqlAlmacenNombre = "SELECT codigo,descripcion
                            FROM almacen_nombre
                            WHERE entidad='{$Proveedor}'
                            AND nombre='productos-terminados'
                            LIMIT 1";

        $almacenTransaccion = fetchOne($sqlTransaccion);
        $almacenNombre = fetchOne($sqlAlmacenNombre);

        $insertData = array();
        $insertData['almacen_id'] = $CodigoProducto;
        $insertData['almacen_transaccion_id'] = $almacenTransaccion->codigo;
        $insertData['usuario'] = $Proveedor;
        $insertData['cantidad'] = 1;
        $insertData['almacen_nombre_id'] = $almacenNombre->codigo;
        $insertData['descripcion'] = '';
        $almacenMovimientoId = insert('almacen_movimiento', $insertData);

        $sqlAlmacen = "SELECT AlmacenCod, stock, cantidad
                    FROM almacen
                    WHERE AlmacenCod={$CodigoProducto} AND Entidad = '{$Proveedor}'
                    LIMIT 1";
        $almacen = fetchOne($sqlAlmacen);

        if (!empty($almacen)) {
            $updateData['stock'] = $almacen->stock - 1;
            update('almacen', $updateData, array('AlmacenCod' => $almacen->AlmacenCod));
        }

        $return["success"] = true;
    }

    $return["msg"] = $auxMsg;

    return $return;
}

function MatriculaEcomerce($vConex, $data,$pagweb="") {
    $resp = GeneraMatricula($data, $vConex);
    
    if($resp["insert"]==false){
        enviar_mail($data, $vConex,$pagweb);
    }
    return $resp;
}

function GeneraMatricula($data, $link_identifier) {
    $AuxEM = false;
    $return = array(
        "success" => false,
        "msg" => "Variables CodAlmacenPN ó CodAlmacenSN ó CodAlmacenTN no estan definidas correctamente"
    );
    /*
      AuxEM : Resultado auxiliar MYSQL para saber el ESTADO MATRICULA ( si no entra en las condiciones es false )
      PN : Campo CodAlmacenPN de la tabla matriculas
      SN : Campo CodAlmacenSN de la tabla matriculas
      TN : Campo CodAlmacenTN de la tabla matriculas
     */
    $Q_P = "SELECT 
            AR.ProductoFab,
            AL.NivelMatricula,
            PR.GrupoProgrId,
            TP.Descripcion AS 'TipoPrograma'
            FROM almacen AL 
            INNER  JOIN articulos AR ON AL.Producto = AR.Producto  
            INNER  JOIN programas PR ON AR.ProductoFab = PR.CodPrograma  
            INNER JOIN tipoprograma TP ON PR.TipoPrograma=TP.IDTipoPrograma
            WHERE  AL.AlmacenCod='{$data['productoId']}'";

    # ProductoFab : El codigo de Programa de la tabla PROGRAMAS
    # NivelMatricula : EL nivel de matricula definido para el Programa en la tabla ALMACEN [Curso,Programa,Modulo]
    # GrupoProgId : El alcance del Programa
    # TipoPrograma :Tipo de Programa (Curso,Diplomado,Seminario,Extendido)
    $ObjP = fetchOne($Q_P, $link_identifier);
    $TP = $ObjP->TipoPrograma;
    $NM = $ObjP->NivelMatricula;

    $data = (array) $data;

    $Proveedor = $data['proveedor'];
    $CodigoProducto = $data['productoId'];
    $alumno = $data['alumno'];
    $usuarioId = $alumno;
    $TipoAccesoMatricula = $NM;
    if ($data['tipoarticulo'] == 'Ebook' || $data['tipoarticulo'] == 'Revista') {
        $NM = "Ecomerce";
        $tipoproducto = $data['tipoarticulo'];
        $TipoAccesoMatricula = $tipoproducto;
        $CodAlmacenPN = $CodigoProducto;
    } else if ($data['tipoarticulo'] == 'Curso' || $data['tipoarticulo'] == 'Diplomado' || $data['tipoarticulo'] == 'Seminario') {
        $CodAlmacenSN = $data["productoId"];
    } else {
        $FacturasCab = $data['FacturasCab'];
        $FacturasDet = $data['FacturasDet'];
        $CodAlmacenPN = $data["CodAlmacenPN"];
        $CodAlmacenSN = $data["CodAlmacenSN"];
        $CodAlmacenTN = $data["CodAlmacenTN"];
    }

    /*
      TP : Tipo Programa
      Curso -> NM: Programa
      Diplomado -> NM: Curso,Programa
      Seminario -> NM: Curso,Programa
      Extendido -> NM: Modulo,Programa
     */
    if ($NM) {
        switch ($NM) {
            case "Programa":
                if ($TP == "Curso" || $TP == "Diplomado" || $TP == "Seminario") {

                    if (!$CodAlmacenSN) {
                        return $return;
                    }

                    $sqlSb = "SELECT Estado, IdMatricula 
                    FROM matriculas 
                    WHERE Cliente='{$usuarioId}' 
                    AND Producto='{$CodigoProducto}' 
                    AND CodAlmacenSN={$CodAlmacenSN};";
                    $Q_PRG = "SELECT PRG.Titulo
                        FROM almacen AL 
                        INNER JOIN articulos AR ON AL.Producto=AR.Producto
                        INNER JOIN programas PRG ON AR.ProductoFab=PRG.CodPrograma
                        AND AL.AlmacenCod={$CodAlmacenSN};";

                    $auxEstructura = 2;
                    $AuxEM  = fetchOne($sqlSb, $link_identifier);
                    $ObjPRG = fetchOne($Q_PRG, $link_identifier);

                    if ($AuxEM) {

                        $EstadoMat = $AuxEM->Estado;
                        $IdMatricula = $AuxEM->IdMatricula; 
                        $AuxEM = true;
                        if($EstadoMat == 'Eliminado'){  
                        
                            $return["success"] = true;                       
                            $sql = "UPDATE matriculas SET Estado = 'Matriculado' WHERE IdMatricula = '" . $IdMatricula . "'  ";
                          
                            xSQL($sql, $link_identifier);
                            $auxMsg = "El alumno " . array_shift(explode("Alumno", $usuarioId)) . " fue matriculado en el Programa {$ObjPRG->Titulo} &check;<br>";
                        }else{                            
                            $auxMsg = "El alumno " . array_shift(explode("Alumno", $usuarioId)) . " ya esta matriculado en el Programa {$ObjPRG->Titulo}<br>";
                        }
                        
                    } else {
                        $auxMsg = "El alumno " . array_shift(explode("Alumno", $usuarioId)) . " fue matriculado en el Programa {$ObjPRG->Titulo} &check;<br>";
                    }
                } 
				
                break;
            case "Ecomerce":

                $sqlSb1 = "SELECT Estado 
                    FROM matriculas 
                    WHERE Cliente='{$usuarioId}' 
                    AND Producto='{$CodigoProducto}' ";

                $Q_PRG1 = "select DOC.Titulo
                            from almacen AL 
                            inner join articulos AR on AL.Producto=AR.Producto
                            inner join documento DOC on AR.ProductoFab=DOC.Codigo
                            where AL.AlmacenCod=$CodigoProducto";
                $AuxEM = fetchOne($sqlSb1, $link_identifier);
                $ObjPRG = fetchOne($Q_PRG1, $link_identifier);
                if ($AuxEM) {
                    $AuxEM = true;
                    $auxMsg = "El alumno " . array_shift(explode("Alumno", $usuarioId)) . " ya esta matriculado en el $tipoproducto {$ObjPRG->Titulo}<br>";
                } else {
                    $auxMsg = "El alumno " . array_shift(explode("Alumno", $usuarioId)) . " fue matriculado en el $tipoproducto {$ObjPRG->Titulo} &check;<br>";
                }
                break;
        }
    } else {
        //W(Msg("ERROR: Falta definir un Nivel de Matricula al Programa", "E"));
    }

    $EstadoMatriculado = $AuxEM;
    if (!$EstadoMatriculado) {
        # REGISTRO EN ALMACEN_ENTIDAD
        $CodAlmacenPNivel = $CodAlmacenPN;
        $CodAlmacenSNivel = $CodAlmacenSN;
        $CodAlmacenTNivel = $CodAlmacenTN;
        $Entidad = $alumno;
        $FechaHoraRegistro = date('Y-m-d H:i:s');
        $EstadoSesionAPP = "Modificado";
        $EstadoSesionData = "Modificado";
        $AlcanceEstructura = $auxEstructura;
        $CodAlmacenContenedor = $CodigoProducto;

        # data_AE : data Almacen Entidad
        $data_AE = array(
            "CodAlmacenPNivel" => $CodAlmacenPNivel,
            "CodAlmacenSNivel" => $CodAlmacenSNivel,
            "CodAlmacenTNivel" => $CodAlmacenTNivel,
            "Entidad" => $Entidad,
            "FechaHoraRegistro" => $FechaHoraRegistro,
            "EstadoSesionAPP" => $EstadoSesionAPP,
            "EstadoSesionData" => $EstadoSesionData,
            "AlcanceEstructura" => $AlcanceEstructura,
            "CodAlmacenContenedor" => $CodAlmacenContenedor
        );
        # data_Matricula
        $tabla = array(
            'name' => 'matriculas',
            'alias' => 'Matricula'
        );
        $data = array(
            'NumeroOperacion' => '',
            'IdFacturasCab' => $FacturasCab,
            'IdFacturasDet' => $FacturasDet,
            'Producto' => $CodigoProducto,
            'Cliente' => $alumno,
            'FechaInscripcion' => date('Y-m-d H:i:s'),
            'Estado' => 'Matriculado',
            'Entidad' => $Proveedor,
            'FechReg' => date('Y-m-d H:i:s'),
            'Proveedor' => $Proveedor,
            "CodAlmacenPN" => $CodAlmacenPN,
            "CodAlmacenSN" => $CodAlmacenSN,
            "CodAlmacenTN" => $CodAlmacenTN,
            "TipoAccesoMatricula" => $TipoAccesoMatricula
        );
        $codigo = array(
            'name' => 'IdMatricula',
            'prefijo' => '',
        );

        # REGISTRO PARA almacen_entidad
        insert("almacen_entidad", $data_AE);
        # REGISTRO PARA matriculas
        insertCorrelativo($tabla, $data, $codigo, $link_identifier);

        # FIN REGISTRO EN ALMACEN_ENTIDAD
        # REGISTRANDO MOVIMIENTOS DE ALMACEN
        $sqlTransaccion = " SELECT codigo,descripcion,tipo
                            FROM almacen_transaccion
                            WHERE entidad='{$Proveedor}'
                            AND nombre='matricula-venta' 
                            LIMIT 1";

        $sqlAlmacenNombre = "SELECT codigo,descripcion
                            FROM almacen_nombre
                            WHERE entidad='{$Proveedor}'
                            AND nombre='productos-terminados' 
                            LIMIT 1";

        $almacenTransaccion = fetchOne($sqlTransaccion);
        $almacenNombre = fetchOne($sqlAlmacenNombre);

        $insertData = array();
        $insertData['almacen_id'] = $CodigoProducto;
        $insertData['almacen_transaccion_id'] = $almacenTransaccion->codigo;
        $insertData['usuario'] = $Proveedor;
        $insertData['cantidad'] = 1;
        $insertData['almacen_nombre_id'] = $almacenNombre->codigo;
        $insertData['descripcion'] = '';
        $almacenMovimientoId = insert('almacen_movimiento', $insertData);

        $sqlAlmacen = "SELECT AlmacenCod, stock, cantidad
                    FROM almacen
                    WHERE AlmacenCod={$CodigoProducto} AND Entidad = '{$Proveedor}'
                    LIMIT 1";
        $almacen = fetchOne($sqlAlmacen);

        if (!empty($almacen)) {
            $updateData['stock'] = $almacen->stock - 1;
            update('almacen', $updateData, array('AlmacenCod' => $almacen->AlmacenCod));
        }

        $return["success"] = true;
    }
    $return["insert"] = $AuxEM;
    $return["msg"] = $auxMsg;
    return $return;
}

function updtIntroTransac($CodigoPrograma, $alumno, $vConex) {

    $sql = "SELECT AR.ProductoFab
        FROM almacen AL 
        LEFT JOIN articulos AR ON AL.Producto=AR.Producto  
        WHERE AL.AlmacenCod={$CodigoPrograma}";

    $rg = fetch($sql);

    $ProductoFab = $rg["ProductoFab"];

    $sql = "SELECT Codigo FROM introduccion WHERE Producto={$ProductoFab}";

    $r = fetch($sql);

    $codigo = $r['Codigo'];

    if ($codigo) {
        $sq = "INSERT INTO introduccion_transaccion (Introduccion, Alumno, Interaccion)
               VALUES('{$codigo}','{$alumno}','NO')";
        xSQL($sq, $vConex);
    }
}
     
function updtAnuncioTransac($ProgramaAlmacen, $Alumno, $vConex) {

    $sql = "SELECT Codigo 
        FROM anuncios 
        WHERE ProgramaAlmacen='{$ProgramaAlmacen}'";
    #W("<BR/>".$sql."<BR/>");
    $consulta = Matris_Datos($sql, $vConex);
    while ($reg = mysql_fetch_array($consulta)) {
        $SQL_INSERT = "INSERT INTO anuncios_transaccion (Anuncio, Alumno, Interaccion)
             VALUES('{$reg[0]}','{$Alumno}','NO')";
            W("<BR/>INSERT AUNCIOS TRANSACCION :".$SQL_INSERT."<BR/>");
        xSQL($SQL_INSERT, $vConex);
    }
}

function update_recurso_eval($producto, $alumno, $vConex,$CursoAlmacen){
 
    $Q_Curricula = " SELECT CU.ProductoCod, CUR.CodCursos
                    FROM curricula CU
                    INNER JOIN almacen AL ON AL.AlmacenCod = CU.ProductoCod
                    INNER JOIN articulos AR ON AR.Producto = AL.Producto
                    INNER JOIN cursos CUR ON CUR.CodCursos = AR.ProductoFab
                    WHERE 
					CU.CodProgAlmacen = {$producto}  ";
					
    if($CursoAlmacen){ $Q_Curricula .= "	AND  CU.ProductoCod  = {$CursoAlmacen} "; }
    $MxCurricula = fetchAll($Q_Curricula, $vConex);
    
    foreach($MxCurricula as $Curricula){
	
        $CodigoAlmacenCurso = (int) $Curricula->ProductoCod;
        $CodigoCurso = (int) $Curricula->CodCursos;
        
        //Registrando al alumno al chat general del Curso
        $Q_ChatAula = "SELECT Codigo 
            FROM sala_chat 
            WHERE CodCurso = {$CodigoAlmacenCurso} 
            AND CodRecursoEvaluacion IS NULL";
        $CodChatAula = (int) fetchOne($Q_ChatAula, $vConex)->Codigo;
        if($CodChatAula){
            $Q_MemberChatRoomClass = "SELECT Codigo 
                FROM sala_chat_miembro 
                WHERE CodSala = {$CodChatAula} 
                AND CodMiembro = '{$alumno}'";
            $CodCMCR = (int) fetchOne($Q_MemberChatRoomClass, $vConex)->Codigo;

            if(!$CodCMCR){
                insert("sala_chat_miembro", array(
                    "CodSala" => $CodChatAula,
                    "CodMiembro" => $alumno
                ), $vConex);
            }
        }
        
        //Recorriendo todos los recursos de evaluacion del Curso
        $Q_RE = "SELECT RE.Codigo, RE.RecursoTipo, RE.Duracion, ED.Codigo As CodDetCurso, ED.EvalConfigCurso, ED.Abreviacion, RE.Usuario
		FROM elrecursoevaluacion RE
        INNER JOIN elevaluaciondetallecurso ED ON RE.EvaluacionDetalleCurso = ED.Codigo  
        INNER JOIN elevaluacionconfcurso EC ON ED.EvalConfigCurso = EC.Codigo 		
		WHERE EC.Almacen = {$CodigoAlmacenCurso} 
        AND RE.Estado = 'Cerrado'";        
        $MxRE = fetchAll($Q_RE, $vConex);
        foreach($MxRE as $RE){
		
            $RECodigo = (int) $RE->Codigo;
            $RecursoTipo = (int) $RE->RecursoTipo;
            $Duracion = $RE->Duracion;
            $CodDetCurso = (int) $RE->CodDetCurso;
            $EvalConfigCurso = (int) $RE->EvalConfigCurso;
            $Abreviacion = $RE->Abreviacion;
            $Usuario = $RE->Usuario;

            if($RecursoTipo == RE_CHAT){
			
                $Q_Chat = "SELECT Codigo 
                    FROM sala_chat 
                    WHERE CodCurso = {$CodigoAlmacenCurso} 
                    AND CodRecursoEvaluacion = {$RECodigo}";
                $CodSala_chat = (int) fetchOne($Q_Chat, $vConex)->Codigo;
                
                if(!$CodSala_chat){
                    $success_sala = insert("sala_chat", 
                    array(
                        "CodCurso" => $CodigoAlmacenCurso,
                        "CodRecursoEvaluacion" => $RECodigo,
                        "tipo" => "Recurso"
                    ), $vConex);
                    
                    $CodSala_chat = $success_sala["lastInsertId"];
                }
                
                $Q_Chat_Miembro = "SELECT Codigo 
                    FROM sala_chat_miembro 
                    WHERE CodSala = {$CodSala_chat} 
                    AND CodMiembro = '{$alumno}'";
                $CodCM = (int) fetchOne($Q_Chat_Miembro, $vConex)->Codigo;
                
                if(!$CodCM){
                    insert("sala_chat_miembro", array(
                        "CodSala" => $CodSala_chat,
                        "CodMiembro" => $alumno
                    ), $vConex);
                }
            }

            
            $Q_TRC = "SELECT Codigo
                    FROM eltransrespuesta_cab 
                    WHERE Recurso= {$RECodigo}
                    AND  Alumno = '{$alumno}'";
            $CodigoTRC =  fetchOne($Q_TRC, $vConex)->Codigo;
            if(empty($CodigoTRC)){
			
			 
                insert("eltransrespuesta_cab", array(
                    "Recurso" => $RECodigo,
                    "Alumno" => $alumno,
                    "FechaReg" => FechaHoraSrv(),
                    "FechaHoraActualizacion" => FechaHoraSrv(),
                    "UsuarioActualizacion" => $Usuario,
                    "Duracion" => $Duracion,
                    "Estado" => "Pendiente"
                ), $vConex);

                //REGISTRANDO EN LA TABLA TRANSRESPUESTA
                switch ($RecursoTipo) {
                    case RE_CUESTIONARIO:
					 FormatoPRCuestionario($RECodigo,$alumno, $vConex);

                    /*
                        $Q_CA = "SELECT ECA.Codigo,ECA.modo_auditoria,ECI.Codigo as 'CodigoECI'
                                FROM elconfig_auditoria ECA
                                LEFT JOIN elcuestionatio_item ECI ON ECA.recursoevaluacion = ECI.RecursoEvaluacion
                                WHERE ECA.recursoevaluacion = {$RECodigo}";
                        $ObjCA = fetchOne($Q_CA, $vConex);
                        $CodigoCA = (int) $ObjCA->Codigo;
                        $modo_auditoria = (string) $ObjCA->modo_auditoria;
                        $CodigoCI = (string) $ObjCA->CodigoECI;
                        if (!$CodigoCI) {
                                    $data_insert = array(
                                        "RecursoEvaluacion" => $RECodigo,
                                        "DescripcionItem" => "Responder a la Pregunta",
                                        "TipoItem" => "General",
                                        "Orden" => 1
                                    );
                                    insert("elcuestionatio_item", $data_insert);
                        }
                        */
                        break;
						
                    default:
					
                             FormatoPtaRpta($RECodigo,$alumno, $vConex);
						
                        break;
                }
            }
        }
    }
}


function FormatoPRCuestionario($RECodigo,$alumno, $vConex){
    ////////////////MMMMMMMMMMMMMMMMMMMMM

    $Q_CA = "SELECT  RecursoTipo FROM  elrecursoevaluacion WHERE Codigo = ".$RECodigo." ";
    $ObjCA = fetchOne($Q_CA, $vConex);
    $RecursoTipo = (int) $ObjCA->RecursoTipo;   

    $Q_Pregunta = "SELECT P.Codigo,
                P.TipoPregunta
                FROM elpregunta P
                WHERE P.RecursoEvaluacion = {$RECodigo}  ";
    $MxPregunta = fetchAll($Q_Pregunta, $vConex);
    foreach ($MxPregunta as $Pregunta) {
    
        //Definimos un array con data constante
        $data_insert_transrespuesta = array(
            "Pregunta" => $Pregunta->Codigo,
            "Respuesta" => 1,
            "Usuario" => $alumno,
            "Nota" => 0,
            "FechaHoraReg" => FechaHoraSrv(),
            "Estado" => "Pendiente",
            "RecursoEvaluacion" => $RECodigo,
            "PreguntaMostrada" => "SI"
        );
        
        $Q_Respuesta = "SELECT R.Codigo 
                                FROM elrespuesta R
                                WHERE R.Pregunta = {$Pregunta->Codigo}";
                $MxRespuesta = fetchAll($Q_Respuesta, $vConex);
                foreach ($MxRespuesta as $Respuesta) {
            
                    $data_insert_transrespuesta["Respuesta"] = $Respuesta->Codigo;
                    insert("eltransrespuesta", $data_insert_transrespuesta);
        }           

        
    }


}


function FormatoPtaRpta($RECodigo,$alumno, $vConex){
    ////////////////MMMMMMMMMMMMMMMMMMMMM

	$Q_CA = "SELECT  RecursoTipo FROM  elrecursoevaluacion WHERE Codigo = ".$RECodigo." ";
	$ObjCA = fetchOne($Q_CA, $vConex);
	$RecursoTipo = (int) $ObjCA->RecursoTipo;	

	$Q_Pregunta = "SELECT P.Codigo,
				P.TipoPregunta
				FROM elpregunta P
				WHERE P.RecursoEvaluacion = {$RECodigo}  ";
	$MxPregunta = fetchAll($Q_Pregunta, $vConex);
	foreach ($MxPregunta as $Pregunta) {
	
		//Definimos un array con data constante
		$data_insert_transrespuesta = array(
			"Pregunta" => $Pregunta->Codigo,
			"Respuesta" => 1,
			"Usuario" => $alumno,
			"FechaHoraReg" => FechaHoraSrv(),
			"Estado" => "Pendiente",
			"RecursoEvaluacion" => $RECodigo,
			"PreguntaMostrada" => "SI"
		);
		
		switch ((int) $Pregunta->TipoPregunta) {
	
			case PREGUNTA_ABIERTA:	

				$Q_Respuesta = "SELECT R.Codigo 
								FROM elrespuesta R
								WHERE R.Pregunta = {$Pregunta->Codigo}";
				$MxRespuesta = fetchAll($Q_Respuesta, $vConex);
				foreach ($MxRespuesta as $Respuesta) {
			
					$data_insert_transrespuesta["Respuesta"] = $Respuesta->Codigo;
					insert("eltransrespuesta", $data_insert_transrespuesta);
				}			
				
				break;
				
			case PREGUNTA_MULTI_SELECTIVA:
			case PREGUNTA_SELECTIVA:
			case PREGUNTA_SELECTIVA_RELACIONAL:

				$Q_Respuesta = "SELECT R.Codigo 
								FROM elrespuesta R
								WHERE R.Pregunta = {$Pregunta->Codigo}";
				$MxRespuesta = fetchAll($Q_Respuesta, $vConex);
				foreach ($MxRespuesta as $Respuesta) {				
					$data_insert_transrespuesta["Respuesta"] = $Respuesta->Codigo;
					insert("eltransrespuesta", $data_insert_transrespuesta);
				}
				break;
		}

		if ($RecursoTipo == RE_ARCHIVO_ADJUNTO) {
		
			$Q_Respuesta = "SELECT R.Codigo 
			FROM elrespuesta R
			WHERE R.Pregunta = {$Pregunta->Codigo}";
			$MxRespuesta = fetchAll($Q_Respuesta, $vConex);
			foreach ($MxRespuesta as $Respuesta) {
				$data_insert_transrespuesta["Respuesta"] = $Respuesta->Codigo;
				insert("eltransrespuesta", $data_insert_transrespuesta);
			}
			
		}
	}


}

function GeneraPedido($data, $link_identifier) {

    $data = (array) $data;
    $Proveedor = $data['proveedor'];
    $CodigoProducto = $data['productoId'];
    $Cliente = $data['cliente'];
    $tipoDePago = "Gra";
    $Precio = $data['Precio'];
    $Moneda = $data['Moneda'];
    $Cantidad = $data['Cantidad'];

    $PedidosCab = insertCorrelativo(array('name' => 'pedidoscab', 'alias' => 'PedidosCab'), array(
        'Cliente' => $Cliente,
        'TipoPago' => $tipoDePago,
        'FechPedio' => date('Y-m-d H:i:s'),
        'Estado' => 'Cerrado',
        'Moneda' => $Moneda,
        'Entidad' => $Proveedor,
        'Proveedor' => $Proveedor,
            ), array(
        'name' => 'IdPedidosCab',
        'prefijo' => 'PC-',
            ), $link_identifier);

    $total = $Cantidad * $Precio;
    $PedidosDet = insertCorrelativo(array('name' => 'pedidosdet', 'alias' => 'PedidosDet'), array(
        'ProductoId' => $CodigoProducto,
        'Precio' => $Precio,
        'Moneda' => $Moneda,
        'Total' => $total,
        'IdPedidosCab' => $PedidosCab,
        'Cantidad' => $Cantidad,
            ), array(
        'name' => 'IdPedidosDet',
        'prefijo' => 'PD-',
            ), $link_identifier);

    $sql = "SELECT SUM(Total) AS Tot FROM pedidosdet WHERE  IdPedidosCab = '" . $PedidosCab . "'  ";
    $rg = fetch($sql);
    $Tot = $rg["Tot"];

    $sql = "UPDATE pedidoscab SET Total = " . $Tot . " WHERE IdPedidosCab = '" . $PedidosCab . "'  ";
    xSQL($sql, $link_identifier);

    return $PedidosCab;
}

function GeneraFactura($data, $link_identifier) {

    $data = (array) $data;
    $Proveedor = $data['proveedor'];
    $CodigoProducto = $data['productoId'];
    $Cliente = $data['cliente'];

    $tipoDePago = "Gra";
    $Precio = $data['Precio'];
    $Moneda = $data['Moneda'];
    $Cantidad = $data['Cantidad'];
    $PedidoCab = $data['PedidoCab'];

    $total = $Cantidad * $Precio;

    $FacturasCab = insertCorrelativo(array('name' => 'facturascab', 'alias' => 'FacturasCab'), array(
        'Cliente' => $Cliente,
        'TipoPago' => $tipoDePago,
        'FechPedio' => date('Y-m-d H:i:s'),
        'Estado' => 'Cerrado',
        'Proveedor' => $Proveedor,
        'Moneda' => $Moneda,
        'Entidad' => $Proveedor,
        'NumeroPedido' => $PedidoCab,
            ), array(
        'name' => 'IdFacturasCab',
        'prefijo' => 'FC-',
            ), $link_identifier);


    $FacturasDet = insertCorrelativo(array('name' => 'facturasdet', 'alias' => 'FacturasDet'), array(
        'ProductoId' => $CodigoProducto,
        'Cantidad' => $Cantidad,
        'Precio' => $Precio,
        'Total' => $total,
        'Moneda' => $Moneda,
        'IdFacturasCab' => $FacturasCab
            ), array(
        'name' => 'IdFacturasDet',
        'prefijo' => 'FD-',
            ), $link_identifier);

    $sql = "SELECT SUM(Total) AS Tot FROM facturasdet WHERE  IdFacturasCab = '" . $FacturasCab . "'  ";
    $rg = fetch($sql);
    $Tot = $rg["Tot"];

    $sql = "UPDATE facturascab SET Total = " . $Tot . " WHERE IdFacturasCab = '" . $FacturasCab . "'  ";
    xSQL($sql, $link_identifier);

    return $FacturasCab . "," . $FacturasDet;
}

function enviar_mail_entrevista($data, $vConex) {
    $alumno = $data['alumno'];
    $sqlUsuario = "SELECT Nombres,Apellidos,Usuario 
        FROM usuarios 
        WHERE IdUsuario='{$alumno}';";
    $objUsuario = fetchOne($sqlUsuario, $vConex);
    $n_a = ucwords(strtolower($objUsuario->Nombres)) . " " . ucwords(strtolower($objUsuario->Apellidos));
    $usuario = $objUsuario->Usuario;

    $sqlAlmacen = "SELECT TipoProducto 
							FROM almacen 
							WHERE AlmacenCod={$data['productoId']}";
    $objAL = fetchOne($sqlAlmacen, $vConex);
    $TipoProducto = $objAL->TipoProducto;

    $sqlEmpresa = "select IdCodCorrelativo,Usuario,UrlId from usuarios where Usuario='" . $data['proveedor'] . "'";
    $rgEmpresa = fetch($sqlEmpresa);
    $nombreEmp = $rgEmpresa['UrlId'];

    $sqlAlmacen = "select AL.AlmacenCod,AR.idArticulo,AL.Estado,ProductoFab, AR.Titulo
				From almacen AL inner join articulos AR on
				AL.Producto = AR.Producto 
				where AL.AlmacenCod=" . $data['productoId'] . "";
    $rgAlmacen = fetch($sqlAlmacen);
    $tituloPrograma = $rgAlmacen['Titulo'];

    $hoy = date('Y-m-d');
    $hoy = FormatFechaText($hoy);

    $body = '<div id=":2m" class="ii gt m144e03aa4c6b1558 adP adO">';
    $body .= '<div id=":1dd" class="a3s" style="overflow: hidden;">';
    $body .= '<div style="background-color:#e3e3e3;margin:0 auto;width:760px;min-height:425px;padding:20px 20px">';
    $body .= '<div style="float:left;width:90%;background-color:#fff;padding:10px 5% 0px 5%;font-size:0.9em;font-family:arial;color:#6b6b6b;min-height:100%">';
    $body .= '<div style="float:left;width:100%;padding:20px 0px;color:#6b6b6b">';
    $body .= '<div style="padding:10px 3px">' . $hoy . '</div>';
    $body .= '<div style="border-bottom:2px solid #e2e2e2;margin:10px 0px 30px 0px" class="adM"></div>';
    $body .= '<div style="font-size:1.5em;color:#6b6b6b;padding:0px 0px 15px 3px">ENTREVISTA </div>';
    $body .= '<div style="font-size:1.2em;color:#6b6b6b;padding:5px 0px 5px 3px">Estimado (a) <span style="color:#35A9AD">' . $n_a . '</span> Usted ha sido invitado a participar de la entrevista  : "<span style="color:#35A9AD">' . $tituloPrograma . '</span>"</div>';
    $body .= '</div>';
    $body .= '<div style="float:left;width:100%;padding:35px 3px;color:#6b6b6b">';
    $body .= '<a href="https://owlgroup.org/' . $nombreEmp . '" style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;color:#f9f9f9;text-decoration:none" target="_blank">Ingresar</a>';
    $body .= '</div>';
    $body .= '<div style="float:left;width:100%;padding:20px 0px;color:#6b6b6b">Atentamente </div>';
    $body .= '</div>';
    $body .= '</div>';
    $body .= '</div>';
    $body .= '</div>';
    $body .= '</div>';

    // WE($body);

    $emal = explode('Alumno', $alumno);
    $asunto = 'Matr\u00edcula - ' . utf8_encode($tituloPrograma);
    $destinatario = $emal[0];
    #$emailE = EMail("", $destinatario, $asunto, $body);
    $emailE = emailSES("", $destinatario, $asunto, $body,"","");

    return $emailE;
}


function enviar_mail($data, $vConex,$pagweb="") {
    global $ipHost,$entidadCreadora;

    $CorreoUsuario=$data['alumnousuario'];
    $CodAlmacen=$data['CodAlmacenSN'];
    $alumno = $data['alumno'];

    $sqlUsuario = "SELECT Nombres,Apellidos,Usuario 
        FROM usuarios 
        WHERE IdUsuario='{$alumno}';";
    $objUsuario = fetchOne($sqlUsuario, $vConex);
    $n_a = ucwords(strtolower($objUsuario->Nombres)) . " " . ucwords(strtolower($objUsuario->Apellidos));
    $usuario = $objUsuario->Usuario;

    $sqlAlmacen = "SELECT TipoProducto 
							FROM almacen 
							WHERE AlmacenCod={$data['productoId']}";
    $objAL = fetchOne($sqlAlmacen, $vConex);
    $TipoProducto = $objAL->TipoProducto;

    if ($TipoProducto == 'libro' || $TipoProducto =='revista') {

        $sqlEmpresa = "select IdCodCorrelativo,Usuario,UrlId, IdUsuario from usuarios where Usuario='" . $data['proveedor'] . "'";
        $rgEmpresa = fetch($sqlEmpresa);
        $nombreEmp = $rgEmpresa['UrlId'];
        $IdUsuarioEmpresa = $rgEmpresa['IdUsuario'];
		
        $sqlAlmacen = "select AL.AlmacenCod,AR.idArticulo,AL.Estado,ProductoFab, AR.Titulo
                    From almacen AL inner join articulos AR on
                    AL.Producto = AR.Producto 
                    where AL.TipoProducto IN('libro','revista') and AL.AlmacenCod=" . $data['productoId'] . "";
        $rgAlmacen = fetch($sqlAlmacen);
        $tituloPrograma = $rgAlmacen['Titulo'];

    } else {
	
        $sql = 'SELECT 
			AR.ProductoFab
			, AL.NivelMatricula
			, PR.GrupoProgrId
			, GP.Descripcion AS AlcancePrograma
			, PR.Valor
			, PR.Precio
			, PR.Moneda
			, PR.CodPrograma
			, PR.titulo
			, U.UrlId as NomEmpresaD
			, U.IdUsuario
			, AL.AliasDeMsgPrograma
			FROM almacen AL 
			INNER  JOIN articulos AR ON AL.Producto = AR.Producto  
			INNER  JOIN programas PR ON AR.ProductoFab = PR.CodPrograma
			INNER  JOIN grupoprograma GP ON GP.IdGrupoPrograma=PR.GrupoProgrId
			INNER  JOIN usuarios U ON AL.Entidad=U.Usuario
			WHERE  AL.AlmacenCod = ' . $data['productoId'] . '  ';
        $rg = fetch($sql);
        $alcanceProg = $rg['AlcancePrograma'];
        $tituloPrograma = $rg['titulo'];
        $nombreEmp = $rg['NomEmpresaD'];
        $AliasEmail = $rg['AliasDeMsgPrograma'];
        $IdUsuarioEmpresa = $rg['IdUsuario'];
		
        if(trim($AliasEmail)){
            $From = $AliasEmail;
        }else{
            $From = "OWLGROUP";
        }

        if ($alcanceProg == 'InHouse') {
            $sql4 = 'select * from progroma_alcance where AlmacenPrograma=' . $data['productoId'] . ' and TipoAcceso=1';
            $rg4 = fetch($sql4);

            if ($rg4['TipoAcceso'] == 1) {
			
                $sql3 = 'select Usuario,IdUsuario,UrlId from usuarios where IdUsuario like' . '"' . $rg4['Empresa'] . '"';
                $rg3 = fetch($sql3);
                $nombreEmp = $rg3['UrlId'];
				$IdUsuarioEmpresa = $rg3['IdUsuario'];
            }
        }
    }
	
	$SqlMsj    = "SELECT EmailEmisor FROM empresa WHERE PaginaWeb='{$IdUsuarioEmpresa}';";
    $msj       = fetch($SqlMsj);
    $EmailEmisor           = $msj['EmailEmisor']; //EmailEmisor General	

	
    $sql = " SELECT * FROM empresa_corporacion";
    $rg = fetch($sql);
    $Soporte_Nombre = $rg["Nombre"];
    $Soporte_Email = $rg["Email_1"];
    $Soporte_Telefono = $rg["Telefono"];
    $Soporte_Anexo = $rg["Anexo"];

    $hoy = date('Y-m-d');
    $hoy = FormatFechaText($hoy);

        if($pagweb=="https://www.owlbook.org/"){
             $alumno  = substr($alumno, 0, strlen($alumno)-6);
             $body='
                                  <div style="width:100%;height:252px;background-color:white;text-align:center"><img src="https://www.owlbook.org/_imagenes/mail01.png"></div><br/><br/>
                                <div style="width:100%;display:flex">
                                    <div style="width:10%;"></div>
                                    <div style="width:80%;text-align:center;font-size:1.3em;font-family: Arial;">
                                            <div style="width:100%;text-align:center;    ">!Felicidades! <label style="text-decoration: underline;">'.$alumno .'</label></div><br/>
                                            <div style="width:100%;text-align:center;    ">Has adquirido tu Ebook exitosamente.</div><br/>
                                            <div style="width:100%;text-align:center;    font-weight: bold;">'.$tituloPrograma .'</div><br/>
                                            <div style="width:100%;text-align:center;    ">Esperamos que lo disfrutes</div><br/>
                                            <div style="width:100%;text-align:center;padding: 0% 35%;font-weight: bold;    padding-bottom: 2em;"><div style="background-color:#262A5D;color:white;width:300px;padding: .5em;width: 30%;"><a href="'.$pagweb.'" style="color: white;    text-decoration: none;">Ir a Mi cuenta</a></div></div>
                                    </div>
                                    <div style="width:10%;"></div>
                                </div>

                                <div style="width:100%;display:flex;">
                                    <div style="width:10%;"></div>
                                    <div style="width:80%"><div style="width: 100%;height:10px;background: #262A5D;"></div></div>
                                    <div style="width:10%;"></div>
                                </div>
                            ';

        }else{
            $CodAlmacen= get('productoId');

            if(get('CodigoAlmacen')){
                $CodAlmacen= get('CodigoAlmacen');
            }
            if($CodAlmacen=="")
                $CodAlmacen = $data["productoId"];

            $MailDetalle="SELECT * FROM maildetalle WHERE CodAlmacen=".$CodAlmacen." AND TipoMail='Matricula' ";
            $ftch=fetchOne($MailDetalle);
            $tipoEnvio=$ftch->TipoEnvio;
            $Imagen=$ftch->Imagen;
            $FooterMail=$ftch->FooterMail;
            $emisor=$ftch->Emisor;
            $From=$ftch->NombreEmisor;

            $tamanioMensaje=strlen($FooterMail);

            $sql = "SELECT Carpeta FROM usuarios  WHERE IdUsuario = '$entidadCreadora' ";
            $rg = fetch($sql);
            $CarpetaEmpresa = $rg["Carpeta"];

            if($tipoEnvio == 'Predeterminado'){
			
                $Texto = 'Estimado(a) <span style="color:#35A9AD">'.$n_a.'</span>, usted fue vinculado correctamente al <span style="color:#35A9AD">' . $tituloPrograma . '</span>';
                $Nota  = '<strong>NOTA:</strong> para obtener un mejor rendimiento de la plataforma acceder con el navegador google chrome, si presenta alg&uacute;n inconveniente, contactarse con el soporte t&eacute;cnico.';

                $body  = "<table border='0' width='95%' style='font-family: arial, open sans'>";
                $body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:22px;" >Vinculación Exitosa</td></tr>';
                $body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
                $body .= '<tr><td colspan="3" style="font-size:13px;"> '.$Texto.'</td></tr>';
                $body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
                $body .= '<tr>
                            <td style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none">
                              <center><a style="color:#f9f9f9;text-decoration: none;" href="'.$ipHost . $nombreEmp . '"  target="_blank">Ingresar</a></center></td>
                             <td style="padding:8px 10px; width:30%;"></td>
                            <td style="padding:8px 30px;width:50%;"></td>
                           </tr>';
                $body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:10px;color:#6b6b6b" >'.$Nota.'</td></tr>';
                $body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
                $body .= '<tr><td colspan="3" style="width:100%;padding:5px 0px;color:#6b6b6b;font-size:9px ">Datos de Soporte: '.$Soporte_Nombre.' <br>  Correo: '.$Soporte_Email.' <br> Tel&eacute;fono.: '.$Soporte_Telefono.' | Anexo: '.$Soporte_Anexo.'  </td></tr>';
                $body .= '<tr><td colspan="3" style="font-size:10px;" >Fecha: ' . $hoy . '<br /></td></tr>';
                $body .= "</table>";
            
			}else if($tipoEnvio == 'Personalizado'){
                $Texto = 'Estimado(a) <span style="color:#35A9AD">'.$n_a.'</span>, usted fue matriculado correctamente al <span style="color:#35A9AD">' . $tituloPrograma . '</span>';
                $Nota  = '<strong>NOTA:</strong> para obtener un mejor rendimiento de la plataforma acceder con el navegador google chrome, si presenta alg&uacute;n inconveniente, contactarse con el soporte t&eacute;cnico.';

                $body  = "<center><table border='0' width='95%' style='font-family: arial, open sans'>";
                $url ="http://owlgroup.s3-website-us-west-2.amazonaws.com/ArchivosEmpresa/".$CarpetaEmpresa."/".$Imagen."";
                $body .= '<tr><td colspan="3"><label>Si no puede visualizar la imagen correctamente, por favor haga <a href="'.$url.'" target="_blank">Clic aqu&iacute;</a></label></td></tr>';
                $body .= '<tr><td colspan="3" style="font-size:13px;"><picture><img src="'.$url.'" width="908px" height="404px"></picture> </td></tr>';
                $body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
				
                $body .= '<tr>
                            <td  style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none">
                              <center><a style="color:#f9f9f9;text-decoration: none;" href="'.$ipHost . $nombreEmp . '"  target="_blank">Ingresar</a></center></td>
                             </td>';
                $body .= '  <td style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none">
                              <center><a style="color:#f9f9f9;text-decoration: none;" href="mailto:'.$emisor.'"  target="_blank">Coordinación</a></center></td>
                             </td>';
                $body .= '  <td style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none">
                              <center><a style="color:#f9f9f9;text-decoration: none;" href="mailto:soporteusuario@owl-group.org"  target="_blank">Soporte</a></center></td>
                            </td>
                           </tr>';
						   
                $body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:10px;color:#6b6b6b" ><span style="width:800px;font-size: 12px;">' .$FooterMail.'</span></td></tr>';
                if($tamanioMensaje <= 6){
                    $body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:10px;color:#6b6b6b" >'.$Nota.'</td></tr>';
                }
                $body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
                $body .= '<tr><td colspan="3" style="width:100%;padding:5px 0px;color:#6b6b6b;font-size:9px ">Datos de Soporte: '.$Soporte_Nombre.' <br>  Correo: '.$Soporte_Email.' <br> Tel&eacute;fono.: '.$Soporte_Telefono.' | Anexo: '.$Soporte_Anexo.'  </td></tr>';
                $body .= '<tr><td colspan="3" style="font-size:10px;" >Fecha: ' . $hoy . '<br /></td></tr>';
                $body .= "</table></center>";
            }else if($tipoEnvio == 'NoEnviar'){
                we();
            }

            $dominio=$_SERVER['HTTP_HOST'];
            $Archivo=post('Archivo');
            if($Archivo){
                $CorreoUsuario  = substr($alumno, 0, strlen($alumno)-6);
                $body .= "<img src='http://$dominio/system/_vistas/g_email.php?tipo=leido&CorreoUsuario=$CorreoUsuario&CodAlmacen=$CodAlmacen' width='100%'/>";
            }else{

                $body .= "<img src='http://$dominio/system/_vistas/g_email.php?tipo=leido&CorreoUsuario=$CorreoUsuario&CodAlmacen=$CodAlmacen' width='100%'/>";
             }
        }
		
    $emal = explode('Alumno', $alumno);
    $destinatario = $emal[0];

	if(empty($EmailEmisor)){
        $EmailEmisor  = 'informacion@sgem.info';
	}else{
        $EmailEmisor  = $EmailEmisor;	
	}	
	
    if ($TipoProducto == 'libro' || $TipoProducto =='revista') {
		   $asunto = "ACCESOS AL EBOOK";	
		   $emailE = emailSES($n_a, $destinatario, $asunto, $body,'','');
    }else{
        if($tipoEnvio){
           $asunto = "ACCESOS AL PROYECTO";			
           $From = " OWL - PROJECTS ";			
           $emailE = emailSES3($n_a, $destinatario, $asunto, $body,'','',$From, $EmailEmisor);
        }else{
            Msg('Configurar el Tipo de Email','E');
        }
    }

    return $emailE;
}


function enviar_mail_dinamico($data, $vConex) {
    global $ipHost;

    $alumno = $data['alumno'];
	
    $sqlUsuario = "SELECT Nombres,Apellidos,Usuario
        FROM usuarios
        WHERE IdUsuario='{$alumno}';";
    $objUsuario = fetchOne($sqlUsuario, $vConex);
    $n_a = ucwords(strtolower($objUsuario->Nombres)) . " " . ucwords(strtolower($objUsuario->Apellidos));

    $usuario = $objUsuario->Usuario;

    $sqlAlmacen = "SELECT TipoProducto
							FROM almacen
							WHERE AlmacenCod={$data['productoId']}";
    $objAL = fetchOne($sqlAlmacen, $vConex);
    $TipoProducto = $objAL->TipoProducto;

        $sql = 'SELECT
			AR.ProductoFab
			, AL.NivelMatricula
			, PR.GrupoProgrId
			, GP.Descripcion AS AlcancePrograma
			, PR.Valor
			, PR.Precio
			, PR.Moneda
			, PR.CodPrograma
			, PR.titulo
			, U.UrlId as NomEmpresaD
			, U.IdUsuario
			, AL.AliasDeMsgPrograma
			FROM almacen AL
			INNER  JOIN articulos AR ON AL.Producto = AR.Producto
			INNER  JOIN programas PR ON AR.ProductoFab = PR.CodPrograma
			INNER  JOIN grupoprograma GP ON GP.IdGrupoPrograma=PR.GrupoProgrId
			INNER  JOIN usuarios U ON AL.Entidad=U.Usuario
			WHERE  AL.AlmacenCod = ' . $data['productoId'] . '  ';
        $rg = fetch($sql);
        $alcanceProg = $rg['AlcancePrograma'];
        $tituloPrograma = $rg['titulo'];
        $nombreEmp = $rg['NomEmpresaD'];
        $IdUsuarioEmpresa = $rg['IdUsuario'];
        $AliasEmail = $rg['AliasDeMsgPrograma'];
        $CodPrograma = $rg['CodPrograma'];
        $CodigoEN  = $data["CodigoEN"];
	
        $SqlMsj    = "SELECT EmailEmisor FROM empresa WHERE PaginaWeb='{$IdUsuarioEmpresa}';";
        $msj       = fetch($SqlMsj);
        $EmailEmisor           = $msj['EmailEmisor']; //EmailEmisor General

	
        $SqlMsj    = "SELECT Asunto,Descripcion,Documento,Imagen,EmailEmisorAuxiliar,EnviarJefe FROM control_mensaje_enviar WHERE Codigo={$CodigoEN};";
        $msj       = fetch($SqlMsj);
        $From           = $msj['Asunto'];
        $Documento      = $msj['Documento'];
        $EmailEmisorAuxiliar      = $msj['EmailEmisorAuxiliar'];
        $DocumentoMSJ   = "https://owlgroup.s3.amazonaws.com/articulos/Programa-$CodPrograma/GestorEmail/$Documento";
        $Imagen         = $msj['Imagen'];
        $ImagenMSJ      = "https://owlgroup.s3.amazonaws.com/articulos/Programa-$CodPrograma/GestorEmail/$Imagen";
        $hoy            = date('Y-m-d');
        $hoy            = FormatFechaText($hoy);
        $EnviarJefe     = $msj['EnviarJefe'];


      $emal = explode('Alumno', $alumno);
      $asunto = '' . $tituloPrograma;
      $destinatario = $emal[0];

    
      $EmailJefe = "SELECT IdEmailJefe FROM usuarios WHERE Usuario = '{$emal[0]}'
                  GROUP BY IdEmailJefe";
  
      $rowJefe = fetch($EmailJefe);
 

      if($rowJefe['IdEmailJefe']){
       
        $DatosJefe = "SELECT Jefe,Apellido FROM usuario_jefe WHERE IdEmailJefe='{$rowJefe['IdEmailJefe']}' ";
        $rowDJ = fetch($DatosJefe);
        $NameJefe = $rowDJ['Jefe']." ".$rowDJ['Apellido'];

        #Usuario
        $bodyA = '<tr><td colspan="3" style="padding:8px 0px;font-size:12px;color:#6b6b6b" >*Este correo fue copiado al sr(a). <span style="color:#E20D0D">'.$NameJefe.'</span>.</td></tr>';
    //  $emailE = emailSES3($n_a, $destinatario,"PLATAFORMA EDUCATIVA", $body.$Copia,'','',$From,$EmailEmisor);
        #Jefe
        $bodyJ = '<tr><td colspan="3" style="padding:8px 0px;font-size:12px;color:#6b6b6b" >*Este correo es una copia del correo enviado al participante <span style="color:#E20D0D">'.$n_a.'</span>.</td></tr>';
      //  $destinatario = $rowJefe['IdEmailJefe'];
    //  $emailE1 = emailSES3($n_a, $destinatario,"PLATAFORMA EDUCATIVA", $body.$Copia,'','',$From,$EmailEmisor);
    }else{
        //$emailE = emailSES3($n_a, $destinatario,"PLATAFORMA EDUCATIVA", $body,'','',$From,$EmailEmisor);
    }



    $Texto = 'Estimado(a) <span style="color:#35A9AD">'.$n_a.'</span>:<BR /><BR />'. $msj['Descripcion'] .'<span style="color:#35A9AD"></span>';
    $Nota  = '<strong>NOTA:  </strong> para obtener un mejor rendimiento de la plataforma acceder con el navegador google Chrome, si presenta alg&uacute;n inconveniente, contactarse con el soporte t&eacute;cnico.';

    $body1  = "<table border='0' width='95%' style='font-family: arial, open sans'>";
    $body1 .= '<tr><td colspan="3" style="padding:8px 0px;font-size:22px;" >'.$From.'</td></tr>';
    $body1 .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
    $body1 .= '<tr><td colspan="3" style="font-size:13px;padding:0px 0px 20px 0px;"> '.$Texto.'</td></tr>';
    
    $body2 = "";
    if(!empty($Imagen)){
        $body2 .= "<tr><td colspan='3' style='padding:8px 10px;font-size:10px;max-width:100%' ><span>Si no puede visualizar la imagen correctamente, por favor haga <a href='$ImagenMSJ' target='_blank'>Clic aquí</a></span></td></tr>";
	    $body2 .= "<tr><td colspan='3' style='padding:8px 0px;font-size:10px;color:#6b6b6b' ><img style='display:flex' src=".$ImagenMSJ."  /></td></tr>";
    }
	
    $body2 .= '<tr>
				<td  style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none">
				  <center><a style="color:#f9f9f9;text-decoration: none;" href="'.$ipHost . $nombreEmp . '"  target="_blank">Ingresar</a></center></td>
				 </td>';
	$body2 .= '  <td style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none">
				  <center><a style="color:#f9f9f9;text-decoration: none;" href="mailto:'.$EmailEmisorAuxiliar.'"  target="_blank">Coordinación</a></center></td>
				 </td>';
	$body2 .= '  <td style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none">
				  <center><a style="color:#f9f9f9;text-decoration: none;" href="mailto:soporteusuario@owl-group.org"  target="_blank">Soporte</a></center></td>
				</td>
			   </tr>';
			   
    $body2 .= '<tr><td colspan="3" style="padding:8px 0px;font-size:10px;color:#6b6b6b" >'.$Nota.'</td></tr>';
    
    if(!empty($Documento)){
      $body2 .= "<tr><td colspan='3' style='padding:8px 10px;font-size:10px;max-width:100%' ><span><a href='$DocumentoMSJ' target='_blank'>DESCARGAR DOCUMENTO ADJUNTO</a></span></td></tr>";
    }				   
    
    $body2 .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
    $body2 .= '<tr><td colspan="3" style="font-size:12px;border-bottom: 2px solid #F3F3F3;width:100%;padding:20px 0px;color:#6b6b6b" >Atentamente</td></tr>';
    $body2 .= '<tr><td colspan="3" style="width:100%;padding:5px 0px;color:#6b6b6b;font-size:9px ">Datos de Soporte:   Correo: soporteusuario@owl-group.org | Tel&eacute;fono.: 6440640 | Anexo: 604  </td></tr>';
    $body2 .= '<tr><td colspan="3" style="font-size:10px;" >Fecha: ' . $hoy . '<br /></td></tr>';
	
    $body2 .= "</table>";

    

  
	
	
	if(empty($EmailEmisor)){
        $EmailEmisor  = 'informacion@sgem.info';
	}else{
        $EmailEmisor  = $EmailEmisor;	
	}
	
    //$emailE = emailSES3($n_a, $destinatario,"PLATAFORMA EDUCATIVA", $body,'','',$From,$EmailEmisor);

   
    
    if($rowJefe['IdEmailJefe']){
       // $DatosJefe = "SELECT Jefe,Apellido FROM usuario_jefe WHERE IdEmailJefe='{$rowJefe['IdEmailJefe']}' ";
       // $rowDJ = fetch($DatosJefe);
       // $NameJefe = $rowDJ['Jefe']." ".$rowDJ['Apellido'];

        #Usuario
       // $Copia = '<BR><span style="padding:8px 0px;font-size:12px;color:#E20D0D" >*Este correo fue copiado al sr(a). <span style="color:#35A9AD">'.$NameJefe.'</span>.</span>';
        

        if($EnviarJefe){
          $emailE = emailSES3($n_a, $destinatario,"PLATAFORMA EDUCATIVA", $body1.$bodyA.$body2,'','',$From,$EmailEmisor);
        #Jefe
       // $Copia = '<BR><span style="padding:8px 0px;font-size:12px;color:#E20D0D" >*Este correo es una copia del correo enviado al participante <span style="color:#35A9AD">'.$n_a.'</span>.</span>';
          $destinatario = $rowJefe['IdEmailJefe'];
          $emailE1 = emailSES3($n_a, $destinatario,"PLATAFORMA EDUCATIVA", $body1.$bodyJ.$body2,'','',$From,$EmailEmisor);    
        }else{
            $emailE = emailSES3($n_a, $destinatario,"PLATAFORMA EDUCATIVA", $body1.$body2,'','',$From,$EmailEmisor);
        }

        
    }else{
        $emailE = emailSES3($n_a, $destinatario,"PLATAFORMA EDUCATIVA", $body1.$body2,'','',$From,$EmailEmisor);
    }

    return $emailE;
}





function InscripcionUsuarioEntidad($data, $link_identifier) {

    $email = $data['Usuario'];
    $empresa = $data['empresa'];
    $Perfil = $data['Perfil'];
    $Nombres = $data['Nombres'];
    $Apellidos = $data['Apellidos'];
    $Sede = $data['Sede'];
    $IdJefe = $data['IdJefe'];
    $email = strtolower($email);
    $email = trim($email);

    if (validEmail($email)) {
	
        $sql = "SELECT * FROM usuario_entidad
                WHERE Usuario = '$email'
                AND EntidadCreadora = '$empresa'
                LIMIT 1";
        $dataUserEntidad = fetchOne($sql, $link_identifier);

        if (empty($dataUserEntidad)) {
            $dataInsertUsuarioEntidad = array(
                'Usuario' => $email,
                'Perfil' => $Perfil,
                'EntidadCreadora' => $empresa,
                'Nombres' => $Nombres,
                'Apellidos' => $Apellidos,
                'Cargo_id' => 12,
                'Area' => 17,
                'Sede' => $Sede,
                'Perfil' => 3,
                'Escuela' => 24,
                'ForzarContrasena' => 'Si',
                'GuiaEstudio' => 'Si',
                'IdJefe' => $IdJefe
            );
            $usuarioEntidadId = insert('usuario_entidad', $dataInsertUsuarioEntidad, $link_identifier);
            $rpta = "SI";
        } else {
//	     W(Msg("El usuario ya existe en la entidad","E"));
            $rpta = "NO";
        }
		
    } else {
        $rpta = "NO";
    }
    return $rpta;
}

// TODO LO QUE NECESITA PARA USAR LA FUNCION [MatriculaAlumno]
/*
    $DATA=array(
        "proveedor"=> Es la empresa creadora. Ejemplo: fri.com.pe
        "productoId"=> Es el producto principal al que se matriculara el alumno
        "alumno"=> Es el email del Alumno seguido del postfijo Alumno. Ejemplo: aaronplate95@gmail.comAlumno
        "FacturasCab"=> Es la factura de Cabezera generada por el Programa (SIN USO)
        "FacturasDet"=> Es la factura de Detalle generada por el Programa (SIN USO)
        "CodAlmacenPN"=> Es el Codigo de Primer Nivel
        "CodAlmacenSN"=> Es el Codigo de Segundo Nivel
        "CodAlmacenTN"=> Es el Codigo de Tercer Nivel
    );

    Entonces explicaremos los parametros a pasar por la variable ARRAY $DATA.
    Todos los campos deben estar llenos, pero menos CodAlmacenPN,CodAlmacenSN,CodAlmacenTN por que estos varian
    segun el Nivel de Matricula y el Tipo de Programa
    -----------------------------------------------------------------------------------------------------------------
    [NIVEL DE MATRICULA]     [TIPO DE PROGRAMA]     
    -----------------------------------------------------------------------------------------------------------------
    CURSO                    [DIPLOMADO][SEMINARIO]
                             DEFINIR CodAlmacenSN CON EL MISMO VALOR QUE productoId 
                             DEFINIR CodAlmacenPN CON Curso a matricular

                            (SE GENERA UN DETALLE DE CURSOS DEL PROGRAMA EN MATRICULAS)
    -----------------------------------------------------------------------------------------------------------------
    PROGRAMA                 [DIPLOMADO][SEMINARIO][CURSO]
                             DEFINIR CodAlmacenSN CON EL MISMO VALOR QUE productoId (SOLO SE GENERA SOLO UN REGISTRO EN MATRICULAS)

                             [EXTENDIDO]
                             DEFINIR CodAlmacenTN CON EL MISMO VALOR QUE productoId 
                             DEFINIR CodAlmacenSN CON EL Programa a matricular

                            (SE GENERA UN DETALLE DE PROGRAMAS DEL PROGRAMA EXTENDIDO EN MATRICULAS)
    -----------------------------------------------------------------------------------------------------------------
    MODULO                   [EXTENDIDO]
                             DEFINIR CodAlmacenTN CON EL MISMO VALOR QUE productoId (SOLO SE GENERA SOLO UN REGISTRO EN MATRICULAS)
    -----------------------------------------------------------------------------------------------------------------
*/



?>
