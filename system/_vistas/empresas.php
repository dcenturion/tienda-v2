<?php
session_start();

require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_usuarios.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/empresas.php";
$enlacePopup = "empresas.php";

$cnPDO = PDOConnection();
$cnOld = conexSys();
$FechaHora = FechaHoraSrv();

$Usuario = $_SESSION['master_access']['string'];
$Entidad = $_SESSION['Entidad']['string'];
$_SESSION['menuVertical']= "3";

if (get('Main') != '') {Main(get('Main'));}
if (get('Busqueda') != '') {Busqueda(get('Busqueda'));}

if (get("metodo") != "") {

    function p_interno($codigo, $campo) {
        global $FechaHora,$Usuario, $Entidad;

        if (get("metodo") == "crea_empresa") {
            if ($campo == "FechaHoraCreacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsuarioCreacion") {
                $valor = $Usuario;
            } elseif ($campo == "UsuarioActualizacion") {
                $valor = $Usuario;
            } elseif ($campo == "FechaHoraActualizacion") {
                $valor = "'" . $FechaHora . "'";
            } elseif ($campo == "UsusriosAlterno") {
                $valor = "'" .post("Usuario"). "'";
            } elseif ($campo == "Clave") {
                $valor = "12345";
            } elseif ($campo == "Tipo") {
                $valor = 1;
            } else {
                $valor = "";
            }
            return $valor;
        }

    }

    function p_before($codigo) {
        global $FechaHora,$Usuario, $Entidad,$cnPDO;
        if (get("transaccion") == "INSERT") {
            if (get("metodo") == "crea_empresa") {
                $lastInsertID=$codigo;
                $nombre=$_POST['Nombre'];
                $Email=$_POST['Email'];
                $UsuarioEntidad=$_POST['Usuario'];
                $SubDominio=$_POST['SubDominio'];
                $Usuario2 = $_SESSION['master_access'];


                $data=[
                    "Nombre"=>"{$nombre}",
                    "Email"=>"{$Email}",
                    "FechaHoraCreacion"=>"{$FechaHora}",
                    "UsuarioCreacion"=>"{$Usuario2}",
                    "Entidades"=>"{$lastInsertID}",
                    "Entidades_suscriptor"=>"{$lastInsertID}",
                    "Perfiles"=>"0"
                    ];

                $insertPDO=OwlPDO::insert('usuarios', $data,$cnPDO);
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

            if (get("metodo") == "crea_empresa") {

                p_gf_udp("crea_empresa",$cnPDO,'','Codigo');

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

            //$pestanas = pestanasLocal(array("&parm=new]Marca","","","",""));

            $listMn = "<i class='icon-chevron-right'></i> Búsqueda Avanzada  [" .$enlace."?Tienda=CreaArticulosRD[panelOculto[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 1 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";
            $listMn .= "<i class='icon-chevron-right'></i> Configuración avanzada 2 [" .$enlace."?Tienda=CreaArticulos[PanelA1-B[[{";

            $btn = "<i class='icon-pencil'></i> Crear  ]" .$enlace."?Main=CrearRegistroRD]panelOculto]]}";
//            $btn .= "<i class='icon-align-justify'></i>  ]SubMenu]{$listMn}]OPCIONES DEL PERFIL]}";
            $btn = Botones($btn, 'botones1', 'sys_form');

            $titulo = "<p>EMPRESAS </p><span></span>";
            $btn_tituloP = panelST2017($titulo, $btn, "auto", "TituloAMSG");


            $titulo = "<p>LISTADO DE EMPRESAS</p><span>Administración de datos</span>";
            $btn_titulo = panelST2017($titulo,"", "auto", "TituloALMB");

            $queryd = get("queryd");
            $queryd = explode(" ", $queryd);;

            $parm1 = $queryd[0];
            $parm2 = $queryd[1];

            if(empty($queryd)){
                $operadorA = "<>";
                $parm1 = "77777777777777777777777777";
                $parm2 = "77777777777777777777777777";
            }else{
                $operadorA = "LIKE";
            }


            $sql = "SELECT Nombre as Empresa
				
				,CONCAT('
					<div>',Email ,' </div>
					<div style=color:#2196F3;font-weight:bold;> ',SubDominio,'</div>
			  
				')AS 'Datos'					
				,  IF(TIPO='1','Empresa','') AS Tipo
				,  UsusriosAlterno AS 'Usuario Alterno'
				,  CONCAT('
					<div  >
					<div class=botIcRepC ><i class=icon-chevron-down ></i> 
						 <ul class=sub_boton >
							
							<li onclick=enviaReg(''EDI',Codigo,''',''{$enlace}?Main=EliminaRegistroRD&CodRegistro=',Codigo,''',''panelOculto'','''');>Eliminar</li>
						 </ul>
					</div>
					</div>
				')AS 'Acción' FROM entidades 
				WHERE Tipo=:Tipo
			";
//            <li onclick=enviaReg(''EDI',Codigo,''',''{$enlace}?Main=EditarRegistroRD&CodRegistro=',Codigo,''',''panelOculto'','''');  >Editar</li>
            $clase = 'reporteA';
            $enlaceCod = 'codigoAlmacen';
            $url = $enlace."?Articulos=EditaArticulos";
            $panel = 'layoutV';

            $where = ["Tipo"=>"1" ];

            $reporte = ListR2('', $sql, $where ,$cnPDO, $clase, '', $url, $enlaceCod, $panel, 'sys_tabla_det_form', '', '','');
            $html .= "<div id = 'panelOculto' style='float:left;width:100%;' ></div>";
            $html .= "<div id = 'PanelInferior' style='float:left;width:100%;' >" . $reporte . "</div>";
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


            $titulo = "<p>CREAR EMPRESA</p><span>Completa los datos del formulario</span>";
            $btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALM");

            $titulo = "";


            $uRLForm = "Guardar]" . $enlace . "?metodo=crea_empresa&transaccion=INSERT]panelB]F]}";
            $uRLForm .= "Cancelar]" . $enlace . "?metodo=crea_empresa&transaccion=INSERT]\$popupEC.close()]JSB]]Plomo}";

            $path = array('Foto' => '/_imagenes/usuarios/');

            $tSelectD = array(
                'Tipo' => 'SELECT Codigo,Nombre FROM usuarios_perfiles'
            );
            $form = c_form_adp($titulo, $cnPDO, "crea_empresa", "CuadroA", $path, $uRLForm, '', $tSelectD, 'Codigo');

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
                        <script> openPopupURI('".$enlacePopup."?Main=EliminaRegistro&CodRegistro={$CodRegistro}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);

            break;

        case 'EliminaRegistro':

            $CodRegistro = get("CodRegistro");

            $btn = "Confirmar ]" .$enlace."?Main=EliminaRegistroAccion&CodRegistro={$CodRegistro}]panelB]]}";
            $btn .= "<div onclick=\$popupEC.close(); >Cancelar</div> ]" .$enlace."?Tienda=CreaArticulos]PanelA1-B]]plomo}";
            $btn = Botones($btn, 'botones1', 'sys_form');
            $titulo = "<p>CONFIRMAR LA ACCIÓN DE ELIMINAR </p><span>El artículo seleccionado</span>";
            $btn_titulo = panelST2017($titulo, $btn, "auto", "TituloALM");

            WE($btn_titulo);
            break;
        case 'EliminaRegistroAccion':

            $CodRegistro = get("CodRegistro");


            OwlPDO::delet("usuarios", ["Entidades_suscriptor"=>$CodRegistro], $cnPDO);
            OwlPDO::delet("entidades", ["Codigo"=>$CodRegistro], $cnPDO);

            W(" <script> \$popupEC.close();</script>");
            Main("Principal");
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
