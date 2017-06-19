<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_tienda.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_curricula.php";
$enlacePopup = "se_curricula.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
// W($Entidad. " Entidad ");
if (get('Curricula') != '') {Curricula(get('Curricula'));}

if (get("metodo") != "") {
        
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario, $Entidad;
		
        $cod_articulo = get("cod_articulo");
        $cod_mov_almacen = get("cod_mov_almacen");
        $CurriculaCod = get("CurriculaCod");
		
        if (get("metodo") == "curricula") {
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;
            } elseif ($campo == "Articulo") {
                $valor = $cod_articulo;
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";				
            } elseif ($campo == "Entidad") {
                $valor = $Entidad;				
            } else {
                $valor = "";
            }
            return $valor;
        }
		
        if (get("metodo") == "curricula_docentes") {
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;
            } elseif ($campo == "Movimiento_Almacen") {
                $valor = $cod_mov_almacen;
            } elseif ($campo == "Curricula") {
                $valor = $CurriculaCod;				
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";				
            } elseif ($campo == "Entidad") {
                $valor = $Entidad;				
            } else {
                $valor = "";
            }
            return $valor;
        }              
			  
			  
        if (get("metodo") == "docente") {
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;		
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";				
            } elseif ($campo == "Entidad") {
                $valor = $Entidad;				
            } else {
                $valor = "";
            }
            return $valor;
        } 

		if (get("metodo") == "Fentidades_educativa") {
			
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;		
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";				
            } elseif ($campo == "Entidad") {
                $valor = $Entidad;				
            } else {
                $valor = "";
            }
            return $valor;
        } 

		
		if (get("metodo") == "curricula_entidades_educativa") {
			
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;		
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";				
            } elseif ($campo == "Entidad") {
                $valor = $Entidad;	
            } elseif ($campo == "Movimiento_Almacen") {
                $valor = get("cod_mov_almacen");				
            } else {
                $valor = "";
            }
            return $valor;
        } 
				
    }

    function p_before($codigo) {
		
        if (get("transaccion") == "INSERT") {	
		
			if (get("metodo") == "docente") {		
				infresarCurriculaDocente($codigo);
			}
			if (get("metodo") == "Fentidades_educativa") {		
				infresarCurriculaEntidadesEducativas($codigo);
			}			
			
        }			
    }

    if (get("TipoDato") == "texto") {

        if (get("transaccion") == "UPDATE") {

            if (get("metodo") == "curricula") {
                p_gf_udp("curricula", $cnPDO, get("codigo"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Curricula("Site");
            }
            if (get("metodo") == "docente") {
                p_gf_udp("docente", $cnPDO, get("codigoDocente"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Curricula("Site");
            }			
            if (get("metodo") == "curricula_entidades_educativa") {
                p_gf_udp("curricula_entidades_educativa", $cnPDO, get("cod_curricula_entidad_educativa"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Curricula("Site");
            }				
			
            if (get("metodo") == "Fentidades_educativa") {
                p_gf_udp("Fentidades_educativa", $cnPDO, get("Cod_Entidad_Educativa"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Curricula("Site");
            }				
						
			
				
        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "curricula_entidades_educativa") {
                p_gf_udp("curricula_entidades_educativa", $cnPDO,"",'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Curricula("Site");
            }					
			
            if (get("metodo") == "curricula") {
                p_gf_udp("curricula",$cnPDO,'','Codigo');
                Curricula("Site");
            }
            if (get("metodo") == "curricula_docentes") {
                p_gf_udp("curricula_docentes",$cnPDO,'','Codigo');
                Curricula("Site");
            }
            if (get("metodo") == "docente") {
                p_gf_udp("docente",$cnPDO,'','Codigo');
                Curricula("Site");
            }			
            if (get("metodo") == "Fentidades_educativa") {
                p_gf_udp("Fentidades_educativa",$cnPDO,'','Codigo');
                Curricula("Site");
            }			
						
			
        }
    }


    exit();
}


function Curricula($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup,$Entidad;
	$enlaceTienda = "_vistas/se_tienda.php";
	
	$cod_articulo = get("cod_articulo");
	$cod_mov_almacen = get("cod_mov_almacen");

	$segmentoUrl =  "&cod_articulo=".$cod_articulo."&cod_mov_almacen=".$cod_mov_almacen;
			
    switch ($Arg) {

        case 'Site':
		
			$pestanas = pestanasBLocal(array("".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]Marca","".$segmentoUrl."]","".$segmentoUrl."]"));
			
            $btn = "<i class='icon-pencil'></i>  Crear ]" .$enlace."?Curricula=Crear{$segmentoUrl}]panelFormA1]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>Currícula y Docentes </p><span>ADMINISTRAR DATOS</span>";
            $btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
       
			$sql = "
			SELECT CU.Descripcion, CU.Codigo, CU.Temas
			FROM curricula CU
			WHERE CU.Articulo = {$cod_articulo}  ";
			$reporte = "<div class='listadoCab'>";
	        $consulta = fetchAll($sql,$cnPDO);			
			foreach($consulta AS $reg){
			$reporte .= '
			    <div class="listadoCab item" style="position:relative;">
					<div style=float:left;width:95%;>
					
					    <h1 style=float:left;width:100%; >'.$reg["Descripcion"].' </h1>
					    <p style=float:left;width:100%; >'.$reg["Temas"].' </p> ';
						
						$sqlA = "SELECT DOC.Nombres, DOC.Codigo
						FROM curricula_docentes CUD
						INNER JOIN docentes DOC ON CUD.Docente = DOC.Codigo
						WHERE CUD.Curricula = ".$reg["Codigo"]."";
						$consultab = fetchAll($sqlA,$cnPDO);			
						foreach($consultab AS $regB){			
		                    $reporte .= ' 
							
							<div class="btnAdd" style="float:left;margin-left:3px;margin-top:10px;">
								<span class="listadoDinamicoSub" onclick=enviaReg("EDIb'.$regB["Codigo"].'","'.$enlace.'?Curricula=editDocente&codigo='.$reg["Codigo"].'&codigoDocente='.$regB["Codigo"].$segmentoUrl.'","panelFormA1",""); >
								'.$regB["Nombres"].'</span>
								<span class=listadoDinamicoBtnCerrar onclick=enviaReg("EDIb'.$regB["Codigo"].'","'.$enlace.'?Curricula=eliminarDocente&codigo='.$reg["Codigo"].'&codigoDocente='.$regB["Codigo"].$segmentoUrl.'","panelFormA1",""); >X</span>
							</div>
							';			   
						}
						
			$reporte .= '  
					    <div class=listadoDinamicoBtn  onclick=enviaReg("EDI'.$reg["Codigo"].'","'.$enlace.'?Curricula=addDocente&codigo='.$reg["Codigo"].$segmentoUrl.'","panelFormA1",""); >
							<i class=icon-plus></i> 
							Añade docentes
						</div>
					
						<div class=botIcRepB style="position:absolute;right:0px;"><i class=icon-chevron-down></i> 
							 <ul class=sub_boton >
	                            <li onclick=enviaReg("DEL'.$reg["Codigo"].'","'.$enlace.'?Curricula=Editar&codigo='.$reg["Codigo"].$segmentoUrl.'","panelFormA1","");   >Editar</li>
		                        <li onclick=enviaReg("EDIb'.$reg["Codigo"].'","'.$enlace.'?Curricula=Eliminar&codigo='.$reg["Codigo"].$segmentoUrl.'","panelFormA1","");  >Eliminar</li>
							 </ul>
						</div>
					</div>
				</div>
				
				<div id =panel'.$reg["Codigo"].' style=float:left;width:100%; ></div> ';				
			}
			$reporte .= "</div>";

            $html .= "<div id = 'panelOcultoB' style='float:left;width:100%;' ></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;' >" . $reporte . "</div>";
			
			

            $sql = "
				SELECT EE.Nombre
				, CONCAT('
					<div  >',EE.ImagenLogo ,' </div>
				')AS 'Detalles'
				,  CONCAT('
					<div  >
					<div class=botIcRepC ><i class=icon-chevron-down ></i> 
						 <ul class=sub_boton >
							<li onclick=enviaReg(''EDI',CEE.Codigo,''',''{$enlace}?Curricula=editEntidadEducativa&cod_curricula_entidad_educativa=',CEE.Codigo,'{$segmentoUrl}'',''panelFormA1'','''');  >Editar</li>
							<li onclick=enviaReg(''EDI',CEE.Codigo,''',''{$enlace}?Curricula=eliminaEntidadEducativa&cod_curricula_entidad_educativa=',CEE.Codigo,'{$segmentoUrl}'',''panelFormA1'','''');>Eliminar</li>
						 </ul>
					</div>
					</div>
				')AS 'Acción' FROM curricula_entidades_educativa CEE
				INNER JOIN entidad_educativa EE ON EE.Codigo = CEE.Entidad_Educativa
				WHERE CEE.Entidad = :Entidad AND CEE.Movimiento_Almacen = :Movimiento_Almacen
				ORDER BY CEE.FechaHoraCreacion DESC
				
			";    
            $clase = 'reporteA';
            $enlaceCod = 'codigoAlmacen';
            $url = $enlace."?Articulos=EditaArticulos";
            $panel = 'layoutV';
			
		    $where = ["Entidad"=>$Entidad,"Movimiento_Almacen"=>$cod_mov_almacen ];
            $reporteB = ListR2('', $sql, $where ,$cnPDO, $clase, '', $url, $enlaceCod, $panel, 'sys_tabla_det_form', '', '','');
						
            $btn = "<i class='icon-pencil'></i>  Crear ]" .$enlace."?Curricula=CrearEntidadesEducativas{$segmentoUrl}]panelFormA1]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>Entidades Educativas</p><span>ADMINISTRAR DATOS</span>";
            $btn_titulo2 = panelST2017($titulo, $btn, "auto", "TituloALMBBG");		
			
            $html2 = "<div id = 'panelOcultoB2' style='float:left;width:100%;' ></div>";
            $html2 .= "<div id = 'PanelInferior2' style='float:left;width:100%;' > ".$reporteB." </div>";	

            $linea = "<div class='LineaIT' ></div>";			
			
			$metodo = get("metodo");
			if($metodo != ""){
				$panel = array(array('PanelA1-A', '100%', $btn_titulo . $linea  . $html . $btn_titulo2  .  $html2 ));
				$Cuerpo = LayoutPage($panel);
				
				$s = layoutV2( $pestanas , $Cuerpo ,"layoutV3","body-lv3");      
				
				$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
				$panel = "<div class='panelContenedorF1' style='float:left;width:98%;padding:1% 1% 5% 1%;overflow: auto;height: 461px;' >" . $s . "</div>";
				WE($Close .$panel);			
				
			}else{
				
				$panel = array(array('PanelA1-A', '100%',  $btn_titulo . $linea  . $html . $btn_titulo2 .  $html2));
				$Cuerpo = LayoutPage($panel);
				$s = layoutV2($pestanas ,$Cuerpo ,"layoutV3","body-lv3");    
				
				$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
				$panel = "<div class='panelContenedorF1' style='float:left;width:98%;padding:1% 1% 5% 1%;overflow: auto;height: 461px;' >" . $s . "</div>";
				WE($Close .$panel);
			}	
			
            break;


        case 'Crear':
		    
			$btn = "<i class='icon-circle-arrow-left'></i> ]" .$enlace."?Curricula=Site&metodo=esc{$segmentoUrl}]panelFormA1]]}";
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>CREAR TEMA</p><span>De la currícula</span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=curricula&transaccion=INSERT{$segmentoUrl}]panelFormA1]F]}";
			$tSelectD = array(
			'Lineaarticulo' => 'SELECT Codigo,Descripcion FROM lineaarticulo'
			);
			$form = c_form_adp($titulo, $cnPDO, "curricula", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:100%;' >" . $btn_titulo . $form . "</div>";
			WE($html);
			
            break;              
     			
        case 'Editar':

            $codigo = get("codigo");
			
			$btn = "<i class='icon-circle-arrow-left'></i> ]" .$enlace."?Curricula=Site&metodo=esc{$segmentoUrl}]panelFormA1]]}";
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>EDITAR EL TEMA</p><span>De la currícula</span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=curricula&transaccion=UPDATE&codigo={$codigo}{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Lineaarticulo' => 'SELECT Codigo,Descripcion FROM lineaarticulo'
			);
			$form = c_form_adp($titulo, $cnPDO, "curricula", "CuadroA", $path, $uRLForm,$codigo, $tSelectD, 'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:100%' >" . $btn_titulo . $form . "</div>";
			WE($html);
			
            break;
			      			
        case 'Eliminar'://eliminarDocente

            $codigo = get("codigo");	
			
            $btn = "Confirmar ]" .$enlace."?Curricula=EliminaAccion&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]}";
            $btn .= "<i class='icon-circle-arrow-left'></i> ]" .$enlace."?Curricula=Site&metodo=esc{$segmentoUrl}]panelFormA1]]}Plomo";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR</p><span>El item seleccionado</span>";
            $btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			$html = "<div class='panelContenedorF' style='float:left;width:100%;' >" . $btn_titulo . "</div>";	
			
			WE($html);
			
            break;	
			
        case 'EliminaAccion':
		
            $codigo = get("codigo");
		
	        DReg("curricula", "Codigo", $codigo, $cnOld);	
			$msg =Msg("El proceso fue cerrado correctamente","C");
			W($msg);			
			Curricula("Site");
			WE("");
            break; 		
        case 'addDocente':
		
            $codigo = get("codigo");
			
			$btn = "Crear ]" .$enlace."?Curricula=creaDocente&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]}";
			$btn .= "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Curricula=Site&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
			$btn = Botones($btn, 'botones1', 'sys_form');

			$titulo = "<p>ADICIONAR DOCENTE </p><span>Al Tema</span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Vincular]" . $enlace . "?metodo=curricula_docentes&transaccion=INSERT&CurriculaCod={$codigo}{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Docente' => 'SELECT Codigo,Nombres FROM docentes'
			);
			$form = c_form_adp($titulo, $cnPDO, "curricula_docentes", "CuadroA", $path, $uRLForm,"", $tSelectD, 'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:100%;' >" . $btn_titulo . $form . "</div>";
			WE($html);
			
            break; 				
        case 'creaDocente':
		
            $codigo = get("codigo");
			
            $btn = "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Curricula=Site&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>CREAR DOCENTE </p><span>Y vincularlo al tema</span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=docente&transaccion=INSERT&CurriculaCod={$codigo}{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Docente' => 'SELECT Codigo,Nombres FROM docentes'
			);
			$path = array('Foto' => '../_imagenes/productos/');	
			$form = c_form_adp($titulo, $cnPDO, "docente", "CuadroA", $path, $uRLForm,"", $tSelectD, 'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:100%;' >" . $btn_titulo . $form . "</div>";
			WE($html);
			
            break; 
			
        case 'editDocente':
		
            $codigo = get("codigo");
            $codigoDocente = get("codigoDocente");
			
            $btn = "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Curricula=Site&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>EDITAR DATOS DEL DOCENTE </p>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = array('Foto' => '../_imagenes/productos/');				
			$uRLForm = "Guardar]" . $enlace . "?metodo=docente&transaccion=UPDATE&codigoDocente={$codigoDocente}&CurriculaCod={$codigo}{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Docente' => 'SELECT Codigo,Nombres FROM docentes'
			);
			$form = c_form_adp($titulo, $cnPDO, "docente", "CuadroA", $path, $uRLForm,$codigoDocente, $tSelectD, 'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:100%;' >" . $btn_titulo . $form . "</div>";
			WE($html);
			
            break; 
	
        case 'eliminarDocente'://eliminarDocente

            $codigo = get("codigo");	
            $codigoDocente = get("codigoDocente");	
			
            $btn = "Eliminar]" .$enlace."?Curricula=EliminaAccionDocentePrograma&metodo=esc&codigoDocente={$codigoDocente}&codigo={$codigo}{$segmentoUrl}]panelFormA1]]}";
            // $btn .= "Eliminar de la BD ]" .$enlace."?Curricula=EliminaAccionDocente&metodo=esc&codigoDocente={$codigoDocente}&codigo={$codigo}{$segmentoUrl}]panelFormA1]]}";
            $btn .= "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Curricula=Site&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR </p><span>El item seleccionado</span>";
            $btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			$html = "<div class='panelContenedorF' style='float:left;width:100%;' >" . $btn_titulo . "</div>";			
			WE($html);
			
            break;	
        case 'EliminaAccionDocentePrograma':
		
            $codigo = get("codigo");
            $codigoDocente = get("codigoDocente");
			
			$sql = "SELECT CD.Codigo 
			FROM  curricula_docentes CD
			WHERE CD.Curricula = {$codigo} AND CD.Docente = {$codigoDocente} 
			";    
			$rg = fetch($sql,$cnPDO);
			$Cod_curri_docente = $rg["Codigo"];			
		
	        DReg("curricula_docentes", "Codigo", $Cod_curri_docente, $cnOld);	
			$msg =Msg("El proceso fue cerrado correctamente","C");
			W($msg);
			Curricula("Site");
			WE("");
            break; 				
        case 'EliminaAccionDocente':
		
            $codigo = get("codigo");
            $codigoDocente = get("codigoDocente");
			
			$sql = "SELECT CD.Codigo 
			FROM  curricula_docentes CD
			WHERE CD.Curricula = {$codigo} AND CD.Docente = {$codigoDocente} 
			";    
			$rg = fetch($sql,$cnPDO);
			$Cod_curri_docente = $rg["Codigo"];			
		
	        DReg("docentes", "Codigo", $codigoDocente, $cnOld);	
	        DReg("curricula_docentes", "Codigo", $Cod_curri_docente, $cnOld);	
			
			Curricula("Site");
			WE("");
            break; 
			
        case 'eliminaEntidadEducativa':
		
            $cod_curricula_entidad_educativa = get("cod_curricula_entidad_educativa");
		
	        DReg("curricula_entidades_educativa", "Codigo", $cod_curricula_entidad_educativa, $cnOld);				
			
			$msg =Msg("El proceso fue cerrado correctamente","C");
			W($msg);
			
			Curricula("Site");
			
				
			WE("");
            break; 	
			
        case 'CrearEntidadesEducativas':
		
            $codigo = get("codigo");
			
            $btn = "Crear]" .$enlace."?Curricula=CrearEntidadEducativaReg&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
            $btn .= "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Curricula=Site&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
			
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>VINCULAR</p><span>ENTIDAD EDUCATIVA</span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=curricula_entidades_educativa&transaccion=INSERT&CurriculaCod={$codigo}{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Entidad_Educativa' => 'SELECT Codigo,Nombre FROM entidad_educativa WHERE Entidad = "'.$Entidad.'" '
			);
			$form = c_form_adp($titulo, $cnPDO, "curricula_entidades_educativa", "CuadroA", $path, $uRLForm,"", $tSelectD, 'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:98%;' >" . $btn_titulo . $form . "</div>";
			WE($html);
			
            break; 

        case 'CrearEntidadEducativaReg':
		
            $codigo = get("codigo");
			
            $btn = "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Curricula=Site&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>CREAR</p><span> ENTIDAD EDUCATIVA </span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = array('ImagenLogo' => '../_imagenes/productos/');		
			$uRLForm = "Guardar]" . $enlace . "?metodo=Fentidades_educativa&transaccion=INSERT&CurriculaCod={$codigo}{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Docente' => 'SELECT Codigo,Nombres FROM docentes'
			);
			$form = c_form_adp($titulo,$cnPDO,"Fentidades_educativa","CuadroA",$path,$uRLForm,"",$tSelectD,'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:98%;' >" . $btn_titulo . $form . "</div>";
			WE($html);
			
            break; 			
			
			
			
	    case 'editEntidadEducativa':
			
		
            $codigo = get("codigo");
            $cod_curricula_entidad_educativa = get("cod_curricula_entidad_educativa");
			
            $btn = "Editar ]" .$enlace."?Curricula=ActualizarDatosEntidadEducativaReg&metodo=esc&cod_curricula_entidad_educativa={$cod_curricula_entidad_educativa}{$segmentoUrl}]panelFormA1]]plomo}";
            $btn .= "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Curricula=Site&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>Editar</p><span> ENTIDAD EDUCATIVA </span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=curricula_entidades_educativa&transaccion=UPDATE&cod_curricula_entidad_educativa={$cod_curricula_entidad_educativa}&CurriculaCod={$codigo}{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Entidad_Educativa' => 'SELECT Codigo,Nombre FROM entidad_educativa WHERE Entidad = "'.$Entidad.'" '
			);
			$form = c_form_adp($titulo, $cnPDO, "curricula_entidades_educativa", "CuadroA", $path, $uRLForm,$cod_curricula_entidad_educativa, $tSelectD, 'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:100%;' >" . $btn_titulo . $form . "</div>";
			WE($html);
			
            break; 
			
        case 'ActualizarDatosEntidadEducativaReg':
		
            $cod_curricula_entidad_educativa = get("cod_curricula_entidad_educativa");
			
			$Query = " 
			SELECT 
			CEE.Entidad_Educativa
			FROM  curricula_entidades_educativa CEE 		
			WHERE CEE.Codigo = :Codigo  
			";
			$where = ["Codigo"=>$cod_curricula_entidad_educativa];	
			$rg = OwlPDO::fetchObj($Query, $where ,$cnPDO);	
			$Cod_Entidad_Educativa = $rg->Entidad_Educativa;
			
            $btn = "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Curricula=Site&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>Actualizar Datos</p><span> ENTIDAD EDUCATIVA </span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = array('ImagenLogo' => '../_imagenes/productos/');		
			$uRLForm = "Guardar]" . $enlace . "?metodo=Fentidades_educativa&transaccion=UPDATE&Cod_Entidad_Educativa={$Cod_Entidad_Educativa}{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Docente' => 'SELECT Codigo,Nombres FROM docentes'
			);
			$form = c_form_adp($titulo,$cnPDO,"Fentidades_educativa","CuadroA",$path,$uRLForm,$Cod_Entidad_Educativa,$tSelectD,'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:100%;' >" . $btn_titulo . $form . "</div>";
			WE($html);
			
            break; 					
        default:		
            exit;
            break;
    }
}


function infresarCurriculaDocente($codigo){
    global $cnPDO,$FechaHora, $Usuario, $Entidad;
	
	$cod_mov_almacen = get("cod_mov_almacen");
	$CurriculaCod = get("CurriculaCod");	
	$tableValue 	=	array();
	$tableValue["Docente"] =   $codigo;
	$tableValue["Movimiento_Almacen"] =   $cod_mov_almacen;
	$tableValue["Curricula"] =   $CurriculaCod;
	$tableValue["FechaHoraActualizacion"] =   $FechaHora;
	$tableValue["FechaHoraCreacion"] =   $FechaHora;
	$tableValue["UsuarioCreacion"] =   $Usuario;
	$tableValue["UsuarioActualizacion"] =   $Usuario;
	$tableValue["Entidad"] =   $Entidad;
	
	$return 			= 	insertPDO("curricula_docentes",$tableValue,$cnPDO);
			
}


function infresarCurriculaEntidadesEducativas($codigo){
    global $cnPDO,$FechaHora, $Usuario, $Entidad;
	
	$cod_mov_almacen = get("cod_mov_almacen");
	$CurriculaCod = get("CurriculaCod");	
	
	$tableValue 	=	array();
	$tableValue["Entidad_Educativa"] =   $codigo;
	$tableValue["Movimiento_Almacen"] =   $cod_mov_almacen;
	$tableValue["FechaHoraActualizacion"] =   $FechaHora;
	$tableValue["FechaHoraCreacion"] =   $FechaHora;
	$tableValue["UsuarioCreacion"] =   $Usuario;
	$tableValue["UsuarioActualizacion"] =   $Usuario;
	$tableValue["Entidad"] =   $Entidad;
	
	$return 			= 	insertPDO("curricula_entidades_educativa",$tableValue,$cnPDO);
			
}

?>
