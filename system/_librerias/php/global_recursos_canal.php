<?php

	function FormularioActividad($vConex, $data){

		$CodigoAlmacen = $data['CodigoAlmacen'];
		$Codigo = $data['Codigo'];
		$CodigoLDT = $data['CodigoLDT'];
		$CodigoACT = $data['CodigoACT'];
		$enlace = $data['enlace'];
		$Concepto = $data['Concepto'];

		if(empty($CodigoACT)){
			$btn = "";
			$btn = Botones($btn, 'botones1', '');
			$menu_titulo = tituloBtnPn("<span>Crear </span><p>ACTIVIDAD</p>", $btn, 'auto', 'TituloA');
			$path = "";
			$uRLForm = "Crear]" . $enlace . "?ProcesaActividad=AumentarSubConcepto&CodigoLDT=" . $CodigoLDT . "&Codigo=" . $Codigo . "&EvalDetalleCod=" . $CodigoACT . "&CodigoAlmacen=".$CodigoAlmacen."&Concepto=".$Concepto."]layoutV]F]}";
			$Form = c_form_adp('', $vConex, "elevaluaciondetallecurso_crear", "CuadroA", $path, $uRLForm, $EvalDetalleCod, "", 'Codigo');
		}else{
			$btn = "";
			$btn = Botones($btn, 'botones1', '');
			$menu_titulo = tituloBtnPn("<span>Editar </span><p>ACTIVIDAD</p>", $btn, 'auto', 'TituloA');
			$path = "";
			
            $uRLForm = "Actualizar]" . $enlace . "?metodo=elevaluaciondetallecurso_edit_curso_update&transaccion=UPDATE&CodigoAlmacen=" . $CodigoAlmacen . "&Codigo=" . $Codigo . "&EvalDetalleCod=" . $CodigoACT . "&Concepto=" . $Concepto . "]layoutV]F]}";
            $uRLForm .="Eliminar]" . $enlace . "?SubConceptosCurso=EliminaSubConceptoConfirmar&transaccion=INSERT&CodigoAlmacen=" . $CodigoAlmacen . "&Codigo=" . $Codigo . "&EvalDetalleCod=" . $CodigoACT . "]layoutV]F]]Red}";

			$Form = c_form_adp('', $vConex, "elevaluaciondetallecurso_edit", "CuadroA", $path, $uRLForm, $CodigoACT, "", 'Codigo');	
		
		}	
			$s = "<div style='float:left;width:100%'>";
			$s .= "<div style='float:left;width:100%' id='SPanelB-INF'>" . $menu_titulo . $Form . "</div>";
			$s .= "</div>";
		return  $s;   		    
	}


	function FormularioRecursoGeneral($vConex, $data){

		$SegGetConstante = $data['SegGetConstante'];
		$CodigoRE = $data['CodigoRE'];
		$Curso = $data['Curso'];
		$enlace = $data['enlace'];
		
		$RecursoAcademico = " SELECT 
						ST.SubTemaCod 
						, ST.TituloArticulo 
						FROM tema T  
						LEFT JOIN subtema ST ON T.CodTema = ST.Tema
						WHERE  T.Curso = " . $Curso . " AND T.TipoTema = 'canal'
						";		
		$tSelectD = array(
			'RecursoTipo' => 'SELECT  Codigo, Descripcion FROM eltiporecursoevaluacion',
			'TipoNota' => 'SELECT  Codigo, Descripcion FROM eltipocalificacion',
			'ModoPregunta' => 'SELECT  Codigo, Descripcion FROM elmodopregunta',
			'RecursoAcademico' => $RecursoAcademico
		);
	
		if($CodigoRE == 0){
		
			$btn = "";
			$menu_titulo = tituloBtnPn("<span>Crear </span><p>RECURSO DE EVALUACIÓN</p>", $btn, 'auto', 'TituloA');
			$path = "";
            $uRLForm = "Crear]" . $enlace . "?metodo=recusro_eval_canal&transaccion=INSERT&".$SegGetConstante."]PanelPInfo]F]}";
            
            $Form = c_form_adp('', $vConex, "recusro_eval_canal", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');

		}else{
			$btn = "";
			$btn = Botones($btn, 'botones1', '');
			$menu_titulo = tituloBtnPn("<span>Editar </span><p>RECURSO DE EVALUACIÓN</p>", $btn, 'auto', 'TituloA');
			$path = "";
			
            $uRLForm = "Actualizar]" . $enlace . "?metodo=recusro_eval_canal&transaccion=UPDATE&".$SegGetConstante."]PanelPInfo]F]}";
			$Form = c_form_adp('', $vConex, "recusro_eval_canal", "CuadroA", $path, $uRLForm,$CodigoRE, $tSelectD, 'Codigo');

		}	
			$s = "<div style='float:left;width:100%'>";
			$s .= "<div style='float:left;width:100%' id='SPanelB-INF'>" . $menu_titulo . $Form . "</div>";
			$s .= "</div>";
		return  $s;   		    
	}
	

	function FormularioRecursoEval($vConex, $data){

		$SegGetConstante = $data['SegGetConstante'];
		$CodigoRE = $data['CodigoRE'];
		$Curso = $data['Curso'];
		$enlace = $data['enlace'];
		
		$RecursoAcademico = " SELECT 
						ST.SubTemaCod 
						, ST.TituloArticulo 
						FROM tema T  
						LEFT JOIN subtema ST ON T.CodTema = ST.Tema
						WHERE  T.Curso = " . $Curso . " AND T.TipoTema = 'canal'
						";		
		$tSelectD = array(
			'RecursoTipo' => 'SELECT  Codigo, Descripcion FROM eltiporecursoevaluacion',
			'TipoNota' => 'SELECT  Codigo, Descripcion FROM eltipocalificacion',
			'ModoPregunta' => 'SELECT  Codigo, Descripcion FROM elmodopregunta',
			'RecursoAcademico' => $RecursoAcademico
		);

			$btn = "";
			$btn = Botones($btn, 'botones1', '');
			$menu_titulo = tituloBtnPn("<span>Recurso </span><p>TIPO EVALUACIÓN </p>", $btn, 'auto', 'TituloA');
			$path = "";
			
            $uRLForm = "Actualizar]" . $enlace . "?metodo=recusro_eval_canal_tipos&transaccion=UPDATE&".$SegGetConstante."]PanelPInfo]F]}";
			$Form = c_form_adp('', $vConex, "recusro_eval_canal_tipos", "CuadroA", $path, $uRLForm,$CodigoRE, $tSelectD, 'Codigo');

			$s = "<div style='float:left;width:100%'>";
			$s .= "<div style='float:left;width:100%' id='SPanelB-INF'>" . $menu_titulo . $Form . "</div>";
			$s .= "</div>";
		return  $s;   		    
	}
	

	function FormularioRecursoEntrega($vConex, $data){
	
		$SegGetConstante = $data['SegGetConstante'];
		$CodigoRE = $data['CodigoRE'];
		$Curso = $data['Curso'];
		$enlace = $data['enlace'];
		$tSelectD = "";

		$btn = "";
		$btn = Botones($btn, 'botones1', '');
		$menu_titulo = tituloBtnPn("<span>Recurso </span><p>ENTREGA DE RESULTADOS </p>", $btn, 'auto', 'TituloA');
		$path = "";

		$uRLForm = "Actualizar]" . $enlace . "?metodo=recusro_eval_canal_entrega&transaccion=UPDATE&".$SegGetConstante."]PanelPInfo]F]}";
		$Form = c_form_adp('', $vConex, "recusro_eval_canal_entrega", "CuadroA", $path, $uRLForm,$CodigoRE, $tSelectD, 'Codigo');

		$s = "<div style='float:left;width:100%'>";
		$s .= "<div style='float:left;width:100%' id='SPanelB-INF'>" . $menu_titulo . $Form . "</div>";
		$s .= "</div>";
		return  $s;   		    
	}	
	
	
	function FormularioRecursoCronograma($vConex, $data){

		$SegGetConstante = $data['SegGetConstante'];
		$CodigoRE = $data['CodigoRE'];
		$Curso = $data['Curso'];
		$enlace = $data['enlace'];

		$tSelectD = "";

			$btn = "";
			$btn = Botones($btn, 'botones1', '');
			$menu_titulo = tituloBtnPn("<span>Recurso </span><p>FECHAS Y HORAS </p>", $btn, 'auto', 'TituloA');
			$path = "";

            $uRLForm = "Actualizar]" . $enlace . "?metodo=recusro_eval_canal_crono&transaccion=UPDATE&".$SegGetConstante."]PanelPInfo]F]}";
			$Form = c_form_adp('', $vConex, "recusro_eval_canal_crono", "CuadroA", $path, $uRLForm,$CodigoRE, $tSelectD, 'Codigo');

			$s = "<div style='float:left;width:100%'>";
			$s .= "<div style='float:left;width:100%' id='SPanelB-INF'>" . $menu_titulo . $Form . "</div>";
			$s .= "</div>";
		return  $s;
	}

    function FormularioRecursoCanal($vConex, $data){

        $SegGetConstante = $data['SegGetConstante'];
        $CodigoRE = $data['CodigoRE'];
        $Curso = $data['Curso'];
        $enlace = $data['enlace'];
        $Codigo= $data['CodigoCA'];
        $Estado= $data['Estado'];
        $btn = "";
        $btn = Botones($btn, 'botones1', '');
        $menu_titulo = tituloBtnPn("<span>Recurso </span><p>Formato </p>", $btn, 'auto', 'TituloA');
        $path = "";

        if($Codigo==""){$transaccion="INSERT";}else{$transaccion="UPDATE";}
      ####  // if($Estado==1){
            // $uRLForm  = "";
    #####333333    // }else{
            $uRLForm  = "Grabar]" . $enlace . "?TipoDato=texto&metodo=sala_video_canal&transaccion={$transaccion}&".$SegGetConstante."&CodigoCA={$Codigo}&estado=0]PanelPInfo]F]}";
            $uRLForm .= "Grabar y Cerrar ]" . $enlace . "?TipoDato=texto&metodo=sala_video_canal&transaccion={$transaccion}&".$SegGetConstante."&CodigoCA={$Codigo}&estado=1]PanelPInfo]F]}";
        // }

        $Form = c_form_adp('', $vConex, "sala_video_canal", "CuadroA", $path, $uRLForm,$Codigo, "", 'Codigo');

        $s = "<div style='float:left;width:100%'>";
        $s .= "<div style='float:left;width:100%' id='SPanelB-INF'>" . $menu_titulo . $Form . "</div>";
        $s .= "</div>";
        return  $s;
    }


	
?>