<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_tienda.php');

if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_tienda_adicionales.php";
$enlacePopup = "se_usuarios.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];


if (get('Main') != '') {Main(get('Main'));}
if (get('Busqueda') != '') {Busqueda(get('Busqueda'));}

if (get("metodo") != "") {
        
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario, $Entidad;
        
        if (get("metodo") == "FArticulosAutoresDet") {
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsusriosAlterno") {
                $valor = "'" .get("Usuario"). "'";
            } elseif ($campo == "Entidad") {
                $valor = $Entidad;	
            } elseif ($campo == "Articulo") {
                $valor = "'" .get("cod_articulo"). "'";					
            } else {
                $valor = "";
            }
            return $valor;
        }
		
		if (get("metodo") == "FArticulosAutores") {
			
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
		
		if (get("metodo") == "FCodigoPlataforma") {

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


              
    }

    function p_before($codigo) {
        if (get("transaccion") == "INSERT") {	
			if (get("metodo") == "FEntidades") {		
				VinculacionUsuario($codigo);
			}
        }		
    }

    if (get("TipoDato") == "texto") {

        if (get("transaccion") == "UPDATE") {

            if (get("metodo") == "FArticulosDatosAdicionales") {
				
                p_gf_udp("FArticulosDatosAdicionales", $cnPDO, get("cod_articulo"),'Codigo');		
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Main("Principal");
				WE("");
            }		
			
            if (get("metodo") == "FArticulosAutoresDet") {
				
                p_gf_udp("FArticulosAutoresDet", $cnPDO, get("articulos_det_cod"),'Codigo');		
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Main("Principal");
				WE("");
            }		

            if (get("metodo") == "FCodigoPlataforma") {
                p_gf_udp("FCodigoPlataforma", $cnPDO, get("cod_mov_almacen"),'Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Main("Principal");
            }
        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "FArticulosAutores") {
				
                p_gf_udp("FArticulosAutores",$cnPDO,'','Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);				
                Main("Principal");
            }
            if (get("metodo") == "FArticulosAutoresDet") {
				
                p_gf_udp("FArticulosAutoresDet",$cnPDO,'','Codigo');
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);				
                Main("Principal");
            }

        }
		
		

    }

    if (get("transaccion") == "DELETE") {
        if (get("metodo") == "sys_tipo_input") {
            DReg("sys_tipo_input", "Codigo", "'" . get("codigo") . "'", $vConex);
            datosAlternos("CreacionTipoDato");
        }
    }

    exit();
}


function Main($Arg) {
    global $cnPDO,$cnOld, $enlace, $FechaHora, $enlacePopup ,$Entidad;
	
	$cod_mov_almacen = get("cod_mov_almacen");

	$rg = datosAlmacenMovimientos($cod_mov_almacen,$cnPDO);
	$cod_articulo = $rg->Codigo;				
	$CodigoTipoArticulo = $rg->TipoArticulos;	
	$Capitulos = $rg->Capitulos;	
	$MasInformacion = $rg->MasInformacion;	
	$CodigoPlataformaEducativa = $rg->CodigoPlataformaEducativa;
	
	$segmentoUrl =  "&cod_articulo=".$cod_articulo."&cod_mov_almacen=".$cod_mov_almacen;
			
	
    switch ($Arg) {

        case 'Principal':
			
			if($CodigoTipoArticulo == 2){//Curso		
	            $pestanas = pestanasBLocal(array("".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]Marca","".$segmentoUrl."]","".$segmentoUrl."]"));
            }elseif($CodigoTipoArticulo == 1){// Ebook
	            $pestanas = pestanasBLocalEbook(array("".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]Marca","".$segmentoUrl."]","".$segmentoUrl."]"));
            }elseif($CodigoTipoArticulo == 3){// Estándar
			
			}	
			
            $sql = "
				SELECT 
				AA.Nombre
				,AD.FechaHoraCreacion AS 'Fecha Hora Creación'
				,  CONCAT('
					<div  >
					<div class=botIcRepC ><i class=icon-chevron-down ></i> 
						 <ul class=sub_boton >
							<li onclick=enviaReg(''EDI',AD.Codigo,''',''{$enlace}?Main=EditAutoresDet&articulos_det_cod=',AD.Codigo,'{$segmentoUrl}'',''panelFormA1'','''');  >Editar</li>
							<li onclick=enviaReg(''EDI',AD.Codigo,''',''{$enlace}?Main=EditAutoresDetEliminar&articulos_det_cod=',AD.Codigo,'{$segmentoUrl}'',''panelFormA1'','''');>Eliminar</li>
						 </ul>
					</div>
					</div>
				')AS 'Acción' FROM articulos_autor_det AD
				INNER JOIN articulos_autor AA ON AA.Codigo = AD.Articulos_autor
				WHERE 
				AD.Entidad = :Entidad
				AND AD.Articulo = :Articulo
				ORDER BY AD.FechaHoraCreacion DESC
			";    
            $clase = 'reporteA';
            $enlaceCod = 'codigoAlmacen';
            $url = $enlace."?Articulos=EditaArticulos";
            $panel = 'layoutV';
		    $where = ["Entidad"=>$Entidad,"Articulo"=>$cod_articulo];
            $reporteB = ListR2('', $sql, $where ,$cnPDO, $clase, '', $url, $enlaceCod, $panel, 'sys_tabla_det_form', '', '','');
			
						
            $btn = "<i class='icon-pencil'></i>  Crear ]" .$enlace."?Main=VincularAutores{$segmentoUrl}]panelFormA1]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>Autores</p><span>ADMINISTRAR DATOS</span>";
            $btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
            $html = "<div id = 'panelOcultoB2' style='float:left;width:100%;' ></div>";
            $html .= "<div class = 'PanelInferior2' style='padding-bottom:20px;'> ".$reporteB." </div>";				

			
			
            $btn = "<i class='icon-pencil'></i>  Editar ]" .$enlace."?Main=EditarAdicionales{$segmentoUrl}]panelFormA1]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');			
            $titulo = "<p>Mas información </p><span>ADMINISTRAR DATOS</span>";
            $btn_titulo2 = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
            $linea = "<div class='LineaIT' ></div>";
						
			$cuerpoHmtl2 = "<h1>Capitulos</h1>";
			$cuerpoHmtl2 .= "<p>{$Capitulos}</p>";
			$cuerpoHmtl2 .= "<h1>Mas información</h1>";
			$cuerpoHmtl2 .= "<p>{$MasInformacion}</p>";
			
            $html2 = "<div id = 'panelOcultoB2' style='float:left;width:100%;' ></div>";
            $html2 .= "<div class = 'PanelInferior2' > ". $linea . $cuerpoHmtl2." </div>";



			$cuerpoHmtl3 .= "<h1>Código de Plataforma Educativa</h1>";
			$cuerpoHmtl3 .= "<p>{$CodigoPlataformaEducativa}</p>";

            $btn = "<i class='icon-pencil'></i>  Editar ]" .$enlace."?Main=EditarDatosPlataformaEducativa{$segmentoUrl}]panelFormA1]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>Configuración </p><span>PLATAFORMA EDUCATIVA</span>";
            $btn_titulo3 = panelST2017($titulo, $btn, "auto", "TituloALMBBG");

            $html3 = "<div id = 'panelOcultoB2' style='float:left;width:100%;' ></div>";
            $html3 .= "<div class = 'PanelInferior2'  > ".$cuerpoHmtl3." </div>";


			$panel = array(array('PanelA1-A', '100%', $btn_titulo . $html . $btn_titulo2. $html2 . $btn_titulo3 . $html3 ));
			$Cuerpo = LayoutPage($panel);
			$s = layoutV2($pestanas ,$Cuerpo ,"layoutV3","body-lv3");    
			
			$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
			$panel = "<div class='panelContenedorF1' style='' >" . $s . "</div>";
			WE($Close .$panel); 
			
            break;
			

        case 'VincularAutores':
		
            $codigo = get("codigo");
			
            $btn = "<i class='icon-pencil'></i> Crear]" .$enlace."?Main=CrearAutorReg&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
            $btn .= "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Main=Principal&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
			
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>Autor del producto </p><span>SELECCIONA Y VINCULA</span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$linea = "<div class='LineaIT'></div>";
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=FArticulosAutoresDet&transaccion=INSERT{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Articulos_autor' => 'SELECT Codigo,Nombre FROM articulos_autor WHERE Entidad = "'.$Entidad.'" '
			);
			$form = c_form_adp($titulo, $cnPDO, "FArticulosAutoresDet", "CuadroA", $path, $uRLForm,"", $tSelectD, 'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:98%;padding: 0px 1%;' >" . $btn_titulo . $linea . $form . "</div>";
			WE($html);
			
            break; 

        case 'CrearAutorReg':
		
            $codigo = get("codigo");
			
            $btn = "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Main=Principal&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>Crear Nuevo Autor</p><span>LLENA LOS CAMPOS</span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = array('ImagenLogo' => '../_imagenes/productos/');		
			$uRLForm = "Guardar]" . $enlace . "?metodo=FArticulosAutores&transaccion=INSERT&CurriculaCod={$codigo}{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Docente' => 'SELECT Codigo,Nombres FROM docentes'
			);
			$form = c_form_adp($titulo,$cnPDO,"FArticulosAutores","CuadroA",$path,$uRLForm,"",$tSelectD,'Codigo');

			$linea = "<div class='LineaIT'></div>";
					
			$html = "<div class='panelContenedorF' style='float:left;width:98%;padding: 0px 1%;' >" . $btn_titulo . $linea . $form . "</div>";
			WE($html);
			
            break; 			
			
				
        case 'EditAutoresDet':
		
            $articulos_det_cod = get("articulos_det_cod");
			
            $btn = "<i class='icon-pencil'></i> Crear]" .$enlace."?Main=CrearAutorReg&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
            $btn .= "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Main=Principal&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
			
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>Autor del producto </p><span>SELECCIONA Y VINCULA</span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Actualizar]" . $enlace . "?metodo=FArticulosAutoresDet&transaccion=UPDATE&articulos_det_cod={$articulos_det_cod}{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Articulos_autor' => 'SELECT Codigo,Nombre FROM articulos_autor WHERE Entidad = "'.$Entidad.'" '
			);
			$form = c_form_adp($titulo, $cnPDO, "FArticulosAutoresDet", "CuadroA", $path, $uRLForm,$articulos_det_cod, $tSelectD, 'Codigo');
			
			$linea = "<div class='LineaIT'></div>";
			$html = "<div class='panelContenedorF' style='float:left;width:98%;padding: 0px 1%;' >" . $btn_titulo . $linea . $form . "</div>";
			WE($html);
			
            break;
				
        case 'EditAutoresDetEliminar'://eliminarDocente

            $articulos_det_cod = get("articulos_det_cod");	
			
            $btn = "Confirmar ]" .$enlace."?Main=EliminaAccionDet&articulos_det_cod={$articulos_det_cod}{$segmentoUrl}]panelFormA1]]}";
            $btn .= "<i class='icon-circle-arrow-left'></i> ]" .$enlace."?Main=Principal&metodo=esc{$segmentoUrl}]panelFormA1]]}Plomo";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR </p><span>El item seleccionado</span>";
            $btn_titulo = tituloBtnPn($titulo, $btn, "auto", "TituloALMBBG");
			
			WE($btn_titulo);
			
            break;	
			
        case 'EliminaAccionDet':
		
			$articulos_det_cod = get("articulos_det_cod");

			DReg("articulos_autor_det", "Codigo", $articulos_det_cod, $cnOld);	
			$msg =Msg("El proceso fue cerrado correctamente","C");
			W($msg);
			Main("Principal");
			
			WE("");
            break; 		
        
        case 'EditarAdicionales':
		
            $cod_articulo = get("cod_articulo");
			
            $btn .= "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Main=Principal&metodo=esc&codigo={$codigo}{$segmentoUrl}]panelFormA1]]plomo}";
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>Editar datos adicionales </p><span> EBOOK </span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Actualizar]" . $enlace . "?metodo=FArticulosDatosAdicionales&transaccion=UPDATE{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = array(
			'Articulos_autor' => 'SELECT Codigo,Nombre FROM articulos_autor WHERE Entidad = "'.$Entidad.'" '
			);
			$form = c_form_adp($titulo, $cnPDO, "FArticulosDatosAdicionales", "CuadroA", $path, $uRLForm,$cod_articulo, $tSelectD, 'Codigo');
			
			$linea = "<div class='LineaIT'></div>";
			$html = "<div class='panelContenedorF' style='float:left;width:98%;padding: 0px 1%;' >" . $btn_titulo . $linea . $form . "</div>";
			WE($html);
			
            break;

        case 'EditarDatosPlataformaEducativa':
		
            $btn = "<i class='icon-circle-arrow-left'></i>  ]" .$enlace."?Main=Principal{$segmentoUrl}]panelFormA1]]plomo}";
			
			$btn = Botones($btn, 'botones1', 'sys_form');
			$titulo = "<p>Configuración de Plataforma Educativa</p><span>ACTUALIZAR</span>";
			$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALMBBG");
			
			$linea = "<div class='LineaIT'></div>";
			
			$titulo = "";
			$path = "";				
			$uRLForm = "Guardar]" . $enlace . "?metodo=FCodigoPlataforma&transaccion=UPDATE{$segmentoUrl}]panelFormA1]F]}";
			
			$tSelectD = "";
			$form = c_form_adp($titulo, $cnPDO, "FCodigoPlataforma", "CuadroA", $path, $uRLForm,$cod_mov_almacen, $tSelectD, 'Codigo');
			
			$html = "<div class='panelContenedorF' style='float:left;width:98%;padding: 0px 1%;' >" . $btn_titulo . $linea . $form . "</div>";
			WE($html);
			
            break;
    }
}

function Busqueda($arg){
    global $vConex,$cnPDO,$enlace,$urlEmpresa,$idEmpresa,$Entidad, $EntidadPersona,$Codigo_Entidad_Usuario,$enterprise_user,$FechaHora;

    switch ($arg) {
     
        case 'Busqueda':	
			
            $queryd = get("queryd");
			
			
			if(empty($queryd)){
			    $operadorA = "<>";
				$queryd = "77777777777777777777777777";
			}else{
				$operadorA = "LIKE";
			}
			
			
            $sql = " SELECT
				    US.Nombre, 
				    US.Descripcion
					FROM usuarios US
					INNER JOIN entidades ENB ON US.Entidades = ENB.Codigo
					WHERE 
					US.Entidades_Suscriptor = :Entidades_Suscriptor	
					AND ( US.Nombre ".$operadorA." :Nombre OR US.Descripcion ".$operadorA." :Descripcion )
					
			"; 
            
			$where = [
			"Nombre" =>'%'.$queryd.'%',
			"Descripcion" =>'%'.$queryd.'%',
			"Entidades_Suscriptor" => $Entidad
			];
			
			$html ="";	
			$countcolumn = OwlPDO::fetchAllArr($sql,$where,$cnPDO);
			$cont = 0;
			foreach ($countcolumn as $reg) {
				$con +=1;	
				// $viewdata = array();
				// $viewdata['Alias'] = $reg["Alias"];
				$html .= "<div id='p-".$con."' class=item onclick=buscadorAccionItem('p-".$con."'); > ".$reg["Nombre"]." ".$reg["Descripcion"]."</div>";
			}
            // vd($countcolumn);			
		    WE($html);
			
		break;
    }		
}

function VinculacionUsuario($codigo){
	global $cnPDO, $FechaHora,$Usuario, $Entidad;
	
	#ADMIN ACCIONES
	$data = array(
	'Nombre' => post('Nombre'),
	'Descripcion' => post('Descripcion'),
	'Email' => post('Email'),
	'Entidades_Suscriptor' => $Entidad,
	'Entidades' => $codigo,
	'Telefono' => post('Telefono'),
	'Perfiles' => post('Tipo'),
	'Foto' => post('Foto'),
	'FechaHoraCreacion' => $FechaHora,
	'FechaHoraActualizacion' => $FechaHora,
	'UsuarioCreacion' => $Usuario,
	'UsuarioActualizacion' => $Usuario,
	);
	$rg = OwlPDO::insert('usuarios', $data, $cnPDO);	
	
}

function ActualizaFoto(){
	global $cnPDO, $FechaHora,$Usuario, $Entidad;	
	$reg = array('Foto' => post('Foto'));
	$where = array('Codigo' =>  get('CodRegistro'));
	$rg = OwlPDO::update('entidades', $reg , $where, $cnPDO);
			

}
?>
