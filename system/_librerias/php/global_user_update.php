<?php

function UpdateUser($email,$OrigenEmail,$vConex){
		
	 $Sql = " SELECT Usuario FROM usuarios WHERE Usuario='{$email}'  ";
	 $rg = fetch($Sql);
	 $NuevoUser = $rg["Usuario"];	 
	 $Sql2 = " SELECT Usuario FROM usuarios WHERE Usuario='{$OrigenEmail}'  ";
	 $rg = fetch($Sql2);
	 $OrigenEmail = $rg["Usuario"];	 

	if( empty($NuevoUser)  &&  !empty($OrigenEmail)){		
		
		$SqlUpdate = "  UPDATE profesores SET Usuario='{$email}Profesor'  WHERE  Usuario = '{$OrigenEmail}Profesor' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);
		
		$SqlUpdate = "  UPDATE matriculas SET Cliente='{$email}Alumno' WHERE Cliente  = '{$OrigenEmail}Alumno' ";
		//W($SqlUpdate.'<br>');		
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE usuarios SET Usuario='{$email}' WHERE Usuario = '{$OrigenEmail}' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE usuarios SET IdUsuario='{$email}Profesor' WHERE IdUsuario = '{$OrigenEmail}Profesor' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE usuarios SET IdUsuario='{$email}Alumno' WHERE IdUsuario = '{$OrigenEmail}Alumno' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE usuarios SET CodigoParlante='{$email}' WHERE CodigoParlante = '{$OrigenEmail}' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE alumnos SET Usuario='{$email}Alumno' WHERE Usuario = '{$OrigenEmail}Alumno' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE alumnos SET Email='{$email}' WHERE Email = '{$OrigenEmail}' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE usuario_entidad SET Usuario='{$email}' WHERE Usuario = '{$OrigenEmail}' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE eltransrespuesta SET Usuario='{$email}Alumno' WHERE Usuario = '{$OrigenEmail}Alumno' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE eltransrespuesta_cab SET Alumno='{$email}Alumno' WHERE Alumno = '{$OrigenEmail}Alumno' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE elasistencia SET Alumno='{$email}Alumno' WHERE Alumno = '{$OrigenEmail}Alumno' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE elevaluacionalumno SET Alumno='{$email}Alumno' WHERE Alumno = '{$OrigenEmail}Alumno' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE elalumnotareas SET Alumno='{$email}Alumno' WHERE Alumno = '{$OrigenEmail}Alumno' ";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE cursos SET Entidad='{$email}Profesor' WHERE Entidad = '{$OrigenEmail}Profesor'";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);

		$SqlUpdate = "  UPDATE almacen SET Origen='{$email}Profesor'  WHERE Origen = '{$OrigenEmail}Profesor'";
		//W($SqlUpdate.'<br>');
		xSQL2($SqlUpdate,$vConex);	;

		$Cambio = true;													

	}else{

		$Cambio = false;

	}

	return $Cambio;
}

    
