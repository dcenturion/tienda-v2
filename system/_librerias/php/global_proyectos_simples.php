<?php

function curricula_sql($CodigoAlmacen,$Cod_Programa,$CodigoLDT,$enlace,$vConex){ 
    global $vConex, $enlace, $FechaHora, $usuarioEntidad, $entidadCreadora, $idEmpresa, $UnidadNegocio_ID, $Escuela_ID, $Codigo_Entidad_Usuario, $cnOwlb, $profile_user, $PersonaUsuario;

	$CoodinadorAux =  CoodinadorAux($Cod_Programa);
	    
		 $SqlT1= "  SELECT  TipoProyecto  FROM empresa  WHERE  PaginaWeb='{$idEmpresa}'   ";
		$rg2 = fetch($SqlT1);
		$TipoProyecto= $rg2["TipoProyecto"];
		
		 $SqlT2= "  SELECT  TituloEncabezado1, TituloEncabezado2 FROM tipo_proyecto  WHERE  Codigo='{$TipoProyecto}'   ";
		$rg3 = fetch($SqlT2);
		$TituloEncabezado1= $rg3["TituloEncabezado1"];
		$TituloEncabezado2= $rg3["TituloEncabezado2"];

		$sql = "SELECT  control  FROM  almacen  WHERE  AlmacenCod = {$CodigoAlmacen}";
		$rg = fetch($sql);
		$control = $rg["control"];

		$enlaceA = $enlace . "?ProgramaSimple=EditarCurricula&Codigo={$Cod_Programa}&CodigoAlmacen=" . $CodigoAlmacen . "&CodigoLDT=" . $CodigoLDT . "";
		$enlaceC ="./_vistas/gad_proyectodos_simples.php?Lista=Contenido&ProgramaAlmacen={$CodigoAlmacen}&CodigoLDTPrograma={$CodigoLDT}";
		$enlaceD ="./_vistas/gad_proyectos_actividades.php?ProcesoActividades=ListarActividades&ProgramaAlmacen={$CodigoAlmacen}";
		$enlaceELI= $enlace . "?ProgramaSimple=Confirmar_Eliminar_Curso&Cod_Curricula={$Cod_Curricula}&Codigo={$Cod_Programa}&ProgramaAlmacen=" . $CodigoAlmacen . "&CodigoLDT=" . $CodigoLDT . "";


		$mostrar =" ,CONCAT('
							<div style=position:relative;>
							<div class=Btn-reporte onclick=enviaReg('''',''$enlaceA&Cod_Curricula=',C.CurriculaCod,''',''Cont_',C.CurriculaCod,''',''''); ><div class=botIcRep><i class=icon-pencil ></i>&nbsp;Editar</div></div>
							</div>
							') AS 'Listado',/*
							CONCAT('
							<div style=position:relative;>
							<div class=Btn-reporte onclick=enviaReg('''',''$enlaceD&Codigo=',AR.ProductoFab,'&CodigoAlmacen=',C.ProductoCod,''',''panelB-R'',''''); ><div class=botIcRep><i class=icon-bar-chart ></i>Administrar</div></div>
							</div>
							') AS 'Actividad',*/
							CONCAT('
							<div style=position:relative;>

							<div class=Btn-reporte  onclick=enviaReg('''',''$enlaceC&Codigo=',AR.ProductoFab,'&CodigoAlmacen=',C.ProductoCod,'&TipoPrograma=gad_programas_simples&Cod_Curricula=',C.CurriculaCod,''',''panelB-R'',''''); ><div class=botIcRep><i class=icon-pencil ></i>&nbsp;Administrar</div></div>

							</div>
							')  AS '{$TituloEncabezado2}',
							CONCAT('
							<div style=position:relative;>
							<div class=Btn-reporte  onclick=enviaReg('''',''$enlaceELI&Cod_Curricula=',C.CurriculaCod,'&PCod_Curricula=',C.ProductoCod,'&CodigoAlmacen=',C.ProductoCod,''',''Cont_',C.CurriculaCod,''',''''); ><div class=botIcRep><i class=icon-trash></i></div></div>

							</div>
							<tr >
							<td colspan=9><div id=Cont_',C.CurriculaCod,' ></div></td> </tr>
							')  AS 'Eliminar'";	
			

			$Q_Reporte = "SELECT
			CONCAT('<div >
			<div class=Circulo_Reporte > ',C.Orden,'</div>
			<div > ',AR.Titulo,'</div>
			</div>')  AS '$TituloEncabezado1',
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
          
	    
    $data = array('reporte' => $reporte, 'sql' => $Q_Reporte);
    return $data;		

}