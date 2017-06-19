<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once('funciones_se_usuarios.php');
if(get("solicitud") != ""){
    require_once('../_librerias/disenoVisual/layout.php');
}
require_once('../_librerias/php/funcionesAdd.php');

$enlace = "./_vistas/se_resgistrate.php";
$enlacePopup = "se_resgistrate.php";

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
				
				// $arr_clientes = array('nombre'=> 'Jose', 'edad'=> '20', 'genero'=> 'masculino',
				// 'email'=> 'correodejose@dominio.com', 'localidad'=> 'Madrid', 'telefono'=> '91000000');


				//Creamos el JSON
				// $json_string = json_encode($arr_clientes);
				// $file = '/clientes.json';
				// file_put_contents($file, $json_string);
				
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

	$sesionId = session_id();
			
    switch ($Arg) {

        case 'ActualizaUsuario':
		
			$CodigoUsuario = get("CodigoUsuario");	
			$reg = array(
			'Nombre' => post("name"),
			'Descripcion' => post("lastname"),
			'Telefono' => post("phone"),
			'Email' => post("email")
			);
			$where = array('Codigo' => $CodigoUsuario );
			$rg = OwlPDO::update('usuarios', $reg , $where, $cnPDO);
				
				
		    $Query=" SELECT Codigo,Clientes FROM usuarios WHERE 	Codigo = :Codigo  ";
			$rg = OwlPDO::fetchObj($Query, ["Codigo" => $CodigoUsuario] ,$cnPDO);
		    $CodigoCliente = $rg->Clientes;	
			
			$reg = array(
			'Nombre' => post("name"),
			'Descripcion' => post("lastname"),
			'Telefono' => post("phone"),
			'Email' => post("email")
			);
			$where = array('Codigo' => $CodigoCliente );
			$rg = OwlPDO::update('clientes', $reg , $where, $cnPDO);
								
				
		    WE("TRUE");
			
		break;

        case 'CerrarPedido':
			
			//Actualiza Datos del cliente
			$pedido = get("pedido");	
			$estado = "Cerrado";
			procesoCambiaEstadoProformasPedidos($pedido,$estado);
			
		    WE("TRUE");
			
		break;	
	
        case 'BorrarItem':
			
			$codigoItem = get("codigoItem");	
			$CodigoPedido = get("CodigoPedido");	
			
			$Query = " 
			SELECT PFD.Precio, PFD.Cantidad, PFC.TotalBruto, PFC.Moneda, AR.Nombre, PFD.Codigo AS CodigoProformaDet, PFD.Total
			FROM proformas_det PFD
			INNER JOIN  proformas_cab PFC ON  PFD.Proformas_Cab = PFC.Codigo
			INNER JOIN movimiento_almacen MA ON MA.Codigo = PFD.Movimiento_Almacen
			INNER JOIN  articulos AR ON AR.Codigo = MA.Articulo
			INNER JOIN clientes CL ON PFC.Clientes = CL.Codigo
			WHERE PFD.Movimiento_Almacen = :Movimiento_Almacen AND PFC.Codigo = :CodigoPedido
			";
			$where = [ "Movimiento_Almacen"=>$codigoItem,"CodigoPedido" =>$CodigoPedido];	
			$regDataB = OwlPDO::fetchArr($Query,$where,$cnPDO);
            $CodigoProformaDet = $regDataB["CodigoProformaDet"];
            $TotalBruto = $regDataB["TotalBruto"];
            $TotalMontoArticulo = $regDataB["Total"];
			
			$nuevoValorMonto = $TotalBruto - $TotalMontoArticulo;
			
		
			$reg = array('TotalBruto' => $nuevoValorMonto );
			$where = array('Codigo' => $CodigoPedido );
			$rg = OwlPDO::update('proformas_cab', $reg , $where, $cnPDO);
					
		
			$where = array('Codigo' => $CodigoProformaDet);
			$rg = OwlPDO::delet('proformas_det', $where, $cnPDO); 
			
		    WE("El Item fue borrato correctamente " );
		break;
        case 'DetallePedido':
		    
			$entidad = get("entidad");
			$Codigo_Cliente = get("Codigo_Cliente");
	
			if(empty($Codigo_Cliente)){
	
				$Query = " 
				SELECT Nombre, Email, Codigo, Descripcion, Telefono
				FROM clientes
				WHERE SesionId = :sesionId
				";
				$where = ["sesionId"=>$sesionId];	
				$rg = OwlPDO::fetchObj($Query, $where ,$cnPDO);
			    $Codigo_Cliente = $rg->Codigo;	
			}
				
			$Query = " 
			SELECT PFD.Precio, PFD.Cantidad, PFC.TotalBruto, PFC.Moneda, AR.Nombre, PFD.Proformas_Cab 
			FROM proformas_det PFD
			INNER JOIN  proformas_cab PFC ON  PFD.Proformas_Cab = PFC.Codigo
			INNER JOIN movimiento_almacen MA ON MA.Codigo = PFD.Movimiento_Almacen
			INNER JOIN  articulos AR ON AR.Codigo = MA.Articulo
			INNER JOIN clientes CL ON PFC.Clientes = CL.Codigo
			WHERE CL.Codigo = :Codigo_Cliente AND PFC.Estado = :Estado
			";
			$where = ["Codigo_Cliente"=>$Codigo_Cliente,"Estado"=>"Pendiente"];	
			$regData = OwlPDO::fetchAllArr($Query,$where,$cnPDO);

			
            $html = '                
					<li class="dropdown-header text-uppercase">Productos añadidos</li>
					';
					foreach ($regData as $reg){
						
						$TotalBruto = $reg["TotalBruto"];
						$Moneda = $reg["Moneda"];
						$Proformas_Cab = $reg["Proformas_Cab"];
						if($Moneda == "Soles"){
							$simbolo = "S/. ";
						}else{
							$simbolo = "$ ";				
						}						
						
		    $html .= '<li>
						<a href="javascript:null">
							<span>'.$reg["Nombre"].'</span>
							<br>
							<em>('.$reg["Cantidad"].') x  '.$simbolo . $reg["Precio"].'</em>
						</a>
					</li>';
					
					}
					
			$html .= '
					<li role="separator" class="divider"></li>
					<li>
						<a href="javascript:null"><strong> Total: '. $simbolo .$TotalBruto.' </strong></a>
					</li>
					<li role="separator" class="divider"></li>
					<li>
						<a href="/carrito-compras/entidad/'.$entidad.'/pedido/'.$Proformas_Cab.'" class="text-uppercase"><strong>Ver carrito de compras</strong></a>
					</li>
					<li>
						<a href="/informacion-pago/entidad/'.$entidad.'/pedido/'.$Proformas_Cab.'" class="text-uppercase"><strong>Ir a pagar</strong></a>
					</li>
						';
			WE($html);
			
            break; 	
			
        case 'CapturaPedido':
		
            $idProducto = get("idProducto");
            $entidad = get("entidad");
            $ListaPreferencias = get("ListaPreferencias");
			if(!empty($ListaPreferencias)){
				
				$Query=" SELECT Codigo FROM movimiento_almacen WHERE AliasId = :AliasId ";
				$rg = OwlPDO::fetchObj($Query, ["AliasId" => $idProducto ] ,$cnPDO);
				$Codigo_movimiento_almacen = $rg->Codigo;	
				
				$where = array('Movimiento_Almacen' => $Codigo_movimiento_almacen);
				$rg = OwlPDO::delet('lista_producto_preferido', $where, $cnPDO); 
				
			}

		    $Query=" SELECT Codigo FROM clientes WHERE 	SesionId = :SesionId AND TipoCliente = :TipoCliente  ";
			$rg = OwlPDO::fetchObj($Query, ["SesionId" => $sesionId,"TipoCliente"=>"Prospecto"] ,$cnPDO);
		    $CodigoCliente = $rg->Codigo;	
			
		    $Query=" SELECT Codigo FROM entidades WHERE SubDominio = :SubDominio  ";
			$rg = OwlPDO::fetchObj($Query, ["SubDominio" => $entidad] ,$cnPDO);
		    $CodigoEntidad = $rg->Codigo;
			
			$info=detectClient();
			
			$usuario_entidad = $_SESSION['usuario_entidad']['string'];
	
		
            if($CodigoCliente == "" &&  $usuario_entidad == ""){
			
				
				$ipvisitante = $_SERVER["REMOTE_ADDR"];
				$tipoDispositivo = tipoDispositivo();
				$data = array(
				'SesionId' => $sesionId
				, 'IpRegistro' => $ipvisitante
				, 'DispositivoRegistro' => $tipoDispositivo
				, 'Navegador' => $info["browser"]
				, 'VersionNavegador' => $info["version"]
				, 'SistemaOperativo' => $info["os"]
				, 'TipoCliente' => "Prospecto"
				, 'Entidad' => $CodigoEntidad
				, 'FechaHoraCreacion' => $FechaHora
				);
				$rg = OwlPDO::insert('clientes', $data, $cnPDO);
				$CodigoCliente = $rg['lastInsertId']; 
				
				
			}else{

				if(!empty($usuario_entidad)){
					
					$Query=" SELECT Clientes FROM usuarios WHERE Codigo = :Codigo  ";
					$rg = OwlPDO::fetchObj($Query, ["Codigo" => $usuario_entidad] ,$cnPDO);
					$CodigoCliente = $rg->Clientes;
					
					$reg = array('FechaHoraActualizacion' => $FechaHora, 'TipoCliente' => 'Prospecto');
					$where = array('Codigo' => $CodigoCliente );
					$rg = OwlPDO::update('clientes', $reg , $where, $cnPDO);
					
				}else{
					
					$reg = array('FechaHoraActualizacion' => $FechaHora);
					$where = array('SesionId' => $sesionId );
					$rg = OwlPDO::update('clientes', $reg , $where, $cnPDO);
				
				}
				

			}			

			//Detalles del producto		
			$Query = " SELECT 
			AR.Nombre, MA.AliasId ,AR.Precio ,AR.Moneda ,MA.Codigo 
			FROM articulos AR
			INNER JOIN movimiento_almacen MA ON AR.Codigo = MA.Articulo
			WHERE  MA.AliasId = :AliasId  ORDER BY AR.FechaHoraCreacion DESC ";
			$where = ["AliasId"=>$idProducto,];	
			$rgART = OwlPDO::fetchObj($Query, $where ,$cnPDO);
			$Precio = $rgART->Precio;			   
			$Codigo_movimiento_almacen = $rgART->Codigo;			   
			$Moneda = $rgART->Moneda;		
			
			///Crea la cabezera de la proforma
			// W("CodigoCliente:: ".$CodigoCliente);
			$Query = "SELECT 
			PC.Codigo, CL.TipoCliente, PC.TotalNeto, PC.TotalBruto
			FROM proformas_cab PC
			INNER JOIN clientes CL ON PC.Clientes = CL.Codigo
			WHERE 
			CL.Codigo = :CodigoCliente AND ( CL.TipoCliente = :TipoCliente OR CL.TipoCliente = :TipoClienteB) AND PC.Estado = :Estado
			";
			$where = ["CodigoCliente"=>$CodigoCliente,"TipoCliente"=>"Prospecto","TipoClienteB"=>"Potencial", "Estado"=>"Pendiente" ];	
			$rg = OwlPDO::fetchObj($Query, $where ,$cnPDO);
			$CodigoProformasCab = $rg->Codigo;	
			$TotalNeto = $rg->TotalNeto;	
			$TotalBruto = $rg->TotalBruto;	
				
			if(empty($CodigoProformasCab)){
				
				$data = array(
				'Clientes' => $CodigoCliente
				, 'Entidad' => $CodigoEntidad
				, 'Estado' => "Pendiente"
				, 'Moneda' => $Moneda
				, 'TipoComprobante' => "VouElectronico"
				, 'TotalNeto' => $Precio
				, 'TotalBruto' => $Precio
				, 'FechaHoraCreacion' => $FechaHora
				, 'FechaHoraActualizacion' => $FechaHora
				, 'UsuarioCreacion' => $CodigoEntidad
				, 'UsuarioActualizacion' => $CodigoEntidad
				, 'OrigenGeneracion' => "SiteCliente"
				);
				$rg = OwlPDO::insert('proformas_cab', $data, $cnPDO);
				$CodigoProformasCab = $rg['lastInsertId']; 
				
			}else{
				
				$reg = array(
					'FechaHoraActualizacion' => $FechaHora
					, 'UsuarioActualizacion' => $CodigoEntidad				
					, 'TotalNeto' => $TotalNeto + $Precio
					, 'TotalBruto' => $TotalNeto + $Precio		
					
				);
				$where = array('Codigo' => $CodigoProformasCab );
				$rg = OwlPDO::update('proformas_cab', $reg , $where, $cnPDO);
			}				
            
			
			$Query = " 
				SELECT 
				PFD.Codigo
				FROM proformas_det PFD
				WHERE 
				PFD.Proformas_Cab = :Proformas_Cab AND PFD.Movimiento_Almacen = :Movimiento_Almacen 
			";
			$where = ["Proformas_Cab"=>$CodigoProformasCab,"Movimiento_Almacen"=>$Codigo_movimiento_almacen];	
			$rg = OwlPDO::fetchObj($Query, $where ,$cnPDO);
			$CodigoProfromasDet = $rg->Codigo;	
	    			
	        if(empty($CodigoProfromasDet)){
				
				$data = array(
				'Proformas_Cab' => $CodigoProformasCab
				, 'Entidad' => $CodigoEntidad
				, 'Movimiento_Almacen' => $Codigo_movimiento_almacen
				, 'Cantidad' => 1
				, 'Precio' => $Precio
				, 'Total' => 1 * $Precio
				, 'FechaHoraCreacion' => $FechaHora
				, 'FechaHoraActualizacion' => $FechaHora
				, 'UsuarioCreacion' => $CodigoEntidad
				, 'UsuarioActualizacion' => $CodigoEntidad
				);
				$rg = OwlPDO::insert('proformas_det', $data, $cnPDO);	
				$msjP = "Se añadió un producto al carrito";				
			}else{
				$msjP = "El producto ya existe en el carrito";
			}
	
		    $Query = " 
				SELECT 
				count(Cantidad) AS TotReg
				FROM proformas_det PFD
				WHERE 
				PFD.Proformas_Cab = :Proformas_Cab  
			";
			$where = ["Proformas_Cab"=>$CodigoProformasCab];	
			$rg = OwlPDO::fetchObj($Query, $where ,$cnPDO);
			$TotReg = $rg->TotReg;
			
			$nro_articulos = $TotReg;			
			$arr_resultado = array("msj"=>$msjP,"nro_articulos"=>$nro_articulos,"codPedido"=>$CodigoProformasCab);	
		
			
			$json_string = json_encode($arr_resultado);
			WE($json_string);
            break; 	
    }
}


function procesoCambiaEstadoProformasPedidos($pedido,$estado){
	global $cnPDO,$FechaHora;
	
	$Query = " 
	SELECT PFC.Clientes, PFC.Entidad , PFC.Codigo AS CodigoProformaCab, PFC.Estado
	FROM proformas_det PFD
	INNER JOIN  proformas_cab PFC ON  PFD.Proformas_Cab = PFC.Codigo
	INNER JOIN clientes CL ON PFC.Clientes = CL.Codigo
	WHERE PFC.Codigo = :Codigo 
	";
	$where = ["Codigo"=>$pedido];	
	$regDataB = OwlPDO::fetchArr($Query,$where,$cnPDO);
	$CodigoProformaCab = $regDataB["CodigoProformaCab"];			
	$Clientes = $regDataB["Clientes"];	
	$Estado = $regDataB["Estado"];	
	$Entidad = $regDataB["Entidad"];	


	//Valida si el cliente prospecto ya existe
	$Query = " 
	SELECT Nombre, Email, Codigo, Descripcion, Telefono
	FROM clientes
	WHERE  Entidad = :Entidad  AND Email =:Email
	";
	$where = ["Entidad"=>$Entidad,"Email"=>post("email")];	
	$rg = OwlPDO::fetchObj($Query, $where ,$cnPDO);
    $codigoClienteExistente = $rg->Codigo;
	
	if(empty($codigoClienteExistente)){
	    $Clientes = $Clientes;
		$creaUsuario = true;
	}else{		
		$sesionUsuarioEntidad = $_SESSION['usuario_entidad']['string'];
		if(empty($sesionUsuarioEntidad)){
			$where = array('Codigo' => $Clientes);
			$rg = OwlPDO::delet('clientes', $where, $cnPDO); 

			$Clientes = $codigoClienteExistente;
			$creaUsuario = false;	

			$Query = " 
			SELECT Codigo
			FROM usuarios
			WHERE  Clientes = :Clientes 
			";
			$where = ["Clientes"=>$Clientes];	
			$rg = OwlPDO::fetchObj($Query, $where ,$cnPDO);
			$Codigo_Usuario = $rg->Codigo;
		
			$_SESSION['usuario_entidad']['string'] = $Codigo_Usuario;			
		}
	}
	
	
	$reg = array(
	'NroDocumento' => post("nroDocumento"),
	'Nombre' => post("nombre"),
	'TipoCliente' => "Potencial",
	'Email' => post("email"),
	'Telefono' => post("telefono"),
	'Calle' => post("street"),
	'Ciudad' => post("city"),
	'Provincia' => post("province"),
	'Pais' => post("country"),
	'SesionId' => ""
	
	);
	$where = array('Codigo' => $Clientes );
	$rg = OwlPDO::update('clientes', $reg , $where, $cnPDO);
    
	if($Estado == "Cerrado"){
		exit();
	}	
	
	///Crear Usuario
	if($creaUsuario){
		crear_usuario($Clientes, $cnPDO, $FechaHora);			
	}		
	
	//Actualiza Datos del cliente en la cabezera de la proforma
	$reg = array(
	'Direccion' => post("street"),
	'Ciudad' => post("city"),
	'Provincia' => post("province"),
	'CodigoPostal' => post("postal-code"),
	'Pais' => post("country"),
	'AceptacionTerminos' => post("terminos"),
	'EnviarMismaDireccionFacturacion' => post("envio"),
	'Estado' => $estado,
	'Clientes' => $Clientes
	);
	$where = array('Codigo' => $CodigoProformaCab );
	$rg = OwlPDO::update('proformas_cab', $reg , $where, $cnPDO);

	//Proceso que llena la tabla pedidos
	$Query = " 
	SELECT 
	PFC.Clientes, 
	PFC.Estado, 
	PFC.Entidad, 
	PFC.Moneda, 
	PFC.TipoComprobante, 
	PFC.TotalNeto, 
	PFC.TotalBruto,
	CL.Email
	FROM proformas_cab PFC 
	INNER JOIN clientes CL ON PFC.Clientes = CL.Codigo
	WHERE PFC.Codigo = :Codigo 
	";
	$where = ["Codigo"=>$pedido];	
	$regDataB = OwlPDO::fetchArr($Query,$where,$cnPDO);
	$Entidad = $regDataB["Entidad"];			
	$Clientes = $regDataB["Clientes"];			
	$Estado = $regDataB["Estado"];			
	$Moneda = $regDataB["Moneda"];			
	$TipoComprobante = $regDataB["TipoComprobante"];			
	$TotalNeto = $regDataB["TotalNeto"];			
	$TotalBruto = $regDataB["TotalBruto"];			
	$Email = $regDataB["Email"];			

	$data = array(
	'Clientes' => $Clientes
	, 'Entidad' => $Entidad
	, 'Estado' => $estado
	, 'Moneda' => $Moneda
	, 'TipoComprobante' => $TipoComprobante
	, 'TotalNeto' => $TotalNeto
	, 'TotalBruto' => $TotalBruto
	, 'FechaHoraCreacion' => $FechaHora
	, 'FechaHoraActualizacion' => $FechaHora
	, 'UsuarioCreacion' => $Entidad
	, 'UsuarioActualizacion' => $Entidad
	, 'OrigenGeneracion' => "SiteCliente"
	, 'Proformas_Cab' => $pedido
	);
	$rg = OwlPDO::insert('pedidos_cab', $data, $cnPDO);
	$CodigoPedidosCab = $rg['lastInsertId']; 
	
	$Query = " 
		SELECT 
		PD.Cantidad,
		PD.Precio, 
		PD.Total, 
		PD.Entidad,
		PD.Movimiento_Almacen,
		AR.Nombre
		FROM proformas_det PD 
		INNER JOIN movimiento_almacen AL ON PD.Movimiento_Almacen = AL.Codigo
		INNER JOIN articulos AR ON AL.Articulo = AR.Codigo
		WHERE PD.Proformas_Cab = :Proformas_Cab 
	";
	$where = ["Proformas_Cab"=>$pedido];	
	$regDataB = OwlPDO::fetchAllArr($Query,$where,$cnPDO);
	
    $DetalleCompra = "";
    $DetalleCompra .= "<table cellpadding='20' width='100%' >";

	    $DetalleCompra .= "<tr style='background-color: #ccc;' >";	
	    $DetalleCompra .= "<th>Artículo</th>";	
	    $DetalleCompra .= "<th>Cantidad</th>";	
	    $DetalleCompra .= "<th>Precio</th>";	
	    $DetalleCompra .= "</tr>";	
		
	    $CantidadArticulos = 0;	
	    $CantidadPrecio = 0;
		
	foreach ($regDataB as $reg){	
	
			$data = array(
			'Pedidos_Cab' => $CodigoPedidosCab
			, 'Entidad' =>  $reg["Entidad"]
			, 'Movimiento_Almacen' => $reg["Movimiento_Almacen"]
			, 'Cantidad' => $reg["Cantidad"]
			, 'Precio' => $reg["Precio"]
			, 'Total' => $reg["Total"]
			, 'FechaHoraCreacion' => $FechaHora
			, 'FechaHoraActualizacion' => $FechaHora
			, 'UsuarioCreacion' => $reg["Entidad"]
			, 'UsuarioActualizacion' => $reg["Entidad"]
			);
			$rg = OwlPDO::insert('pedidos_det', $data, $cnPDO);	
			
	    $DetalleCompra .= "<tr >";	
	    $DetalleCompra .= "<td>".$reg["Nombre"]."</td>";	
	    $DetalleCompra .= "<td>".$reg["Cantidad"]."</td>";	
	    $DetalleCompra .= "<td>".$reg["Precio"]."</td>";	
	    $DetalleCompra .= "</tr>";	
		
		$CantidadArticulos += $reg["Cantidad"];	
		$CantidadPrecio += $reg["Precio"];	
		
	}
	    $DetalleCompra .= "<tr>";	
	    $DetalleCompra .= "<td><b>TOTALES</b></td>";	
	    $DetalleCompra .= "<td><b>".$CantidadArticulos."</b></td>";	
	    $DetalleCompra .= "<td><b>".$CantidadPrecio."</b></td>";	
	    $DetalleCompra .= "</tr>";	
		
    $DetalleCompra .= "</table>";
	
	
	// $Clientes
	$Query=" SELECT Nombre, Descripcion
	FROM Clientes WHERE Codigo = :Codigo  ";
	$rg = OwlPDO::fetchObj($Query, ["Codigo" => $Clientes ] ,$cnPDO);
	$Nombre = $rg->Nombre;	
	$Descripcion = $rg->Descripcion;	
	
	
	$Query=" SELECT 
	Codigo, ImagenLogo, ColorCabeceraEmail
	, ColorCuerpoEmail, ColorFondoEmail, TextoEmailInscripcion, TextoEmailCompra
	, EmailSoporteCliente
	, NroTelefonoSoporteCliente
	, SubDominio
	, ColorMenuHorizontal
	FROM entidades WHERE Codigo = :Codigo  ";
	$rg = OwlPDO::fetchObj($Query, ["Codigo" => $Entidad ] ,$cnPDO);
	$CodigoEntidad = $rg->Codigo;			
	$ColorCabeceraEmail = $rg->ColorCabeceraEmail;			
	$ColorCuerpoEmail = $rg->ColorCuerpoEmail;			
	$ColorFondoEmail = $rg->ColorFondoEmail;			
	$ImagenLogo = $rg->ImagenLogo;			
	$TextoEmailInscripcion = $rg->TextoEmailInscripcion;			
	$TextoEmailCompra = $rg->TextoEmailCompra;			
	$EmailSoporteCliente = $rg->EmailSoporteCliente;			
	$NroTelefonoSoporteCliente = $rg->NroTelefonoSoporteCliente;	
	$SubDominio = $rg->SubDominio;	
	$ColorMenuHorizontal = $rg->ColorMenuHorizontal;	
	
	$dominio = siteUrl();
	
	if( $ColorCabeceraEmail == "Ninguno"){  
		$ColorCabeceraEmail = "#fff";
	}

	$Cabecera = "
		<div style='background-color:".$ColorCabeceraEmail.";padding:50px;'>
			<img src='http://fri.com.pe/_imagenes/iconos/logo.png' >
			<br>
			<p style='text-align:right;color:".$ColorMenuHorizontal.";font-weight:bold;'>SISTEMA DE VENTAS ONLINE</p>
		</div>

	";
	
	$Cuerpo = "
		
		<div style='background-color:".$ColorCabeceraEmail.";padding:50px;'>
			<p>Hola ".$Nombre ."  ". $Descripcion .":</p>
			<br>
			<p> ".$TextoEmailCompra ." </p>	
			<br>
			<p><b>DETALLE DE TU COMPRA</b></p>
			<br>		
			<div>".$DetalleCompra."</div>
			  
			<br>
		</div>
		
		<div style='background-color:".$ColorCabeceraEmail.";padding:30px 30px;text-align: center;'>
			<a href='".$dominio."".$SubDominio."'  style='text-decoration: none; background-color: ".$ColorMenuHorizontal."; padding: 10px 20px; color: #fff;'>INGRESAR</a>
			<br>
		</div>				
	
	";
	
	$Footer = "
		<div style='background-color:".$ColorCabeceraEmail.";padding:50px;'>
			<br>
			<p style='color:#8e8d8d;'>Email de Soporte: ".$EmailSoporteCliente."</p>
			<p style='color:#8e8d8d;'>Teléfono de Soporte: ".$NroTelefonoSoporteCliente."</p>
		</div>
		
	";				
	$NombreReceptor = "COMPRAS";				
	$Asunto = "PLATAFORMA DE VENTAS ONLINE";	
	
	$data = array('Cabecera' => $Cabecera ,'Cuerpo'=> $Cuerpo
	               , 'ColorFondo' => $ColorFondoEmail, 'Footer' => $Footer
	               , 'NombreReceptor' => $NombreReceptor, 'Asunto' => $Asunto
				   );			
	emailInscripcion($data,$Email,$cnPDO);
}

?>
