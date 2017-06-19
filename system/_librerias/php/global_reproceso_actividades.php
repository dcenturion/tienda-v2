<?php

function Reproceso($ProgramaAlmacen,$vConex,$Codigo_Entidad_Usuario,$alumnos) {

    foreach($alumnos as $Alumno) {

        $SqlCurricula = "SELECT ProductoCod FROM curricula WHERE CodProgAlmacen={$ProgramaAlmacen} ";
        $RowCur = fetchAll($SqlCurricula);
        foreach ($RowCur as $obj) {

            $CursoAlmacen = $obj->ProductoCod;

            $Q_E = "SELECT EEDC.EvalConfigCurso,EEDC.Codigo,EEDC.Abreviacion
                    FROM elevaluaciondetallecurso AS EEDC
                    INNER JOIN elevaluacionconfcurso AS EECC ON EEDC.EvalConfigCurso=EECC.Codigo
                    INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
                    WHERE EECC.Almacen={$CursoAlmacen}
                    GROUP BY EEDC.Codigo
                    ORDER BY EEDC.EvalConfigCurso";
            $MxE = fetchAll($Q_E, $vConex);

            $FechaHora = FechaHoraSrv();

            foreach ($MxE as $Evaluacion) {
                //EvaluacionDetalleCurso de la tabla elrecursoevaluacion debe estar lleno
                if (!empty($Evaluacion->Codigo)) {

                    $Q_EA = "SELECT Codigo
                                    FROM elevaluacionalumno
                                    WHERE Alumno='{$Alumno}'
                                    AND EvalDetCurso={$Evaluacion->Codigo}";
                    $ObjEA = fetchOne($Q_EA, $vConex);
                    if (!$ObjEA) {
                        $data_insert = array(
                            "EvalConfigCurso" => $Evaluacion->EvalConfigCurso,
                            "EvalDetCurso" => $Evaluacion->Codigo,
                            "Abrev" => $Evaluacion->Abreviacion,
                            "Nota" => 0,
                            "Alumno" => $Alumno,
                            "FechaRegistro" => $FechaHora
                        );

                        insert("elevaluacionalumno", $data_insert, $vConex);
                        update_recurso_eval($ProgramaAlmacen, $Alumno, $vConex);

                        $SQL = "select * from elrecursoevaluacion where EvaluacionDetalleCurso = " . $Evaluacion->Codigo . "";
                        $rg = fetch($SQL);
                        $Codigo_RE = $rg["Codigo"];

                        $Q_RE = "select * from eltransrespuesta_cab where Alumno = '" . $Alumno . "' and Recurso = '" . $Codigo_RE . "'";
                        $Obj = fetchOne($Q_RE, $vConex);
                        $CodTransrespuesta_cab = $Obj->Codigo;

                        $sql2 = "INSERT INTO eltransrespuesta_cab_modificaciones (CodEltransrespuesta_cab, FechaActualizacion, Estado, Usuario)
                                      VALUES ('" . $CodTransrespuesta_cab . "','" . $FechaHora . "','Pendiente', '" . $Codigo_Entidad_Usuario . "')";
                        xSQL($sql2, $vConex);

                        W(Msg("Alumno {$Alumno}: Se añadio la evaluación {$Evaluacion->Abreviacion} a su lista &check;", "A"));
                    }
                }
            }

        }
    }

}
