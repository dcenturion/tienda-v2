<?php
session_start();
function html($s){
	if(get("solicitud") != ""){
		$s = documentoHtmlAdmin($s);	
	}
	return $s;	
}	

function htmlApp($s){
	if(get("solicitud") != ""){
		$s = documentoHtmlApp($s);	
	}
	return $s;	
}	


function datosEntidad($EntidadGet,$cnPDO){
	
	$Query=" SELECT  ImagenLogo
	,ColorMenuHorizontal
	,ColorMenuHorizontal_Boton
	,ColorMenuVertical
	,ColorMenuVerticalBoton
	,ColorBotonesInternos 
	,Codigo AS CodEntidad 
	FROM entidades WHERE SubDominio = :SubDominio ";
	$rg = OwlPDO::fetchObj($Query, ["SubDominio" => $EntidadGet ] ,$cnPDO);
	return $rg;
	
}

function datosAlmacenMovimientos($cod_mov_almacen,$cnPDO){
	
	$sql = "SELECT 
	AR.Codigo 
	,AR.TipoArticulos
	,AR.Capitulos
	,AR.NotaImportante AS MasInformacion
	,MA.InformesInscripciones AS Horarios
	,MA.Detalles AS Lugar
	,MA.Referencias AS Requisitos
	,MA.FechaInicio 
	,MA.CodigoPlataformaEducativa 
	FROM  articulos AR
	INNER JOIN movimiento_almacen MA ON AR.Codigo = MA.Articulo
	WHERE MA.Codigo = :Codigo
	GROUP BY MA.Codigo
	";    
    $rg = OwlPDO::fetchObj($sql, ["Codigo" => $cod_mov_almacen] ,$cnPDO);
	return $rg;
	
}


function crear_usuario($cliente, $cnPDO, $FechaHora){
	
	$Query = " 
		SELECT 
		Nombre, Email, Telefono, NroDocumento, Entidad, Descripcion
		FROM clientes 
		WHERE 
		Codigo = :Codigo 
	";
	$where = ["Codigo"=>$cliente];	
	$rg = OwlPDO::fetchObj($Query, $where ,$cnPDO);
	$Nombre = $rg->Nombre;	
	$Email = $rg->Email;	
	$Telefono = $rg->Telefono;	
	$NroDocumento = $rg->NroDocumento;	
	$Entidad = $rg->Entidad;	
	$Descripcion = $rg->Descripcion;	
	$Clave = post("password");
	$ClaveBK = post("password");
	if(empty($Clave)){
		$Clave = $Telefono;
		$ClaveBK = $Telefono;
	}
    $Clave =  _crypt($Clave);
	$data = array(
	'Email' => $Email
	, 'Nombre' => $Nombre
	, 'Descripcion' => $Descripcion	
	, 'Telefono' => $Telefono	
	, 'Usuario' => $Email
	, 'UsusriosAlterno' => $Email
	, 'Clave' => $Clave
	, 'Tipo' => "2"
	, 'FechaHoraCreacion' => $FechaHora
	, 'FechaHoraActualizacion' => $FechaHora
	, 'UsuarioCreacion' => $Entidad
	, 'UsuarioActualizacion' => $Entidad
	);
	$rg = OwlPDO::insert('entidades', $data, $cnPDO);
	$EntidadUser = $rg['lastInsertId']; 
	
	$data = array(
	  'Email' => $Email
	, 'Nombre' => $Nombre
	, 'Descripcion' => $Descripcion
	, 'Telefono' => $Telefono
	, 'FechaHoraCreacion' => $FechaHora
	, 'FechaHoraActualizacion' => $FechaHora
	, 'UsuarioCreacion' => $Entidad
	, 'UsuarioActualizacion' => $Entidad
	, 'Entidades_suscriptor' => $Entidad
	, 'Entidades' => $EntidadUser
	, 'Clientes' => $cliente
	);
	$rg = OwlPDO::insert('usuarios', $data, $cnPDO);
	$Codigo_Usuario = $rg['lastInsertId']; 	
	
	$_SESSION['usuario_entidad']['string'] = $Codigo_Usuario;
	

	$Query=" SELECT 
	Codigo, ImagenLogo, ColorCabeceraEmail
	, ColorCuerpoEmail, ColorFondoEmail, TextoEmailInscripcion, TextoEmailCompra
	, EmailSoporteCliente
	, NroTelefonoSoporteCliente
	, SubDominio
	, ColorMenuHorizontal
	FROM entidades WHERE Codigo = :Codigo  ";
	$rg = OwlPDO::fetchObj($Query, ["Codigo" => $Entidad] ,$cnPDO);
	
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
			<p> ".$TextoEmailInscripcion ." </p>	
			<br>
			<p><b>DATOS DE INGRESO</b></p>
			<br>		
			<p style=''>USUARIO: ".$Email."</p>
			<p style=''>CONTRASEÑA: ".$ClaveBK."</p>   
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
				
	$NombreReceptor = "BIENVENIDO";				
	$Asunto = "PLATAFORMA DE VENTAS ONLINE";				
	$data = array('Cabecera' => $Cabecera ,'Cuerpo'=> $Cuerpo
	               , 'ColorFondo' => $ColorFondoEmail, 'Footer' => $Footer
	               , 'NombreReceptor' => $NombreReceptor, 'Asunto' => $Asunto
				   );			
	emailInscripcion($data,$Email,$cnPDO);

		
}

function login($username,$password,$CodEntidad,$cnPDO){
	
	$password =  _crypt($password);	
	$Query = " SELECT USU.Codigo,USU.Entidades_Suscriptor, ENT.Usuario, ENT.KeySuscripcionProEducative FROM usuarios USU 
	INNER JOIN entidades ENT ON USU.Entidades = ENT.Codigo
	WHERE ENT.UsusriosAlterno = :UsusriosAlterno AND ENT.Clave = :Clave AND USU.Entidades_Suscriptor = :Entidades_Suscriptor ";

	$where = ["UsusriosAlterno"=>$username,"Clave"=>$password, "Entidades_Suscriptor"=>$CodEntidad];	
	$rg = OwlPDO::fetchObj($Query, $where ,$cnPDO);		
	$_SESSION['usuario_entidad']['string'] = $rg->Codigo;
    return $rg;	
} 

function emailInscripcion($data,$emailDestino,$cnPDO){
				// var_dump($data);
	$Nombre_vendedor =$data["NombreReceptor"];
	$emailDestino = "defs.centurion@gmail.com";
	$EmailEmisor  = "soporte@proeducative.org";
	$asunto = $data["Asunto"];

	$body = "
	<div style='padding:50px;background-color:".$data["ColorFondo"].";width:840px;' >
	
		<table style='width:100%;' >
			<tr style='background-color:'>
				<td>
				".$data["Cabecera"]."
				</td>
			</tr>
			<tr>
				<td>
				".$data["Cuerpo"]."					
				</td>
			</tr>
			<tr>
				<td>
				".$data["Footer"]."		
				</td>
			</tr>		
		</table>
		
	</div>
	
	";
	emailSES3($Nombre_vendedor, $emailDestino, $asunto, $body, '','', 'EguruClub', $EmailEmisor);
}

?>