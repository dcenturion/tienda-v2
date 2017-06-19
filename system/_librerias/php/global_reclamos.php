<?php

function ReclamosControlVistas($CodReclamo,$emisor,$CoResponsable,$movimiento_reclamo,$vConex){

	$sql = "SELECT * FROM reclamos WHERE Codigo = $CodReclamo ";
	$rg = fetch($sql);
	$NombreReclamo = $rg["asunto"];
	$AlmacenPrograma = $rg["Programa_Almacen"];
	$Curso_Almacen = $rg["Curso_Almacen"];

	$sql = "SELECT  usuario_id ,descripcion FROM reclamos_movimientos
		WHERE reclamo_id = ".$CodReclamo."  AND usuario_id <> ".$emisor."
		GROUP BY usuario_id ";

		$consulta = Matris_Datos($sql,$vConex);
		while ($reg =  mysql_fetch_array($consulta)) {
				$sql = " INSERT  INTO reclamos_control_vistas (reclamo_id,Estado,usuario_id,movimiento_reclamo)
				VALUES (".$CodReclamo.",0,".$reg["usuario_id"].",".$movimiento_reclamo.")  ";
				xSQL($sql,$vConex);

				$sql = "SELECT
				U.Codigo,
				P.Descripcion AS Perfi,
				U.EntidadCreadora,
				U.Usuario,
				CONCAT (US.Nombres,'  ',US.Apellidos) AS Nombres
				FROM ((usuario_entidad AS U
				INNER JOIN usuario_perfil  AS P ON U.Perfil = P.Codigo)
				INNER JOIN usuarios  AS US ON U.Usuario = US.Usuario)
				WHERE U.Codigo = ".$reg["usuario_id"]." ";
				$rg = fetch($sql);
				$Nombres = $rg["Nombres"];
				$EntidadCreadora = $rg["EntidadCreadora"];
				$destinatario = $rg["Usuario"];

				$FechaHora = FechaHoraSrv();
				$sql = "SELECT a.RazonSocial,a.Contacto,a.MailContacto,a.Direccion, u.UrlId
				FROM empresa AS a INNER JOIN
				usuarios AS u ON u.IdUsuario = a.PaginaWeb  WHERE PaginaWeb = '".$EntidadCreadora."'  ";
				$rg = fetch($sql);
				$sRasonSocial= $rg["RazonSocial"];
				$sContacto= $rg["Contacto"];
				$sMailContacto= $rg["MailContacto"];
				$sDireccion = $rg["Direccion"];
				$sUrlEmpresa = $rg["UrlId"];

			   /*
				$cabezeraMail = "
					<div style='border-bottom:2px solid #e2e2e2;margin:10px 0px 30px 0px;'></div>
					<div style='padding:10px 3px;'>".$FechaHora."</div>
					<div style='padding:3px 3px;'>".$sContacto."</div>
					<div style='padding:2px 3px;font-size:0.8em;'>".$sMailContacto." </div>
					<div style='font-size:1.5em;color:#6b6b6b;padding:5px 0px 5px 3px;'>PLATAFORMA EDUCATIVA </div>
					<div style='font-size:1.5em;color:#6b6b6b;padding:5px 0px 5px 3px;'>".$sRasonSocial." </div>
					<div >".$sDireccion."</div>
				";

				if($CoResponsable != "" ){
					$cuerpoMail = "
						 <div style='font-size:1.5em;padding:10px 0px 10px 3px;color:#4396de;'>RECLAMO  # -  ".$CodReclamo." FUE TRANSFERIDO</div>
					 <div >Estimado ".$Nombres." este reclamo le fue transferido , revize su bandeja de reclamos </div>
					 <div >ingresando a la Plataforma Educativa : <a href='http://system.org/".$sUrlEmpresa."'>".$sUrlEmpresa." </a></div>
				";

			}else{

				$cuerpoMail = "
					 <div style='font-size:1.5em;padding:10px 0px 10px 3px;color:#4396de;'>RECLAMO  # -  ".$CodReclamo."</div>
					 <div >Este reclamo ha sido contestado, revize su bandeja de reclamos </div>
					 <div >ingresando a la Plataforma Educativa : <a href='http://system.org/".$sUrlEmpresa."'>".$sUrlEmpresa." </a></div>
				";

			}

			$footerMail = "Atentamente ";
			$asunto = "RECLAMO  # -  ".$CodReclamo."";
			$body = LayouMailA($cabezeraMail,$cuerpoMail,$footerMail);
			$emailE = EMail("",$destinatario,$asunto,$body);*/

        ########################################


            $sql = "SELECT US.Usuario AS Email,AL.Coordinador FROM almacen AS AL
							INNER JOIN usuario_entidad AS US ON AL.Coordinador = US.Codigo
							WHERE AlmacenCod = " . $AlmacenPrograma . "  ";
            $rg = fetch($sql);
            $Coordinador = $rg["Email"];

            $sqlA = "SELECT  descripcion FROM reclamos_movimientos
		           WHERE Codigo = ".$movimiento_reclamo." ";
            $rgA = fetch($sqlA);
            $Descripcion = $rgA["descripcion"];

            $DataAnuncio = array(
                "ProductoBase" => $Curso_Almacen,
                "ProgramaAlmacen" => $AlmacenPrograma,
                "TipoProducto" => "Curso",
                "Titulo" => $NombreReclamo,
                "Entidad" => $EntidadCreadora,
                "Profesor" => $Coordinador."Profesor",
                "IdTipoAnuncio" => $movimiento_reclamo,
                "TipoAnuncio" => "Soporte",
                "Descripcion" => $Descripcion,
                "Estado" => "Activo",
                "FechaRegistro" => $FechaHora,
                "IDRaiz" => $CodReclamo

            );
            $IdAnuncio =  insert("anuncios",$DataAnuncio,$vConex);

            $DataAnuncioDet = array(
                "Anuncio" => $IdAnuncio['lastInsertId'],
                "Alumno" => $destinatario."Alumno",
                "Interaccion" => "SI",
            );

            insert("anuncios_transaccion",$DataAnuncioDet,$vConex);

/*
            $IdAnuncio = $IdAnuncio['lastInsertId'];
            $SqlSoporte = "  UPDATE matriculas
                             INNER JOIN anuncios as a on matriculas.Producto = a.ProgramaAlmacen
                             INNER JOIN anuncios_transaccion as t on a.Codigo = t.Anuncio
                             INNER JOIN almacen  AS AL ON  AL.AlmacenCod   =   a.ProductoBase
                             INNER JOIN articulos  AS AR ON  AR.Producto   =   AL.Producto
                             SET a.Estado='Desactivo'
                             WHERE Cliente = '{$destinatario}Alumno'
                             AND a.ProductoBase = {$Curso_Almacen}
                             AND a.ProgramaAlmacen = {$AlmacenPrograma}
                             AND a.Estado = 'Activo'
                             AND a.Codigo NOT LIKE '{$IdAnuncio}'
                             AND t.Alumno =  '{$destinatario}Alumno'";

            xSQL2($SqlSoporte,$vConex);
*/
        ########################################



		$sql = " SELECT * FROM empresa_corporacion";
		$rg = fetch($sql);
		$Soporte_Nombre = $rg["Nombre"];
		$Soporte_Email = $rg["Email_1"];
		$Soporte_Telefono = $rg["Telefono"];
		$Soporte_Anexo = $rg["Anexo"];

		$hoy = date('Y-m-d');
		$hoy = FormatFechaText($hoy);

		$sql2 = "select AL.AliasDeMsgPrograma,ART.Titulo AS Programa FROM almacen as AL INNER JOIN articulos AS ART ON AL.Producto = ART.Producto where AL.AlmacenCod = ".$AlmacenPrograma." ";
		$rg2 = fetch($sql2);
		$AliasDeMsgPrograma = $rg2['AliasDeMsgPrograma'];
		$NombrePrograma = $rg2['Programa'];
		if(empty($AliasDeMsgPrograma)){
			$AliasDeMsgPrograma = "PLATAFORMA EDUCATIVA";
		}
		if($CoResponsable != "" ) {
			$Texto = 'Estimado(a) <span style="color:#35A9AD">' . $Nombres . '</span>, te informamos que el soporte del Programa <span style="color:#4396de">"' . $NombrePrograma . '"</span> le fue transferido, por el presente enviamos el link para que pueda visualizarlo.';
		}else {
			$Texto = 'Estimado(a) <span style="color:#35A9AD">' . $Nombres . '</span>, te informamos que su pregunta de soporte al Programa <span style="color:#4396de">"' . $NombrePrograma . '"</span> fue respondido, por el presente enviamos el link para que pueda visualizarlo.';
		}
		$Nota  = '<strong>NOTA:</strong> para obtener un mejor rendimiento de la plataforma acceder con el navegador google chrome, si presenta alg&uacute;n inconveniente, contactarse con el soporte t&eacute;cnico.';

		$body  = "<table border='0' width='95%' style='font-family: arial, open sans'>";
		$body .= '<tr><td colspan="3" style="font-size:0.8em;color:#444;" >' . $hoy . '<br /></td></tr>';
		$body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:22px;" >'.$NombreReclamo.'</td></tr>';
		$body .= '<tr><td colspan="3" style="font-size:13px;"> '.$Texto.' </td></tr>';
		$body .= '<tr><td colspan="3" style="padding:8px 10px;font-size:10px;" ></td></tr>';
		$body .= '<tr>
		<td style="font-size:12px;border:1px solid #006ad0;padding:8px 30px;background-color:#4396de;text-decoration:none"><center><a style="color:#f9f9f9;text-decoration: none;" href="'.siteUrl().$sUrlEmpresa.'/Soporte/'.$Curso_Almacen.'/'.$AlmacenPrograma.'/'.$CodReclamo.'"  target="_blank">Ingresar</a></center></td>
		<td style="padding:8px 10px;width:30%;"></td>
		<td style="padding:8px 30px;width:50%;"></td>
	   </tr>';
		$body .= '<tr><td colspan="3" style="font-size:12px;border-bottom: 2px solid #F3F3F3; float:left;width:100%;padding:15px 0px;color:#6b6b6b" >Atentamente</td></tr>';
		$body .= '<tr><td colspan="3" style="padding:8px 0px;font-size:10px;color:#6b6b6b" >'.$Nota.'</td></tr>';
		$body .= '<tr><td colspan="3" style="width:100%;padding:5px 0px;color:#6b6b6b;font-size:9px ">Datos de Soporte: '.$Soporte_Nombre.' <br> Correo: '.$Soporte_Email.' <br> Tel&eacute;fono.: '.$Soporte_Telefono.' | Anexo: '.$Soporte_Anexo.'  </td></tr>';
		$body .= "</table>";

           # $asunto='pasión';
           # utf8_decode($asunto);
           # utf8_encode($asunto);

		$asunto = 'Soporte Técnico';
           # $asunto=   utf8_decode($asunto);
           # $asunto=   $asunto;
		emailSES2("", $destinatario,$asunto, $body,'','',$AliasDeMsgPrograma);

            return $destinatario."Alumno";
	}
}

?>