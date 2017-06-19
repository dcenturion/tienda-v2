<?php

function Programas_Enseno_Principal($UserProfesor, $Estado, $vConex) {

    $sqlS = "SELECT 
        CU.ProductoCod,
        CU.ProgramaCod,
        CU.Orden,
        MT.IdFacturasCab,
        MT.Producto,
        articulos.Titulo,
        CU.CodProgAlmacen 
        ,CA.Descripcion AS Categoria 
        ,CA.Color 
        FROM matriculas AS MT 
        LEFT JOIN curricula  AS CU ON MT.Producto = CU.CodProgAlmacen
        LEFT JOIN almacen ON CU.ProductoCod = almacen.AlmacenCod
        LEFT JOIN articulos on almacen.Producto = articulos.Producto
        LEFT JOIN categorias CA ON articulos.Categoria = CA.CategoriCod 
        WHERE  almacen.Origen = '" . $UserProfesor . "' 
        AND almacen.Estado =  '" . $Estado . "' 
        AND (almacen.TipoProducto = 'Curso' OR almacen.TipoProducto = 'curso') 
        GROUP BY CU.ProgramaCod ";

    $consulta = mysql_query($sqlS, $vConex);
    $resultado = $consulta or die(mysql_error());

    while ($registro = mysql_fetch_array($resultado)) {

        $valor .= "<div class='CPGeneral' >";
        $valor .= "<div class='contenedorProductos' >";
        $valor .= "<div class='tit-pn-dpl2'>";
        $valor .= "<div style='float:left;' class='tit-sub'> ";
        $valor .= "<button class='botonProgramas' > <div class='botIconF2Pro' title='Anuncios'><i class='icon-info-sign' ></i></div> </button>";
        $valor .= "<div class='titulo-oculto' title='Dar click en el boton' >";
        $valor .= CursosProgramaDicto($registro["CodProgAlmacen"], $registro["ProductoCod"], $vConex);
        $valor .= "</div>";
        $valor .= "</div>";
        $valor .= "</div>";

        $valor .= "<div class='Panel_Curso_A_Principal' style='background-color:" . $registro["Color"] . ";' >";
        $valor .= "<div class='Panel_Curso_Sunperior'>";
        $valor .= "<div class='adorno_book'></div>";
        $valor .= "<p class='Panel_Curso_Sunperior text_PC_001'>";
        if (strlen($registro["Titulo"]) > 60) {
            $valor = $valor . substr($registro["Titulo"], 0, 60) . "...";
        } else {
            $valor = $valor . $registro["Titulo"];
        }
        $valor .= "</p>";
        $valor .= "</div>";
        $valor .= "</div>";
        $valor = $valor . "</div>";
        $valor = $valor . "</div>";
    }

    return $valor;
}
#aaa1
function ProgramasTomados($UsuarioEntidad, $idEmpresa, $vConex) {
    $alumno = str_replace("Alumno", "", $UsuarioEntidad);
    $sql = " SELECT AR.Titulo as Titulo ";
    $sql .=" ,AR.Descripcion as Descripcion ";
    $sql .=" ,AL.TipoProducto as TipProducto";
    $sql .=" ,AR.ProductoFab ";
    $sql .=" ,CA.Descripcion as categorias ";
    $sql .=" ,AR.ProductoFab ";
    $sql .=" ,CA.Color ";
    $sql .=" ,AL.AlmacenCod AS AlmacenProductoMatriculado ";
    $sql .=" ,AL.Acceso ";
    $sql .=" ,TP.Descripcion as DescripTipoProd ";
    $sql .=" ,MA.IdFacturasCab";
    $sql .=" ,AL.control  AS ControlSede";
    $sql .=" ,AL.Entidad";
    $sql .=" FROM matriculas MA ";
    $sql .=" LEFT JOIN almacen AL ON MA.Producto = AL.AlmacenCod ";
    $sql .=" LEFT JOIN articulos AR ON AR.Producto = AL.Producto ";
    $sql .=" LEFT JOIN categorias CA on AR.Categoria = CA.CategoriCod ";
    $sql .=" LEFT JOIN tipoproducto TP on AL.TipoProducto = TP.TipoProductoId ";
    $sql .=" WHERE MA.Estado = 'Matriculado' and MA.Cliente = '" . $UsuarioEntidad . "' ";
    $sql .=" AND AL.TipoProducto LIKE 'programa%'  ";
    // $sql .=" AND AL.AlmacenCod  = '3335' ";  
    // WE($sql);

    $consulta = mysql_query($sql, $vConex);
    $resultado = $consulta or die(mysql_error());
    $valor = "";
    while ($registro = mysql_fetch_array($resultado)) {
        /*
        Entidad
        Acceso
        control
        AlmacenProductoMatriculado
             */

        $idEmpresa = $registro['Entidad'];
        $acceso = $registro['Acceso'];
        $ControlSede = $registro['ControlSede'];

        $sql = "SELECT  
                U.Codigo, U.Usuario,
                P.Codigo AS Perfi,  
                U.Area, U.Empresa, U.Escuela, US.Carpeta,U.Sede 
                FROM ((usuario_entidad AS U 
                INNER JOIN usuario_perfil  AS P ON U.Perfil = P.Codigo)
                INNER JOIN usuarios  AS US ON U.Usuario = US.Usuario)  
                WHERE US.IdUsuario = '" . $UsuarioEntidad . "'  AND  U.EntidadCreadora = '" . $idEmpresa . "'  ";
        // W($sql);
        $rg = fetch($sql);
        $Perfi_Entidad_Usuario = $rg["Perfi"];
        $SedeUsuario = $rg["Sede"];

        $valor .= "<div class='CPGeneral' >";

        $valor .= "<div class='contenedorProductos' >";
        // $valor .= "<div  class='FranjaLibro' >P. Educativo</div>";
        $valor .= "<div class='tit-pn-dpl2'>";
        $valor .= "<div style='float:left;' class='tit-sub'> ";
        $valor .= "<button class='botonProgramas' ></button>";
        $valor .= "<div class='titulo-oculto' title='Dar click en el boton' >";
        // WE("control  ".$registro['ControlSede']."  --  ddd$Perfi_Entidad_Usuari : ".$Perfi_Entidad_Usuario."  <br>");
            if (($registro['ControlSede'] == 1 || $registro['ControlSede'] == 0) && ($Perfi_Entidad_Usuario == 8 || $Perfi_Entidad_Usuario == 12 || $Perfi_Entidad_Usuario == 16)) { //Coordinador
                $valor .= CursosPrograma($registro["AlmacenProductoMatriculado"], $vConex, "", $acceso, $alumno, $Perfi_Entidad_Usuario,$SedeUsuario);
            } else if ($registro['ControlSede'] == 0 && $Perfi_Entidad_Usuario == 3) {
                $valor .= CursosPrograma($registro["AlmacenProductoMatriculado"], $vConex, "", $acceso, $alumno,  $Perfi_Entidad_Usuario,$SedeUsuario);
            } else if ($registro['ControlSede'] == 1 && $Perfi_Entidad_Usuario == 3) {
                $valor .= CursosPrograma($registro["AlmacenProductoMatriculado"], $vConex, $ControlSede, $acceso, $alumno, $Perfi_Entidad_Usuario,$SedeUsuario);
            } else {
                $valor .= CursosPrograma($registro["AlmacenProductoMatriculado"], $vConex, $ControlSede, $acceso, $alumno,  $Perfi_Entidad_Usuario,$SedeUsuario);
            }
            
        $valor .= "</div>";
        $valor .= "</div>";
        $valor .= "</div>";

        $valor .= "<div class='Panel_Curso_A_Principal' style='background-color:" . $registro["Color"] . ";' >";
        $valor .= "<div class='Panel_Curso_Sunperior'>";

        $valor .= "<p class='Panel_Curso_Sunperior text_PC_001'>";
        if (strlen($registro[0]) > 60) {
            $valor = $valor . substr($registro[0], 0, 60) . "...";
        } else {
            $valor = $valor . $registro[0];
        }
        $valor .= "</p>";
        $valor .= "</div>";
        $valor .= "</div>";
        $valor = $valor . "</div>";
        $valor = $valor . "</div>";
    }
    return $valor;
}

function Reuniones($UsuarioEntidad, $idEmpresa, $vConex) {

    $sql = " SELECT AR.Titulo as Titulo ";
    $sql .=" ,AR.Descripcion as Descripcion ";
    $sql .=" ,AL.TipoProducto as TipProducto";
    $sql .=" ,AR.ProductoFab ";
    $sql .=" ,CA.Descripcion as categorias ";
    $sql .=" ,CA.Color ";
    $sql .=" ,AL.AlmacenCod AS AlmacenProductoMatriculado ";
    $sql .=" ,TP.Descripcion as DescripTipoProd ";
    $sql .=" ,MA.IdFacturasCab ";
    $sql .=" FROM matriculas MA ";
    $sql .=" LEFT JOIN almacen AL ON MA.Producto = AL.AlmacenCod ";
    $sql .=" LEFT JOIN articulos AR ON AR.Producto = AL.Producto ";
    $sql .=" LEFT JOIN categorias CA on AR.Categoria = CA.CategoriCod ";
    $sql .=" LEFT JOIN tipoproducto TP on AL.TipoProducto = TP.TipoProductoId ";
    $sql .=" WHERE MA.Estado = 'Matriculado' and MA.Cliente = '" . $UsuarioEntidad . "' ";
    $sql .=" AND AL.TipoProducto LIKE 'Entrevista%' ";
    $consulta = mysql_query($sql, $vConex);
    $resultado = $consulta or die(mysql_error());
    $valor = "";
    while ($registro = mysql_fetch_array($resultado)) {

        $valor .= "<div class='CPGeneral' >";
        $valor = $valor . "<a href='" . $Url . "' >";
        $valor .= "<div class='contenedorProductos' >";
        $valor .= "<div class='Panel_Curso_A_Principal_Entrevista' style='background-color:#11ADAD;' >";
        $Url = "/system/aula_entrevista.php?AlmacenCurso=" . $registro["AlmacenProductoMatriculado"] . "&Access=y";

        $valor .= "<div class='Panel_Curso_Sunperior'>";
        $valor .= "<div class='adorno_book'></div>";
        $valor .= "<p class='Panel_Curso_Sunperior text_PC_001'>";
        if (strlen($registro[0]) > 60) {
            $valor = $valor . substr($registro[0], 0, 60) . "...";
        } else {
            $valor = $valor . $registro[0];
        }
        $valor .= "</p>";
        $valor .= "</div>";
        $valor .= "</div>";
        $valor = $valor . "</div>";
        $valor .= "</a>";
        $valor = $valor . "</div>";
    }
    return $valor;
}

function Entrevistas($UsuarioEntidad, $idEmpresa, $vConex) {

    $sql = " SELECT AR.Titulo as Titulo ";
    $sql .=" ,AR.Descripcion as Descripcion ";
    $sql .=" ,AL.TipoProducto as TipProducto";
    $sql .=" ,AR.ProductoFab ";
    $sql .=" ,CA.Descripcion as categorias ";
    $sql .=" ,CA.Color ";
    $sql .=" ,AL.AlmacenCod AS AlmacenProductoMatriculado ";
    $sql .=" ,TP.Descripcion as DescripTipoProd ";
    $sql .=" ,MA.IdFacturasCab ";
    $sql .=" FROM matriculas MA ";
    $sql .=" LEFT JOIN almacen AL ON MA.Producto = AL.AlmacenCod ";
    $sql .=" LEFT JOIN articulos AR ON AR.Producto = AL.Producto ";
    $sql .=" LEFT JOIN categorias CA on AR.Categoria = CA.CategoriCod ";
    $sql .=" LEFT JOIN tipoproducto TP on AL.TipoProducto = TP.TipoProductoId ";
    $sql .=" WHERE MA.Estado = 'Matriculado' and MA.Cliente = '" . $UsuarioEntidad . "' ";
    $sql .=" AND AL.TipoProducto LIKE 'Entrevista%' ";
    $consulta = mysql_query($sql, $vConex);
    $resultado = $consulta or die(mysql_error());
    $valor = "";
    while ($registro = mysql_fetch_array($resultado)) {
        $Url = "/system/aula_entrevista.php?AlmacenCurso=" . $registro["AlmacenProductoMatriculado"] . "&Access=y";

        $valor .= "<div class='CPGeneral' >";
        $valor = $valor . "<a href='" . $Url . "' >";
        $valor .= "<div class='contenedorProductos' >";
        $valor .= "<div class='Panel_Curso_A_Principal_Entrevista' style='background-color:#11ADAD;' >";

        $valor .= "<div class='Panel_Curso_Sunperior'>";
        $valor .= "<div class='adorno_book'></div>";
        $valor .= "<p class='Panel_Curso_Sunperior text_PC_001'>";
        if (strlen($registro[0]) > 60) {
            $valor = $valor . substr($registro[0], 0, 60) . "...";
        } else {
            $valor = $valor . $registro[0];
        }
        $valor .= "</p>";
        $valor .= "</div>";
        $valor .= "</div>";
        $valor = $valor . "</div>";
        $valor .= "</a>";
        $valor = $valor . "</div>";
    }
    return $valor;
}

function CursosProgramaDicto($ProgramaAlmacen, $CursoAlmacen, $vConex) {

    $sqlb = "SELECT 
              AR.Titulo
              ,AL.AlmacenCod  AS AlmacenCurso
              ,CA.Descripcion AS Categoria 
              ,CA.Color 
              FROM almacen AL 
              LEFT JOIN articulos AR ON AL.Producto  = AR.Producto
              LEFT JOIN categorias CA ON AR.Categoria = CA.CategoriCod  
              WHERE AL.AlmacenCod = " . $CursoAlmacen . "  ";

    $consultaB = mysql_query($sqlb, $vConex);
    $resultadoB = $consultaB or die(mysql_error());
    while ($registroB = mysql_fetch_array($resultadoB)) {

        $valor = $valor . "<div class='Panel_Curso_A_PrincipalA2' style='background-color:" . $registroB["Color"] . ";' >";
        $Url = "/system/aula.php?AlmacenCurso=" . $registroB["AlmacenCurso"] . "&AlmacenPrograma=" . $ProgramaAlmacen . "&Access=y";
        $valor = $valor . "<div class='Panel_Curso_SunperiorA2'>";
        $valor = $valor . "<a href='" . $Url . "' >";
        $valor = $valor . "<p class='Panel_Curso_SunperiorA2 text_PC_001'>";
        if (strlen($registroB["Titulo"]) > 110) {
            $valor = $valor . substr($registroB["Titulo"], 0, 110) . "...";
        } else {
            $valor = $valor . $registroB["Titulo"];
        }
        $valor = $valor . "</p>";
        $valor = $valor . "</a>";
        $valor = $valor . "</div>";
        $valor = $valor . "</div>";
    }

    return $valor;
}
#aaa


function CursosPrograma($ProgramaAlmacen, $vConex, $Sede, $acceso, $alumno, $order,$SedeUsuario,$perfil, $arg) {
 
 
    $Q_P = "SELECT 
    AR.ProductoFab,
    AR.Titulo,
    AL.AlmacenCod,
    AL.NivelMatricula,
   AL.Nombre_Grupo as TituloGrupo,
    CAT.CategoriCod as categoryId,
    CAT.Color,
    CAT.Descripcion as CategoryTitle,
    PR.CodPrograma,
    PR.GrupoProgrId,
    PR.ImagenUrl,
    PR.Titulo AS titulo_programa,
    TP.Descripcion AS 'TipoPrograma',
    AL.Entidad,
    AL.DiaInicio,
    AL.DiaFinal,
    PR.pertenece,
    PR.nombrecorto,
    AL.CursosFases,
    AL.NotaMinCertificacion
    FROM almacen AL
    INNER JOIN articulos AR ON AL.Producto = AR.Producto  
    INNER JOIN categorias CAT on AR.Categoria = CAT.CategoriCod
    INNER JOIN programas PR ON AR.ProductoFab = PR.CodPrograma  
    INNER JOIN tipoprograma TP ON PR.TipoPrograma=TP.IDTipoPrograma
    WHERE AL.AlmacenCod='{$ProgramaAlmacen}' ";
    $Programa = fetchOne($Q_P, $vConex);

    //$entidadCreadora = "topitop.com";   
    $sql = "SELECT   Carpeta  FROM  usuarios  WHERE  IdUsuario = '" . $Programa->Entidad . "'  GROUP BY  Carpeta  ";                     
    $rg = fetch($sql);
    $CarpetaEmpresa = $rg["Carpeta"];


    $acceso = (int) $acceso;
    $Sede= (int) $Sede;

    $Q_Curso = "
    SELECT 
    AL.AlmacenCod  AS AlmacenCurso,
    C.ProductoCod,
    C.Orden,
    CA.Descripcion AS Categoria,
    CA.Color,
    CU.TituloCurso,
    CU.DescripcionCurso,
    AL.Entidad,
    AL.Origen,
    SC.Nombre  AS Sede,
    AL.ImagenCurso,
    CU.CodCursos
    FROM curricula C
    INNER JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod
    INNER JOIN articulos AR ON AL.Producto = AR.Producto
    INNER JOIN categorias CA ON AR.Categoria = CA.CategoriCod
    INNER JOIN cursos CU ON AR.ProductoFab = CU.CodCursos
    LEFT JOIN sede_sucursal SC ON SC.Codigo = AL.Sede ";
    
    if ($acceso == 1 ) {
        $Q_Curso .= "LEFT JOIN configuracion_accesos_curricula AS CAC ON CAC.curricula = C.CurriculaCod ";
    }

    $Q_Curso .=" WHERE C.CodProgAlmacen = {$ProgramaAlmacen} ";

    if ($acceso == 1 ){
        $Q_Curso .="
        AND CAC.accesos = 1
        AND CAC.alumno = '{$alumno}'";
    }
    
    
    if ($Sede ==  1 ){
        $Q_Curso .= "  AND AL.Sede = {$SedeUsuario} ";
    }
    
    $Q_Curso .= " ORDER BY C.Orden asc ";
        
    $MxCU = fetchAll($Q_Curso, $vConex);
    //Creando un array para almacenar los cursos del programa
    $JSONdataCourses = array();
    $JSONdataCoursesProfesor = array();
    $emailEntidadPersona = explode("Alumno", $alumno);
    
    foreach ($MxCU as $CU) {    
        if($CU->Sede){
         $Sede = " | ".$CU->Sede;
        }else{
         $Sede = "";
        }
       
        $numActivities = (int) fetchOne("SELECT COUNT(RE.Codigo) AS numActivities
        FROM elevaluacionalumno AS EEA
        INNER JOIN elevaluaciondetallecurso AS EEDC ON EEA.EvalDetCurso=EEDC.Codigo
        INNER JOIN elevaluacionconfcurso AS EECC ON EEDC.EvalConfigCurso=EECC.Codigo
        INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
        INNER JOIN elrecursoevaluacion AS RE ON EEDC.Codigo=RE.EvaluacionDetalleCurso
        INNER JOIN eltiporecursoevaluacion AS TRE ON RE.RecursoTipo=TRE.Codigo
        INNER JOIN eltransrespuesta_cab AS ETC ON RE.Codigo=ETC.Recurso
        WHERE EECC.Almacen='{$CU->AlmacenCurso}'
        AND EEA.Alumno='{$alumno}'
        AND ETC.Alumno = '{$alumno}'
        AND ETC.Estado IN('Iniciado', 'Pendiente')
        AND EET.Tipo NOT IN ('MP','DCP','ROC','EXPC','TES')
        AND EECC.Estado <> 'Anulado'
        ORDER BY RE.FechaHoraReg DESC")->numActivities;
        
        if($CU->ImagenCurso){
            $ImagenCurso = "http://owlgroup.s3-website-us-west-2.amazonaws.com/ArchivosEmpresa/{$CarpetaEmpresa}/CU-{$CU->CodCursos}/{$CU->ImagenCurso}";                          
        }else{
            $ImagenCurso = "http://owlgroup.s3-website-us-west-2.amazonaws.com/desktopimg/6086-curso2.jpg";
        }

        if ($Programa->pertenece != '') {
            $courseId = str_replace('cuorsera', '', $Programa->pertenece);
           
            $courseraLearnUri = "https://es.coursera.org/learn";
            
            
            $redireccionar = $courseraLearnUri."/".$Programa->nombrecorto;
            //WE($redireccionar);
            $tipo = "coursera";
        }else{
            if($arg == 'noCoordina'){
                $redireccionar = "/system/room.php?AlmacenCurso={$CU->ProductoCod}&AlmacenPrograma={$ProgramaAlmacen}&Access=y&vista=$arg";
                $tipo = "normal";
            }else{
                $redireccionar = "/system/room.php?AlmacenCurso={$CU->ProductoCod}&AlmacenPrograma={$ProgramaAlmacen}&Access=y";
                $tipo = "normal";
            }
        }

        if($Programa->CursosFases && $CU->Orden != 1){

            $OrdenCursoAnterior = $CU->Orden - 1;

            $sql_1 = "SELECT ProductoCod FROM curricula WHERE CodProgAlmacen = $ProgramaAlmacen AND Orden = $OrdenCursoAnterior ";
            $rg_1 = fetch($sql_1);
            $CursoAlmacenAnterior = $rg_1["ProductoCod"];

            $Q_EE = "   SELECT
            US.Usuario AS Email ,
            EEDC.Codigo AS Actividad ,
            ETRC.Nota ,
            EECC.Codigo AS Concepto ,
            EECC.Peso
			FROM eltransrespuesta_cab AS ETRC
			INNER JOIN elrecursoevaluacion AS RE ON RE.Codigo=ETRC.Recurso
			INNER JOIN elevaluaciondetallecurso AS EEDC ON EEDC.Codigo=RE.EvaluacionDetalleCurso
			INNER JOIN elevaluacionconfcurso AS EECC ON EECC.Codigo=EEDC.EvalConfigCurso
			INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
			INNER JOIN usuarios AS US ON US.IdUsuario = ETRC.Alumno
			INNER JOIN matriculas as MAT on (ETRC.Alumno = MAT.Cliente AND MAT.Producto = {$ProgramaAlmacen} )
			INNER JOIN usuario_entidad AS UE ON (REPLACE(ETRC.Alumno,'Alumno','') = UE.Usuario AND UE.EntidadCreadora = '{$Programa->Entidad}')
			INNER JOIN alumnos AS ALUM ON ETRC.Alumno = ALUM.Usuario
			WHERE EECC.Almacen={$CursoAlmacenAnterior}
			AND EEDC.SCpt_Evaluado='SI'
			AND MAT.Estado NOT IN('Anulado','Eliminado')
			AND ETRC.Alumno <>''
			AND EET.Codigo NOT IN(23,24,25,26,27)
			AND ETRC.Alumno = '{$alumno}' ";

            $SQLTotPeso = " SELECT CS1.Peso FROM (".$Q_EE .")  AS  CS1 GROUP BY  CS1.Concepto ";
            $SQLSumPeso = " SELECT SUM(CS1.Peso) AS TotPeso FROM (".$SQLTotPeso .")  AS  CS1 ";
            $rg = fetch($SQLSumPeso);
            $TotPeso = $rg["TotPeso"];
            if ($TotPeso == "") {$TotPeso = 0;}

            $sqlResumenA = " SELECT '1'  AS ActividadMarcador  FROM (".$Q_EE.") AS CSL1 GROUP BY CSL1.Actividad ";
            $sqlResumen = " SELECT  SUM(CSL1.ActividadMarcador)  AS CtdadActividad FROM (".$sqlResumenA.") AS CSL1 ";
            $rg = fetch($sqlResumen);
            $CtdadActividad = $rg["CtdadActividad"];
            if ($CtdadActividad == ""){$CtdadActividad = 0;}

            $sqlResumenA2D = " SELECT '1' AS Participante, SUM(CSL1.Nota) AS TotNota, CSL1.Email FROM (".$Q_EE.") AS CSL1 GROUP BY CSL1.Email ";
            $sqlResumenA2 = " SELECT SUM(CSL1.Participante)  AS CtdadParticipante FROM (".$sqlResumenA2D.") AS CSL1 ";
            $rg = fetch($sqlResumenA2);
            $CtdadParticipante = $rg["CtdadParticipante"];
            $agrupaTotAlumno = " SELECT (((CSL1.TotNota / ".$CtdadActividad.")* ".$TotPeso.")/100) AS NotaAlumno FROM (".$sqlResumenA2D.") AS CSL1 GROUP BY CSL1.Email ";
            $SQLTotAprobadosSum  = "  SELECT COUNT(*) AS TotAlumnos FROM (".$agrupaTotAlumno .") AS CS1 WHERE CS1.NotaAlumno >= ".$Programa->NotaMinCertificacion." ";
             $rg = fetch($SQLTotAprobadosSum);
            $TotAlumnosAprobados = $rg["TotAlumnos"];

            $TotalAlumnoDesaprobados = $CtdadParticipante - $TotAlumnosAprobados;
            if($CtdadActividad == 0){$TotalAlumnoDesaprobados = 1;}
        }

        if($TotalAlumnoDesaprobados){
            $redireccionar = "#";
            $style = "background: rgba(128, 128, 128, 0.15);cursor: default;";
            $styleText = "text-decoration: blink;";
        }

        $JSONdataCourses[] = array(
            "title"             => $CU->TituloCurso.$Sede,
            "description"       => $CU->TituloCurso,
            "orden"             => $CU->Orden,
            "color"             => $CU->Color,
            "AlmacenCurso"      => $CU->ProductoCod,
            "ImgCurso"          => $ImagenCurso,
            "url_redirect"      => $redireccionar,
            "num_activities"    => $numActivities,
            "tipo"              => $tipo,
            "style"             => $style,
            "styleText"         => $styleText
        );
    }
    
	if($perfil== 8){
     $titulo_programaA= $Programa->titulo_programa."  |  ".$Programa->TituloGrupo."  |  ".$Programa->DiaInicio;
	}else{
	  $titulo_programaA= $Programa->titulo_programa."  |  ".$Programa->DiaInicio;
     }// $titulo_programaA= $Programa->titulo_programa."  |  ".$Programa->DiaInicio;
    
    $image_url = "/system/_imagenes/program_wall.jpg";
    
    if ($Programa->pertenece != '') {
        $image_url = $Programa->ImagenUrl;
    }elseif(trim($Programa->ImagenUrl)){
        $image_url = CONS_IPArchivos . "/articulos/Programa-{$Programa->CodPrograma}/{$Programa->ImagenUrl}";
    }
    
    $key = "program-category-{$Programa->categoryId}";
    $JSONdata = [];
    
    $sql_cer = "SELECT PR.certificado_programa AS estCertificado, PR.TipoCertificacion AS Tipo
                FROM almacen AS AL
                INNER JOIN articulos AS AR ON AL.producto = AR.producto
                INNER JOIN programas AS PR ON PR.CodPrograma = AR.ProductoFab
                WHERE AL.almacenCod = $ProgramaAlmacen";
    $rg_cer = fetch($sql_cer);
    $estCertificado = $rg_cer['estCertificado'];
    $TipoCer = $rg_cer['Tipo'];
    if($TipoCer == '' || $TipoCer == 'Ninguna'){
        $certificado = '';
    }else{
        if ($estCertificado == 'Ninguno' || $estCertificado == '') {
            $certificado = '';
        }else{
            $certificado = " | <div style='display: inline-block; color:white; background: #15B85B; padding: 5px;cursor:pointer;' onclick=openPopupURI('/system/_vistas/gad_programas_simples.php?MostrarCertificado=Validar&ProgramaAlmacen=$ProgramaAlmacen&alumno=$alumno')>Certificado</div>";
        }
    }
    
    $JSONdata[$key]["items"][] = array(
        "almacenId" => $Programa->AlmacenCod,
        "title" => $titulo_programaA,
        "subtitle" => $Programa->CategoryTitle,
        "image_url" => $image_url,
        "description" => $Programa->Titulo,
        "color" => $Programa->Color,
        "title_opt" => "Cursos",
        "options" => $JSONdataCourses,
        "certificado" => $certificado
    );
    
    $JSONdata2[] = array(
        "almacenId" => $Programa->AlmacenCod,
        "title" => $titulo_programaA,
        "subtitle" => $Programa->CategoryTitle,
        "image_url" => $image_url,
        "description" => $Programa->Titulo,
        "color" => $Programa->Color,
        "title_opt" => "Cursos",
        "tipo"=>$Programa->TipoPrograma,
        "options" => $JSONdataCourses,
        "order" => $order,
        "matriculado" => false,
        "certificado" => $certificado
    );
    
    return $JSONdata2;
}

//HEREDIA
function CursosPrograma2($ProgramaAlmacen, $vConex, $Sede, $acceso, $alumno, $order,$SedeUsuario,$perfil, $arg,$tipo_c) {
 
 
    $Q_P = "SELECT 
    AR.ProductoFab,
    AR.Titulo,
    AL.AlmacenCod,
    AL.NivelMatricula,
   AL.Nombre_Grupo as TituloGrupo,
    CAT.CategoriCod as categoryId,
    CAT.Color,
    CAT.Descripcion as CategoryTitle,
    PR.CodPrograma,
    PR.GrupoProgrId,
    PR.ImagenUrl,
    PR.Titulo AS titulo_programa,
    TP.Descripcion AS 'TipoPrograma',
    AL.Entidad,
    AL.DiaInicio,
    AL.DiaFinal,
    PR.pertenece,
    PR.nombrecorto,
    AL.CursosFases,
    AL.NotaMinCertificacion
    FROM almacen AL
    INNER JOIN articulos AR ON AL.Producto = AR.Producto  
    INNER JOIN categorias CAT on AR.Categoria = CAT.CategoriCod
    INNER JOIN programas PR ON AR.ProductoFab = PR.CodPrograma  
    INNER JOIN tipoprograma TP ON PR.TipoPrograma=TP.IDTipoPrograma
    WHERE AL.AlmacenCod='{$ProgramaAlmacen}'";


    $Programa = fetchOne($Q_P, $vConex);



    //$entidadCreadora = "topitop.com";   
    $sql = "SELECT   Carpeta  FROM  usuarios  WHERE  IdUsuario = '" . $Programa->Entidad . "'  GROUP BY  Carpeta  ";                     

    $rg = fetch($sql);
    $CarpetaEmpresa = $rg["Carpeta"];


    $acceso = (int) $acceso;
    $Sede= (int) $Sede;

    $Q_Curso = "
    SELECT 
    AL.AlmacenCod  AS AlmacenCurso,
    C.ProductoCod,
    C.Orden,
    CA.Descripcion AS Categoria,
    CA.Color,
    CU.TituloCurso,
    CU.DescripcionCurso,
    AL.Entidad,
    AL.Origen,
    SC.Nombre  AS Sede,
    AL.ImagenCurso,
    CU.CodCursos
    FROM curricula C
    INNER JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod
    INNER JOIN articulos AR ON AL.Producto = AR.Producto
    INNER JOIN categorias CA ON AR.Categoria = CA.CategoriCod
    INNER JOIN cursos CU ON AR.ProductoFab = CU.CodCursos
    LEFT JOIN sede_sucursal SC ON SC.Codigo = AL.Sede ";
    
    if ($acceso == 1 ) {
        $Q_Curso .= "LEFT JOIN configuracion_accesos_curricula AS CAC ON CAC.curricula = C.CurriculaCod ";
    }

    $Q_Curso .=" WHERE C.CodProgAlmacen = {$ProgramaAlmacen} ";

    if ($acceso == 1 ){
        $Q_Curso .="
        AND CAC.accesos = 1
        AND CAC.alumno = '{$alumno}'";
    }
    
    
    if ($Sede ==  1 ){
        $Q_Curso .= "  AND AL.Sede = {$SedeUsuario} ";
    }

    $profesor = str_replace("Alumno", "Profesor", $alumno);

    if ($tipo_c == 1) {
        $Q_Curso .= " ORDER BY C.Orden asc ";
    }else if ($tipo_c == 2) {
       $Q_Curso .= "  AND AL.Origen='{$profesor}' ORDER BY C.Orden asc ";
    }
        
    $MxCU = fetchAll($Q_Curso, $vConex);


    //Creando un array para almacenar los cursos del programa
    $JSONdataCourses = array();
    $JSONdataCoursesProfesor = array();
    $emailEntidadPersona = explode("Alumno", $alumno);
    
    foreach ($MxCU as $CU) {    
        if($CU->Sede){
         $Sede = " | ".$CU->Sede;
        }else{
         $Sede = "";
        }
       
        $numActivities = (int) fetchOne("SELECT COUNT(RE.Codigo) AS numActivities
        FROM elevaluacionalumno AS EEA
        INNER JOIN elevaluaciondetallecurso AS EEDC ON EEA.EvalDetCurso=EEDC.Codigo
        INNER JOIN elevaluacionconfcurso AS EECC ON EEDC.EvalConfigCurso=EECC.Codigo
        INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
        INNER JOIN elrecursoevaluacion AS RE ON EEDC.Codigo=RE.EvaluacionDetalleCurso
        INNER JOIN eltiporecursoevaluacion AS TRE ON RE.RecursoTipo=TRE.Codigo
        INNER JOIN eltransrespuesta_cab AS ETC ON RE.Codigo=ETC.Recurso
        WHERE EECC.Almacen='{$CU->AlmacenCurso}'
        AND EEA.Alumno='{$alumno}'
        AND ETC.Alumno = '{$alumno}'
        AND ETC.Estado IN('Iniciado', 'Pendiente')
        AND EET.Tipo NOT IN ('MP','DCP','ROC','EXPC','TES')
        AND EECC.Estado <> 'Anulado'
        ORDER BY RE.FechaHoraReg DESC")->numActivities;
        
        if($CU->ImagenCurso){
            $ImagenCurso = "http://owlgroup.s3-website-us-west-2.amazonaws.com/ArchivosEmpresa/{$CarpetaEmpresa}/CU-{$CU->CodCursos}/{$CU->ImagenCurso}";                          
        }else{
            $ImagenCurso = "http://owlgroup.s3-website-us-west-2.amazonaws.com/desktopimg/6086-curso2.jpg";
        }

        if ($Programa->pertenece != '') {
            $courseId = str_replace('cuorsera', '', $Programa->pertenece);
           
            $courseraLearnUri = "https://es.coursera.org/learn";
            
            
            $redireccionar = $courseraLearnUri."/".$Programa->nombrecorto;
            //WE($redireccionar);
            $tipo = "coursera";
        }else{
            if($arg == 'noCoordina'){
                $redireccionar = "/system/room.php?AlmacenCurso={$CU->ProductoCod}&AlmacenPrograma={$ProgramaAlmacen}&Access=y&vista=$arg";
                $tipo = "normal";
            }else{
                $redireccionar = "/system/room.php?AlmacenCurso={$CU->ProductoCod}&AlmacenPrograma={$ProgramaAlmacen}&Access=y";
                $tipo = "normal";
            }
        }

        if($Programa->CursosFases && $CU->Orden != 1){

            $OrdenCursoAnterior = $CU->Orden - 1;

            $sql_1 = "SELECT ProductoCod FROM curricula WHERE CodProgAlmacen = $ProgramaAlmacen AND Orden = $OrdenCursoAnterior ";
            $rg_1 = fetch($sql_1);
            $CursoAlmacenAnterior = $rg_1["ProductoCod"];

            $Q_EE = "   SELECT
            US.Usuario AS Email ,
            EEDC.Codigo AS Actividad ,
            ETRC.Nota ,
            EECC.Codigo AS Concepto ,
            EECC.Peso
            FROM eltransrespuesta_cab AS ETRC
            INNER JOIN elrecursoevaluacion AS RE ON RE.Codigo=ETRC.Recurso
            INNER JOIN elevaluaciondetallecurso AS EEDC ON EEDC.Codigo=RE.EvaluacionDetalleCurso
            INNER JOIN elevaluacionconfcurso AS EECC ON EECC.Codigo=EEDC.EvalConfigCurso
            INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
            INNER JOIN usuarios AS US ON US.IdUsuario = ETRC.Alumno
            INNER JOIN matriculas as MAT on (ETRC.Alumno = MAT.Cliente AND MAT.Producto = {$ProgramaAlmacen} )
            INNER JOIN usuario_entidad AS UE ON (REPLACE(ETRC.Alumno,'Alumno','') = UE.Usuario AND UE.EntidadCreadora = '{$Programa->Entidad}')
            INNER JOIN alumnos AS ALUM ON ETRC.Alumno = ALUM.Usuario
            WHERE EECC.Almacen={$CursoAlmacenAnterior}
            AND EEDC.SCpt_Evaluado='SI'
            AND MAT.Estado NOT IN('Anulado','Eliminado')
            AND ETRC.Alumno <>''
            AND EET.Codigo NOT IN(23,24,25,26,27)
            AND ETRC.Alumno = '{$alumno}' ";

            $SQLTotPeso = " SELECT CS1.Peso FROM (".$Q_EE .")  AS  CS1 GROUP BY  CS1.Concepto ";
            $SQLSumPeso = " SELECT SUM(CS1.Peso) AS TotPeso FROM (".$SQLTotPeso .")  AS  CS1 ";
            $rg = fetch($SQLSumPeso);
            $TotPeso = $rg["TotPeso"];
            if ($TotPeso == "") {$TotPeso = 0;}

            $sqlResumenA = " SELECT '1'  AS ActividadMarcador  FROM (".$Q_EE.") AS CSL1 GROUP BY CSL1.Actividad ";
            $sqlResumen = " SELECT  SUM(CSL1.ActividadMarcador)  AS CtdadActividad FROM (".$sqlResumenA.") AS CSL1 ";
            $rg = fetch($sqlResumen);
            $CtdadActividad = $rg["CtdadActividad"];
            if ($CtdadActividad == ""){$CtdadActividad = 0;}

            $sqlResumenA2D = " SELECT '1' AS Participante, SUM(CSL1.Nota) AS TotNota, CSL1.Email FROM (".$Q_EE.") AS CSL1 GROUP BY CSL1.Email ";
            $sqlResumenA2 = " SELECT SUM(CSL1.Participante)  AS CtdadParticipante FROM (".$sqlResumenA2D.") AS CSL1 ";
            $rg = fetch($sqlResumenA2);
            $CtdadParticipante = $rg["CtdadParticipante"];
            $agrupaTotAlumno = " SELECT (((CSL1.TotNota / ".$CtdadActividad.")* ".$TotPeso.")/100) AS NotaAlumno FROM (".$sqlResumenA2D.") AS CSL1 GROUP BY CSL1.Email ";
            $SQLTotAprobadosSum  = "  SELECT COUNT(*) AS TotAlumnos FROM (".$agrupaTotAlumno .") AS CS1 WHERE CS1.NotaAlumno >= ".$Programa->NotaMinCertificacion." ";
             $rg = fetch($SQLTotAprobadosSum);
            $TotAlumnosAprobados = $rg["TotAlumnos"];

            $TotalAlumnoDesaprobados = $CtdadParticipante - $TotAlumnosAprobados;
            if($CtdadActividad == 0){$TotalAlumnoDesaprobados = 1;}
        }

        if($TotalAlumnoDesaprobados){
            $redireccionar = "#";
            $style = "background: rgba(128, 128, 128, 0.15);cursor: default;";
            $styleText = "text-decoration: blink;";
        }

        $JSONdataCourses[] = array(
            "title"             => $CU->TituloCurso.$Sede,
            "description"       => $CU->TituloCurso,
            "orden"             => $CU->Orden,
            "color"             => $CU->Color,
            "AlmacenCurso"      => $CU->ProductoCod,
            "ImgCurso"          => $ImagenCurso,
            "url_redirect"      => $redireccionar,
            "num_activities"    => $numActivities,
            "tipo"              => $tipo,
            "style"             => $style,
            "styleText"         => $styleText
        );
    }
    
    if($perfil== 8){
     $titulo_programaA= $Programa->titulo_programa."  |  ".$Programa->TituloGrupo."  |  ".$Programa->DiaInicio;
    }else{
      $titulo_programaA= $Programa->titulo_programa."  |  ".$Programa->DiaInicio;
     }// $titulo_programaA= $Programa->titulo_programa."  |  ".$Programa->DiaInicio;
    
    $image_url = "/system/_imagenes/program_wall.jpg";
    
    if ($Programa->pertenece != '') {
        $image_url = $Programa->ImagenUrl;
    }elseif(trim($Programa->ImagenUrl)){
        $image_url = CONS_IPArchivos . "/articulos/Programa-{$Programa->CodPrograma}/{$Programa->ImagenUrl}";
    }
    
    $key = "program-category-{$Programa->categoryId}";
    $JSONdata = [];
    
    $sql_cer = "SELECT PR.certificado_programa AS estCertificado, PR.TipoCertificacion AS Tipo
                FROM almacen AS AL
                INNER JOIN articulos AS AR ON AL.producto = AR.producto
                INNER JOIN programas AS PR ON PR.CodPrograma = AR.ProductoFab
                WHERE AL.almacenCod = $ProgramaAlmacen";
    $rg_cer = fetch($sql_cer);
    $estCertificado = $rg_cer['estCertificado'];
    $TipoCer = $rg_cer['Tipo'];
    if($TipoCer == '' || $TipoCer == 'Ninguna'){
        $certificado = '';
    }else{
        if ($estCertificado == 'Ninguno' || $estCertificado == '') {
            $certificado = '';
        }else{
            $certificado = " | <div style='display: inline-block; color:white; background: #15B85B; padding: 5px;cursor:pointer;' onclick=openPopupURI('/system/_vistas/gad_programas_simples.php?MostrarCertificado=Validar&ProgramaAlmacen=$ProgramaAlmacen&alumno=$alumno')>Certificado</div>";
        }
    }
    
    $JSONdata[$key]["items"][] = array(
        "almacenId" => $Programa->AlmacenCod,
        "title" => $titulo_programaA,
        "subtitle" => $Programa->CategoryTitle,
        "image_url" => $image_url,
        "description" => $Programa->Titulo,
        "color" => $Programa->Color,
        "title_opt" => "Cursos",
        "options" => $JSONdataCourses,
        "certificado" => $certificado
    );
    
    $JSONdata2[] = array(
        "almacenId" => $Programa->AlmacenCod,
        "title" => $titulo_programaA,
        "subtitle" => $Programa->CategoryTitle,
        "image_url" => $image_url,
        "description" => $Programa->Titulo,
        "color" => $Programa->Color,
        "title_opt" => "Cursos",
        "tipo"=>$Programa->TipoPrograma,
        "options" => $JSONdataCourses,
        "order" => $order,
        "matriculado" => false,
        "certificado" => $certificado
    );
    
    return $JSONdata2;
}


function alertaContrasena($UsuarioEntidad, $vConex) {

    $sqlS = "SELECT  Contrasena  FROM usuarios WHERE IdUsuario ='" . $UsuarioEntidad . "' ";
    $rg = fetch($sqlS);
    $contrasena = $rg['Contrasena'];
    $t = '';
    if ($contrasena == '000') {
        $t = "<div class='MessageErro' >";
        $t .="<div class='Mensaje Error' >";
        $t .="<span>¡Porfavor, actualize su contraseña o perderá acceso a los cursos!</span> ";
        $t .="<span class='mesaje21'>para actualizar su contrase&ntilde;a haga click </span><a href='/alumnos/updatealumno/hari/id' target='_blank' >aqui</a><span class='mesaje21'>  </span>";
        $t .="</div>";
        $t .="</div>";
    }
    return $t;
}

function verificarAcceso($UsuarioEntidad, $vConex) {

    $sqlS = "SELECT Contrasena, ControlContrasena FROM usuarios WHERE IdUsuario ='" . $UsuarioEntidad . "' ";
    $rg = fetch($sqlS);
    $control = $rg['ControlContrasena'];
    $contrasena = $rg['Contrasena'];
    if ($contrasena == '000' && $control >= 2) {
        $t = "<div class='MessageErro erroracesos' >";
        $t .="<div class='Mensaje Error' >";
        $t .="<span>Usted debe cambiar su contrase&ntilde;a para accesar a sus curso</span> ";
        $t .="<span class='mesaje21'>Porfavor actualice su contrase&ntilde;a </span><a href='/alumnos/updatealumno/hari/id' target='_blank' >aqui</a><span class='mesaje21'>  </span>";
        $t .="</div>";
        $t .="</div>";
    } else {
        $t = 'SI';
    }
    return $t;
}

function tituloInterrno($t_p, $t_s) {
    $t = "<div class='cabezera' style='width:100%;float:left;position:relative;padding:0px 0px 35px 0px;'>";
    $t .="<h1 style='line-height:24px !important;'><span style='font-size:1.0em !important;'>" . $t_p . " </span></h1>";
    $t .="</div>";
    return $t;
}

## FRANK

function CursosLlevoF($enlace, $UsuarioEntidad, $vConex, $UserProfesor, $Sede, $Perfi_Entidad_Usuario) {
    global $enlace, $UsuarioEntidad, $vConex, $UserProfesor;
    $estadoAcceso = verificarAcceso($UsuarioEntidad, $vConex);

    if ($estadoAcceso == 'SI') {
        $panelA .= ProgramasTomados($UsuarioEntidad, $idEmpresa, $vConex);
        $panel = array(array('PanelA', '100%', $panelA));
        $In = LayoutPage($panel);
        $S .='<div  style="width:100%;float:left;padding:10px 0px 0px 0px;">' . $In . '</div>';
    } else {
        $S = "";
    }
    return $S;
}

function EntrevistasMatriculado($enlace, $UsuarioEntidad, $vConex, $UserProfesor) {
    global $enlace, $UsuarioEntidad, $vConex, $UserProfesor;

    $estadoAcceso = verificarAcceso($UsuarioEntidad, $vConex);
    if ($estadoAcceso == 'SI') {

        $panelA .= Entrevistas($UsuarioEntidad, $idEmpresa, $vConex);

        $panel = array(array('PanelA', '100%', $panelA));
        $In = LayoutPage($panel);
        $S .='<div  style="width:100%;float:left;padding:10px 0px 0px 0px;min-width:500px;">' . $In . '</div>';
    } else {
        $S = "";
    }
    return $S;
}

function ReunionesMatriculado($enlace, $UsuarioEntidad, $vConex, $UserProfesor) {
    global $enlace, $UsuarioEntidad, $vConex, $UserProfesor;

    $panelA .= Reuniones($UsuarioEntidad, $idEmpresa, $vConex);
    $panel = array(array('PanelA', '100%', $panelA));
    $In = LayoutPage($panel);
    $S .='<div  style="width:100%;float:left;padding:10px 0px 0px 0px;min-width:500px;">' . $In . '</div>';

    return $S;
}

function CursosLlevoEBOOK($enlace, $UsuarioEntidad, $vConex, $UserProfesor) {
    global $enlace, $UsuarioEntidad, $vConex, $UserProfesor;
    $estadoAcceso = verificarAcceso($UsuarioEntidad, $vConex);

    if ($estadoAcceso == 'SI') {

        $sql = "SELECT COUNT(*) AS Reg FROM matriculas
                                INNER JOIN almacen on matriculas.Producto = almacen.AlmacenCod            
                                WHERE Cliente ='" . $UsuarioEntidad . "' 
                                AND almacen.TipoProducto<>'documento' ";
        $rg = fetch($sql);
        $ctdProductoLlevo = $rg['Reg'];

        if ($ctdProductoLlevo > 0) {
            $panelA .= ebookPublicado($vConex, $UsuarioEntidad);
        }
        $panel = array(array('PanelA', '100%', $panelA));
        $In = LayoutPage($panel);
        $S .='<div  style="width:100%;float:left;padding:10px 0px 0px 0px;">' . $In . '</div>';
    } else {
        $S = "";
    }
    return $S;
}

# MIS CURSOS 2

function ProductosLlevo($UsuarioEntidad, $vConex, $UserProfesor, $data) {

    $estadoAcceso = verificarAcceso($UsuarioEntidad, $vConex);
    if ($estadoAcceso == 'SI') {
        $sql = "SELECT COUNT(*) AS Reg FROM matriculas
                                INNER JOIN almacen on matriculas.Producto = almacen.AlmacenCod            
                                WHERE Cliente ='" . $UsuarioEntidad . "' 
                                AND almacen.TipoProducto<>'documento' ";
        $rg = fetch($sql);
        $ctdProductoLlevo = $rg['Reg'];

        if ($ctdProductoLlevo > 0) {
            $panelA .=alertaContrasena($UsuarioEntidad, $vConex);
            $panelA .= MyPrograms($UsuarioEntidad, $vConex, $data);
        }

        $panel = array(array('PanelA_1', '100%', $panelA));
        $In = LayoutPage($panel);
        $S .='<div  style="width:100%;float:left;padding:0px 0px 0px 0px;">' . $In . '</div>';
    } else {
        $S = "";
    }
    return $S;
}
#aaa
function MyPrograms2($UsuarioEntidad, $vConex) {

    $arguments = func_get_args();

    $WherePrograma = "";

            switch (sizeof($arguments)) {
    
                case 3:
    
                $WherePrograma = "AND AL.AlmacenCod='".$arguments[2]."'";
    
                    break;
    
                default:
    
                    break;
 
            }

     // Q_PRG : Query Programa
     $Q_PRG = "
     SELECT 
     AL.AlmacenCod,
     AL.TipoProducto,
     AL.Acceso AS acceso,
     AL.control AS ControlSede,
     AL.Entidad AS empresa,
     PED.orden
     FROM matriculas MA 
     INNER JOIN almacen AL ON MA.Producto = AL.AlmacenCod 
     INNER JOIN articulos AS AR ON AL.Producto = AR.Producto
     INNER JOIN programas AS PR ON AR.ProductoFab = PR.CodPrograma
     LEFT JOIN programaespecialdet PED ON PED.AlmacenCod = AL.AlmacenCod
     WHERE MA.Estado = 'Matriculado' 
     AND (PR.vista = '' OR PR.vista = 'Activado')
     AND MA.Cliente = '{$UsuarioEntidad}'
     AND (PR.vista = '' OR PR.vista = 'Activado')
     AND MA.Cliente = '{$UsuarioEntidad}'
     AND AL.TipoProducto LIKE 'programa%'
    $WherePrograma
    order by AL.TipoProducto, PED.orden asc";
    $MxPRG = fetchAll($Q_PRG, $vConex);

    if (!$MxPRG) {
        WE("null");
    }

    //Creando un array para almacenar los programas
    $JSONdata = [];

    foreach ($MxPRG as $PRG) {
        $url_empresa = $PRG->empresa;
        $acceso = $PRG->acceso;
        $ControlSede = $PRG->ControlSede;
        $control = $PRG->control;
        $cod_almacen_programa = $PRG->AlmacenCod;
        $order = (int) $PRG->orden;

        $Q_PerfilEmpresa = "
        SELECT
        P.Codigo AS Perfil,
        U.Sede
        FROM usuario_entidad AS U
        INNER JOIN usuario_perfil AS P ON U.Perfil = P.Codigo
        INNER JOIN usuarios AS US ON U.Usuario = US.Usuario
        WHERE US.IdUsuario = '{$UsuarioEntidad}'
        AND  U.EntidadCreadora = '{$url_empresa}'";

        $perfil_empresa = fetchOne($Q_PerfilEmpresa, $vConex);
        $perfil = $perfil_empresa->Perfil;
        $sedeUsuario = $perfil_empresa->Sede;

        ///////////////////////////////////
        if (($ControlSede == 1 || $ControlSede == 0) && ($perfil == 8 || $perfil == 12 || $perfil == 16)) {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, 0, $acceso, $UsuarioEntidad, $order,$sedeUsuario);
        } else if ($ControlSede == 0 && $perfil == 3) {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, 0, $acceso, $UsuarioEntidad, $order,$sedeUsuario);
        } else if ($ControlSede == 1 && $perfil == 3) {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, $ControlSede, $acceso, $UsuarioEntidad, $order,$sedeUsuario);
        } else {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, $ControlSede, $acceso, $UsuarioEntidad, $order,$sedeUsuario);
        }
        /////////////PPPPPPPPPPPPPPPPP
        $JSONdata = array_merge_recursive ($JSONdata, $cursos);
    }

    return $JSONdata;
}

function MyPrograms3($UsuarioEntidad, $vConex,$arg,$idEmpresa) {

    $arguments = func_get_args();

    $WherePrograma = "";

            switch (sizeof($arguments)) {

                case 3:

                    $WherePrograma = "AND AL.AlmacenCod='".$arguments[2]."'";

                    break;
                case 4:

                    $WherePrograma = "AND PR.Entidad='".$arguments[3]."'";

                    break;
                default:

                    break;

            }

     // Q_PRG : Query Programa
     $Q_PRG = "
     SELECT
     AL.AlmacenCod,
     AL.TipoProducto,
     AL.Acceso AS acceso,
     AL.control AS ControlSede,
     AL.Entidad AS empresa,
	
     PED.orden
     FROM matriculas MA
     INNER JOIN almacen AL ON MA.Producto = AL.AlmacenCod
     INNER JOIN articulos AS AR ON AL.Producto = AR.Producto
     INNER JOIN programas AS PR ON AR.ProductoFab = PR.CodPrograma
     LEFT JOIN programaespecialdet PED ON PED.AlmacenCod = AL.AlmacenCod
     WHERE MA.Estado = 'Matriculado'
     AND (PR.vista = '' OR PR.vista = 'Activado')
     AND MA.Cliente = '{$UsuarioEntidad}'
     AND AL.TipoProducto LIKE 'programa%'
    $WherePrograma
    order by AL.TipoProducto, PED.orden asc";
    $MxPRG = fetchAll($Q_PRG, $vConex);

    if (!$MxPRG) {
        WE("null");
    }
    
    //Creando un array para almacenar los programas 
    $JSONdata = [];

    foreach ($MxPRG as $PRG) {
        $url_empresa = $PRG->empresa;
        $acceso = $PRG->acceso;
        $ControlSede = $PRG->ControlSede;
        $control = $PRG->control;
        $cod_almacen_programa = $PRG->AlmacenCod;
	
        $order  = (int) $PRG->orden;
        
        $Q_PerfilEmpresa = "
        SELECT  
        P.Codigo AS Perfil,  
        U.Sede 
        FROM usuario_entidad AS U 
        INNER JOIN usuario_perfil AS P ON U.Perfil = P.Codigo
        INNER JOIN usuarios AS US ON U.Usuario = US.Usuario
        WHERE US.IdUsuario = '{$UsuarioEntidad}' 
        AND  U.EntidadCreadora = '{$url_empresa}'";
        
        $perfil_empresa = fetchOne($Q_PerfilEmpresa, $vConex);
        $perfil = $perfil_empresa->Perfil;
        $sedeUsuario = $perfil_empresa->Sede;
        
        ///////////////////////////////////
        if (($ControlSede == 1 || $ControlSede == 0) && ($perfil == 8 || $perfil == 12 || $perfil == 16)) {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, 0, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil );
        } else if ($ControlSede == 0 && $perfil == 3) {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, 0, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil );
        } else if ($ControlSede == 1 && $perfil == 3) {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, $ControlSede, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil );
        } else {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, $ControlSede, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil );
        }
        /////////////PPPPPPPPPPPPPPPPP
        // $JSONdata =$perfil ;
		
		$JSONdata = array_merge_recursive ($JSONdata, $cursos);
    }

    return $JSONdata;
}

function MyPrograms3Creacion($UsuarioEntidad, $vConex,$arg,$idEmpresa) {
    $arguments = func_get_args();
    $esta = false;
    $WherePrograma = "";

            switch (sizeof($arguments)) {

                case 3:

                    $WherePrograma = "AND AL.AlmacenCod='".$arguments[2]."'";

                    break;
                case 4:
                    $WherePrograma = "AND PR.Entidad='".$arguments[3]."'";

                    if($arg == 'creacion'){
                        $data = explode('Alumno', $UsuarioEntidad);
                        $usuario = $data[0];
                        $sql = "SELECT Codigo FROM usuario_entidad WHERE Usuario = '$usuario' AND EntidadCreadora = '$idEmpresa'";
                        $rg = fetch($sql);
                        $CodigoUsuario = $rg['Codigo'];
                        $WherePrograma .= "AND PR.Usuario = $CodigoUsuario";

                        $esta = true;
                        $sqlCor = "SELECT
                                     AL.AlmacenCod,
                                     AL.TipoProducto,
                                     AL.Acceso AS acceso,
                                     AL.control AS ControlSede,
                                     AL.Entidad AS empresa,
                                    
                                     PED.orden FROM lista_trabajo_det AS LTD
                                     INNER JOIN lista_trabajo AS LT ON LT.Codigo=LTD.Lista
                                     INNER JOIN almacen AS AL ON LTD.CodigoAlmacen=AL.AlmacenCod
                                     INNER JOIN programas AS PR ON LTD.CodigoProducto=PR.CodPrograma
                                     INNER JOIN lista_trabajo_det_coordinacion AS LTDC ON LTDC.lista_trabajo_det= LTD.Codigo 
                                     LEFT JOIN programaespecialdet PED ON PED.AlmacenCod = AL.AlmacenCod
                                     WHERE LT.Empresa='{$idEmpresa}'
                                     AND (PR.vista = '' OR PR.vista = 'Activado')
                                     AND LTDC.Coordinador={$CodigoUsuario} AND LTDC.Estado = 'Activo' 
                                     AND LTD.Estado = 'Abierto'  
                                     AND LTD.TipoProducto like 'Programa%'
                                     AND (AL.HerenciaOrigen = 'Original'  OR AL.HerenciaOrigen = '') 
                                     AND LTD.CodigoProducto IS NOT NULL
                                     order by AL.TipoProducto, PED.orden asc";
                    }

                    break;

                default:

                    break;

            }

     // Q_PRG : Query Programa
     $Q_PRG = "
     SELECT
     AL.AlmacenCod,
     AL.TipoProducto,
     AL.Acceso AS acceso,
     AL.control AS ControlSede,
     AL.Entidad AS empresa,
    
     PED.orden
     FROM matriculas MA
     INNER JOIN almacen AL ON MA.Producto = AL.AlmacenCod
     INNER JOIN articulos AS AR ON AL.Producto = AR.Producto
     INNER JOIN programas AS PR ON AR.ProductoFab = PR.CodPrograma
     LEFT JOIN programaespecialdet PED ON PED.AlmacenCod = AL.AlmacenCod
     WHERE MA.Estado = 'Matriculado'
     AND (PR.vista = '' OR PR.vista = 'Activado')
     AND MA.Cliente = '{$UsuarioEntidad}'
     AND AL.TipoProducto LIKE 'programa%'
    $WherePrograma
    order by AL.TipoProducto, PED.orden asc";

    if($esta){
        $Q_PRG = $sqlCor;
    }
    $MxPRG = fetchAll($Q_PRG, $vConex);

    if (!$MxPRG) {
        WE("null");
    }
    
    //Creando un array para almacenar los programas 
    $JSONdata = [];

    foreach ($MxPRG as $PRG) {
        $url_empresa = $PRG->empresa;
        $acceso = $PRG->acceso;
        $ControlSede = $PRG->ControlSede;
        $control = $PRG->control;
        $cod_almacen_programa = $PRG->AlmacenCod;
    
        $order  = (int) $PRG->orden;
        
        $Q_PerfilEmpresa = "
        SELECT  
        P.Codigo AS Perfil,  
        U.Sede 
        FROM usuario_entidad AS U 
        INNER JOIN usuario_perfil AS P ON U.Perfil = P.Codigo
        INNER JOIN usuarios AS US ON U.Usuario = US.Usuario
        WHERE US.IdUsuario = '{$UsuarioEntidad}' 
        AND  U.EntidadCreadora = '{$url_empresa}'";
        
        $perfil_empresa = fetchOne($Q_PerfilEmpresa, $vConex);
        $perfil = $perfil_empresa->Perfil;
        $sedeUsuario = $perfil_empresa->Sede;
        
        ///////////////////////////////////
        if (($ControlSede == 1 || $ControlSede == 0) && ($perfil == 8 || $perfil == 12 || $perfil == 16)) {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, 0, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil ,$arg);
        } else if ($ControlSede == 0 && $perfil == 3) {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, 0, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil ,$arg);
        } else if ($ControlSede == 1 && $perfil == 3) {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, $ControlSede, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil ,$arg);
        } else {
            $cursos = CursosPrograma($cod_almacen_programa, $vConex, $ControlSede, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil ,$arg);
        }
        /////////////PPPPPPPPPPPPPPPPP
        // $JSONdata =$perfil ;
        
        $JSONdata = array_merge_recursive ($JSONdata, $cursos);
    }

    return $JSONdata;
}

function MyPrograms4Creacion($UsuarioEntidad, $vConex,$arg,$idEmpresa) {
    $arguments = func_get_args();
    $esta = false;
    $WherePrograma = "";

            switch (sizeof($arguments)) {

                case 3:

                    $WherePrograma = "AND AL.AlmacenCod='".$arguments[2]."'";

                    break;
                case 4:
                    $WherePrograma = "AND PR.Entidad='".$arguments[3]."'";
                    
                    if($arg == 'creacion'){
                        $data = explode('Alumno', $UsuarioEntidad);
                        $usuario = $data[0];
                        $sql = "SELECT Codigo FROM usuario_entidad WHERE Usuario = '$usuario' AND EntidadCreadora = '$idEmpresa'";
                        $rg = fetch($sql);
                        $CodigoUsuario = $rg['Codigo'];
                        $WherePrograma .= "AND PR.Usuario = $CodigoUsuario";

                        $esta = true;
                        $sqlCor = "SELECT 
                                        AL.AlmacenCod,
                                        AL.TipoProducto,
                                        AL.Acceso AS acceso,
                                        AL.control AS ControlSede,
                                        AL.Entidad AS empresa,
                                        PED.orden,
                                        '1' AS tipo_c
                                        FROM lista_trabajo_det AS LTD  
                                        INNER JOIN lista_trabajo AS LT ON LT.Codigo=LTD.Lista  
                                        INNER JOIN almacen AS AL ON LTD.CodigoAlmacen=AL.AlmacenCod  
                                        INNER JOIN programas AS PR ON LTD.CodigoProducto=PR.CodPrograma 
                                        INNER JOIN profesoraux AS PX ON PX.lista_trabajo_det = LTD.Codigo  
                                        LEFT JOIN programaespecialdet PED ON PED.AlmacenCod = AL.AlmacenCod  
                                        WHERE LT.Empresa='{$idEmpresa}' 
                                        AND (PR.vista = '' OR PR.vista = 'Activado')  
                                        AND PX.Usuario ={$CodigoUsuario}  
                                        AND LTD.Estado = 'Abierto'  
                                        AND LTD.TipoProducto like 'Programa%'  
                                        AND (AL.HerenciaOrigen = 'Original'  OR AL.HerenciaOrigen = '')  
                                        AND LTD.CodigoProducto IS NOT NULL  ";

                                   // WE($sqlCor);
                    }

                    break;

                default:

                    break;

            }

     // Q_PRG : Query Programa
     $Q_PRG = "
     SELECT
     AL.AlmacenCod,
     AL.TipoProducto,
     AL.Acceso AS acceso,
     AL.control AS ControlSede,
     AL.Entidad AS empresa,
     PED.orden,
     '2' AS tipo_c
     FROM matriculas MA
     INNER JOIN almacen AL ON MA.Producto = AL.AlmacenCod
     INNER JOIN articulos AS AR ON AL.Producto = AR.Producto
     INNER JOIN programas AS PR ON AR.ProductoFab = PR.CodPrograma
     LEFT JOIN programaespecialdet PED ON PED.AlmacenCod = AL.AlmacenCod
     WHERE MA.Estado = 'Matriculado'
     AND (PR.vista = '' OR PR.vista = 'Activado')
     AND MA.Cliente = '{$UsuarioEntidad}'
     AND AL.TipoProducto LIKE 'programa%' ";

    $JSONdata = [];
/*
    if($esta){
       
        $MxPRG2 = fetchAll($sqlCor, $vConex);

            //Creando un array para almacenar los programas 

        foreach ($MxPRG2 as $PRG) {
            $url_empresa = $PRG->empresa;
            $acceso = $PRG->acceso;
            $ControlSede = $PRG->ControlSede;
            $control = $PRG->control;
            $cod_almacen_programa = $PRG->AlmacenCod;
        
            $order  = (int) $PRG->orden;
            
            $Q_PerfilEmpresa = "
            SELECT  
            P.Codigo AS Perfil,  
            U.Sede 
            FROM usuario_entidad AS U 
            INNER JOIN usuario_perfil AS P ON U.Perfil = P.Codigo
            INNER JOIN usuarios AS US ON U.Usuario = US.Usuario
            WHERE US.IdUsuario = '{$UsuarioEntidad}' 
            AND  U.EntidadCreadora = '{$url_empresa}'";

            //W($Q_PerfilEmpresa);
            
            $perfil_empresa = fetchOne($Q_PerfilEmpresa, $vConex);
            $perfil = $perfil_empresa->Perfil;
            $sedeUsuario = $perfil_empresa->Sede;
            
            $cursos = CursosPrograma2($cod_almacen_programa, $vConex, 0, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil ,$arg,1);
            /////////////PPPPPPPPPPPPPPPPP
            // $JSONdata =$perfil ;
            
            $JSONdata = array_merge_recursive ($JSONdata, $cursos);
        }

    }
*/
    $Q_PRG = $Q_PRG . "  UNION DISTINCT "  . $sqlCor;

    //W($Q_PRG);

    $MxPRG = fetchAll($Q_PRG, $vConex);

    if (!$MxPRG) {
        WE("null");
    }
    

        foreach ($MxPRG as $PRG) {



            $url_empresa = $PRG->empresa;
            $acceso = $PRG->acceso;
            $ControlSede = $PRG->ControlSede;
            $control = $PRG->control;
            $cod_almacen_programa = $PRG->AlmacenCod;
            $tipo_c = $PRG->tipo_c;
        
            $order  = (int) $PRG->orden;
            
            $Q_PerfilEmpresa = "
            SELECT  
            P.Codigo AS Perfil,  
            U.Sede 
            FROM usuario_entidad AS U 
            INNER JOIN usuario_perfil AS P ON U.Perfil = P.Codigo
            INNER JOIN usuarios AS US ON U.Usuario = US.Usuario
            WHERE US.IdUsuario = '{$UsuarioEntidad}' 
            AND  U.EntidadCreadora = '{$url_empresa}'";
            
            $perfil_empresa = fetchOne($Q_PerfilEmpresa, $vConex);
            $perfil = $perfil_empresa->Perfil;
            $sedeUsuario = $perfil_empresa->Sede;


            //validacion si hay cursoso en el programa
                    $acceso = (int) $acceso;
                    $Sede= (int) $Sede;

                    $Q_Curso = "
                    SELECT 
                    AL.AlmacenCod  
                    FROM curricula C
                    INNER JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod
                    INNER JOIN articulos AR ON AL.Producto = AR.Producto
                    INNER JOIN categorias CA ON AR.Categoria = CA.CategoriCod
                    INNER JOIN cursos CU ON AR.ProductoFab = CU.CodCursos
                    LEFT JOIN sede_sucursal SC ON SC.Codigo = AL.Sede ";
        
                if ($acceso == 1 ) {
                 $Q_Curso .= "LEFT JOIN configuracion_accesos_curricula AS CAC ON CAC.curricula = C.CurriculaCod ";
             }

              $Q_Curso .=" WHERE C.CodProgAlmacen = {$cod_almacen_programa} ";

             if ($acceso == 1 ){
                    $Q_Curso .="
                 AND CAC.accesos = 1
                 AND CAC.alumno = '{$UsuarioEntidad}'";
             }
        
        
              if ($Sede ==  1 ){
                 $Q_Curso .= "  AND AL.Sede = {$sedeUsuario} ";
                }

             $profesor = str_replace("Alumno", "Profesor", $UsuarioEntidad);

             if ($tipo_c == 1) {
                 $Q_Curso .= " ORDER BY C.Orden asc ";
                }else if ($tipo_c == 2) {
                $Q_Curso .= "  AND AL.Origen='{$profesor}' ORDER BY C.Orden asc ";
                }
            
             $numero_de_cursos = fetchAll($Q_Curso, $vConex);
             //fin de vlaidacion

        if ($numero_de_cursos) {
            
        
        
            ///////////////////////////////////
            if (($ControlSede == 1 || $ControlSede == 0) && ($perfil == 8 || $perfil == 12 || $perfil == 16)) {
                $cursos = CursosPrograma2($cod_almacen_programa, $vConex, 0, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil ,$arg,$tipo_c);
            } else if ($ControlSede == 0 && $perfil == 3) {
                $cursos = CursosPrograma($cod_almacen_programa, $vConex, 0, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil ,$arg);
            } else if ($ControlSede == 1 && $perfil == 3) {
                $cursos = CursosPrograma($cod_almacen_programa, $vConex, $ControlSede, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil ,$arg);
            } else {
                $cursos = CursosPrograma($cod_almacen_programa, $vConex, $ControlSede, $acceso, $UsuarioEntidad, $order,$sedeUsuario,$perfil ,$arg);
            }
            /////////////PPPPPPPPPPPPPPPPP
            // $JSONdata =$perfil ;
            
            $JSONdata = array_merge_recursive ($JSONdata, $cursos);
        }
    }

    return $JSONdata;
}

//HEREDIA

//validar si exsten cursos en el programa>> 
function validar_programa($ProgramaAlmacen, $vConex, $Sede, $acceso, $alumno, $order,$SedeUsuario,$perfil, $arg,$tipo_c) {

    $acceso = (int) $acceso;
    $Sede= (int) $Sede;

    $Q_Curso = "
    SELECT 
    AL.AlmacenCod  
    FROM curricula C
    INNER JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod
    INNER JOIN articulos AR ON AL.Producto = AR.Producto
    INNER JOIN categorias CA ON AR.Categoria = CA.CategoriCod
    INNER JOIN cursos CU ON AR.ProductoFab = CU.CodCursos
    LEFT JOIN sede_sucursal SC ON SC.Codigo = AL.Sede ";
    
    if ($acceso == 1 ) {
        $Q_Curso .= "LEFT JOIN configuracion_accesos_curricula AS CAC ON CAC.curricula = C.CurriculaCod ";
    }

    $Q_Curso .=" WHERE C.CodProgAlmacen = {$ProgramaAlmacen} ";

    if ($acceso == 1 ){
        $Q_Curso .="
        AND CAC.accesos = 1
        AND CAC.alumno = '{$alumno}'";
    }
    
    
    if ($Sede ==  1 ){
        $Q_Curso .= "  AND AL.Sede = {$SedeUsuario} ";
    }

    $profesor = str_replace("Alumno", "Profesor", $alumno);

    if ($tipo_c == 1) {
        $Q_Curso .= " ORDER BY C.Orden asc ";
    }else if ($tipo_c == 2) {
       $Q_Curso .= "  AND AL.Origen='{$profesor}' ORDER BY C.Orden asc ";
    }
        
    $numero_de_cursos = fetchAll($Q_Curso, $vConex);

    
    if ($MxCU) {
       return true;
    }else{
        return false;
    }
}

function CatalogoPrograms($UsuarioEntidad, $vConex, $idEmpresa){
    $Sql = "select IDTipoPrograma, Descripcion from tipoprograma";  
    $SqlCategoria = fetchAll($Sql,$vConex);

    if($SqlCategotria) {        
        WE("null");
    }
    $JSONdata = array();
   
    foreach ($SqlCategoria as $key) {
     
        $IdCategoria = $key->IDTipoPrograma;
        $DesCategoria = $key->Descripcion;
        
        $programas = AllPrograms($vConex,$IdCategoria,$idEmpresa,$UsuarioEntidad);

        if (sizeof($programas)>0) {
        
       $image_url = "";
       $JSONdata[] = array(
        "almacenId" => $IdCategoria,
        "title" => $DesCategoria,
        "subtitle" => $Descripcion,
        "image_url" => $image_url,
        "description" => "titulo",
        "color" => "red",
        "title_opt" => "Cursos",
        "tipo"=>"2",
        "options" => $programas,
        "order" => "2"
        
        );
       }
    }


       return $JSONdata;   
}

function  AllPrograms($vConex,$IdCategoria,$idEmpresa,$UsuarioEntidad){
    $Sql = "Select PR.FechaInicial, PR.FechaFinal, AL.AlmacenCod, PR.CodPrograma AS programa, PR.Titulo, PR.ImagenUrl AS imagen 
            from programas AS PR 
            INNER JOIN articulos AS AR ON AR.ProductoFab = PR.CodPrograma
            INNER JOIN almacen AS AL ON AL.Producto = AR.Producto
            INNER JOIN categorias AS CA ON PR.CategoriaCod = CA.CategoriCod 
            WHERE  ExposicionCatalogo='si'
            AND AL.Entidad = '".$idEmpresa."'
            AND PR.vista <> 'Desactivado'
            AND PR.TipoPrograma = ".$IdCategoria;
           // WE($Sql);
    $SqlProgramas = fetchAll($Sql);
    /*
    if ($SqlProgramas) {
        VD(1);
        WE("null");
    }   
    */
    $programas = array();

    foreach ($SqlProgramas as $key) {
        $IdPrograma = $key->programa;
        $TituloPrograma = $key->Titulo;
        $fechaIni = ExtraerMes($key->FechaInicial);
        $fechaFin = ExtraerMes($key->FechaFinal);
        $TituloPrograma = $TituloPrograma." \n ".$fechaIni." - ".$fechaFin;
        $Imagen = $key->imagen;
        $ImagenUrl="https://d2mv2wiw5k8g3l.cloudfront.net//".$Imagen;
        $cod_almacen_programa = $key->AlmacenCod;
        $matricula = true;
        
        $sqlM = "SELECT MAT.Cliente
            FROM matriculas AS MAT
            INNER JOIN alumnos AS ALUM ON MAT.Cliente=ALUM.Usuario
            INNER JOIN usuario_entidad AS UE ON (REPLACE(MAT.Cliente,'Alumno','') = UE.Usuario  AND UE.EntidadCreadora = '$idEmpresa')
            INNER JOIN sede_sucursal AS SS ON UE.Sede = SS.Codigo
            INNER JOIN sedes AS SSC ON SS.UnidadNegocio = SSC.Codigo
            INNER JOIN usuario_perfil AS UP ON UP.Codigo = UE.Perfil
            INNER JOIN usuarios AS US ON ALUM.Usuario = US.IdUsuario
            WHERE MAT.Producto=$cod_almacen_programa
      
            AND (MAT.Estado NOT IN('Anulado','Eliminado'))
            AND MAT.Cliente = '$UsuarioEntidad'
            ORDER BY SS.Nombre";
       
        $rg = fetch($sqlM);
        $Matriculado = $rg["Cliente"];
        if($Matriculado){
            $matricula = false;
        }

        $programas[] = array(
            "Id" => $IdPrograma,
            "title" => $TituloPrograma,
            "Imagen" => $ImagenUrl,
            "matricula" =>$matricula,
            "IdAlmacen" =>$cod_almacen_programa
        );
    }

    return $programas;

}

function Catalogo($UsuarioEntidad, $vConex, $idEmpresa) {

     $Q_PRG = "SELECT DISTINCT
        AL.AlmacenCod,
        AL.TipoProducto,
        AL.Acceso AS acceso,
        AL.control AS ControlSede,
        AL.Entidad AS empresa,
        PED.orden
        FROM lista_trabajo_det AS LTD
        INNER JOIN lista_trabajo AS LT ON LT.Codigo=LTD.Lista
        INNER JOIN almacen AS AL ON LTD.CodigoAlmacen=AL.AlmacenCod
        LEFT JOIN programaespecialdet PED ON PED.AlmacenCod = AL.AlmacenCod
        INNER JOIN lista_trabajo_det_coordinacion AS LTDC ON LTDC.lista_trabajo_det= LTD.Codigo
        WHERE LT.Empresa='{$idEmpresa}'
        AND LTDC.Estado = 'Activo'
        AND LTD.Estado = 'Abierto'
        AND LTD.TipoProducto like 'Programa%'
        AND LTD.CodigoProducto IS NOT NULL
        AND AL.ExposicionCatalogo = 'Si'
        ORDER BY LTD.Estado ASC;";

        $MxPRG = fetchAll($Q_PRG, $vConex);

        if (!$MxPRG) {
            WE("null");
        }

        
        $JSONdata = [];

        foreach ($MxPRG as $PRG) {
            $url_empresa = $PRG->empresa;
            $acceso = $PRG->acceso;
            $ControlSede = $PRG->ControlSede;
            $control = $PRG->control;
            $cod_almacen_programa = $PRG->AlmacenCod;
            $order = (int) $PRG->orden;

            $Q_PerfilEmpresa = "
            SELECT
            P.Codigo AS Perfil,
            U.Sede
            FROM usuario_entidad AS U
            INNER JOIN usuario_perfil AS P ON U.Perfil = P.Codigo
            INNER JOIN usuarios AS US ON U.Usuario = US.Usuario
            WHERE U.EntidadCreadora = '{$url_empresa}'";

            $perfil_empresa = fetchOne($Q_PerfilEmpresa, $vConex);
            $perfil = $perfil_empresa->Perfil;
            $sedeUsuario = $perfil_empresa->Sede;


            if (($ControlSede == 1 || $ControlSede == 0) && ($perfil == 8 || $perfil == 12 || $perfil == 16)) {
                $cursos = CursosPrograma($cod_almacen_programa, $vConex, 0, $acceso, '', $order,$sedeUsuario);
            } else if ($ControlSede == 0 && $perfil == 3) {
                $cursos = CursosPrograma($cod_almacen_programa, $vConex, 0, $acceso, '', $order,$sedeUsuario);
            } else if ($ControlSede == 1 && $perfil == 3) {
                $cursos = CursosPrograma($cod_almacen_programa, $vConex, $ControlSede, $acceso, '', $order,$sedeUsuario);
            } else {
                $cursos = CursosPrograma($cod_almacen_programa, $vConex, $ControlSede, $acceso, '', $order,$sedeUsuario);
            }

            $sql = "SELECT MAT.Cliente
                FROM matriculas AS MAT
                INNER JOIN alumnos AS ALUM ON MAT.Cliente=ALUM.Usuario
                INNER JOIN usuario_entidad AS UE ON (REPLACE(MAT.Cliente,'Alumno','') = UE.Usuario  AND UE.EntidadCreadora = '$idEmpresa')
                INNER JOIN sede_sucursal AS SS ON UE.Sede = SS.Codigo
                INNER JOIN sedes AS SSC ON SS.UnidadNegocio = SSC.Codigo
                INNER JOIN usuario_perfil AS UP ON UP.Codigo = UE.Perfil
                INNER JOIN usuarios AS US ON ALUM.Usuario = US.IdUsuario
                WHERE MAT.Producto=$cod_almacen_programa
                AND MAT.CodAlmacenSN=$cod_almacen_programa
                AND (MAT.Estado NOT IN('Anulado','Eliminado'))
                AND MAT.Cliente = '$UsuarioEntidad'
                ORDER BY SS.Nombre";
            $rg = fetch($sql);
            $Matriculado = $rg["Cliente"];
            if($Matriculado){
                $cursos[0]["matriculado"] = true;
            }
            $JSONdata = array_merge_recursive ($JSONdata, $cursos);
        }

        return $JSONdata;
}

function MyPrograms($UsuarioEntidad, $vConex, $data) {
    // Q_PRG : Query Programa
    $Q_PRG = "SELECT AL_E.CodAlmacenContenedor
        FROM almacen_entidad AL_E
        WHERE AL_E.Entidad='{$UsuarioEntidad}'";
    
    $MxPRG = fetchAll($Q_PRG, $vConex);

    if (!$MxPRG) {
        WE("NO TIENES NINGUN PRODUCTO EN NUESTRA PLATAFORMA");
    }

    //Creando un array para almacenar los programas del Programa Extendido
    $JSONdata = array();

    foreach ($MxPRG as $PRG) {
        $Q_P = "SELECT 
                AR.ProductoFab,
                AL.NivelMatricula,
                CAT.Color,
                PR.GrupoProgrId,
                PR.Titulo,
                TP.Descripcion AS 'TipoPrograma'
                FROM almacen AL 
                INNER JOIN articulos AR ON AL.Producto = AR.Producto  
                INNER JOIN categorias CAT on AR.Categoria = CAT.CategoriCod
                INNER JOIN programas PR ON AR.ProductoFab = PR.CodPrograma  
                INNER JOIN tipoprograma TP ON PR.TipoPrograma=TP.IDTipoPrograma
                WHERE AL.AlmacenCod='{$PRG->CodAlmacenContenedor}'";
                
        # ProductoFab : El codigo de Programa de la tabla PROGRAMAS
        # NivelMatricula : EL nivel de matricula definido para el Programa en la tabla ALMACEN [Curso,Programa,Modulo]
        # GrupoProgId : El alcance del Programa
        # TipoPrograma :Tipo de Programa (Curso,Diplomado,Seminario,Extendido)
                
        $ObjP = fetchOne($Q_P, $vConex);
        $TP = $ObjP->TipoPrograma;
        $NM = $ObjP->NivelMatricula;
        $dataPRG = array(
            "Titulo" => $ObjP->Titulo,
            "Color" => $ObjP->Color
        );

        switch ($NM) {
            case "Curso":
                if (($TP == "Diplomado" && $data["Diplomado"]) || ($TP == "Seminario" && $data["Seminario"])) {
                    //PRIMERO RECUPERO LOS CURSOS AL QUE TIENE ACCESO
                    $Q_CUR_ACCESS = "SELECT MAT.CodAlmacenPN
                                        FROM matriculas MAT 
                                        WHERE MAT.Producto={$PRG->CodAlmacenContenedor} 
                                        AND MAT.CodAlmacenSN={$PRG->CodAlmacenContenedor} 
                                        AND MAT.TipoAccesoMatricula='Curso'
                                        AND MAT.Cliente='{$UsuarioEntidad}'";
                                        
                    $MxCUR_ACCESS = fetchAll($Q_CUR_ACCESS, $vConex);

                    //Mx_FCA : Matriz Filtro Curso Acceso
                    $Mx_FCA = array();
                    foreach ($MxCUR_ACCESS as $CUR_ACCESS) {
                        $Mx_FCA[] = $CUR_ACCESS->CodAlmacenPN;
                    }

                    $FCA = implode(",", $Mx_FCA);
                    $dataPRG["FCA"] = $FCA;

                    $JSONdata[] = dataProgram($PRG->CodAlmacenContenedor, $vConex, $dataPRG);
                }
                break;
            case "Modulo":
                if ($TP == "Extendido" && $data["Extendido"]) {
                    $JSONdata[] = dataProgramExt($PRG->CodAlmacenContenedor, $vConex, $dataPRG);
                }
                break;
            case "Programa":
                if (($TP == "Curso" && $data["Curso"]) || ($TP == "Diplomado" && $data["Diplomado"]) || ($TP == "Seminario" && $data["Seminario"])) {
                    $JSONdata[] = dataProgram($PRG->CodAlmacenContenedor, $vConex, $dataPRG);
                } else if ($TP == "Extendido" && $data["Extendido"]) {
                    //PRIMERO RECUPERO LOS PROGRAMAS AL QUE TIENE ACCESO
                    $Q_PRG_ACCESS = "SELECT MAT.CodAlmacenSN
                                        FROM matriculas MAT 
                                        WHERE MAT.Producto={$PRG->CodAlmacenContenedor} 
                                        AND MAT.CodAlmacenTN={$PRG->CodAlmacenContenedor} 
                                        AND MAT.TipoAccesoMatricula='Programa'
                                        AND MAT.Cliente='{$UsuarioEntidad}'";
                    $MxPRG_ACCESS = fetchAll($Q_PRG_ACCESS, $vConex);

                    //Mx_FA : Matriz Filtro Programa Acceso
                    $Mx_FPA = array();
                    foreach ($MxPRG_ACCESS as $PRG_ACCESS) {
                        $Mx_FPA[] = $PRG_ACCESS->CodAlmacenSN;
                    }

                    $FPA = implode(",", $Mx_FPA);
                    $dataPRG["FPA"] = $FPA;

                    $JSONdata[] = dataProgramExt($PRG->CodAlmacenContenedor, $vConex, $dataPRG);
                }
                break;
        }
    }

    return $JSONdata;
}

function dataProgram($CodigoAlmacenPrograma, $vConex, array $dataPRG) {
    $TituloPRG = $dataPRG["Titulo"];
    $ColorPRG = $dataPRG["Color"];

    //Evaluando si hay un filtro de Cursos
    //FCA : Filtro Programa Acceso
    if ($dataPRG["FCA"]) {
        $FCA = $dataPRG["FCA"];
        $Q_FCA = "AND C.ProductoCod IN ({$FCA}) ";
    } else {
        $Q_FCA = "";
    }

    //CONSULTANDO CURSOS DEL PROGRAMA
    $Q_CU = "SELECT C.ProductoCod,CU.TituloCurso,CU.DescripcionCurso,CU.FechaInicio,CAT.Color
        FROM curricula C 
        INNER JOIN almacen AL ON C.ProductoCod=AL.AlmacenCod 
        INNER JOIN articulos AR ON AL.Producto=AR.Producto
        INNER JOIN categorias CAT on AR.Categoria = CAT.CategoriCod
        INNER JOIN cursos CU ON AR.ProductoFab=CU.CodCursos
        WHERE C.CodProgAlmacen={$CodigoAlmacenPrograma} {$Q_FCA}
        ORDER BY C.Orden ASC";
        
    $MxCU = fetchAll($Q_CU, $vConex);

    //Creando un array para almacenar los cursos del programa
    $JSONdataCourses = array();

    foreach ($MxCU as $CU) {
        $JSONdataCourses[] = array(
            "title" => $CU->TituloCurso,
            "description" => $CU->DescripcionCurso,
            "color" => $CU->Color,
            "url_redirect" => "/system/room.php?AlmacenCurso={$CU->ProductoCod}&AlmacenPrograma={$CodigoAlmacenPrograma}&Access=y"
        );
    }
    
    $JSONdata = array(
        "title" => $TituloPRG,
        "image_url" => "/system/_imagenes/program_wall.jpg",
        "description" => "Descripcion del Programa {$TituloPRG}",
        "color" => $ColorPRG,
        "title_opt" => "Cursos",
        "options" => $JSONdataCourses
    );
    
    return $JSONdata;
}

function dataProgramExt($CodigoAlmacenPrograma, $vConex, array $dataPRG) {
    $TituloPRG = $dataPRG["Titulo"];
    $ColorPRG = $dataPRG["Color"];

    //Evaluando si hay un filtro de Programas
    //FPA : Filtro Programa Acceso
    if ($dataPRG["FPA"]) {
        $FPA = $dataPRG["FPA"];
        $Q_FPA = "AND CPE.CodAlmacenSN IN ({$FPA}) ";
    } else {
        $Q_FPA = "";
    }

    $Q_PRG = "SELECT CPE.CodAlmacenSN,PRG.Titulo,PRG.Descripcion,CAT.Color
        FROM curricula_programa_extendido CPE
        INNER JOIN almacen AL ON AL.AlmacenCod=CPE.CodAlmacenSN
        INNER JOIN articulos AR ON AR.Producto=AL.Producto
        INNER JOIN categorias CAT on AR.Categoria = CAT.CategoriCod
        INNER JOIN programas PRG ON AR.ProductoFab=PRG.CodPrograma
        INNER JOIN tipoprograma TP ON PRG.TipoPrograma=TP.IDTipoPrograma
        WHERE CPE.CodAlmacenTC={$CodigoAlmacenPrograma} {$Q_FPA}";
        
    $MxPRG = fetchAll($Q_PRG, $vConex);

    //Creando un array para almacenar los programas del Programa Extendido
    $JSONdataPrograms = array();

    foreach ($MxPRG as $PRG) {
        $JSONdataPrograms[] = array(
            "title" => $PRG->Titulo,
            "description" => $PRG->Descripcion
        );
    }

    $JSONdata = array(
        "title" => $TituloPRG,
        "image_url" => "/system/_imagenes/program_wall.jpg",
        "description" => "Descripcion del Programa Extendido {$TituloPRG}",
        "color" => $ColorPRG,
        "title_opt" => "Programas",
        "options" => $JSONdataPrograms
    );

    return $JSONdata;
}

function ProcesaPreguntasSelectMultiples($UsuarioAlumno, $IdREval, $Pregunta, $CantidadNota, $vConex) {

    ////////////////////// INICIO DEL PROCESO : Notas de Preguntas Multiselectivas
    $sql = " SELECT tab3.CantidadNota AS TotNota ";
    $sql .= " ,tab1.Estado  ";
    $sql .= " ,tab4.MaximoNota ";
    $sql .= " ,tab5.Codigo AS Concepto ";
    $sql .= " ,tab5.EvalConfigCurso ";
    $sql .= " , tab1.Descripcion  AS RptaAlumno, tab2.RespuestaCorrecta , tab3.CantidadNota";
    $sql .= " FROM eltransrespuesta AS tab1 ";
    $sql .= " INNER JOIN elrespuesta AS tab2 ON tab1.Respuesta = tab2.Codigo ";
    $sql .= " INNER JOIN elpregunta AS tab3 ON tab2.Pregunta = tab3.codigo ";
    $sql .= " INNER JOIN elrecursoevaluacion AS tab4 ON tab3.RecursoEvaluacion = tab4.Codigo ";
    $sql .= " INNER JOIN elevaluaciondetallecurso AS tab5 ON tab4.EvaluacionDetalleCurso = tab5.Codigo ";
    $sql .= " WHERE tab1.Usuario = '" . $UsuarioAlumno . "' AND (tab4.TipoNota = 1 OR tab4.TipoNota = 3) ";
    $sql .= " AND tab4.codigo = " . $IdREval . " AND tab3.TipoPregunta = 2  AND  tab2.Pregunta =  " . $Pregunta . " ";
    // W(" <BR> ".$sql );

    $consulta = Matris_Datos($sql, $vConex);
    $CountRespuestaCorectas = 0;
    $CountRPSelectivaRCorrecta = 0;
    $CountRPSelectivaRInCorrecta = 0;
    $CountRPSelectiva = 0;
    $TotNotaPorPregunta = $CantidadNota;

    while ($regA2 = mysql_fetch_array($consulta)) {



        $CountRPSelectiva += 1;
        $RptaAlumno = $regA2["RptaAlumno"];
        if (!empty($RptaAlumno)) {
            $RptaAlumno = 1;
        } else {
            $RptaAlumno = 0;
        }

        if ($RptaAlumno == $regA2["RespuestaCorrecta"] && $regA2["RespuestaCorrecta"] == 1) {
            $CountRPSelectivaRCorrecta += 1;
        }

        if ($RptaAlumno !== $regA2["RespuestaCorrecta"] && $RptaAlumno == 1 && $regA2["RespuestaCorrecta"] == 0) {
            $CountRPSelectivaRInCorrecta += 1;
        }

        if ($regA2["RespuestaCorrecta"] == 1) {
            $CountRespuestaCorectas += 1;
        }
    }

    if ($CountRPSelectiva !== 0) {
        $ValorPorPreguntaCorrecta = $TotNotaPorPregunta / $CountRespuestaCorectas;
        $RptaCorrectasTot = $ValorPorPreguntaCorrecta * $CountRPSelectivaRCorrecta;
        $RptaIncorrectasTot = $ValorPorPreguntaCorrecta * $CountRPSelectivaRInCorrecta;
        $TotNotaPreguntaSelectivaM = $RptaCorrectasTot - $RptaIncorrectasTot;
    } else {
        $TotNotaPreguntaSelectivaM = 0;
    }

    return $TotNotaPreguntaSelectivaM;
    ////////////////////// FIN  DEL PROCESO : Notas de Preguntas Multiselectivas
}

function NotaParcialExamen($IdREval, $UsuarioAlumno, $vConex) {
    //CONSTANTES DE EXAMEN
    define("PREGUNTA_SELECTIVA", 1);
    define("PREGUNTA_MULTI_SELECTIVA", 2);
    define("PREGUNTA_ABIERTA", 3);
    define("PREGUNTA_SELECTIVA_RELACIONAL", 4);

    $NotaParcial = 0;

    $Q_Pregunta = "SELECT P.Codigo,
        P.Descripcion,
        P.TipoNota,
        P.RecursoEvaluacion,
        P.CantidadNota,
        P.Orden,
        P.TipoPregunta,
        TP.Descripcion AS TipoPreguntaDesc
        FROM elpregunta P
        INNER JOIN tipo_pregunta TP ON TP.Codigo = P.TipoPregunta
        WHERE P.RecursoEvaluacion = {$IdREval}
        AND P.TipoPregunta NOT IN( " . PREGUNTA_ABIERTA . ")
        ORDER BY P.Orden ";

    $MxPregunta = fetchAll($Q_Pregunta, $vConex);

    foreach ($MxPregunta as $Pregunta) {
        $TipoPregunta = (int) $Pregunta->TipoPregunta;

        $AuxNOTA = 0;

        switch ($TipoPregunta) {
            case PREGUNTA_SELECTIVA:
                $Q_NOTA_PARCIAL = "SELECT SUM(EP.CantidadNota) AS totalNotaParcial
                    FROM eltransrespuesta AS ETR
                    INNER JOIN elrespuesta AS ER ON ETR.Respuesta = ER.Codigo
                    INNER JOIN elpregunta AS EP ON ER.Pregunta = EP.codigo
                    INNER JOIN elrecursoevaluacion AS RE ON EP.RecursoEvaluacion = RE.Codigo
                    WHERE ETR.Usuario = '{$UsuarioAlumno}'
                    AND EP.Codigo = {$Pregunta->Codigo}
                    AND RE.Codigo = {$IdREval}
                    AND ER.RespuestaCorrecta = 1
                    AND ETR.Descripcion = '1'";
                $AuxNOTA = (int) fetchOne($Q_NOTA_PARCIAL, $vConex)->totalNotaParcial;
                $NotaParcial += $AuxNOTA;
                break;
            case PREGUNTA_MULTI_SELECTIVA:
                $AuxNOTA = ProcesaPreguntasSelectMultiples($UsuarioAlumno, $IdREval, $Pregunta->Codigo, $Pregunta->CantidadNota, $vConex);
                $NotaParcial += $AuxNOTA;
                break;
        }

        //Nota por Pregunta
        $sqlA = "UPDATE eltransrespuesta
            SET NotaObtenida = {$AuxNOTA}
            WHERE Pregunta = {$Pregunta->Codigo}
            AND Usuario = '{$UsuarioAlumno}'";
        xSQL2($sqlA, $vConex);
    }

    return $NotaParcial;
}

function ebookPublicado($cnOwl, $UsuarioEntidad) {
    // global $cnOwl,$enlace,$UsuarioEntidad;  
    // $cnOwl = conexSys();
    $t .="<div style='width:100%;'>";

    $sql = " SELECT AR.Titulo as Titulo ";
    $sql .=" ,AR.Descripcion as Descripcion ";
    $sql .=" ,CA.Descripcion as categorias ";
    $sql .=" ,AL.TipoProducto as TipProducto";
    $sql .=" ,AR.ProductoFab As Producto ";
    $sql .=" ,AL.AlmacenCod As CodAlmacen ";
    $sql .=" ,CA.Imagen ";
    $sql .=" ,TP.Descripcion as DescripTipoProd ";
    $sql .=" ,MA.IdFacturasCab ";
    $sql .=" ,CA.Color ";
    $sql .=" FROM matriculas MA ";
    $sql .=" LEFT JOIN almacen AL ON MA.Producto = AL.AlmacenCod ";
    $sql .=" LEFT JOIN articulos AR ON AR.Producto = AL.Producto ";
    $sql .=" LEFT JOIN categorias CA on AR.Categoria = CA.CategoriCod ";
    $sql .=" LEFT JOIN tipoproducto TP on AL.TipoProducto = TP.TipoProductoId ";
    $sql .=" WHERE MA.Estado = 'Matriculado' and MA.Cliente = '" . $UsuarioEntidad . "' ";
    $sql .=" AND (AL.TipoProducto = 'libro'  or AL.TipoProducto = 'revista') ";
    // echo $sql; exit;
    $consulta = Matris_Datos($sql, $cnOwl);
    $resultado = $consulta;
    $Persona = $_SESSION["Persona"]["string"];
    $urlEmpresa = $_SESSION["urlEmpresa"]["string"];
    while ($registroB = mysql_fetch_array($resultado)) {

        $sqlSA = " SELECT  Carpeta,UrlId  FROM usuarios  WHERE  IdUsuario ='" . $registroB[3] . "'  ";
        $Contador = $Contador + 1;
        $tituloPrograma = $registroB['Titulo'];

        $valor .= "<div style='padding:10px 10px 20px 10px;float:left;' >";
        $valor .= "<div class='contenedorProductos' >";

        $valor.="<a target='_blank'  paginaId='" . $registroB['Producto'] . "' href='http://archivos.owlgroup.org/$urlEmpresa/ebook/$Persona/" . $registroB['Producto'] . "'>";
        $valor .= "<div class='Panel_Curso_A_Principal_ebook' style='background-color:" . $registroB["Color"] . ";' >";
        $valor .= "<div CLASS='Panel_Curso_Sunperior'>";
        $valor .= "<div class='adorno_book'></div>";
        $valor .= "<div CLASS='Panel_Curso_Sunperior text_PC_001'>" . $tituloPrograma . "</div>";
        $valor .= "</div>";
        $valor .= "</div>";
        $valor .= "</a>";
        $valor .= "</div>";
        $valor .= "</div>";
    }
    return $valor;
}


function ValidarProgramasAprobados($ProgramaAlmacen,$user,$enterprise_user,$vConex){
    //WE($ProgramaAlmacen." ".$user." ".$enterprise_user);
    $sql = "SELECT PR.CodPrograma AS CodPrograma, A.NotaMinCertificacion AS NotaMinCertificacion, A.Entidad AS Entidad,
            PR.certificado_programa AS Cprograma, PR.certificado_cantidad AS Ccantidad
            FROM almacen A 
            INNER JOIN articulos AR ON A.Producto = AR.Producto 
            INNER JOIN programas AS PR ON PR.CodPrograma = AR.ProductoFab
            WHERE A.AlmacenCod = {$ProgramaAlmacen} ";

    $rg = fetch($sql);
    $NotaMinCertificacion = $rg["NotaMinCertificacion"];
    $enterprise_user = $rg["Entidad"];
    $Cprograma = $rg["Cprograma"];
    $Ccantidad = $rg["Ccantidad"];
    $CodPrograma = $rg["CodPrograma"];

    switch ($Cprograma) {
        case 'Ninguno':
            return false;
            break;
        case 'Apruebo':
            $sqlCurso = " SELECT ProductoCod
                FROM curricula
                WHERE CodProgAlmacen = $ProgramaAlmacen
                ORDER BY Orden ASC";
            $MxCurso = fetchAll($sqlCurso, $vConex);
            $paso = 0;

            foreach ($MxCurso as $Curso) {

                $CursoAlmacen = $Curso->ProductoCod;

                $Q_EE = "  SELECT US.CodigoParlante AS Usuario,
                    US.Usuario AS Email , CONCAT( ALUM.Nombres,' ', ALUM.ApellidosPat ) AS PARTICIPANTE ,
                    EEDC.Codigo AS Actividad ,ETRC.Estado AS EstadoActividad ,ETRC.Nota ,EEDC.AliasExtendido ,
                    EEDC.AliasAbreviado ,EECC.Codigo AS Concepto ,EECC.Peso ,RE.RecursoAcademico, RE.RecursoTipo,
                    RE.Codigo AS Recurso ,
                    (CASE WHEN ETRC.Nota < {$NotaMinCertificacion} THEN 'Desaprobado' ELSE 'Aprobado' END ) AS EstadoAprobatorio
                    FROM eltransrespuesta_cab AS ETRC
                    INNER JOIN elrecursoevaluacion AS RE ON RE.Codigo=ETRC.Recurso
                    INNER JOIN elevaluaciondetallecurso AS EEDC ON EEDC.Codigo=RE.EvaluacionDetalleCurso
                    INNER JOIN elevaluacionconfcurso AS EECC ON EECC.Codigo=EEDC.EvalConfigCurso
                    INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
                    INNER JOIN usuarios AS US ON US.IdUsuario = ETRC.Alumno
                    INNER JOIN matriculas as MAT on (ETRC.Alumno = MAT.Cliente AND MAT.Producto = {$ProgramaAlmacen} )
                    INNER JOIN usuario_entidad AS UE ON (REPLACE(ETRC.Alumno,'Alumno','') = UE.Usuario AND UE.EntidadCreadora = '{$enterprise_user}')
                    INNER JOIN alumnos AS ALUM ON ETRC.Alumno = ALUM.Usuario
                    WHERE EECC.Almacen={$CursoAlmacen}
                    AND EEDC.SCpt_Evaluado='SI'
                    AND MAT.Estado NOT IN('Anulado','Eliminado')
                    AND ETRC.Alumno <>''
                    AND EET.Codigo NOT IN(23,24,25,26,27)
                    AND ETRC.Alumno = '{$user}' ";

                $ListActividades = fetchAll($Q_EE);
                foreach ($ListActividades as $key) {
                    if ($key->EstadoActividad != 'Revisado') {
                        return false;
                    }
                }


                $SQLTotPeso = " SELECT CS1.Peso FROM (".$Q_EE .")  AS  CS1 GROUP BY  CS1.Concepto ";


                $SQLSumPeso = " SELECT SUM(CS1.Peso) AS TotPeso FROM (".$SQLTotPeso .")  AS  CS1 ";
                $rg = fetch($SQLSumPeso);
                $TotPeso = $rg["TotPeso"];
                if ($TotPeso == "") {
                    $TotPeso = 0;
                }

                $sqlResumenA = " SELECT '1'  AS ActividadMarcador  FROM (".$Q_EE.") AS CSL1 GROUP BY CSL1.Actividad ";

                $sqlResumen = " SELECT  SUM(CSL1.ActividadMarcador)  AS CtdadActividad FROM (".$sqlResumenA.") AS CSL1 ";
                $rg = fetch($sqlResumen);
                $CtdadActividad = $rg["CtdadActividad"];
                if ($CtdadActividad == "") {
                    $CtdadActividad = 0;
                }

                $sqlResumenA2D = " SELECT '1' AS Participante, SUM(CSL1.Nota) AS TotNota, CSL1.Email FROM (".$Q_EE.") AS CSL1 GROUP BY CSL1.Email ";


                $sqlResumenA2 = " SELECT SUM(CSL1.Participante)  AS CtdadParticipante FROM (".$sqlResumenA2D.") AS CSL1 ";
                $rg = fetch($sqlResumenA2);
                $CtdadParticipante = $rg["CtdadParticipante"];


                $agrupaTotAlumno = " SELECT (((CSL1.TotNota / ".$CtdadActividad.")* ".$TotPeso.")/100) AS NotaAlumno FROM (".$sqlResumenA2D.") AS CSL1 GROUP BY CSL1.Email ";


                $SQLTotAprobadosSum  = "  SELECT COUNT(*) AS TotAlumnos FROM (".$agrupaTotAlumno .") AS CS1 WHERE CS1.NotaAlumno >= ".$NotaMinCertificacion." ";




                $rg = fetch($SQLTotAprobadosSum);
                //WE($SQLTotAprobadosSum);
                $TotAlumnosAprobados = $rg["TotAlumnos"];

                $TotalAlumnoDesaprobados = $CtdadParticipante - $TotAlumnosAprobados;


                if($CtdadActividad == 0){
                    return false;
                }

                if($TotalAlumnoDesaprobados == 0){

                    $paso = 1;
                }else{
                    return false;
                }
            }

            if ($paso == 1) {
                return true;
            }else{
                return false;
            }
            break;
        case 'Cantidad':
            $sqlCurso = " SELECT ProductoCod
                FROM curricula
                WHERE CodProgAlmacen = $ProgramaAlmacen
                ORDER BY Orden ASC";
            $MxCurso = fetchAll($sqlCurso, $vConex);
            $paso = 0;
            $cursoAprobado = 0;
            foreach ($MxCurso as $Curso) {
            $estadoAct = true;

                $CursoAlmacen = $Curso->ProductoCod;

                $Q_EE = "  SELECT US.CodigoParlante AS Usuario,
                    US.Usuario AS Email , CONCAT( ALUM.Nombres,' ', ALUM.ApellidosPat ) AS PARTICIPANTE ,
                    EEDC.Codigo AS Actividad ,ETRC.Estado AS EstadoActividad ,ETRC.Nota ,EEDC.AliasExtendido ,
                    EEDC.AliasAbreviado ,EECC.Codigo AS Concepto ,EECC.Peso ,RE.RecursoAcademico, RE.RecursoTipo,
                    RE.Codigo AS Recurso ,
                    (CASE WHEN ETRC.Nota < {$NotaMinCertificacion} THEN 'Desaprobado' ELSE 'Aprobado' END ) AS EstadoAprobatorio
                    FROM eltransrespuesta_cab AS ETRC
                    INNER JOIN elrecursoevaluacion AS RE ON RE.Codigo=ETRC.Recurso
                    INNER JOIN elevaluaciondetallecurso AS EEDC ON EEDC.Codigo=RE.EvaluacionDetalleCurso
                    INNER JOIN elevaluacionconfcurso AS EECC ON EECC.Codigo=EEDC.EvalConfigCurso
                    INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
                    INNER JOIN usuarios AS US ON US.IdUsuario = ETRC.Alumno
                    INNER JOIN matriculas as MAT on (ETRC.Alumno = MAT.Cliente AND MAT.Producto = {$ProgramaAlmacen} )
                    INNER JOIN usuario_entidad AS UE ON (REPLACE(ETRC.Alumno,'Alumno','') = UE.Usuario AND UE.EntidadCreadora = '{$enterprise_user}')
                    INNER JOIN alumnos AS ALUM ON ETRC.Alumno = ALUM.Usuario
                    WHERE EECC.Almacen={$CursoAlmacen}
                    AND EEDC.SCpt_Evaluado='SI'
                    AND MAT.Estado NOT IN('Anulado','Eliminado')
                    AND ETRC.Alumno <>''
                    AND EET.Codigo NOT IN(23,24,25,26,27)
                    AND ETRC.Alumno = '{$user}' ";

                $ListActividades = fetchAll($Q_EE);
                foreach ($ListActividades as $key) {
                    if ($key->EstadoActividad != 'Revisado') {
                        $estadoAct = false;
                    }
                }


                $SQLTotPeso = " SELECT CS1.Peso FROM (".$Q_EE .")  AS  CS1 GROUP BY  CS1.Concepto ";


                $SQLSumPeso = " SELECT SUM(CS1.Peso) AS TotPeso FROM (".$SQLTotPeso .")  AS  CS1 ";
                $rg = fetch($SQLSumPeso);
                $TotPeso = $rg["TotPeso"];
                if ($TotPeso == "") {
                    $TotPeso = 0;
                }

                $sqlResumenA = " SELECT '1'  AS ActividadMarcador  FROM (".$Q_EE.") AS CSL1 GROUP BY CSL1.Actividad ";

                $sqlResumen = " SELECT  SUM(CSL1.ActividadMarcador)  AS CtdadActividad FROM (".$sqlResumenA.") AS CSL1 ";
                $rg = fetch($sqlResumen);
                $CtdadActividad = $rg["CtdadActividad"];
                if ($CtdadActividad == "") {
                    $CtdadActividad = 0;
                }

                $sqlResumenA2D = " SELECT '1' AS Participante, SUM(CSL1.Nota) AS TotNota, CSL1.Email FROM (".$Q_EE.") AS CSL1 GROUP BY CSL1.Email ";


                $sqlResumenA2 = " SELECT SUM(CSL1.Participante)  AS CtdadParticipante FROM (".$sqlResumenA2D.") AS CSL1 ";
                $rg = fetch($sqlResumenA2);
                $CtdadParticipante = $rg["CtdadParticipante"];


                $agrupaTotAlumno = " SELECT (((CSL1.TotNota / ".$CtdadActividad.")* ".$TotPeso.")/100) AS NotaAlumno FROM (".$sqlResumenA2D.") AS CSL1 GROUP BY CSL1.Email ";


                $SQLTotAprobadosSum  = "  SELECT COUNT(*) AS TotAlumnos FROM (".$agrupaTotAlumno .") AS CS1 WHERE CS1.NotaAlumno >= ".$NotaMinCertificacion." ";




                $rg = fetch($SQLTotAprobadosSum);
                //WE($SQLTotAprobadosSum);
                $TotAlumnosAprobados = $rg["TotAlumnos"];

                $TotalAlumnoDesaprobados = $CtdadParticipante - $TotAlumnosAprobados;


                if($CtdadActividad != 0){
                    if($TotalAlumnoDesaprobados == 0){
                        if ($estadoAct) {
                            $cursoAprobado = $cursoAprobado+1;
                        }
                    }
                }

                
            }

            if ($cursoAprobado >= $Ccantidad) {
                return true;
            }else{
                return false;
            }
            break;
        case 'Obligatorio':
            $sqlCurso = "Select A.AlmacenCod AS ProductoCod
                        FROM almacen A 
                        INNER JOIN articulos AR ON A.Producto = AR.Producto 
                        INNER JOIN cursos AS CU ON CU.CodCursos = AR.ProductoFab
                        INNER JOIN certificado_detalle AS CD ON CU.CodCursos = CD.cod_curso
                        WHERE CD.tipo = 'Programa'
                        AND CD.cod_programa ={$CodPrograma}";
            
            $MxCurso = fetchAll($sqlCurso, $vConex);
            $paso = 0;

            foreach ($MxCurso as $Curso) {

                $CursoAlmacen = $Curso->ProductoCod;

                $Q_EE = "  SELECT US.CodigoParlante AS Usuario,
                    US.Usuario AS Email , CONCAT( ALUM.Nombres,' ', ALUM.ApellidosPat ) AS PARTICIPANTE ,
                    EEDC.Codigo AS Actividad ,ETRC.Estado AS EstadoActividad ,ETRC.Nota ,EEDC.AliasExtendido ,
                    EEDC.AliasAbreviado ,EECC.Codigo AS Concepto ,EECC.Peso ,RE.RecursoAcademico, RE.RecursoTipo,
                    RE.Codigo AS Recurso ,
                    (CASE WHEN ETRC.Nota < {$NotaMinCertificacion} THEN 'Desaprobado' ELSE 'Aprobado' END ) AS EstadoAprobatorio
                    FROM eltransrespuesta_cab AS ETRC
                    INNER JOIN elrecursoevaluacion AS RE ON RE.Codigo=ETRC.Recurso
                    INNER JOIN elevaluaciondetallecurso AS EEDC ON EEDC.Codigo=RE.EvaluacionDetalleCurso
                    INNER JOIN elevaluacionconfcurso AS EECC ON EECC.Codigo=EEDC.EvalConfigCurso
                    INNER JOIN elevaluaciontipo AS EET ON EET.Codigo=EECC.EvaluacionTipo
                    INNER JOIN usuarios AS US ON US.IdUsuario = ETRC.Alumno
                    INNER JOIN matriculas as MAT on (ETRC.Alumno = MAT.Cliente AND MAT.Producto = {$ProgramaAlmacen} )
                    INNER JOIN usuario_entidad AS UE ON (REPLACE(ETRC.Alumno,'Alumno','') = UE.Usuario AND UE.EntidadCreadora = '{$enterprise_user}')
                    INNER JOIN alumnos AS ALUM ON ETRC.Alumno = ALUM.Usuario
                    WHERE EECC.Almacen={$CursoAlmacen}
                    AND EEDC.SCpt_Evaluado='SI'
                    AND MAT.Estado NOT IN('Anulado','Eliminado')
                    AND ETRC.Alumno <>''
                    AND EET.Codigo NOT IN(23,24,25,26,27)
                    AND ETRC.Alumno = '{$user}' ";

                $ListActividades = fetchAll($Q_EE);
                foreach ($ListActividades as $key) {
                    if ($key->EstadoActividad != 'Revisado') {
                        return false;
                    }
                }


                $SQLTotPeso = " SELECT CS1.Peso FROM (".$Q_EE .")  AS  CS1 GROUP BY  CS1.Concepto ";


                $SQLSumPeso = " SELECT SUM(CS1.Peso) AS TotPeso FROM (".$SQLTotPeso .")  AS  CS1 ";
                $rg = fetch($SQLSumPeso);
                $TotPeso = $rg["TotPeso"];
                if ($TotPeso == "") {
                    $TotPeso = 0;
                }

                $sqlResumenA = " SELECT '1'  AS ActividadMarcador  FROM (".$Q_EE.") AS CSL1 GROUP BY CSL1.Actividad ";

                $sqlResumen = " SELECT  SUM(CSL1.ActividadMarcador)  AS CtdadActividad FROM (".$sqlResumenA.") AS CSL1 ";
                $rg = fetch($sqlResumen);
                $CtdadActividad = $rg["CtdadActividad"];
                if ($CtdadActividad == "") {
                    $CtdadActividad = 0;
                }

                $sqlResumenA2D = " SELECT '1' AS Participante, SUM(CSL1.Nota) AS TotNota, CSL1.Email FROM (".$Q_EE.") AS CSL1 GROUP BY CSL1.Email ";


                $sqlResumenA2 = " SELECT SUM(CSL1.Participante)  AS CtdadParticipante FROM (".$sqlResumenA2D.") AS CSL1 ";
                $rg = fetch($sqlResumenA2);
                $CtdadParticipante = $rg["CtdadParticipante"];


                $agrupaTotAlumno = " SELECT (((CSL1.TotNota / ".$CtdadActividad.")* ".$TotPeso.")/100) AS NotaAlumno FROM (".$sqlResumenA2D.") AS CSL1 GROUP BY CSL1.Email ";


                $SQLTotAprobadosSum  = "  SELECT COUNT(*) AS TotAlumnos FROM (".$agrupaTotAlumno .") AS CS1 WHERE CS1.NotaAlumno >= ".$NotaMinCertificacion." ";




                $rg = fetch($SQLTotAprobadosSum);
                //WE($SQLTotAprobadosSum);
                $TotAlumnosAprobados = $rg["TotAlumnos"];

                $TotalAlumnoDesaprobados = $CtdadParticipante - $TotAlumnosAprobados;


                if($CtdadActividad == 0){
                    return false;
                }

                if($TotalAlumnoDesaprobados == 0){

                    $paso = 1;
                }else{
                    return false;
                }
            }

            if ($paso == 1) {
                return true;
            }else{
                return false;
            }
            break;
        default:
            return false;
            break;
    }

    
}
