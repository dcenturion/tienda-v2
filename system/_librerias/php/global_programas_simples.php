<?php

function curricula_sql($CodigoAlmacen,$Cod_Programa,$CodigoLDT,$enlace,$vConex){ 
    global $vConex, $enlace, $FechaHora, $usuarioEntidad, $entidadCreadora, $idEmpresa, $UnidadNegocio_ID, $Escuela_ID, $Codigo_Entidad_Usuario, $cnOwlb, $profile_user, $PersonaUsuario,$Perfi_Entidad_Usuario;

	     $CoodinadorAux =  CoodinadorAux($Cod_Programa);

		$sql = "SELECT  control,cerrar_curso  FROM  almacen  WHERE  AlmacenCod = {$CodigoAlmacen}";
		$rg = fetch($sql);
		$control = $rg["control"];
		$EstadoPrograma = $rg["cerrar_curso"];

		$enlaceA = $enlace . "?ProgramaSimple=EditarCurricula&Codigo={$Cod_Programa}&CodigoAlmacen=" . $CodigoAlmacen . "&CodigoLDT=" . $CodigoLDT . "";
		$enlaceC ="./_vistas/gad_cursos_simples.php?Lista=Contenido&ProgramaAlmacen={$CodigoAlmacen}&CodigoLDTPrograma={$CodigoLDT}";
		$enlaceD ="./_vistas/gad_cursos_actividades.php?ProcesoActividades=ListarActividades&ProgramaAlmacen={$CodigoAlmacen}";
		$enlaceELI= $enlace . "?ProgramaSimple=Confirmar_Eliminar_Curso&Cod_Curricula={$Cod_Curricula}&Codigo={$Cod_Programa}&ProgramaAlmacen=" . $CodigoAlmacen . "&CodigoLDT=" . $CodigoLDT . "";


		if($control == 1){

			$sql = "SELECT  CONCAT('',SSC.Descripcion,'-', SC.Nombre ,'') AS Nombre_Sede,SC.Codigo AS Cod_Sede_Sucursal
			FROM curricula C 
			INNER JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod 
			LEFT JOIN sede_sucursal SC ON SC.Codigo=AL.Sede 
			LEFT JOIN sedes AS SSC ON SC.UnidadNegocio = SSC.Codigo 
			WHERE C.ProgramaCod = '{$Cod_Programa}' 
			GROUP BY Nombre_Sede
			ORDER BY C.Orden ASC";

			$MxSedes = fetchAll($sql, $vConex);
			
			if($MxSedes){

				foreach ($MxSedes as $Sedes){

					$Cod_Sede_Sucursal=$Sedes->Cod_Sede_Sucursal;
					$Nombre_Sede=$Sedes->Nombre_Sede;			

					$reporte .= '<div style="float:left;width:97%;background:#e2e2e2;padding: 10px 0px 10px 0px;font-size: 0.7em;" >';

					if($Cod_Sede_Sucursal){

						$reporte .=	'<div id="nombrepro" style="padding-left: 10px;">SEDE '.$Nombre_Sede.'</div>';

						$Q_Reporte = "SELECT
						CONCAT('<div >
						<div class=Circulo_Reporte > ',C.Orden,'</div>
						<div > ',AR.Titulo,'</div>
						</div>')  AS 'Nombre del Curso',
						CONCAT('<div>
						<div ><b> ',P.Nombres ,'  ', P.ApellidosPat,'</b></div>
						<div > ',CA.Descripcion,'</div>
						<div > ',C.FechReg,'</div>
						</div>')  AS 'Detalles',
						(CASE TipoProceso
						WHEN 1 THEN CONCAT('Reingreso : ',AL.Ingreso)
						WHEN 2 THEN CONCAT('Duplicado : ',AL.Ingreso)
						ELSE 'Original'
						END ) AS 'Tipo Origen',

						C.CurriculaCod AS CodigoAjax,
						CONCAT('
						<div style=position:relative;width:80px>
						<div class=Btn-reporte onclick=enviaReg('''',''$enlaceA&Cod_Sede_Sucursal=$Cod_Sede_Sucursal&Cod_Curricula=',C.CurriculaCod,''',''Cont_',C.CurriculaCod,''',''''); ><div class=botIcRep><i class=icon-pencil ></i>&nbsp;Editar</div></div>
						</div>
						') AS 'Curricula',
						CONCAT('
						<div style=position:relative;width:80px>
						<div class=Btn-reporte onclick=enviaReg('''',''$enlaceD&Codigo=',AR.ProductoFab,'&CodigoAlmacen=',C.ProductoCod,''',''panelB-R'',''''); ><div class=botIcRep><i class=icon-bar-chart ></i>&nbsp;Crear</div></div>
						</div>
						') AS 'Actividad',
						CONCAT('
						<div style=position:relative;width:80px>

						<div class=Btn-reporte  onclick=enviaReg('''',''$enlaceC&Cod_Sede_Sucursal=$Cod_Sede_Sucursal&Codigo=',AR.ProductoFab,'&CodigoAlmacen=',C.ProductoCod,'&TipoPrograma=gad_programas_simples&Cod_Curricula=',C.CurriculaCod,''',''panelB-R'',''''); ><div class=botIcRep><i class=icon-pencil ></i>&nbsp;Administrar</div></div>

						</div>
						')  AS 'Curso',
						CONCAT('
						<div style=position:relative;width:50px>
						<div class=Btn-reporte  onclick=enviaReg('''',''$enlaceELI&Cod_Curricula=',C.CurriculaCod,'&PCod_Curricula=',C.ProductoCod,'&CodigoAlmacen=',C.ProductoCod,''',''Cont_',C.CurriculaCod,''',''''); ><div class=botIcRep><i class=icon-trash></i></div></div>

						</div>
						<tr >
						<td colspan=9><div id=Cont_',C.CurriculaCod,' ></div></td> </tr>
						')  AS 'Eliminar'

						FROM curricula C
						INNER JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod
						INNER JOIN articulos AR ON AL.Producto  = AR.Producto
						INNER JOIN categorias CA ON CA.CategoriCod  = AR.Categoria
						INNER JOIN profesores P ON AL.Origen = P.Usuario 
						LEFT JOIN sede_sucursal SC ON SC.Codigo=AL.Sede
						LEFT JOIN sedes AS SSC ON SC.UnidadNegocio = SSC.Codigo
						WHERE C.ProgramaCod = '{$Cod_Programa}'
						AND SC.Codigo = '{$Cod_Sede_Sucursal}'
						ORDER BY C.Orden ASC ";	

					}else{
						
						$reporte .=	'<div id="nombrepro" style="padding-left: 10px;">SIN SEDES</div>';

						$Q_Reporte = "
						SELECT Sede.Primero AS 'Nombre del Curso',Sede.Detalles, Sede.Tipo AS 'Tipo Origen',
						Sede.Curricula ,Sede.Curso, Sede.Eliminar FROM (
						SELECT  IF(AL.Sede NOT LIKE '',AL.Sede,0) AS CodSede ,
						CONCAT('<div >
						<div class=Circulo_Reporte > ',C.Orden,'</div>
						<div > ',AR.Titulo,'</div>
						</div>')  AS 'Primero',
						CONCAT('<div>
						<div ><b> ',P.Nombres ,'  ', P.ApellidosPat,'</b></div>
						<div > ',CA.Descripcion,'</div>
						<div > ',C.FechReg,'</div>
						</div>')  AS 'Detalles',
						(CASE TipoProceso
						WHEN 1 THEN CONCAT('Reingreso : ',AL.Ingreso)
						WHEN 2 THEN CONCAT('Duplicado : ',AL.Ingreso)
						ELSE 'Original'
						END ) AS 'Tipo',

						C.CurriculaCod AS CodigoAjax,
						CONCAT('
						<div style=position:relative;width:80px>
						<div class=Btn-reporte onclick=enviaReg('''',''$enlaceA&Cod_Curricula=',C.CurriculaCod,''',''Cont_',C.CurriculaCod,''',''''); ><div class=botIcRep><i class=icon-pencil ></i>&nbsp;Editar</div></div>
						</div>
						') AS 'Curricula',
						CONCAT('
						<div style=position:relative;width:80px>
						<div class=Btn-reporte onclick=enviaReg('''',''$enlaceD&Codigo=',AR.ProductoFab,'&CodigoAlmacen=',C.ProductoCod,''',''panelB-R'',''''); ><div class=botIcRep><i class=icon-bar-chart ></i>&nbsp;Crear</div></div>
						</div>
						') AS 'Actividad',
						CONCAT('
						<div style=position:relative;width:80px>

						<div class=Btn-reporte  onclick=enviaReg('''',''$enlaceC&Codigo=',AR.ProductoFab,'&CodigoAlmacen=',C.ProductoCod,'&TipoPrograma=gad_programas_simples&Cod_Curricula=',C.CurriculaCod,''',''panelB-R'',''''); ><div class=botIcRep><i class=icon-pencil ></i>&nbsp;Administrar</div></div>

						</div>
						')  AS 'Curso',
						CONCAT('
						<div style=position:relative;width:50px>
						<div class=Btn-reporte  onclick=enviaReg('''',''$enlaceELI&Cod_Curricula=',C.CurriculaCod,'&PCod_Curricula=',C.ProductoCod,'&CodigoAlmacen=',C.ProductoCod,''',''Cont_',C.CurriculaCod,''',''''); ><div class=botIcRep><i class=icon-trash></i></div></div>

						</div>
						<tr >
						<td colspan=9><div id=Cont_',C.CurriculaCod,' ></div></td> </tr>
						')  AS 'Eliminar'

						FROM curricula C
						INNER JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod
						INNER JOIN articulos AR ON AL.Producto  = AR.Producto
						INNER JOIN categorias CA ON CA.CategoriCod  = AR.Categoria
						INNER JOIN profesores P ON AL.Origen = P.Usuario 
						LEFT JOIN sede_sucursal SC ON SC.Codigo=AL.Sede
						LEFT JOIN sedes AS SSC ON SC.UnidadNegocio = SSC.Codigo
						WHERE C.ProgramaCod = '{$Cod_Programa}'
						ORDER BY C.Orden ASC) Sede
						WHERE Sede.CodSede = 0";

						

					}

					$reporte .= '<div class="linea" style="float:left;"></div></div>';
					$clase = 'reporteB';
					$enlaceCod ="";
					$url = "";
					$panel ="";
		            $reporte .= ListR2("", $Q_Reporte, $vConex, $clase, '', '', '', '', '', '');
				}
			}else{
					$reporte .=	'<div id="nombrepro" style="padding-left: 10px;">SIN SEDES</div>';

						$Q_Reporte = "
						SELECT Sede.Primero AS 'Nombre del Curso',Sede.Detalles, Sede.Tipo AS 'Tipo Origen',
						Sede.Curricula ,Sede.Curso, Sede.Eliminar FROM (
						SELECT  IF(AL.Sede NOT LIKE '',AL.Sede,0) AS CodSede ,
						CONCAT('<div >
						<div class=Circulo_Reporte > ',C.Orden,'</div>
						<div > ',AR.Titulo,'</div>
						</div>')  AS 'Primero',
						CONCAT('<div>
						<div ><b> ',P.Nombres ,'  ', P.ApellidosPat,'</b></div>
						<div > ',CA.Descripcion,'</div>
						<div > ',C.FechReg,'</div>
						</div>')  AS 'Detalles',
						(CASE TipoProceso
						WHEN 1 THEN CONCAT('Reingreso : ',AL.Ingreso)
						WHEN 2 THEN CONCAT('Duplicado : ',AL.Ingreso)
						ELSE 'Original'
						END ) AS 'Tipo',

						C.CurriculaCod AS CodigoAjax,
						CONCAT('
						<div style=position:relative;width:80px>
						<div class=Btn-reporte onclick=enviaReg('''',''$enlaceA&Cod_Curricula=',C.CurriculaCod,''',''Cont_',C.CurriculaCod,''',''''); ><div class=botIcRep><i class=icon-pencil ></i>&nbsp;Editar</div></div>
						</div>
						') AS 'Curricula',
						CONCAT('
						<div style=position:relative;width:80px>
						<div class=Btn-reporte onclick=enviaReg('''',''$enlaceD&Codigo=',AR.ProductoFab,'&CodigoAlmacen=',C.ProductoCod,''',''panelB-R'',''''); ><div class=botIcRep><i class=icon-bar-chart ></i>&nbsp;Crear</div></div>
						</div>
						') AS 'Actividad',
						CONCAT('
						<div style=position:relative;width:80px>

						<div class=Btn-reporte  onclick=enviaReg('''',''$enlaceC&Codigo=',AR.ProductoFab,'&CodigoAlmacen=',C.ProductoCod,'&TipoPrograma=gad_programas_simples&Cod_Curricula=',C.CurriculaCod,''',''panelB-R'',''''); ><div class=botIcRep><i class=icon-pencil ></i>&nbsp;Administrar</div></div>

						</div>
						')  AS 'Curso',
						CONCAT('
						<div style=position:relative;width:50px>
						<div class=Btn-reporte  onclick=enviaReg('''',''$enlaceELI&Cod_Curricula=',C.CurriculaCod,'&PCod_Curricula=',C.ProductoCod,'&CodigoAlmacen=',C.ProductoCod,''',''Cont_',C.CurriculaCod,''',''''); ><div class=botIcRep><i class=icon-trash></i></div></div>

						</div>
						<tr >
						<td colspan=9><div id=Cont_',C.CurriculaCod,' ></div></td> </tr>
						')  AS 'Eliminar'

						FROM curricula C
						INNER JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod
						INNER JOIN articulos AR ON AL.Producto  = AR.Producto
						INNER JOIN categorias CA ON CA.CategoriCod  = AR.Categoria
						INNER JOIN profesores P ON AL.Origen = P.Usuario 
						LEFT JOIN sede_sucursal SC ON SC.Codigo=AL.Sede
						LEFT JOIN sedes AS SSC ON SC.UnidadNegocio = SSC.Codigo
						WHERE C.ProgramaCod = '{$Cod_Programa}'
						ORDER BY C.Orden ASC) Sede
						WHERE Sede.CodSede = 0";

					$reporte .= '<div class="linea" style="float:left;"></div></div>';
					$clase = 'reporteB';
					$enlaceCod ="";
					$url = "";
					$panel ="";
		            $reporte .= ListR2("", $Q_Reporte, $vConex, $clase, '', '', '', '', '', '');
				
			}

		}else{
	
		 
			if($CoodinadorAux['ProcesoAsignado'] == 'Todos'  ||  $CoodinadorAux['ProcesoAsignado'] == 'Contenidos'   ||  $CoodinadorAux['ProcesoAsignado'] == 'Actividades'    ){
				if($EstadoPrograma != 'cerrar'){
				$mostrar =" ,CONCAT('
							<div style=position:relative;width:80px>
							<div class=Btn-reporte onclick=enviaReg('''',''$enlaceA&Cod_Curricula=',C.CurriculaCod,''',''Cont_',C.CurriculaCod,''',''''); ><div class=botIcRep><i class=icon-pencil ></i>&nbsp;Editar</div></div>
							</div>
							') AS 'Curricula' ";
				}							
				if($CoodinadorAux['ProcesoAsignado'] == 'Todos'  ||  $CoodinadorAux['ProcesoAsignado'] == 'Actividades'  ){				
					if($EstadoPrograma != 'cerrar'){
					$mostrar .=" ,CONCAT('
								<div style=position:relative;width:80px>
								<div class=Btn-reporte onclick=enviaReg('''',''$enlaceD&Codigo=',AR.ProductoFab,'&CodigoAlmacen=',C.ProductoCod,''',''panelB-R'',''''); ><div class=botIcRep><i class=icon-bar-chart ></i>&nbsp;Crear</div></div>
								</div>
								') AS 'Actividad' ";
					}								
				}	
				
				if($CoodinadorAux['ProcesoAsignado'] == 'Todos'  ||  $CoodinadorAux['ProcesoAsignado'] == 'Contenidos'  ){					
				    $mostrar .=",CONCAT(' 
							<div style=position:relative;width:80px>

							<div style=position:absolute;top:-15px; class=Btn-reporte  onclick=enviaReg('''',''$enlaceC&Codigo=',AR.ProductoFab,'&CodigoAlmacen=',C.ProductoCod,'&TipoPrograma=gad_programas_simples&Cod_Curricula=',C.CurriculaCod,''',''panelB-R'',''''); ><div class=botIcRep><i class=icon-pencil ></i>&nbsp;Administrar</div></div>

							</div>
							')  AS 'Curso' ";
				}		
				
				if($EstadoPrograma != 'cerrar'){				
				$mostrar .=",CONCAT('
							<div style=position:relative;width:50px>
							<div class=Btn-reporte  onclick=enviaReg('''',''$enlaceELI&Cod_Curricula=',C.CurriculaCod,'&PCod_Curricula=',C.ProductoCod,'&CodigoAlmacen=',C.ProductoCod,''',''Cont_',C.CurriculaCod,''',''''); ><div class=botIcRep><i class=icon-trash></i></div></div>

							</div>
							<tr >
							<td colspan=9><div id=Cont_',C.CurriculaCod,' ></div></td> </tr>
							')  AS 'Eliminar'";	
				}
			}
			
			if($Perfi_Entidad_Usuario == PERFIL_SOCIO){
				$mostrar .=",CONCAT(' 
							<div style=position:relative;>

							<div style=position:absolute;top:-15px; class=Btn-reporte  onclick=enviaReg('''',''$enlaceC&Codigo=',AR.ProductoFab,'&CodigoAlmacen=',C.ProductoCod,'&TipoPrograma=gad_programas_simples&Cod_Curricula=',C.CurriculaCod,''',''panelB-R'',''''); ><div class=botIcRep><i class=icon-pencil ></i>&nbsp;Administrar</div></div>

							</div>
							')  AS 'Curso' ";				
			}

			$Q_Reporte = "SELECT
			CONCAT('<div >
			<div class=Circulo_Reporte > ',C.Orden,'</div>
			<div > ',AR.Titulo,'</div>
			</div>')  AS 'Nombre del Curso',
			CONCAT('<div>
			<div ><b> ',P.Nombres ,'  ', P.ApellidosPat,'</b></div>
			<div > ',CA.Descripcion,'</div>
			<div > ',C.FechReg,'</div>
			</div>')  AS 'Detalles',
			C.CurriculaCod AS CodigoAjax
			{$mostrar}
			FROM curricula C
			INNER JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod
			INNER JOIN articulos AR ON AL.Producto  = AR.Producto
			INNER JOIN categorias CA ON CA.CategoriCod  = AR.Categoria
			INNER JOIN profesores P ON AL.Origen = P.Usuario 
			LEFT JOIN sede_sucursal SC ON SC.Codigo=AL.Sede
			LEFT JOIN sedes AS SSC ON SC.UnidadNegocio = SSC.Codigo
			WHERE C.ProgramaCod = '{$Cod_Programa}' 
			ORDER BY C.Orden ASC";
            $clase = 'reporteB';

            $enlaceCod ="";
            $url = "";
            $panel =""; 
            $reporte = ListR2("", $Q_Reporte, $vConex, $clase, '', '', '', '', '', '');
          
	    }
		
    $data = array('reporte' => $reporte, 'sql' => $Q_Reporte);
    return $data;				
}

function CoodinadorAux($Cod_Programa){
    global $Codigo_Entidad_Usuario;

    $sql = "SELECT  Codigo FROM lista_trabajo_det  WHERE CodigoProducto={$Cod_Programa} AND TipoProducto LIKE 'Programa%' ";
    $rg = fetch($sql);
    $CodigoLDT = $rg["Codigo"];

    $Q_Aux = " SELECT Codigo, ProcesoAsignado, Alcance,MaximoMat,ListaNegra,Duplicar,TipoAccesos FROM lista_trabajo_det_coordinacion
                       WHERE Coordinador = '{$Codigo_Entidad_Usuario}' AND lista_trabajo_det = '{$CodigoLDT}' AND Estado = 'Activo'";
    $rgAux = fetch($Q_Aux);
    $output['Codigo']          = $rgAux["Codigo"];
    $output['ProcesoAsignado'] = $rgAux["ProcesoAsignado"];
    $output['Alcance']         = $rgAux["Alcance"];
    $output['MaximoMat']       = $rgAux["MaximoMat"];
    $output['ListaNegra']       = $rgAux["ListaNegra"];
    $output['Duplicar']       = $rgAux["Duplicar"];
    $output['TipoAccesos']       = $rgAux["TipoAccesos"];
    return $output;
}

function CoodinadorAuxMat($CodigoAlmacen){
	global $Codigo_Entidad_Usuario, $entidadCreadora;
	
	$SQL=" SELECT Producto FROM almacen  WHERE AlmacenCod={$CodigoAlmacen} ";
	$rg2=fetch($SQL);
	$Cod_Programa = substr($rg2[Producto],4);
	$CoodinadorAux =  CoodinadorAux($Cod_Programa);
	
	if($CoodinadorAux['Alcance'] == 'Auxiliar'){	
		if($CoodinadorAux['MaximoMat'] > 0){		
			$MaxMat = $CoodinadorAux['MaximoMat'];
			$Inscritos="SELECT COUNT(*) AS matricula 
			FROM (SELECT 1 
			FROM matriculas MAT
			INNER JOIN ( SELECT U.Codigo, CONCAT (US.Nombres,' ',US.Apellidos) AS Nombres 
			FROM usuario_entidad AS U INNER JOIN usuarios AS US ON U.Usuario = US.Usuario 
			WHERE U.EntidadCreadora = '{$entidadCreadora}' ) AS USU ON MAT.UsuarioCreacion = USU.Codigo AND USU.Codigo= {$Codigo_Entidad_Usuario}
			WHERE Producto='$CodigoAlmacen'
			AND MAT.Estado NOT IN('Anulado','Eliminado') 
			GROUP BY MAT.Cliente) tab";
			$ftchMat=fetchOne($Inscritos);
			$TotalMat=$ftchMat->matricula;			
			if($MaxMat > $TotalMat){
				$output['Estado']      = 'Si';
				$output['Disponible']  = $MaxMat - $TotalMat;# 1,2,3...					
				return $output;
			}else{
				$output['Estado'] = 'No';			
				return $output;
			}
		}		
	}	
	$output['Estado'] = 'Si';
	return $output;
}

function ProgramaCerrado($ProgramaAlmacen,$AlmacenCod){
    global $vConex,$entidadCreadora;
		
		$Q = "SELECT cerrar_curso FROM almacen  WHERE AlmacenCod = {$AlmacenCod} ";
		$Curso= fetchOne($Q, $vConex);
		$EstadoCurso = $Curso->cerrar_curso;
		
		if($EstadoCurso != 'cerrar'){
			
			$Q_P = "SELECT AL.NotaMinCertificacion FROM almacen AL WHERE AL.AlmacenCod='{$ProgramaAlmacen}' ";
			$Programa = fetchOne($Q_P, $vConex);
			$NotaMinCertificacion = $Programa->NotaMinCertificacion;

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
			INNER JOIN usuario_entidad AS UE ON (REPLACE(ETRC.Alumno,'Alumno','') = UE.Usuario AND UE.EntidadCreadora = '{$entidadCreadora}')
			INNER JOIN alumnos AS ALUM ON ETRC.Alumno = ALUM.Usuario
			WHERE EECC.Almacen={$AlmacenCod}
			AND EEDC.SCpt_Evaluado='SI'
			AND MAT.Estado NOT IN('Anulado','Eliminado')
			AND ETRC.Alumno <>''
			AND EET.Codigo NOT IN(23,24,25,26,27) ";

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
			$agrupaTotAlumno = " SELECT ROUND((((CSL1.TotNota / ".$CtdadActividad.")* ".$TotPeso.")/100),2) AS NotaAlumno,CSL1.Email   FROM (".$sqlResumenA2D.") AS CSL1 GROUP BY CSL1.Email ";
				$MxTotAlumno = fetchAll($agrupaTotAlumno, $vConex);
				foreach ($MxTotAlumno as $TotAlumno) {
					$Alumno  = $TotAlumno->Email;
					$NotaAlumno = $TotAlumno->NotaAlumno;						
					
					$sql = " SELECT MAT.FechaInscripcion 
					FROM matriculas AS MAT
					INNER JOIN usuario_entidad  AS UE ON (REPLACE(MAT.Cliente,'Alumno','') = UE.Usuario AND UE.EntidadCreadora = '$entidadCreadora')				
					WHERE MAT.Producto = $ProgramaAlmacen
					AND MAT.CodAlmacenSN = $ProgramaAlmacen
					AND UE.Usuario = '$Alumno' ";
					$rg = fetch($sql);				
					$FechaInscripcion = $rg["FechaInscripcion"];
					
					$sql = " SELECT * FROM programa_cerrado 
					WHERE Programa = $ProgramaAlmacen 
					AND Curso = $AlmacenCod 
					AND Alumno = '$Alumno' ";
					$rg = fetch($sql);						
					if(!$rg){
						$data = array(
										'Programa' => $ProgramaAlmacen,
										'Curso' => $AlmacenCod,
										'Alumno' => $Alumno,
										'Nota' => $NotaAlumno,
										'EntidadCreadora' => $entidadCreadora,
										'FechaMatricula' => $FechaInscripcion,
											);
						insert("programa_cerrado", $data);
					}
				}
				
			$vSQL = "UPDATE almacen SET cerrar_curso = 'cerrar' WHERE AlmacenCod = {$AlmacenCod} ";
			xSQL($vSQL, $vConex);	
		}
}