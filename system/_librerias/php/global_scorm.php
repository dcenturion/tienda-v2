<?php

function UpdateScormUser($NewUser,$productoId){

	$Cont = 0;
	$JsonUser = "["	;
	foreach ($NewUser as $Js) {		
		$User= explode("Alumno", $Js);
		if( $Cont == count($NewUser) -1){

			$JsonUser .= '{"Email":"'.$User[0].'"}';		
		}
		else {
			$JsonUser .= '{"Email":"'.$User[0].'"},';
		}
		$Cont++;
	}
	$JsonUser .= "]";	
	$NroScorm = 0;
	$SqlPrograma = "SELECT C.ProductoCod,C.CodProgAlmacen
					FROM curricula C
					INNER JOIN almacen AL ON C.ProductoCod = AL.AlmacenCod
					INNER JOIN articulos AR ON AL.Producto  = AR.Producto
					INNER JOIN categorias CA ON CA.CategoriCod  = AR.Categoria
					INNER JOIN profesores P ON AL.Origen = P.Usuario 
					LEFT JOIN sede_sucursal SC ON SC.Codigo=AL.Sede
					LEFT JOIN sedes AS SSC ON SC.UnidadNegocio = SSC.Codigo
					WHERE C.CodProgAlmacen = '{$productoId}' 
					ORDER BY C.Orden ASC";

	$RowPrograma = fetchAll($SqlPrograma);
	foreach ($RowPrograma as $Curricula) {
		$sQLcURSO = "SELECT ALC.AlmacenCod, AL.AlmacenCod, T.CodTema  AS groupId,ST.SubTemaCod, ST.Componente, 
					 ST.SubTemaCod,AL.TipoProducto,SC.IdExterno,SC.codigo AS CodScorm,ST.TituloArticulo
					FROM tema T
					LEFT JOIN subtema ST ON T.CodTema = ST.Tema
					LEFT JOIN almacen AL ON ST.Componente  =  AL.AlmacenCod
					INNER JOIN scorm SC ON REPLACE(AL.Producto,'SC-','')  = SC.codigo
					LEFT JOIN articulos AR ON AL.Producto  = AR.Producto
					INNER JOIN cursos CU ON  T.Curso = CU.CodCursos  
					INNER JOIN articulos ARC ON CU.CodCursos = ARC.ProductoFab
					INNER JOIN almacen ALC ON ARC.Producto = ALC.Producto
					WHERE  ALC.AlmacenCod = {$Curricula->ProductoCod}  AND  T. TipoTema  = 'Curso' 
					AND AL.TipoProducto = 'Scorm'
					AND SC.procedencia='Externo' AND SC.IdExterno NOT LIKE ''
					ORDER BY  T.Sesion, ST.Sesion  ASC ";
		$RowCurso = fetchAll($sQLcURSO);

		foreach ($RowCurso as $Scorm){
			$Titulo          = $Scorm->TituloArticulo;
			$id              =  $Scorm->CodScorm; #$rowSC['codigo'];
			$ProgramaAlmacen = $Curricula->CodProgAlmacen; #get('ProgramaAlmacen');
			$uniqidScorm     = $Scorm->IdExterno;#$rowSC['IdExterno'];						
			$parametros= "id={$id}&user={$JsonUser}&AlmacenProgramas={$ProgramaAlmacen}&uniqidScorm={$uniqidScorm}"; 
			$s = "<iframe src='https://archivos.owlgroup.org/system/_librerias/php/scorm/samples/uploadCourseUpdate.php?{$parametros}' style='width: 100%;height: 44px;' frameborder='0' ></iframe>";
			$NroScorm++;
			W("<div style='background:#e5e5e5; display:inline-block; margin:0 6px; padding:7px 20px 7px; color:#333; text-decoration:none; text-shadow: 0 1px 1px #FFF; font-size: 10px;  border:1px solid #ccc;' >
				Anexando Scorm '".$Titulo."' a los participantes. Esperar mensaje de confirmación. 
			   </div>".$s);
		}				
	}


}

