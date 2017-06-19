<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_configuracion.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_configuracion.php";
$enlacePopup = "se_configuracion.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
$_SESSION['menuVertical']= "4";

if (get('Main') != '') {Main(get('Main'));}
if (get('Busqueda') != '') {Busqueda(get('Busqueda'));}

if (get("metodo") != "") {
        
    function p_interno($codigo, $campo) {
		global $FechaHora,$Usuario, $Entidad;
        
        if (get("metodo") == "FEntidades") {
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
            } elseif ($campo == "Tipo") {
                $valor = 2;					
            } else {
                $valor = "";
            }
            return $valor;
        }
		
        if (get("metodo") == "FUsuarios") {
			
            if ($campo == "UsuarioActualizacion") {
                 $valor = $Usuario;
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";
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

            if (get("metodo") == "FUsuarios") {
                p_gf_udp("FUsuarios", $cnPDO, get("CodRegistro"),'Codigo');
				ActualizaFoto();
				
				W(" <script> \$popupEC.close();</script>");				
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Main("Principal");
				WE("");
            }
            if (get("metodo") == "FDatosEmpresa") {
                p_gf_udp("FDatosEmpresa", $cnPDO, get("EntidadCod"),'Codigo');			
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W($msg);
				Main("Principal");
				WE("");
            }		
		
        }

        if (get("transaccion") == "INSERT") {
			
            if (get("metodo") == "FEntidades") {
				
                p_gf_udp("FEntidades",$cnPDO,'','Codigo');
				
				W(" <script> \$popupEC.close();</script>");
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
	
    switch ($Arg) {

        case 'Principal':
     
			$pestanas = funcion_local_pestanas_A(array("&parm=new]Marca","","","",""));
			
            // $listMn = "<i class='icon-chevron-right'></i> Búsqueda Avanzada  [" .$enlace."?Tienda=CreaArticulosRD[panelOculto[[{";
            // $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 1 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";
            // $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 2 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";

            $btn = "<i class='icon-pencil'></i> Crear Usuario ]" .$enlace."?Main=CrearRegistroRD]panelOculto]]}";
            // $btn .= "<i class='icon-align-justify'></i>  ]SubMenu]{$listMn}]OPCIONES DEL PERFIL]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
			
			$titulo = "<p>CONFIGURACIÓN </p><span>Personaliza el Sitio</span>";
            $btn_tituloP = panelST2017($titulo, $btn, "auto", "TituloAMSG");
			
	
            $titulo = "<p>DATOS PRINCIPALES</p><span>Administración de datos</span>";
            $btn_titulo = panelST2017($titulo,"", "auto", "TituloALMB");
			
			$uRLForm = "Actualizar]" . $enlace . "?metodo=FDatosEmpresa&transaccion=UPDATE&EntidadCod={$Entidad}]panelB]F]}";
			
			$path = array('Foto' => '/_imagenes/usuarios/');
			
			$tSelectD = array(
			'Perfiles' => 'SELECT Codigo,Nombre FROM usuarios_perfiles'
			);
			
			$form = c_form_adp("", $cnPDO, "FDatosEmpresa", "CuadroA", $path, $uRLForm, $Entidad, $tSelectD, 'Codigo');
            $html .= "<div id = 'panelOculto' style='float:left;width:100%;' ></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;background:#fff;padding-bottom:29px;text-aling' >" . $form . "</div>";
            $panel = array(array('PanelA1-A', '100%', $html));
            $Cuerpo = LayoutPage($panel);
			
            $s = layoutV2($btn_tituloP , $pestanas . $btn_titulo . $Cuerpo); 
			  
			WE(htmlApp( $s));	

            break;
		
        case 'CrearRegistroRD':
		
            $CodRegistro = get("CodRegistro");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?Main=CrearRegistro&', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      

        case 'CrearRegistro':
        

				$titulo = "<p>CREAR USUARIOS</p><span>Completa los datos del formulario</span>";
				$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALM");
			
                $titulo = "";
     
	 
                $uRLForm = "Guardar]" . $enlace . "?metodo=FEntidades&transaccion=INSERT]panelB]F]}";
                $uRLForm .= "Cancelar]" . $enlace . "?metodo=FEntidades&transaccion=INSERT]\$popupEC.close()]JSB]]Plomo}";
				
				$path = array('Foto' => '/_imagenes/usuarios/');
				
                $tSelectD = array(
				'Tipo' => 'SELECT Codigo,Nombre FROM usuarios_perfiles'
				);
                $form = c_form_adp($titulo, $cnPDO, "FEntidades", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');
				
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$form = "<div style='float:left;width:100%;padding:0px;' >" . $form . "</div>";
				$html = "<div style='float:left;width:600px;' >" . $btn_titulo . $form . "</div>";
                WE($html);
				
            break;   

        case 'EditarRegistroRD':
		
            $CodRegistro = get("CodRegistro");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?Main=EditarRegistro&CodRegistro={$CodRegistro}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;      

        case 'EditarRegistro':
        
                $CodRegistro = get("CodRegistro");
				$titulo = "<p>EDITAR DATOS DEL USUARIO</p><span>Completa los datos del formulario</span>";
				$btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALM");
			
                $titulo = "";
     
	 
                $uRLForm = "Actualizar]" . $enlace . "?metodo=FUsuarios&transaccion=UPDATE&CodRegistro={$CodRegistro}]panelB]F]}";
                $uRLForm .= "Cancelar]" . $enlace . "?metodo=FUsuarios&transaccion=INSERT]\$popupEC.close()]JSB]]Plomo}";
				
				$path = array('Foto' => '/_imagenes/usuarios/');
				
                $tSelectD = array(
				'Perfiles' => 'SELECT Codigo,Nombre FROM usuarios_perfiles'
				);
				
                $form = c_form_adp($titulo, $cnPDO, "FUsuarios", "CuadroA", $path, $uRLForm, $CodRegistro, $tSelectD, 'Codigo');
				
		        $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
				$form = "<div style='float:left;width:100%;padding:0px;' >" . $form . "</div>";
				$html = "<div style='float:left;width:600px;' >" . $btn_titulo . $form . "</div>";
                WE($html);
				
            break; 			

        case 'EliminaRegistroRD':
            $CodRegistro = get("CodRegistro");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?Tienda=EliminaRegistro&CodRegistro={$CodRegistro}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			
            break;  
			
        case 'EliminaRegistro':
            
			$CodRegistro = get("CodRegistro");	
			
            $btn = "Confirmar ]" .$enlace."?Tienda=EliminaRegistroAccion&CodRegistro={$CodRegistro}]panelB]]}";
            $btn .= "<div onclick=\$popupEC.close(); >Cancelar</div> ]" .$enlace."?Tienda=CreaArticulos]PanelA1-B]]plomo}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR </p><span>El artículo seleccionado</span>";
            $btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALM");
			
			WE($btn_titulo);
            break;  		
        case 'EliminaRegistroAccion':
		
            $CodRegistro = get("CodRegistro");
			
		    $Query=" SELECT Entidades FROM usuarios WHERE Codigo = :CodRegistro ";
			$rg = OwlPDO::fetchObj($Query, ["CodRegistro" => $CodRegistro] ,$cnPDO);
		    $Entidades = $rg->Entidades;			
					
	        DReg("usuarios", "Codigo", $CodRegistro, $cnOld);	
	        DReg("entidades", "Codigo",$Entidades, $cnOld);	
			
			W(" <script> \$popupEC.close();</script>");
			Tienda("Site");
			WE("");
            break; 	

        case 'Temas':
		
		
     
			$pestanas = funcion_local_pestanas_A(array("&parm=new]","&parm=new]Marca","]"));
			
			$titulo = "<p>CONFIGURACIÓN </p><span></span>";
            $btn_tituloP = panelST2017($titulo,"", "auto", "TituloAMSG");
			
            $btn = "<i class='icon-pencil'></i> Editar Pantone  ]" .$enlace."?Main=Temas&Edit=pantone]panelB]]}";
            $btn = Botones($btn, 'botones1', 'sys_form');
				
            $titulo = "<p>PANTONE </p><span>Edita los colores de tu empresa</span>";
            $btn_titulo = panelST2017($titulo, $btn , "auto", "TituloALMB");
			
		    $Query=" SELECT Nombre FROM entidades_color WHERE Entidad = :Entidad AND Estado = :Estado ";
			$rg = OwlPDO::fetchObj($Query, ["Entidad" => $Entidad, "Estado"=>"Aprobado"] ,$cnPDO);
		    $Nombre = $rg->Nombre;			
		  
			if(!empty($Nombre)){
				if(get("Edit")=="pantone"){
					// W("SS");
			        $htmlNativo = htmlNativo();						
				}else{
				    $htmlNativo = htmlPantone();
				}
			}else{
			    $htmlNativo = htmlNativo();				
			}
	
            $html .= "<div id = 'panelOculto' style='float:left;width:100%;'></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;background:#fff;padding-bottom:29px;' >" . $htmlNativo . "</div>";
            $panel = array(array('PanelA1-A', '100%', $html));
            $Cuerpo = LayoutPage($panel);
			
            $s = layoutV2($btn_tituloP , $pestanas . $btn_titulo . $Cuerpo); 
			  
			WE(htmlApp($s));	

            break;
			
        case 'Pantonera':
		
				$where = array('Entidad' => $Entidad,'Estado'=>'pendiente');
				$rg = OwlPDO::delet('entidades_color', $where, $cnPDO);		

		        $pallete = get("pallete");
		        $color = get("color");

				$listPallete = json_decode($pallete);
				$colorArray = json_decode($color);
				
				$ccolorA = "rgb(";
				foreach($colorArray as $colorItem)
				{
					$ccolorA .= $colorItem.',';
				}
				$ccolorA .= ")";	
				$ccolorA = ereg_replace(",)", ")", $ccolorA);				
			
			    $data = array(
					'Nombre' => $ccolorA,
					'Entidad' => $Entidad,
					'Tipo' => "Primario",
					'Estado' => "pendiente",
					
					);
				$rg = OwlPDO::insert('entidades_color', $data, $cnPDO);					
		
                
				foreach($listPallete as $pallete)
				{
					$ccolor = "rgb(";
					foreach($pallete as $item)
					{
						$ccolor .= $item.',';
					}
                    $ccolor .= ")";	
					$ccolor = ereg_replace(",)", ")", $ccolor);
						
					$data = array(
					'Nombre' => $ccolor,
					'Entidad' => $Entidad,
					'Estado' => "pendiente",
					'Tipo' => "Secundario",
					
					);
					$rg = OwlPDO::insert('entidades_color', $data, $cnPDO);						
 
				}
		        
				WE(" ");	
			
            break;
			
        case 'GuardarPanton':
		
				$where = array('Entidad' => $Entidad,'Estado'=>'Aprobado');
				$rg = OwlPDO::delet('entidades_color', $where, $cnPDO);		

				$reg = array('Estado' => "Aprobado");
				$where = array('Entidad' => $Entidad);
				$rg = OwlPDO::update('entidades_color', $reg , $where, $cnPDO);

            break;
			
        default:
            exit;
            break;
    }
}


function htmlPantone(){
    global $Entidad, $cnPDO;
	
	$Query=" SELECT Nombre FROM entidades_color WHERE Entidad = :Entidad AND Tipo = :Tipo AND Estado = :Estado ";
	$rg = OwlPDO::fetchObj($Query, ["Entidad" => $Entidad , "Tipo" => "Primario" ,  "Estado" => "Aprobado" ] ,$cnPDO);
	$codigoColor = $rg->Nombre;			
	
	$s= " <div class='Pantone'>
	
	        <div class='row'>
				<div class='Titulo'>
				    COLOR PRINCIPAL DEL PANTONE
				</div>	
				<div class='Color' style='background-color:".$codigoColor."'>
					
				</div>					
			</div>
			
	        <div class='row'>
				<div class='Titulo2'>
				    COLORES SECUNDARIOS DEL PANTONE
				</div>	
				<div class='Colores'>  ";
				
				$Query=" SELECT Nombre FROM entidades_color WHERE Entidad = :Entidad AND Tipo = :Tipo AND Estado = :Estado ";
				$rg = OwlPDO::fetchAllObj($Query, ["Entidad" => $Entidad , "Tipo" => "Secundario",  "Estado" => "Aprobado" ] ,$cnPDO);				
					foreach ($rg as $key) {
						
						$s .= "<div class='item' style='background-color:".$key->Nombre."'></div>";
						
					}					
					
		$s .=   "</div>					
			</div>			
			
	    </div>";
    return $s;		
}	

function htmlNativo(){
	
  $s =  '
			<script>
			$.getScript("/system/_librerias/color-thief-master/src/color-thief.js");
			$.getScript("/system/_librerias/color-thief-master/examples/js/mustache.js");
			$.getScript("/system/_librerias/color-thief-master/examples/js/demo.js");
			
			</script>
		';
		
  $s .= '
  
  <section id="drag-drop" class="drag-drop-section">
    <div class="container">
      <div id="drop-zone" class="drop-zone">
        <div class="drop-zone-label default-label">Drag an image here</div>
        <div class="drop-zone-label dragging-label">Drop it!</div>
      </div>
      <div id="dragged-images" class="dragged-images"></div>
    </div>
  </section>
  
  <!-- Mustache templates -->
  <script id="image-section-template" type="text/x-mustache">
    {{#images}}
    <div class="image-section {{class}}">
		  <div class="image-wrap">
			<div class="imagen">
				<button class="run-functions-button">
				  <span class="no-touch-label">Click</span>
				  <span class="touch-label">Tap</span>
				</button>
				<img class="target-image" src="{{file}}" />
			</div>	
			<div class="imagen_titulo" >	
				<p>Pantonera sugerida</p>
				<div>
				<a onclick=enviaVistaBT("./_vistas/se_configuracion.php?Main=GuardarPanton","panelOculto","");> Guardar Colores </a>
				</div>
			</div>	
			
		  </div>
        <div class="color-thief-output"></div>
    </div>
    {{/images}}
  </script>

  <script id="color-thief-output-template" type="text/x-mustache">
    <div class="function get-color">
      <h3 class="function-title">Dominant Color</h3>
      <div class="swatches">
        <div class="swatch" style="background-color: rgb({{color.0}}, {{color.1}}, {{color.2}})"></div>
      </div>
      <div class="function-code">
        <code>colorThief.getColor(image):{{elapsedTimeForGetColor}}ms</code>
      </div>
    </div>
    <div class="function get-palette">
      <h3 class="function-title">Palette</h3>
      <div class="function-output">
        <div class="swatches">
          {{#palette}}
            <div class="swatch" style="background-color: rgb({{0}}, {{1}}, {{2}})"></div>
          {{/palette}}
        </div>
      </div>
      <div class="function-code">
        <code>colorThief.getPalette(image):{{elapsedTimeForGetPalette}}ms</code>
      </div>
    </div>
  </script> ';
  return $s;
}

function Busqueda($arg){
    global $vConex,$cnPDO,$enlace,$urlEmpresa,$idEmpresa,$Entidad, $EntidadPersona,$Codigo_Entidad_Usuario,$enterprise_user,$FechaHora;

    switch ($arg) {
     
        case 'Busqueda':	

		    WE("Busqueda Desactivada");
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


?>
