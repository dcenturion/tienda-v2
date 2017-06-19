<?php
# DECLARACION DE VARIABLES CONSTANTES
//TIPO DE EVALUACIONES
define("EVALUACION_DESEMPENO", 1);
define("EVALUACION_FINAL", 2);
define("PARTICIPACION", 3);
define("PRACTICA", 4);
define("PROMEDIO_EVALUACION_CONTINUA", 5);
define("TAREA_ACADEMICA", 6);
define("TRABAJO_INICIAL", 7);
define("TRABAJO_FINAL", 8);
define("TRABAJO_PARCIAL", 9);
define("ASISTENCIA", 10);
define("INTERACCION", 11);
define("CASO", 12);
define("CONTROL", 13);
define("TRABAJO_INDIVIDUAL", 14);
define("TRABAJO_GRUPAL", 15);
define("NOTA_FINAL", 16);
define("EXAMEN_FINAL", 17);
define("EXAMEN_PARCIAL", 18);
define("CUESTINARIO", 20);
define("TEST", 21);
define("INTERACCION_PARCIAL", 22);
define("METODOLOGIA_PROFESOR", 23);
define("DESARROLLO_CONTENIDO_PERTINECIA", 24);
define("RESPUESTA_OPORTUNA_CLARA", 25);
define("EXPLICACION_CONTENIDO", 26);
define("TRATO_ESTUDIANTE", 27);

function procesaSumarioCursos($ProgramaAlmacen,$CursoAlmacen, $CodigoCurso,$FechaHora, $Codigo_Entidad_Usuario, $entidadCreadora, $vConex ){

    $sql = "SELECT AliasListaNota, ExportarNotaPromedio,Control, NotaMinCertificacion, NotaMaxCertificacion FROM almacen WHERE AlmacenCod = " . $ProgramaAlmacen . "   ";
    $rg = fetch($sql);
    $AliasListaNota = $rg["AliasListaNota"];
    $ExportarNotaPromedio = $rg["ExportarNotaPromedio"];
    $NotaMinCertificacion = $rg["NotaMinCertificacion"];
    $NotaMaxCertificacion = $rg["NotaMaxCertificacion"];
    $Control = $rg["Control"];

    $MxET = array();
    $MxET[] = METODOLOGIA_PROFESOR;
    $MxET[] = DESARROLLO_CONTENIDO_PERTINECIA;
    $MxET[] = RESPUESTA_OPORTUNA_CLARA;
    $MxET[] = EXPLICACION_CONTENIDO;
    $MxET[] = TRATO_ESTUDIANTE;
    $FILTER_ET = implode(",", $MxET);
    $NOT_INCLUDE = "";
    $EvaluacionesAuditoria = get("EvaluacionesAuditoria");
    if (!$EvaluacionesAuditoria) {
        $NOT_INCLUDE = "NOT";
    }

    $sql = " DELETE FROM sumario_acta_notas WHERE ProgramaAlmacen = ".$ProgramaAlmacen."   AND  CursoAlmacen = ".$CursoAlmacen."   ";
    xSQL($sql, $vConex);

    # Q_EE: Query Estado de Evaluaciones
    $Q_EE = "
			    SELECT US.CodigoParlante  AS Usuario,  US.Usuario AS Email
				, CONCAT( ALUM.Nombres,'  ', ALUM.ApellidosPat ) AS PARTICIPANTE
				,EEDC.Codigo AS Actividad
				,ETRC.Estado AS EstadoActividad
				,ETRC.Nota
				,EEDC.AliasExtendido
				,EEDC.AliasAbreviado
				,EECC.Codigo AS Concepto
				,EECC.Peso
				,RE.RecursoAcademico, RE.RecursoTipo,
				RE.Codigo AS Recurso
				,(CASE WHEN ETRC.Nota < ".$NotaMinCertificacion." THEN  'Desaprobado' ELSE  'Aprobado' END )  AS EstadoAprobatorio
				FROM eltransrespuesta_cab AS ETRC
				INNER JOIN elrecursoevaluacion AS RE ON RE.Codigo=ETRC.Recurso
				INNER JOIN elevaluaciondetallecurso AS EEDC ON EEDC.Codigo=RE.EvaluacionDetalleCurso
				INNER JOIN elevaluacionconfcurso AS EECC ON EECC.Codigo=EEDC.EvalConfigCurso
				INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
				INNER JOIN usuarios AS US ON US.IdUsuario = ETRC.Alumno
				INNER JOIN matriculas as MAT on (ETRC.Alumno = MAT.Cliente AND MAT.Producto = {$ProgramaAlmacen} )
				INNER JOIN usuario_entidad  AS UE ON (REPLACE(ETRC.Alumno,'Alumno','') = UE.Usuario AND UE.EntidadCreadora = '{$entidadCreadora}')
				INNER JOIN alumnos AS ALUM ON ETRC.Alumno = ALUM.Usuario
				WHERE EECC.Almacen={$CursoAlmacen}
				AND EEDC.SCpt_Evaluado='SI'  AND MAT.Estado  NOT IN('Anulado','Eliminado')
				AND ETRC.Alumno<>''
				AND EET.Codigo {$NOT_INCLUDE} IN({$FILTER_ET})
				AND (MAT.visualizacion_actividad= 'si' OR MAT.visualizacion_actividad = '') ";

    // WE($Q_EE);

    $MxDetEval = fetchAll($Q_EE, $vConex);
    foreach ($MxDetEval as $DetEval) {

        $Usuario = $DetEval->Usuario;
        $Email = $DetEval->Email;
        $PARTICIPANTE = $DetEval->PARTICIPANTE;
        $Actividad = $DetEval->Actividad;
        $EstadoActividad = $DetEval->EstadoActividad;
        $Nota = $DetEval->Nota;
        $AliasExtendido = $DetEval->AliasExtendido;
        $AliasAbreviado = $DetEval->AliasAbreviado;

        $EstadoAprobatorio = $DetEval->EstadoAprobatorio;

        $RecursoAcademico = $DetEval->RecursoAcademico;
        $RecursoTipo = $DetEval->RecursoTipo;
        $Concepto = $DetEval->Concepto;
        $Peso = $DetEval->Peso;
        $Recurso = $DetEval->Recurso;

        $data_AE= array(
            'ProgramaAlmacen' => $ProgramaAlmacen,
            'CursoAlmacen' => $CursoAlmacen,
            'EntidadCreadora' => $entidadCreadora,
            'Actividad' => $Actividad,
            'EstadoActividad' => $EstadoActividad,
            'EstadoReporte' => 'PENDIENTE',
            'ActividadDescAbrev' =>$AliasAbreviado,
            'ActividadDescExtendida' =>$AliasExtendido,
            'Usuario'=>$Usuario,
            'Email'=>$Email,
            'NombresApellidos'=>$PARTICIPANTE,
            'EstadoAprobatorio'=>$EstadoAprobatorio,
            'FechaHoraCreacion'=>$FechaHora,
            'FechaHoraActualizacion'=>$FechaHora,
            'UsuarioCreacion'=>$Codigo_Entidad_Usuario,
            'UsuarioActualizacion'=>$Codigo_Entidad_Usuario,
            'RecursoAcademico'=>$RecursoAcademico,
            'RecursoTipo'=>$RecursoTipo,
            'Concepto'=>$Concepto,
            'PesoConcepto'=>$Peso,
            'Recurso'=>$Recurso,
            'Nota'=>$Nota

        );
        insert("sumario_acta_notas", $data_AE);

    }
}

function procesaSumarioCursosCabezera($ProgramaAlmacen,$CursoAlmacen, $CodigoCurso,$FechaHora, $Codigo_Entidad_Usuario, $entidadCreadora, $vConex ){

    $sql = "SELECT AliasListaNota, ExportarNotaPromedio,Control, NotaMinCertificacion, NotaMaxCertificacion FROM almacen WHERE AlmacenCod = " . $ProgramaAlmacen . "   ";

    $rg = fetch($sql);
    $AliasListaNota = $rg["AliasListaNota"];
    $ExportarNotaPromedio = $rg["ExportarNotaPromedio"];
    $NotaMinCertificacion = $rg["NotaMinCertificacion"];
    $NotaMaxCertificacion = $rg["NotaMaxCertificacion"];
    $Control = $rg["Control"];

    $MxET = array();
    $MxET[] = METODOLOGIA_PROFESOR;
    $MxET[] = DESARROLLO_CONTENIDO_PERTINECIA;
    $MxET[] = RESPUESTA_OPORTUNA_CLARA;
    $MxET[] = EXPLICACION_CONTENIDO;
    $MxET[] = TRATO_ESTUDIANTE;
    $FILTER_ET = implode(",", $MxET);
    $NOT_INCLUDE = "";
    $EvaluacionesAuditoria = get("EvaluacionesAuditoria");
    if (!$EvaluacionesAuditoria) {
        $NOT_INCLUDE = "NOT";
    }

    $sql = " DELETE FROM sumario_actividades_cab WHERE ProgramaAlmacen = ".$ProgramaAlmacen."   AND  CursoAlmacen = ".$CursoAlmacen."   ";
    xSQL($sql, $vConex);

    # Q_EE: Query Estado de Evaluaciones
    $Q_EE = "
			    SELECT US.CodigoParlante  AS Usuario,  US.Usuario AS Email
				, CONCAT( ALUM.Nombres,'  ', ALUM.ApellidosPat ) AS PARTICIPANTE
				,EEDC.Codigo AS Actividad
				,ETRC.Estado AS EstadoActividad
				,ETRC.Nota
				,EECC.Peso
				,EECC.Codigo AS Concepto
				,EEDC.AliasExtendido
				,EEDC.AliasAbreviado
				,RE.RecursoAcademico, RE.RecursoTipo
				,(CASE WHEN ETRC.Nota < ".$NotaMinCertificacion." THEN  'Desaprobado' ELSE  'Aprobado' END )  AS EstadoAprobatorio
				FROM eltransrespuesta_cab AS ETRC INNER JOIN elrecursoevaluacion AS RE ON RE.Codigo=ETRC.Recurso
				INNER JOIN elevaluaciondetallecurso AS EEDC ON EEDC.Codigo=RE.EvaluacionDetalleCurso
				INNER JOIN elevaluacionconfcurso AS EECC ON EECC.Codigo=EEDC.EvalConfigCurso
				INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
				INNER JOIN usuarios AS US ON US.IdUsuario = ETRC.Alumno
				INNER JOIN matriculas as MAT on (ETRC.Alumno = MAT.Cliente AND MAT.Producto = {$ProgramaAlmacen} )
				INNER JOIN usuario_entidad  AS UE ON (REPLACE(ETRC.Alumno,'Alumno','') = UE.Usuario AND UE.EntidadCreadora = '{$entidadCreadora}')
				INNER JOIN alumnos AS ALUM ON ETRC.Alumno = ALUM.Usuario
				WHERE EECC.Almacen={$CursoAlmacen}  AND MAT.Estado  NOT IN('Anulado','Eliminado')
				AND EEDC.SCpt_Evaluado='SI'
				AND ETRC.Alumno<>''
				AND EET.Codigo {$NOT_INCLUDE} IN({$FILTER_ET}) ";
   
    $SQLTotPeso   = " SELECT  ";
    $SQLTotPeso  .= " CS1.Peso ";
    $SQLTotPeso  .= " FROM   ";
    $SQLTotPeso  .= " (".$Q_EE .")  AS  CS1 ";
    $SQLTotPeso  .= " GROUP BY  CS1.Concepto ";

    $SQLSumPeso   = " SELECT  ";
    $SQLSumPeso  .= " SUM(CS1.Peso) AS TotPeso ";
    $SQLSumPeso  .= " FROM   ";
    $SQLSumPeso  .= " (".$SQLTotPeso .")  AS  CS1 ";
    $rg = fetch($SQLSumPeso);
    $TotPeso = $rg["TotPeso"];
    
    $sqlResumenA = "
				SELECT '1'  AS ActividadMarcador  FROM (".$Q_EE.") AS CSL1
				GROUP BY CSL1.Actividad";

    $sqlResumen = "
				SELECT  SUM(CSL1.ActividadMarcador)  AS CtdadActividad FROM (".$sqlResumenA.") AS CSL1 ";

        
        
    $rg = fetch($sqlResumen);

    $CtdadActividad = $rg["CtdadActividad"];	

    if ($CtdadActividad == null){
      //W("hola-------->>>>>");
      Configuracion("listar");
      //W("hola-------->>>>>");
    }

    $sqlResumenRecursoTipo = "
				SELECT '1'  AS RecursoTipoMarcador  FROM (".$Q_EE.") AS CSL1
				GROUP BY CSL1.RecursoTipo";

    $sqlResumen = "
				SELECT  SUM(CSL1.RecursoTipoMarcador)  AS CtdadRecursoTipo FROM (".$sqlResumenRecursoTipo.") AS CSL1 ";

      //W("hola-------->>>>>");
    $rg = fetch($sqlResumen);
      

    $CtdadRecursoTipo = $rg["CtdadRecursoTipo"];	//////////////////////////////////7

    $sqlResumenA2D = "
				SELECT '1'  AS Participante, SUM(CSL1.Nota) AS TotNota, CSL1.Email    FROM (".$Q_EE.") AS CSL1
				GROUP BY CSL1.Email ";

    $sqlResumenA2 = "
				SELECT  SUM(CSL1.Participante)  AS CtdadParticipante FROM (".$sqlResumenA2D.") AS CSL1 ";
    $rg = fetch($sqlResumenA2);
    $CtdadParticipante = $rg["CtdadParticipante"];    

    $agrupaTotAlumno = "
				SELECT
				(((CSL1.TotNota / ".$CtdadActividad.")* ".$TotPeso.")/100) AS NotaAlumno
				FROM (".$sqlResumenA2D.") AS CSL1
				GROUP BY CSL1.Email ";

    $SQLTotAprobadosSum  = "  SELECT  ";
    $SQLTotAprobadosSum  .= " COUNT(*) AS  TotAlumnos  ";
    $SQLTotAprobadosSum  .= " FROM   ";
    $SQLTotAprobadosSum  .= " (".$agrupaTotAlumno .")  AS  CS1 ";
    $SQLTotAprobadosSum  .= "  WHERE CS1.NotaAlumno >= ".$NotaMinCertificacion." ";

    $rg = fetch($SQLTotAprobadosSum);
    $TotAlumnosAprobados = $rg["TotAlumnos"];

    $TotalAlumnoDesaprobados = $CtdadParticipante - $TotAlumnosAprobados;



    $estadoDocumentos = "
				SELECT  CSL1.EstadoActividad, Count(*)  AS TotalEstado   FROM (".$Q_EE.") AS CSL1
				GROUP BY CSL1.EstadoActividad ";

    $documentosPendientes ="
				SELECT  CSL1.EstadoActividad, CSL1.TotalEstado   FROM (".$estadoDocumentos.") AS CSL1
				WHERE  CSL1.EstadoActividad = 'Pendiente' ";

    $rg = fetch($documentosPendientes);
    $docPendientes = $rg["TotalEstado"];

    $documentosRevisados ="
				SELECT  CSL1.EstadoActividad, CSL1.TotalEstado   FROM (".$estadoDocumentos.") AS CSL1
				WHERE  CSL1.EstadoActividad = 'Revisado' ";
    $rg = fetch($documentosRevisados);
    $docRevisado = $rg["TotalEstado"];

    $documentosIniciado ="
				SELECT  CSL1.EstadoActividad, CSL1.TotalEstado   FROM (".$estadoDocumentos.") AS CSL1
				WHERE  CSL1.EstadoActividad = 'Iniciado' ";
    $rg = fetch($documentosIniciado);
    $docIniciado = $rg["TotalEstado"];

    $documentosPorRevisar ="
				SELECT  CSL1.EstadoActividad, CSL1.TotalEstado   FROM (".$estadoDocumentos.") AS CSL1
				WHERE  (CSL1.EstadoActividad = 'Enviado'  OR CSL1.EstadoActividad = 'RevisionIniciado' ) ";
    $rg = fetch($documentosPorRevisar);
    $docPorRevisar = $rg["TotalEstado"];

    //W("hola");

    $data_AE= array(
        'ProgramaAlmacen' => $ProgramaAlmacen,
        'CursoAlmacen' => $CursoAlmacen,
        'EntidadCreadora' => $entidadCreadora,
        'EstadoReporte' => 'PENDIENTE',
        'FechaHoraCreacion'=>$FechaHora,
        'FechaHoraActualizacion'=>$FechaHora,
        'UsuarioCreacion'=>$Codigo_Entidad_Usuario,
        'UsuarioActualizacion'=>$Codigo_Entidad_Usuario,
        'TotalActividades'=>$CtdadActividad,
        'TotalAlumnos'=>$CtdadParticipante,
        'TotalAprobados'=>$TotAlumnosAprobados,
        'TotalDesaprobados'=>$TotalAlumnoDesaprobados,
        'TotalRevisados'=>$docRevisado,
        'TotalPendientes'=>$docPendientes,
        'TotalIniciados'=>$docIniciado,
        'TotalEnviados'=>$docPorRevisar,
        'TotalTiposRecurso'=>$CtdadRecursoTipo
    );
    insert("sumario_actividades_cab", $data_AE);
    return;

}

function procesaSumarioCursosPrograma($ProgramaAlmacen,$CursoAlmacen, $CodigoCurso,$FechaHora, $Codigo_Entidad_Usuario, $entidadCreadora, $vConex ){

      $sqlCursos="SELECT ProductoCod as 'CodigoAamacenCurso' FROM curricula where CodProgAlmacen=" . $ProgramaAlmacen . "   ";
      $ftchCursos=fetchAll($sqlCursos);
      foreach($ftchCursos as $ftchCursos){
          $CursoAlmacen=$ftchCursos->CodigoAamacenCurso;



      $sql = "SELECT AliasListaNota, ExportarNotaPromedio,Control, NotaMinCertificacion, NotaMaxCertificacion FROM almacen WHERE AlmacenCod = " . $ProgramaAlmacen . "   ";
      $rg = fetch($sql);
      $AliasListaNota = $rg["AliasListaNota"];
      $ExportarNotaPromedio = $rg["ExportarNotaPromedio"];
      $NotaMinCertificacion = $rg["NotaMinCertificacion"];
      $NotaMaxCertificacion = $rg["NotaMaxCertificacion"];
      $Control = $rg["Control"];

      $MxET = array();
      $MxET[] = METODOLOGIA_PROFESOR;
      $MxET[] = DESARROLLO_CONTENIDO_PERTINECIA;
      $MxET[] = RESPUESTA_OPORTUNA_CLARA;
      $MxET[] = EXPLICACION_CONTENIDO;
      $MxET[] = TRATO_ESTUDIANTE;
      $FILTER_ET = implode(",", $MxET);
      $NOT_INCLUDE = "";
      $EvaluacionesAuditoria = get("EvaluacionesAuditoria");
      if (!$EvaluacionesAuditoria) {
          $NOT_INCLUDE = "NOT";
      }

      $sql = " DELETE FROM sumario_acta_notas WHERE ProgramaAlmacen = ".$ProgramaAlmacen."   AND  CursoAlmacen = ".$CursoAlmacen."   ";
      xSQL($sql, $vConex);

      # Q_EE: Query Estado de Evaluaciones
      $Q_EE = "
                  SELECT US.CodigoParlante  AS Usuario,  US.Usuario AS Email
                  , CONCAT( ALUM.Nombres,'  ', ALUM.ApellidosPat ) AS PARTICIPANTE
                  ,EEDC.Codigo AS Actividad
                  ,ETRC.Estado AS EstadoActividad
                  ,ETRC.Nota
                  ,EEDC.AliasExtendido
                  ,EEDC.AliasAbreviado
                  ,EECC.Codigo AS Concepto
                  ,EECC.Peso
                  ,RE.RecursoAcademico, RE.RecursoTipo,
                  RE.Codigo AS Recurso
                  ,(CASE WHEN ETRC.Nota < ".$NotaMinCertificacion." THEN  'Desaprobado' ELSE  'Aprobado' END )  AS EstadoAprobatorio
                  FROM eltransrespuesta_cab AS ETRC
                  INNER JOIN elrecursoevaluacion AS RE ON RE.Codigo=ETRC.Recurso
                  INNER JOIN elevaluaciondetallecurso AS EEDC ON EEDC.Codigo=RE.EvaluacionDetalleCurso
                  INNER JOIN elevaluacionconfcurso AS EECC ON EECC.Codigo=EEDC.EvalConfigCurso
                  INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
                  INNER JOIN usuarios AS US ON US.IdUsuario = ETRC.Alumno
                  INNER JOIN matriculas as MAT on (ETRC.Alumno = MAT.Cliente AND MAT.Producto = {$ProgramaAlmacen} )
                  INNER JOIN usuario_entidad  AS UE ON (REPLACE(ETRC.Alumno,'Alumno','') = UE.Usuario AND UE.EntidadCreadora = '{$entidadCreadora}')
                  INNER JOIN alumnos AS ALUM ON ETRC.Alumno = ALUM.Usuario
                  WHERE EECC.Almacen={$CursoAlmacen}
                  AND EEDC.SCpt_Evaluado='SI'  AND MAT.Estado  NOT IN('Anulado','Eliminado')
                  AND ETRC.Alumno<>''
                  AND EET.Codigo {$NOT_INCLUDE} IN({$FILTER_ET})
                  AND (MAT.visualizacion_actividad= 'si' OR MAT.visualizacion_actividad = '') ";

      // WE($Q_EE);

      $MxDetEval = fetchAll($Q_EE, $vConex);
      foreach ($MxDetEval as $DetEval) {

          $Usuario = $DetEval->Usuario;
          $Email = $DetEval->Email;
          $PARTICIPANTE = $DetEval->PARTICIPANTE;
          $Actividad = $DetEval->Actividad;
          $EstadoActividad = $DetEval->EstadoActividad;
          $Nota = $DetEval->Nota;
          $AliasExtendido = $DetEval->AliasExtendido;
          $AliasAbreviado = $DetEval->AliasAbreviado;

          $EstadoAprobatorio = $DetEval->EstadoAprobatorio;

          $RecursoAcademico = $DetEval->RecursoAcademico;
          $RecursoTipo = $DetEval->RecursoTipo;
          $Concepto = $DetEval->Concepto;
          $Peso = $DetEval->Peso;
          $Recurso = $DetEval->Recurso;

          $data_AE= array(
              'ProgramaAlmacen' => $ProgramaAlmacen,
              'CursoAlmacen' => $CursoAlmacen,
              'EntidadCreadora' => $entidadCreadora,
              'Actividad' => $Actividad,
              'EstadoActividad' => $EstadoActividad,
              'EstadoReporte' => 'PENDIENTE',
              'ActividadDescAbrev' =>$AliasAbreviado,
              'ActividadDescExtendida' =>$AliasExtendido,
              'Usuario'=>$Usuario,
              'Email'=>$Email,
              'NombresApellidos'=>$PARTICIPANTE,
              'EstadoAprobatorio'=>$EstadoAprobatorio,
              'FechaHoraCreacion'=>$FechaHora,
              'FechaHoraActualizacion'=>$FechaHora,
              'UsuarioCreacion'=>$Codigo_Entidad_Usuario,
              'UsuarioActualizacion'=>$Codigo_Entidad_Usuario,
              'RecursoAcademico'=>$RecursoAcademico,
              'RecursoTipo'=>$RecursoTipo,
              'Concepto'=>$Concepto,
              'PesoConcepto'=>$Peso,
              'Recurso'=>$Recurso,
              'Nota'=>$Nota

          );
          insert("sumario_acta_notas", $data_AE);

      }
  }
}

function procesaSumarioCursosCabezeraPrograma($ProgramaAlmacen,$CursoAlmacen, $CodigoCurso,$FechaHora, $Codigo_Entidad_Usuario, $entidadCreadora, $vConex ){

    $sqlCursos="SELECT ProductoCod as 'CodigoAamacenCurso' FROM curricula where CodProgAlmacen=" . $ProgramaAlmacen . "   ";
    $ftchCursos=fetchAll($sqlCursos);
    foreach($ftchCursos as $ftchCursos) {
        $CursoAlmacen = $ftchCursos->CodigoAamacenCurso;
        //$CursoAlmacen =6195 ;
        $sql = "SELECT AliasListaNota, ExportarNotaPromedio,Control, NotaMinCertificacion, NotaMaxCertificacion FROM almacen WHERE AlmacenCod = " . $ProgramaAlmacen . "   ";
        $rg = fetch($sql);
        $AliasListaNota = $rg["AliasListaNota"];
        $ExportarNotaPromedio = $rg["ExportarNotaPromedio"];
        $NotaMinCertificacion = $rg["NotaMinCertificacion"];
        $NotaMaxCertificacion = $rg["NotaMaxCertificacion"];
        $Control = $rg["Control"];

        $MxET = array();
        $MxET[] = METODOLOGIA_PROFESOR;
        $MxET[] = DESARROLLO_CONTENIDO_PERTINECIA;
        $MxET[] = RESPUESTA_OPORTUNA_CLARA;
        $MxET[] = EXPLICACION_CONTENIDO;
        $MxET[] = TRATO_ESTUDIANTE;
        $FILTER_ET = implode(",", $MxET);
        $NOT_INCLUDE = "";
        $EvaluacionesAuditoria = get("EvaluacionesAuditoria");
        if (!$EvaluacionesAuditoria) {
            $NOT_INCLUDE = "NOT";
        }

        $sql = " DELETE FROM sumario_actividades_cab WHERE ProgramaAlmacen = " . $ProgramaAlmacen . "   AND  CursoAlmacen = " . $CursoAlmacen . "   ";
        xSQL($sql, $vConex);

        # Q_EE: Query Estado de Evaluaciones
        $Q_EE = "
			    SELECT US.CodigoParlante  AS Usuario,  US.Usuario AS Email
				, CONCAT( ALUM.Nombres,'  ', ALUM.ApellidosPat ) AS PARTICIPANTE
				,EEDC.Codigo AS Actividad
				,ETRC.Estado AS EstadoActividad
				,ETRC.Nota
				,EECC.Peso
				,EECC.Codigo AS Concepto
				,EEDC.AliasExtendido
				,EEDC.AliasAbreviado
				,RE.RecursoAcademico, RE.RecursoTipo
				,(CASE WHEN ETRC.Nota < " . $NotaMinCertificacion . " THEN  'Desaprobado' ELSE  'Aprobado' END )  AS EstadoAprobatorio
				FROM eltransrespuesta_cab AS ETRC INNER JOIN elrecursoevaluacion AS RE ON RE.Codigo=ETRC.Recurso
				INNER JOIN elevaluaciondetallecurso AS EEDC ON EEDC.Codigo=RE.EvaluacionDetalleCurso
				INNER JOIN elevaluacionconfcurso AS EECC ON EECC.Codigo=EEDC.EvalConfigCurso
				INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
				INNER JOIN usuarios AS US ON US.IdUsuario = ETRC.Alumno
				INNER JOIN matriculas as MAT on (ETRC.Alumno = MAT.Cliente AND MAT.Producto = {$ProgramaAlmacen} )
				INNER JOIN usuario_entidad  AS UE ON (REPLACE(ETRC.Alumno,'Alumno','') = UE.Usuario AND UE.EntidadCreadora = '{$entidadCreadora}')
				INNER JOIN alumnos AS ALUM ON ETRC.Alumno = ALUM.Usuario
				WHERE EECC.Almacen={$CursoAlmacen}  AND MAT.Estado  NOT IN('Anulado','Eliminado')
				AND EEDC.SCpt_Evaluado='SI'
				AND ETRC.Alumno<>''
				AND EET.Codigo {$NOT_INCLUDE} IN({$FILTER_ET}) ";

        // W($Q_EE);

        $SQLTotPeso = " SELECT  ";
        $SQLTotPeso .= " CS1.Peso ";
        $SQLTotPeso .= " FROM   ";
        $SQLTotPeso .= " (" . $Q_EE . ")  AS  CS1 ";
        $SQLTotPeso .= " GROUP BY  CS1.Concepto ";

        $SQLSumPeso = " SELECT  ";
        $SQLSumPeso .= " SUM(CS1.Peso) AS TotPeso ";
        $SQLSumPeso .= " FROM   ";
        $SQLSumPeso .= " (" . $SQLTotPeso . ")  AS  CS1 ";
        $rg = fetch($SQLSumPeso);
        $TotPeso = $rg["TotPeso"];
        // W("peso  (".$TotPeso." ) ");

        $sqlResumenA = "
				SELECT '1'  AS ActividadMarcador  FROM (" . $Q_EE . ") AS CSL1
				GROUP BY CSL1.Actividad";

        $sqlResumen = "
				SELECT  SUM(CSL1.ActividadMarcador)  AS CtdadActividad FROM (" . $sqlResumenA . ") AS CSL1 ";
        $rg = fetch($sqlResumen);
        $CtdadActividad = $rg["CtdadActividad"];    //////////////////////////////////7
        if($CtdadActividad){

            $sqlResumenRecursoTipo = "
				SELECT '1'  AS RecursoTipoMarcador  FROM (" . $Q_EE . ") AS CSL1
				GROUP BY CSL1.RecursoTipo";

            $sqlResumen = "
				SELECT  SUM(CSL1.RecursoTipoMarcador)  AS CtdadRecursoTipo FROM (" . $sqlResumenRecursoTipo . ") AS CSL1 ";
            $rg = fetch($sqlResumen);
            $CtdadRecursoTipo = $rg["CtdadRecursoTipo"];    //////////////////////////////////7

            $sqlResumenA2D = "
				SELECT '1'  AS Participante, SUM(CSL1.Nota) AS TotNota, CSL1.Email    FROM (" . $Q_EE . ") AS CSL1
				GROUP BY CSL1.Email ";

            $sqlResumenA2 = "
				SELECT  SUM(CSL1.Participante)  AS CtdadParticipante FROM (" . $sqlResumenA2D . ") AS CSL1 ";
            $rg = fetch($sqlResumenA2);
            $CtdadParticipante = $rg["CtdadParticipante"];

            $agrupaTotAlumno = "
				SELECT
				(((CSL1.TotNota / " . $CtdadActividad . ")* " . $TotPeso . ")/100) AS NotaAlumno
				FROM (" . $sqlResumenA2D . ") AS CSL1
				GROUP BY CSL1.Email ";

            $SQLTotAprobadosSum = "  SELECT  ";
            $SQLTotAprobadosSum .= " COUNT(*) AS  TotAlumnos  ";
            $SQLTotAprobadosSum .= " FROM   ";
            $SQLTotAprobadosSum .= " (" . $agrupaTotAlumno . ")  AS  CS1 ";
            $SQLTotAprobadosSum .= "  WHERE CS1.NotaAlumno >= " . $NotaMinCertificacion . " ";
            $rg = fetch($SQLTotAprobadosSum);
            $TotAlumnosAprobados = $rg["TotAlumnos"];

            $TotalAlumnoDesaprobados = $CtdadParticipante - $TotAlumnosAprobados;

            $estadoDocumentos = "
				SELECT  CSL1.EstadoActividad, Count(*)  AS TotalEstado   FROM (" . $Q_EE . ") AS CSL1
				GROUP BY CSL1.EstadoActividad ";

            // WE($estadoDocumentos );

            $documentosPendientes = "
				SELECT  CSL1.EstadoActividad, CSL1.TotalEstado   FROM (" . $estadoDocumentos . ") AS CSL1
				WHERE  CSL1.EstadoActividad = 'Pendiente' ";
            $rg = fetch($documentosPendientes);
            $docPendientes = $rg["TotalEstado"];

            $documentosRevisados = "
				SELECT  CSL1.EstadoActividad, CSL1.TotalEstado   FROM (" . $estadoDocumentos . ") AS CSL1
				WHERE  CSL1.EstadoActividad = 'Revisado' ";
            $rg = fetch($documentosRevisados);
            $docRevisado = $rg["TotalEstado"];

            $documentosIniciado = "
				SELECT  CSL1.EstadoActividad, CSL1.TotalEstado   FROM (" . $estadoDocumentos . ") AS CSL1
				WHERE  CSL1.EstadoActividad = 'Iniciado' ";
            $rg = fetch($documentosIniciado);
            $docIniciado = $rg["TotalEstado"];

            $documentosPorRevisar = "
				SELECT  CSL1.EstadoActividad, CSL1.TotalEstado   FROM (" . $estadoDocumentos . ") AS CSL1
				WHERE  (CSL1.EstadoActividad = 'Enviado'  OR CSL1.EstadoActividad = 'RevisionIniciado' ) ";
            $rg = fetch($documentosPorRevisar);
            $docPorRevisar = $rg["TotalEstado"];

            $data_AE = array(
                'ProgramaAlmacen' => $ProgramaAlmacen,
                'CursoAlmacen' => $CursoAlmacen,
                'EntidadCreadora' => $entidadCreadora,
                'EstadoReporte' => 'PENDIENTE',
                'FechaHoraCreacion' => $FechaHora,
                'FechaHoraActualizacion' => $FechaHora,
                'UsuarioCreacion' => $Codigo_Entidad_Usuario,
                'UsuarioActualizacion' => $Codigo_Entidad_Usuario,
                'TotalActividades' => $CtdadActividad,
                'TotalAlumnos' => $CtdadParticipante,
                'TotalAprobados' => $TotAlumnosAprobados,
                'TotalDesaprobados' => $TotalAlumnoDesaprobados,
                'TotalRevisados' => $docRevisado,
                'TotalPendientes' => $docPendientes,
                'TotalIniciados' => $docIniciado,
                'TotalEnviados' => $docPorRevisar,
                'TotalTiposRecurso' => $CtdadRecursoTipo
            );
            insert("sumario_actividades_cab", $data_AE);
            
        }else{
            
            delete('sumario_acta_notas', array('CursoAlmacen' => $CursoAlmacen));

        }


    }
return;
}



function sumarioReporteCursos( $codigoAlmacenCurso, $ProgramaAlmacen,$vConex, $entidadCreadora, $NotaMinCertificacion,$Codigo_Entidad_Usuario,$FechaHora){

    $sq2 = "SELECT AR.Titulo,CUR.Entidad, AL.Nombre_Grupo
			FROM almacen AL
			LEFT JOIN articulos AR ON AL.Producto = AR.Producto
			LEFT JOIN cursos CUR ON CUR.CodCursos = AR.ProductoFab
			WHERE AL.AlmacenCod = ".$codigoAlmacenCurso." ";
    // WE($sq2 );
    $rgB = fetch($sq2);
    $TituloCurso = $rgB["Titulo"];


    $sql = " DELETE FROM sumario_cursos_programa WHERE
			ProgramaAlmacen = ".$ProgramaAlmacen."   AND  CursoAlmacen = ".$codigoAlmacenCurso."   ";
    xSQL($sql, $vConex);

    $sql = " SELECT
			AN.Concepto ,AN.PesoConcepto
			FROM sumario_acta_notas AN
			WHERE  AN.ProgramaAlmacen = " . $ProgramaAlmacen . "   AND AN.CursoAlmacen = " . $codigoAlmacenCurso . "
			GROUP BY AN.Concepto  ORDER BY  AN.Concepto  ";
    $MxDetEvalA = fetchAll($sql, $vConex);
    $sSQL1 ="  ";
    $cb = "";
    $sintaxisSQL1 = "";
    $sintaxisSQL2 = "";
    $sumaConcepto = "0 ";
    $totConcepto = 0;
    $numAprobados = 0;
    foreach ($MxDetEvalA as $DetEvalA) {

        $totConcepto += 1;

        $sql = " SELECT
					AN.Concepto
					,EEDC.AliasExtendido
					,EEDC.AliasAbreviado
					,EEDC.Abreviacion
					,AN.Nota
					,AN.Actividad
					FROM sumario_acta_notas AN
					INNER JOIN elevaluaciondetallecurso AS EEDC ON EEDC.Codigo=AN.Actividad
					INNER JOIN elrecursoevaluacion AS RE ON RE.EvaluacionDetalleCurso = AN.Actividad
					WHERE  AN.ProgramaAlmacen = " . $ProgramaAlmacen . "   AND AN.CursoAlmacen = " . $codigoAlmacenCurso . "
					AND AN.Concepto = " . $DetEvalA->Concepto . "
					GROUP BY AN.Actividad  ORDER BY  AN.Actividad    ";
        $MxDetEvalB = fetchAll($sql, $vConex);
        $sumaActividad =" 0 ";
        $totActividad = 0;
        foreach ($MxDetEvalB as $DetEvalB) {


            $totActividad += 1;

            $sintaxisSQL1 .=" SUM(CASE WHEN AN.Actividad  = ". $DetEvalB->Actividad."  THEN AN.Nota ELSE 0  END)  AS 'ACT" . $DetEvalB->Actividad . "' , ";
            $sintaxisSQL1 .=" SUM(CASE WHEN  AN.Actividad  = ".$DetEvalB->Actividad."  THEN AN.Actividad  ELSE 0 END) AS 'ACTIVIDAD" . $DetEvalB->Actividad . "'  , ";

            $sintaxisSQL4 .=" CS2.ACT" . $DetEvalB->Actividad . " AS  '" . $DetEvalB->AliasExtendido . " (".$DetEvalB->AliasAbreviado.")'  , ";
            $sintaxisSQL4 .=" CS2.Estado" . $DetEvalB->Actividad . "   AS 'Estado".$totActividad."' , ";

            $acumula .= " CS2.Estado" . $DetEvalB->Actividad . "  = 'Revisado' AND ";

            $sintaxisSQL3 .=" CS1.ACT" . $DetEvalB->Actividad . " , ";

            $sintaxisSQL3 .="  (CASE WHEN CS1.ACTIVIDAD" . $DetEvalB->Actividad . "  = ".$DetEvalB->Actividad ." THEN
							(SELECT EstadoActividad FROM sumario_acta_notas WHERE Actividad = ".$DetEvalB->Actividad ."
							AND Email = CS1.Email ) END) AS 'Estado" . $DetEvalB->Actividad . "'	 ,
                             ";
            $sumaActividad .="  + CS1.ACT" . $DetEvalB->Actividad . "  ";

        }
        $sumaConcepto .="  + (((".$sumaActividad.")/".$totActividad.") * (".$DetEvalA->PesoConcepto."/100) )   ";
        $sintaxisSQL3 .= " ROUND((((".$sumaActividad.")/".$totActividad.") * (".$DetEvalA->PesoConcepto."/100)),2) AS 'Ponderado".$totConcepto."' ,  ";

        $sintaxisSQL4 .=" CS2.Ponderado" . $totConcepto . "  AS 'Ponderado".$totConcepto." (".$DetEvalA->PesoConcepto.")'  , ";
    }

    $sintaxisSQL3 .= " ROUND(( ".$sumaConcepto ."),2) AS 'Promedio_General'  ";

    $sintaxisSQL4 .=" CS2.Promedio_General, ";

    $sintaxisSQL4 .="  (CASE WHEN  ".$acumula."   1=1   THEN
								   (CASE WHEN CS2.Promedio_General < ".$NotaMinCertificacion."
									   THEN 'Desaprobado'
									   ELSE 'Aprobado'
									   END)
							  ELSE
								  'Pendiente'
							  END
							 ) AS 'Estado_General'  ";


    // WE($sintaxisSQL4);
    $sqlMx = " SELECT
			AN.Usuario,
			AN.NombresApellidos AS Participantes,
			AN.EstadoActividad ,
			 AN.Actividad,
			".$sintaxisSQL1."
			AN.Email
			FROM sumario_acta_notas AN
			WHERE  AN.ProgramaAlmacen = " . $ProgramaAlmacen . "   AND AN.CursoAlmacen = " . $codigoAlmacenCurso . "
			GROUP BY AN.Email ";

    $sqlP = " SELECT
			 CS1.Usuario AS DNI
			,CS1.PARTICIPANTES
			,CS1.Email
			,".$sintaxisSQL3."
			FROM  (".$sqlMx.")  AS CS1 ";

    $sqlP2 = " SELECT
			 CS2.DNI
			,CS2.PARTICIPANTES
			,CS2.Email
			,".$sintaxisSQL4."
			FROM  (".$sqlP.")  AS CS2 ";


    $resultados = fetchAll($sqlP2, $vConex);
    $aprobados = 0;
    $Desaprobados = 0;
    $TotAlumnos = 0;
    foreach ($resultados as $resul) {

        if($resul->Estado_Geneal == "Aprobado"){
            $aprobados += 1;
        }

        if($resul->Estado_Geneal == "Desaprobado"){
            $Desaprobados += 1;
        }

        $TotAlumnos += 1;

        $data_AE= array(
            'ProgramaAlmacen' => $ProgramaAlmacen,
            'CursoAlmacen' => $codigoAlmacenCurso,
            'EntidadCreadora' => $entidadCreadora,
            'FechaHoraCreacion'=>$FechaHora,
            'FechaHoraActualizacion'=>$FechaHora,
            'UsuarioCreacion'=>$Codigo_Entidad_Usuario,
            'UsuarioActualizacion'=>$Codigo_Entidad_Usuario,
            'Email'=>$resul->Email,
            'Nombres'=>$resul->PARTICIPANTES,
            'Usuario'=>$resul->DNI,
            'EstadoAprobatorio'=>$resul->Estado_General,
            'Nota'=>$resul->Promedio_General,
            'NombreCurso'=>$TituloCurso
        );
        insert("sumario_cursos_programa", $data_AE);

    }
    $viewdata = array();
    $viewdata['TotAlumnos'] = $TotAlumnos;
    $viewdata['Desaprobados'] = $Desaprobados;
    $viewdata['aprobados'] = $aprobados;

    return  $viewdata;
}




function ActaNotasGlobal( $codigoAlmacenCurso, $ProgramaAlmacen, $Cod_Curso, $vConex, $IdEmpresa ,$Exportar){

  //WE("hola");


    $sql = " SELECT  P.Nombres, P.ApellidosPat , P.ApellidosMat, AR.Titulo, A.Nombre_Grupo, A.DiaInicio, A.DiaFinal
            FROM almacen AS A
			INNER JOIN  articulos AR ON  A.Producto = AR.Producto
            LEFT JOIN profesores AS P ON A.Origen = P.Usuario
            INNER JOIN usuario_entidad UE ON UE.Usuario=P.Email
            WHERE AlmacenCod = " . $codigoAlmacenCurso . " ";

          //  

    $rg = fetch($sql);
    $Nombres = $rg["Nombres"];
    $ApellidosPat = $rg["ApellidosPat"];
    $ApellidosMat = $rg["ApellidosMat"];
    $NombreExcel = "CURSO_".$rg["DiaInicio"]."_".$rg["Titulo"];
    $tituloCurso = $rg["Titulo"];
    $FechaInicio = $rg["DiaInicio"];
    $FechaFin = $rg["DiaFinal"];




    $sql = "SELECT A.AliasListaNota, A.ExportarNotaPromedio, A.Control, A.NotaMinCertificacion, A.NotaMaxCertificacion
	,AR.Titulo, A.Nombre_Grupo, A.DiaInicio, A.DiaFinal
	FROM almacen A
	INNER JOIN  articulos AR ON  A.Producto = AR.Producto
	WHERE A.AlmacenCod = " . $ProgramaAlmacen . "   ";
    $rg = fetch($sql);
    $AliasListaNota = $rg["AliasListaNota"];
    $ExportarNotaPromedio = $rg["ExportarNotaPromedio"];
    $NotaMinCertificacion = $rg["NotaMinCertificacion"];
    $NotaMaxCertificacion = $rg["NotaMaxCertificacion"];
    $Nombre_Grupo = $rg["Nombre_Grupo"];
    $tituloPrograma = $rg["Titulo"];
    $FechaInicioPrograma = $rg["DiaInicio"];
    $FechaFinPrograma = $rg["DiaFinal"];
    $Control = $rg["Control"];

    $sql = "SELECT  articulos.Titulo,
    almacen.Estado, categorias.Descripcion,
    concat(alu.Nombres,' ',alu.ApellidosPat) Profesor,
    concat(UE.Nombres,' ',UE.Apellidos) Coordinador
    FROM almacen
    INNER JOIN articulos ON almacen.Producto = articulos.Producto
    INNER JOIN alumnos alu ON  replace(alu.Usuario, 'Alumno', 'Profesor') = almacen.Origen
    INNER JOIN usuario_entidad UE  ON  UE.Codigo = almacen.Coordinador
    INNER JOIN categorias ON articulos.Categoria = categorias.CategoriCod
    WHERE almacen.AlmacenCod = '$codigoAlmacenCurso'  ";
    $rg = fetch($sql);
    $TituloCurso = $rg["Titulo"];
    $Profesor = $rg["Profesor"];
    $Coordinador = $rg["Coordinador"];

    $sql = " SELECT  SA.TotalActividades
	         , SA.TotalAlumnos
	         , SA.TotalPendientes
	         , SA.TotalRevisados
	         , SA.TotalIniciados
	         , SA.TotalEnviados
	         , SA.TotalAprobados
	         , SA.TotalDesaprobados
            FROM sumario_actividades_cab  SA
            WHERE SA.ProgramaAlmacen = " . $ProgramaAlmacen . "   AND SA.CursoAlmacen = " . $codigoAlmacenCurso . "  ";

    

    $rg = fetch($sql);
    $TotalActividades = $rg["TotalActividades"];
    $TotalAlumnos = $rg["TotalAlumnos"];
    // $TotalPendientes = $rg["TotalPendientes"];
    // $TotalRevisados = $rg["TotalRevisados"];
    // $TotalIniciados = $rg["TotalIniciados"];
    // $TotalEnviados = $rg["TotalEnviados"];
    $TotalAprobados = $rg["TotalAprobados"];
    // $TotalDesaprobados = $rg["TotalDesaprobados"];

    $cb  = "<div  style='width:100%;float:left;'>
            <div class= 'cuadrosA02' style='float:left;background-color:#F0F3F2'>
                 <span style='display:block' class='body_cuadrosA'>PROFESOR : $Profesor</span>
            </div>
             <div class= 'cuadrosA02' style='float:left;background-color:#F0F3F2'>
                 <span style='display:block' class='body_cuadrosA'>Coordinador : $Coordinador</span>
            </div>
            <div class= 'cuadrosA02' style='float:left;background-color:#F0F3F2'>
                 <span style='display:block' class='body_cuadrosA'>Nro. Actividades : $TotalActividades</span>
            </div>
            <div class= 'cuadrosA02' style='float:left;background-color:#F0F3F2'>
                 <span style='display:block' class='body_cuadrosA'>Total de Alumnos : $TotalAlumnos</span>
            </div>
            <div class= 'cuadrosA02' style='float:left;background-color:#F0F3F2'>
                 <span style='display:block' class='body_cuadrosA'>Nota Minima: ".$NotaMinCertificacion."</span>
            </div>


            ";
    $cb .= "</div>";

    $sql = " SELECT
			AN.Concepto ,AN.PesoConcepto
			FROM sumario_acta_notas AN

			WHERE  AN.ProgramaAlmacen = " . $ProgramaAlmacen . "   AND AN.CursoAlmacen = " . $codigoAlmacenCurso . "
			GROUP BY AN.Concepto  ORDER BY  AN.Concepto  ";
    $MxDetEvalA = fetchAll($sql, $vConex);
    $sSQL1 ="  ";
    $sintaxisSQL1 = "";
    $sintaxisSQL2 = "";
    $sintaxisSQL3 = "";
    $acumula = "";
    $sumaConcepto = "0 ";
    $totConcepto = 0;
    $totActividad = 0;

    foreach ($MxDetEvalA as $DetEvalA) {
        $totConcepto += 1;
        $sql = " SELECT
					AN.Concepto
					,EEDC.AliasExtendido
					,EEDC.AliasAbreviado
					,EEDC.Abreviacion
					,AN.Nota
					,AN.Actividad
					,AN.EstadoActividad
					FROM sumario_acta_notas AN
					INNER JOIN elevaluaciondetallecurso AS EEDC ON EEDC.Codigo=AN.Actividad
					INNER JOIN elrecursoevaluacion AS RE ON RE.EvaluacionDetalleCurso = AN.Actividad
					WHERE  AN.ProgramaAlmacen = " . $ProgramaAlmacen . "   AND AN.CursoAlmacen = " . $codigoAlmacenCurso . "
					AND AN.Concepto = " . $DetEvalA->Concepto . "
					GROUP BY AN.Actividad  ORDER BY  AN.Actividad    ";
        $MxDetEvalB = fetchAll($sql, $vConex);
        $sumaActividad =" 0 ";
        $totActividad =0;

        foreach ($MxDetEvalB as $DetEvalB) {

            $totActividad += 1;

            $sintaxisSQL1 .=" SUM(CASE WHEN AN.Actividad  = ". $DetEvalB->Actividad."  THEN AN.Nota ELSE 0  END)  AS 'ACT" . $DetEvalB->Actividad . "' , ";
            $sintaxisSQL1 .=" SUM(CASE WHEN  AN.Actividad  = ".$DetEvalB->Actividad."  THEN AN.Actividad  ELSE 0 END) AS 'ACTIVIDAD" . $DetEvalB->Actividad . "'  , ";

            $sintaxisSQL4 .=" CS2.ACT" . $DetEvalB->Actividad . " AS  '" . $DetEvalB->AliasExtendido . " (".$DetEvalB->AliasAbreviado.")'  , ";
            $sintaxisSQL4 .=" CS2.Estado" . $DetEvalB->Actividad . "   AS 'Estado".$totActividad."' , ";

            $acumula .= " CS2.Estado" . $DetEvalB->Actividad . "  = 'Revisado' AND ";

            $sintaxisSQL3 .=" CS1.ACT" . $DetEvalB->Actividad . " , ";
            $sintaxisSQL3 .="  (CASE WHEN CS1.ACTIVIDAD" . $DetEvalB->Actividad . "  = ".$DetEvalB->Actividad ." THEN
							(SELECT EstadoActividad FROM sumario_acta_notas WHERE Actividad = ".$DetEvalB->Actividad ."
							AND Email = CS1.Email ) END) AS 'Estado" . $DetEvalB->Actividad . "'	 ,
                             ";
            $sumaActividad .="  + CS1.ACT" . $DetEvalB->Actividad . "  ";
        }
        $sumaConcepto .="  + (((".$sumaActividad.")/".$totActividad.") * (".$DetEvalA->PesoConcepto."/100) )   ";
        $sintaxisSQL3 .= " ROUND((((".$sumaActividad.")/".$totActividad.") * (".$DetEvalA->PesoConcepto."/100)),2) AS 'Ponderado".$totConcepto."' ,  ";
        $sintaxisSQL4 .=" CS2.Ponderado" . $totConcepto . "  AS 'Ponderado".$totConcepto." (".$DetEvalA->PesoConcepto.")'  , ";

    }

    $sintaxisSQL3 .= " ROUND(( ".$sumaConcepto ."),2) AS 'Promedio_General'  ";

    $sintaxisSQL4 .=" CS2.Promedio_General, ";

    if($Exportar !=='Excel'){

        $sintaxisSQL4 .="  (CASE WHEN  ".$acumula."   1=1   THEN
								   (CASE WHEN CS2.Promedio_General < ".$NotaMinCertificacion."
									   THEN CONCAT('<div class=notaDesaAprobatoria >Desaprobado</div>')
									   ELSE CONCAT('<div class=notaAprobatoria  >Aprobado</div>')
									   END)
							  ELSE
								  CONCAT('<div class=notaPendiente >Pendiente</div>')
							  END
							 ) AS 'EstadoGeneal'  ";
    }else{
        $sintaxisSQL4 .="  (CASE WHEN  ".$acumula."   1=1   THEN
								   (CASE WHEN CS2.Promedio_General < ".$NotaMinCertificacion."
									   THEN 'Desaprobado'
									   ELSE 'Aprobado'
									   END)
							  ELSE
								  'Pendiente'
							  END
							 ) AS 'EstadoGeneal'  ";
    }

    $sqlMx = " SELECT
	AN.Usuario,
	AN.NombresApellidos AS Participantes,
	AN.EstadoActividad ,
	 AN.Actividad,
	".$sintaxisSQL1."
	AN.Email
	FROM sumario_acta_notas AN
	WHERE  AN.ProgramaAlmacen = " . $ProgramaAlmacen . "   AND AN.CursoAlmacen = " . $codigoAlmacenCurso . "
	GROUP BY AN.Email ";

    $sqlP = " SELECT
	 CS1.Usuario AS DNI
	,CS1.PARTICIPANTES
	,CS1.Email
	,".$sintaxisSQL3."
	FROM  (".$sqlMx.")  AS CS1 ";

    if($Exportar !=='Excel'){
        $sqlP2 = " SELECT
         CONCAT('<span style=text-transform:uppercase; >',CS2.PARTICIPANTES,'</span><br><span style=color:#109AD9; >',CS2.Email,'</span>') as PARTICIPANTES
        ,CS2.DNI AS DNI
        ,".$sintaxisSQL4."
        ,CONCAT(UE.Usuario,'Alumno')  AS CodigoAjax
        FROM  (".$sqlP.")  AS CS2
        INNER JOIN usuario_entidad UE ON  (CS2.Email = UE.Usuario
        AND UE.EntidadCreadora = '$IdEmpresa'
        )
        WHERE UE.Perfil = 3
        ORDER BY PARTICIPANTES ASC";

    }else{
        $sqlP2 = " SELECT
         CS2.PARTICIPANTES as Alumno,
         CS2.DNI,
         CS2.Email
        ,".$sintaxisSQL4."
        FROM  (".$sqlP.")  AS CS2
        INNER JOIN usuario_entidad UE ON  CS2.Email = UE.Usuario
        WHERE UE.Perfil = 3
        ORDER BY Alumno ASC";
    } 
    //ruta
    $clase = 'panelB-R';
    $enlaceCod = 'Alumno';
    $url = "./_vistas/gad_cursos_actividades.php?Notas=Editar&CursoAlmacen=" . $codigoAlmacenCurso . "&ProgramaAlmacen=" . $ProgramaAlmacen . "&Cod_Curso=" . $Cod_Curso . "&CodigoLDT=" . $CodigoLDT ."&Empresa=Empresarial";
    $panel = 'layoutV';
    $reporte = ListR2( "", $sqlP2, $vConex, $clase, '', $url, $enlaceCod, $panel, 'cursos-alumnos', '' );

    $output = $cb . $reporte ;

    ################Exportar ARCHIVO################################

    if($Exportar=='Excel'){

        $Titulo = 'EXPORTAR';
        $datos = array();
        // $datos['Titulo'] = "ACTA DE NOTAS DEL CURSO:  ".$tituloCurso."  |  FECHA INICIO:   ".$FechaInicio;
        $datos['Titulo'] = "ACTA DE NOTAS DEL CURSO:  ".$tituloCurso."  |  F. INICIO :   ".$FechaInicio."  |  F. FINAL  :   ".$FechaFin."  ";
        $datos['SubTitulo'] = "PROGRAMA :  ".$tituloPrograma."  |  GRUPO:  ".$Nombre_Grupo."  |  F. INICIO PROG. :   ".$FechaInicioPrograma."  |  F. FINAL PROG.  :   ".$FechaFinPrograma."  ";
        $datos['Indicadores'] = "
		  N. PARTICIPANTES: ".$TotalAlumnos."
		   |     N. ACTIVIDAD: ".$TotalActividades."
		   |     Nota Minima: ".$NotaMinCertificacion."
		";
        ExportExcel($sqlP2,$vConex,"informe_".$FechaInicio."_acta_de_notas", $datos);
        unlink('../_files/'.$Titulo);
        W("<div class='MensajeB vacio' style='width:300px;font-size:11px;margin:10px 30px;'>Se Exporto correcta mente</div>");
    }
    ################################################
    return $output;
}