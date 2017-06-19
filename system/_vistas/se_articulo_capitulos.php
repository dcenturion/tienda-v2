<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_tienda.php');

if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_usuarios.php";
$enlacePopup = "se_usuarios.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();
$Usuario = $_SESSION['Usuario']['string'];
$Entidad = $_SESSION['Entidad']['string'];
$_SESSION['menuVertical']= "3";

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
            break;
		
        case 'CrearRegistroRD':
            break;      

        case 'CrearRegistro':
				
            break;   

        case 'EditarRegistroRD':
		
            break;      

        case 'EditarRegistro':
			
				$cod_articulo = get("cod_articulo");
				$cod_mov_almacen = get("cod_mov_almacen");

				$segmentoUrl =  "&cod_articulo=".$cod_articulo."&cod_mov_almacen=".$cod_mov_almacen;

				$Query=" SELECT TipoArticulos FROM articulos  WHERE Codigo = :Codigo ";
				$rg = OwlPDO::fetchObj($Query, ["Codigo" => $cod_articulo] ,$cnPDO);
				$CodigoTipoArticulo = $rg->TipoArticulos;	

				$pestanas = pestanasBLocalEbook(array("".$segmentoUrl."]","".$segmentoUrl."]","".$segmentoUrl."]Marca","".$segmentoUrl."]","".$segmentoUrl."]"));

				$cur = conexionCurPost(PLATAFORMA_EDUCATIVA."_vistas/pruba_d.php?Epub=ListSegmentsbyEntity",[
				'CodArticuloVenta'=> $id,
				'entity'=>$_SESSION['KeySuscripcionProEducative']['string']
				],true,"POST");
			     
                // $form = vd($cur)." ----";
				$form = "<div style='padding:1% 1%;width:98%;float:left;' >".$pestanas .$form."</div>";
				$Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";		
				$html = "<div style='width:40rem;' id='panelFormA1' >" . $Close . $form . "</div>";	
				$html .= "<script>resiSizePopup();</script>";	

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
        default:
            exit;
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
