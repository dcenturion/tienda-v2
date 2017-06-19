<?php
	function ReclamosControlVistas($CodReclamo,$emisor,$CoResponsable,$movimiento_reclamo,$vConex){
			$sql = "SELECT  usuario_id  FROM reclamos_movimientos
			WHERE reclamo_id = ".$CodReclamo."  AND usuario_id <> ".$emisor."
			GROUP BY usuario_id ";		
			$consulta = Matris_Datos($sql,$vConex);
			while ($reg =  mysql_fetch_array($consulta)) {
					$sql = " INSERT  INTO reclamos_control_vistas (reclamo_id,Estado,usuario_id,movimiento_reclamo)
					VALUES (".$CodReclamo.",0,".$reg["usuario_id"].",".$movimiento_reclamo.")  ";
					$s = xSQL($sql,$vConex);					
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
					$rg = rGT($vConex,$sql);
					$Nombres = $rg["Nombres"];             
					$EntidadCreadora = $rg["EntidadCreadora"];   
					$destinatario = $rg["Usuario"];   
				
					$FechaHora = FechaHoraSrv();
					$sql = "SELECT a.RazonSocial,a.Contacto,a.MailContacto,a.Direccion, u.UrlId
					FROM empresa AS a INNER JOIN 
					usuarios AS u ON u.IdUsuario = a.PaginaWeb  WHERE PaginaWeb = '".$EntidadCreadora."'  ";
					$rg = rGT($vConex,$sql);
					$sRasonSocial= $rg["RazonSocial"];		
					$sContacto= $rg["Contacto"];		
					$sMailContacto= $rg["MailContacto"];		
					$sDireccion = $rg["Direccion"];
					$sUrlEmpresa = $rg["UrlId"];					
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
							 <div >ingresando a la Plataforma Educativa : <a href='http://owlgroup.org/".$sUrlEmpresa."'>".$sUrlEmpresa." </a></div>
						";
					}else{		
						$cuerpoMail = "
							 <div style='font-size:1.5em;padding:10px 0px 10px 3px;color:#4396de;'>RECLAMO  # -  ".$CodReclamo."</div>
							 <div >Este reclamo ha sido contestado, revize su bandeja de reclamos </div>
							 <div >ingresando a la Plataforma Educativa : <a href='http://owlgroup.org/".$sUrlEmpresa."'>".$sUrlEmpresa." </a></div>
						";
					}	

					$footerMail = "Atentamente ";			 
					$asunto = "RECLAMO  # -  ".$CodReclamo."";
					$body = LayouMailA($cabezeraMail,$cuerpoMail,$footerMail);
					$emailE = EMail("",$destinatario,$asunto,$body);				
			}
			
	}

?>