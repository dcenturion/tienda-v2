<?php

    function Proc_Inpacto_TabAlmacen_Publicacion($dataInsert,$vConex){
       
	    $CodigoArticulo = $dataInsert['CodigoArticulo'];
	    $CodigoAlmacen = $dataInsert['CodigoAlmacen'];
	    $Estado = $dataInsert['Estado'];
	    $TipoProducto = $dataInsert['TipoProducto'];
	    $TipoIngreso = $dataInsert['TipoIngreso'];
	    $FechaHora = $dataInsert['FechaHora'];
	    $entidadCreadora = $dataInsert['entidadCreadora'];
	    $UnidadNegocio_ID = $dataInsert['UnidadNegocio_ID'];
	    $Codigo_Entidad_Usuario = $dataInsert['Codigo_Entidad_Usuario'];
	    $Escuela_ID = $dataInsert['Escuela_ID'];
	    $Descripcion = $dataInsert['Descripcion'];

		$sql = "SELECT  
		IdArticulo, Fabricante, Entidad, 
		TipoProducto, Producto, ProductoFab
		, Categoria ,DiaFinalInscripcion
		,DiaInicio ,DiaFinal, Cantidad
		FROM articulos  WHERE IdArticulo = ".$CodigoArticulo." ";
    
		$rg = rGT($vConex,$sql);
		$IdArticulo = $rg["IdArticulo"];
		$Entidad = $rg["Entidad"];
		$TipoProducto = $rg["TipoProducto"];
		$Producto = $rg["Producto"];
		$Categoria = $rg["Categoria"];
		$DiaFinalInscripcion = $rg["DiaFinalInscripcion"];
		$DiaInicio = $rg["DiaInicio"];
		$DiaFinal = $rg["DiaFinal"];
		$Cantidad = $rg["Cantidad"];
		
		if(empty($CodigoAlmacen) || $CodigoAlmacen != 0 ){
			
			   //Genera Codigo Unico de almacen
				$Sql = " INSERT INTO almacen ( 
							 Origen,Entidad,Producto
							,TipoProducto,Estado,DiaFinalInscripcion,DiaInicio
							,DiaFinal,FechReg,cantidad, stock ,Descripcion
							) VALUES (
								'".$Entidad."','".$entidadCreadora."','".$Producto."'
								,'".$TipoProducto."','".$Estado."','".$DiaFinalInscripcion."'
								,'".$DiaInicio."','".$DiaFinal."','".$FechaHora."',".$Cantidad.",".$Cantidad.",'".$Descripcion."'
							) ";		    
				xSQL($Sql,$vConex); 	
				$CodAlmacen = mysql_insert_id($vConex);

        }else{
		
			   //Genera Codigo Unico de almacen
				$Sql = "UPDATE almacen SET Estado = '".$Estado."'  WHERE  AlmacenCod = ".$CodigoAlmacen."  ";	    
				xSQL($Sql,$vConex); 			
		        
		        $CodAlmacen = $CodigoAlmacen;		
		}
				//Llama a las transacciones y almacenes
				$sql = "SELECT  codigo FROM almacen_transaccion 
				WHERE entidad = '".$entidadCreadora."' AND  nombre = 'ingreso-produccion' ";
				$rg = rGT($vConex,$sql);
				$TransaccionIngProduccion = $rg["codigo"];		

				$sql = "SELECT  codigo FROM almacen_nombre 
				WHERE entidad = '".$entidadCreadora."' AND  nombre = 'productos-desarrollo' ";
				$rg = rGT($vConex,$sql);
				$AlmacenDesarrollo = $rg["codigo"];	

				$sql = "SELECT  codigo FROM almacen_transaccion 
				WHERE entidad = '".$entidadCreadora."' AND  nombre = 'transferencia-productos-terminados' ";
				$rg = rGT($vConex,$sql);
				$TransaccionIngSalidaProduccionAPT = $rg["codigo"];	
				
				$sql = "SELECT  codigo FROM almacen_transaccion 
				WHERE entidad = '".$entidadCreadora."' AND  nombre = 'ingreso-produccion-productos-terminados' ";
				$rg = rGT($vConex,$sql);
				$TransaccionIngAPT = $rg["codigo"];
				
				$sql = "SELECT  codigo FROM almacen_nombre 
				WHERE entidad = '".$entidadCreadora."' AND  nombre = 'productos-terminados' ";
				$rg = rGT($vConex,$sql);
				$AlmacenAPT = $rg["codigo"];						
				

				if($TipoIngreso =="Desarrollo"){
						
						//Genera Movimiento de Ingreso a produccion
						$Sql = " INSERT INTO almacen_movimiento ( 
										almacen_id,almacen_transaccion_id ,documento
										,usuario, cantidad, almacen_nombre_id,
										descripcion, fecha_registro ,UNegocio,
										Escuela,Usuario_Entidad
									) VALUES (
									
										".$CodAlmacen .",".$TransaccionIngProduccion.",''
										,'".$entidadCreadora."' ,".$Cantidad.",".$AlmacenDesarrollo.",''
										,'".$FechaHora."',".$UnidadNegocio_ID.",".$Escuela_ID."
										,".$Codigo_Entidad_Usuario."

									) ";

						xSQL($Sql,$vConex); 										
				}
				
				if($TipoIngreso =="Abrir"){
						
						$Sql = " INSERT INTO almacen_movimiento ( 
									 almacen_id,almacen_transaccion_id,documento,usuario,cantidad
									,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela
									,Usuario_Entidad
									) VALUES (
									
										".$CodAlmacen .",".$TransaccionIngSalidaProduccionAPT.",'','".$entidadCreadora."',".$Cantidad."
										,".$AlmacenDesarrollo.",'','".$FechaHora."',".$UnidadNegocio_ID.",".$Escuela_ID."
										,".$Codigo_Entidad_Usuario."
									) ";
									// WE($Sql);
						xSQL($Sql,$vConex); 

						$Sql = " INSERT INTO almacen_movimiento ( 
									 almacen_id, almacen_transaccion_id, documento, usuario, cantidad
									,almacen_nombre_id ,descripcion ,fecha_registro ,UNegocio ,Escuela
									,Usuario_Entidad
									) VALUES (
									
										".$CodAlmacen ." ,".$TransaccionIngAPT." ,''	,'".$entidadCreadora."',".$Cantidad."
										,".$AlmacenAPT.",'' ,'".$FechaHora."',".$UnidadNegocio_ID.",".$Escuela_ID."
										,".$Codigo_Entidad_Usuario."

									) ";
						xSQL($Sql,$vConex); 								
				     
				}
				
				if($TipoIngreso =="Desarrollo-Abierto"){
						
						//Genera Movimiento de Ingreso a produccion  y a productos terminados
						$Sql = " INSERT INTO almacen_movimiento ( 
									 almacen_id, almacen_transaccion_id ,documento, usuario,cantidad
									,almacen_nombre_id, descripcion ,fecha_registro ,UNegocio ,Escuela
									,Usuario_Entidad
									) VALUES (
									
										".$CodAlmacen .",".$TransaccionIngProduccion.",'','".$entidadCreadora."',".$Cantidad."
										,".$AlmacenDesarrollo.",'','".$FechaHora."',".$UnidadNegocio_ID.",".$Escuela_ID.",
										".$Codigo_Entidad_Usuario."

									) ";
						xSQL($Sql,$vConex); 

						$Sql = " INSERT INTO almacen_movimiento ( 
									 almacen_id,almacen_transaccion_id,documento,usuario,cantidad
									,almacen_nombre_id,descripcion,fecha_registro,UNegocio,Escuela
									,Usuario_Entidad
									) VALUES (
									
										".$CodAlmacen .",".$TransaccionIngSalidaProduccionAPT.",'','".$entidadCreadora."',".$Cantidad."
										,".$AlmacenDesarrollo.",'','".$FechaHora."',".$UnidadNegocio_ID.",".$Escuela_ID."
										,".$Codigo_Entidad_Usuario."
									) ";
						xSQL($Sql,$vConex); 

						$Sql = " INSERT INTO almacen_movimiento ( 
									 almacen_id, almacen_transaccion_id, documento, usuario, cantidad
									,almacen_nombre_id ,descripcion ,fecha_registro ,UNegocio ,Escuela
									,Usuario_Entidad
									) VALUES (
									
										".$CodAlmacen ." ,".$TransaccionIngAPT." ,''	,'".$entidadCreadora."',".$Cantidad."
										,".$AlmacenAPT.",'' ,'".$FechaHora."',".$UnidadNegocio_ID.",".$Escuela_ID."
										,".$Codigo_Entidad_Usuario."

									) ";
						xSQL($Sql,$vConex); 								
				     
				}				
							
		return $CodAlmacen;	

	}
	
	function Proc_Actuliza_Lista($dataInsert,$vConex){
	
	    $CodigoAlmacen = $dataInsert['CodigoAlmacen'];
	    $CodigoLDT = $dataInsert['CodigoLDT'];
	    $Estado = $dataInsert['Estado'];
	
		$Sql = " UPDATE lista_trabajo_det SET  
		CodigoAlmacen = ".$CodigoAlmacen." 
		,Estado = '".$Estado."'
		WHERE Codigo = ".$CodigoLDT."";
		xSQL($Sql,$vConex); 		     

	}
	
	
	function Process_Lista($dataInsert,$vConex){
	
	    //Proceso que genera la lista de trabajo
	    $Email_Entidad_Usuario = $dataInsert['Email_Entidad_Usuario'];
	    $entidadCreadora = $dataInsert['entidadCreadora'];		
	    $UnidadNegocio_ID = $dataInsert['UnidadNegocio_ID'];
	    $Escuela_ID = $dataInsert['Escuela_ID'];
	    $codigo = $dataInsert['codigo'];
	    $TipoProducto = $dataInsert['TipoProducto'];
	    $FechaHora = $dataInsert['FechaHora'];
		

		$sql = "SELECT Codigo FROM lista_trabajo 
			   WHERE Entidad ='" . $Email_Entidad_Usuario . "' 
			   AND Usuario = '" . $entidadCreadora . "'  AND  Estado = 'Trabajando'  ";
		$rg = rGT($vConex,$sql);
		$CodigoLista = $rg["Codigo"];
		
		if($CodigoLista){
			 $id_lista = $CodigoLista;
		}else{                    
			$sql3 = "INSERT INTO lista_trabajo(Entidad,Usuario,FechaReg,Estado,Nombre , Empresa, Escuela)
			VALUES('" . $Email_Entidad_Usuario. "','" . $entidadCreadora . "','" .$FechaHora. "','Trabajando','Esta Lista se Generó Automaticamente',".$UnidadNegocio_ID.",".$Escuela_ID.")";
			xSQL($sql3,$vConex);
			$id_lista =  mysql_insert_id();
		}
		
		#insertamos el detalle en la lista de trabajo
		$sql4 = " INSERT INTO lista_trabajo_det(Fecha,Lista,TipoProducto,CodigoProducto, Estado)
		VALUES('" .date('Y-m-d H:i:s') . "','" . $id_lista . "','".$TipoProducto."','" .$codigo . "','Planeacion')";
		xSQL($sql4,$vConex); 
		
	}
	
	function LogProductos($dataInsert,$vConex){
 	    $codigo = $dataInsert['codigo'];
	    $TipoProducto = $dataInsert['TipoProducto'];
	    $FechaHora = $dataInsert['FechaHora'];  
	    $Tipo = $dataInsert['Tipo'];  

        $sql2 = "INSERT INTO log_cursos(FechaReg,Curso,UsuarioEntidad,Empresa,Tipo)
		VALUES('" .$FechaHora. "','" . $codigo . "','" . $Email_Entidad_Usuario . "','" . $entidadCreadora . "','".$Tipo."')";
        xSQL($sql2,$vConex);
   }
   
   	function Log_programas($dataInsert,$vConex){
 	    $codigo = $dataInsert['codigo'];
	    $TipoProducto = $dataInsert['TipoProducto'];
	    $FechaHora = $dataInsert['FechaHora'];  
	    $Tipo = $dataInsert['Tipo'];  

        $sql2 = "INSERT INTO log_programas(FechaReg,Programa,UsuarioEntidad,Empresa,Tipo)
		VALUES('" .$FechaHora. "','" . $codigo . "','" . $Email_Entidad_Usuario . "','" . $entidadCreadora . "','".$Tipo."')";
        xSQL($sql2,$vConex);
   }
	
?>