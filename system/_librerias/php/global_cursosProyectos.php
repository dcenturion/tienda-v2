<?php
session_start();
$urlEmpresa = isset($_SESSION['urlEmpresa']['string']) ? $_SESSION['urlEmpresa']['string'] : '';
		

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
function CursosPrograma($ProgramaAlmacen, $vConex, $Sede, $acceso, $alumno, $order,$SedeUsuario) {
 
 
    $Q_P = "SELECT 
    AR.ProductoFab,
    AR.Titulo,
    AL.AlmacenCod,
    AL.NivelMatricula,
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
    AL.DiaFinal
    FROM almacen AL 
    INNER JOIN articulos AR ON AL.Producto = AR.Producto  
    INNER JOIN categorias CAT on AR.Categoria = CAT.CategoriCod
    INNER JOIN programas PR ON AR.ProductoFab = PR.CodPrograma  
    INNER JOIN tipoprograma TP ON PR.TipoPrograma=TP.IDTipoPrograma
    WHERE AL.AlmacenCod='{$ProgramaAlmacen}' ";
    $Programa = fetchOne($Q_P, $vConex);

	$privilegiosParticipante = "
	SELECT  AccionesSobrePrograma  FROM matriculas
	WHERE Producto = '{$ProgramaAlmacen}'   AND  Cliente = '{$alumno}' ";
    $reg = fetchOne($privilegiosParticipante, $vConex);
    $AccionesSobrePrograma = $reg->AccionesSobrePrograma;
	
	
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
   ///// arreglo jSON proyecto
        $JSONdataCourses[] = array(
            "title"             => $CU->TituloCurso.$Sede,
            "description"       => $CU->TituloCurso,
            "orden"             => $CU->Orden,
            "color"             => $CU->Color,
            "AlmacenCurso"      => $CU->ProductoCod,
            "ImgCurso"          => $ImagenCurso,
            "url_redirect"      => "/system/roomProyectos.php?AlmacenCurso={$CU->ProductoCod}&AlmacenPrograma={$ProgramaAlmacen}&Access=y",
            "num_activities"    => $numActivities
        );
    }
    
    $titulo_programaA= $Programa->titulo_programa;
	
    $image_url = "/system/_imagenes/program_wall.jpg";
    
    if(trim($Programa->ImagenUrl)){
        $image_url = CONS_IPArchivos . "/articulos/Programa-{$Programa->CodPrograma}/{$Programa->ImagenUrl}";
    }
    
    $key = "program-category-{$Programa->categoryId}";
    $JSONdata = [];
    
    $JSONdata[$key]["items"][] = array(
        "almacenId" => $Programa->AlmacenCod,
        "title" => $titulo_programaA,
        "subtitle" => $Programa->CategoryTitle,
        "image_url" => $image_url,
        "description" => $Programa->Titulo,
        "color" => $Programa->Color,
        "title_opt" => "Cursos",
        "options" => $JSONdataCourses
    );
	
	if($AccionesSobrePrograma == "CrearComponentes" || $AccionesSobrePrograma == "Todos"){
	    $urlAddCurso =  "/system/_vistas/gad_proyecto_participante.php?CursoSimple=Crear&AlmacenPrograma={$ProgramaAlmacen}";
	}else{
	    $urlAddCurso =  "/system/_vistas/gad_proyecto_participante.php?CursoSimple=No&AlmacenPrograma={$ProgramaAlmacen}";	
	}
    
    $JSONdata2[] = array(
        "almacenId" => $Programa->AlmacenCod,
        "title" => $titulo_programaA,
        "urlAddCurso" => $urlAddCurso,
        "subtitle" => $Programa->CategoryTitle,
        "image_url" => $image_url,
        "description" => $Programa->Titulo,
        "color" => $Programa->Color,
        "title_opt" => "Cursos",
        "tipo"=>$Programa->TipoPrograma,
        "options" => $JSONdataCourses,
        "order" => $order
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
function MyPrograms3($UsuarioEntidad, $vConex) {
global $urlEmpresa  ;

	// return $urlEmpresa;
	$sqlUsuario="SELECT IdUsuario FROM usuarios WHERE UrlId='{$urlEmpresa}'  ";
	$rg2 = fetch($sqlUsuario);
	$IdUsuario= $rg2["IdUsuario"];

	$SqlTP= "  SELECT  TipoProyecto  FROM empresa  WHERE  PaginaWeb='{$IdUsuario}'   ";
	$rg2 = fetch($SqlTP);
	$TipoProyecto= $rg2["TipoProyecto"];
	
	$arguments = func_get_args();
	$WherePrograma = "";

    		switch (sizeof($arguments)) {
 	
    			case 3:
 	
    			$WherePrograma = "AND AL.AlmacenCod='".$arguments[2]."'";
 	
    				break;
 	
    			default:
 	
    				break;
 
    		}
		
			//WE($urlEmpresa);
			
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
     LEFT JOIN programaespecialdet PED ON PED.AlmacenCod = AL.AlmacenCod
     WHERE MA.Estado = 'Matriculado' 
     AND MA.Cliente = '{$UsuarioEntidad}' 
     AND AL.TipoProducto LIKE 'programa%' 
    AND AL.TipoGestionProyecto={$TipoProyecto}
   $WherePrograma 
    order by AL.TipoProducto, PED.orden asc";
	     // return $Q_PRG;
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
            "url_redirect" => "/system/roomProyectos.php?AlmacenCurso={$CU->ProductoCod}&AlmacenPrograma={$CodigoAlmacenPrograma}&Access=y"
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
