<?php

function otorgarAccesos($CodigoAlmacen, $usuarioEntidad, $FechaHora, $codPrograma, $acceso,$participante,$vConex ){

		/* CAPTURAR CODCURRICULAS */
		$Q_Reporte = "SELECT C.CurriculaCod
		FROM curricula C
		LEFT JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod
		LEFT JOIN articulos AR ON AL.Producto  = AR.Producto
		LEFT JOIN categorias CA ON CA.CategoriCod  = AR.Categoria
		LEFT JOIN profesores P ON AL.Origen = P.Usuario
		LEFT JOIN sede_sucursal SC ON SC.Codigo=AL.Sede
		WHERE C.ProgramaCod = '{$codPrograma}'
		ORDER BY C.Orden";
		$curriculas = Matris_Datos($Q_Reporte, $vConex);
		$tempcurriculas = $curriculas;

		/* CAPTURAR MATRICULADOS */
		$Q_Matriculados = " SELECT MAT.IdMatricula, ALUM.Email
		FROM matriculas AS MAT
		INNER JOIN alumnos AS ALUM ON MAT.Cliente=ALUM.Usuario
		WHERE MAT.Producto={$CodigoAlmacen}
		AND MAT.CodAlmacenSN={$CodigoAlmacen}
		AND (MAT.Estado NOT IN('Anulado','Desactivado')) ";
		if($participante !== "EnLote"){
		     $Q_Matriculados .= " AND  MAT.Cliente = '".$participante."' ";		     
		}
		$Q_Matriculados .= " ORDER BY ALUM.ApellidosPat ";
		$matriculados = Matris_Datos($Q_Matriculados, $vConex);
		/* MODIFICAREMOS PERMISOS  A TODOS LOS MATRICULADOS */
		while ($row = mysql_fetch_assoc($matriculados)) {
			while ($row2 = mysql_fetch_assoc($curriculas)) {
			
				$sql = "SELECT COUNT(Codigo) filas from configuracion_accesos_curricula WHERE alumno='" . $row['Email'] . "Alumno' AND curricula=" . $row2['CurriculaCod'] . "";
				$rowcod = fetch($sql);
				if ($rowcod['filas'] == 0) {//INSERTAMOS NUEVOS PERMISOS SI EL MATRICULADO NO EXISTE EN TABLA  'configuracion_accesos_curricula'
					$userAcc = $row['Email'];
					$sql = "SELECT Perfil FROM usuario_entidad WHERE Usuario = '$userAcc'";
					$rgf = fetch($sql);
					$perfil = $rgf['Perfil'];
					if($perfil != 2){
						$edicion = 'EdicionTotal';
					}else{
						$edicion = 'EdicionTotal';
					}

					$insertpermisos = "INSERT INTO configuracion_accesos_curricula 
					(
					alumno
					,curricula
					,accesos
					,FechaHoraCreacion
					,UsuarioCreacion
					,FechaHoraActualizacion
					,UsuarioActualizacion
					,TareaAsignada
					,ControlEstadosContenido
					,FuncionProyecto
					)VALUES(
					'" . $row['Email'] . "Alumno'
					," . $row2['CurriculaCod'] . "
					," . $acceso . "
					,'" . $FechaHora . "'
					,'" . $usuarioEntidad . "'
					,'" . $FechaHora . "'
					,'" . $usuarioEntidad . "'
					,'" . $edicion . "'
					,'Total'
					,'Todo'
					)";
					xSQL($insertpermisos, $vConex);
					
					$Msj = '> Alumno: ' . $row['Email'] . ' ha sido registrado en  configuracion de accesos a la curricula de código ' . $row2['CurriculaCod'] . '<br/>';
					W("<div style=' background-color: #357492;  color: #fff;  font-size: 10px;    padding: 0.6em;border: #fff 1px solid;'>{$Msj}</div>");

					$SqlMatricula = "SELECT EstadoAcceso,AccionesSobrePrograma FROM matriculas WHERE IdMatricula=".$row['IdMatricula'];
					$RMatricula = fetch($SqlMatricula);
					if(!$RMatricula['AccionesSobrePrograma']){
						$AccionesSobrePrograma = ",AccionesSobrePrograma='Todos'";

					}
					$UpAlmacen = "UPDATE matriculas SET EstadoAcceso=1 {$AccionesSobrePrograma} WHERE IdMatricula=".$row['IdMatricula'];
					xSQL($UpAlmacen, $vConex);

				} else {
					$sqlalmacen = "UPDATE configuracion_accesos_curricula SET
						FechaHoraActualizacion='" . $FechaHora . "'
						,UsuarioActualizacion='" . $usuarioEntidad . "'
						,accesos= " . $acceso . "
						WHERE alumno='" . $row['Email'] . "Alumno' AND curricula=" . $row2['CurriculaCod'] . "";
					xSQL($sqlalmacen, $vConex);
				}
				
			}

			$Q_Reporte = "SELECT C.CurriculaCod
				FROM curricula C
				LEFT JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod
				LEFT JOIN articulos AR ON AL.Producto  = AR.Producto
				LEFT JOIN categorias CA ON CA.CategoriCod  = AR.Categoria
				LEFT JOIN profesores P ON AL.Origen = P.Usuario
				LEFT JOIN sede_sucursal SC ON SC.Codigo=AL.Sede
				WHERE C.ProgramaCod = '{$codPrograma}'
				ORDER BY C.Orden";
			$curriculas = Matris_Datos($Q_Reporte, $vConex);
		}
	
}

?>