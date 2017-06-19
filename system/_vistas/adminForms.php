<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
// error_reporting(E_ERROR);
$enlace = "./_vistas/adminForms.php";
$enlacePopup = "/system/_vistas/adminForms.php";
$vConex = conexSys();
$fechaHoraSrv = FechaHoraSrv();
$userTool = $_SESSION['UserTool']['string'];
if (get('muestra') !=''){ detalleForm(get('muestra'));}
if (get('formAdmin') !=''){ formAdmin(get('formAdmin'));}
if (get('CrearFormualrio') !=''){ CrearFormualrio(get('CrearFormualrio'));}

//

if(get("metodo") != ""){// esta condicion inicia cuando se procesa la info de un formulario
	if(get("TipoDato") == "archivo"){
		// if(get("metodo") == "SysFormDet1"){
		// p_ga("daniel","fri",$vConex);
		// }
	}
	
	function p_interno($codigo,$campo){
	
	    global $fechaHoraSrv, $userTool;
		
		 if(get("metodo") == "SysFomr1"){
		
				switch ($campo) {
					case "Estado":
						 $valor = "'Activo'";
						 break;
					case "FechaHoraCreacion":
						 $valor = "'".$fechaHoraSrv."'";
						 break;
					case "FechaHoraActualizacion":
						 $valor = "'".$fechaHoraSrv."'";
						 break;
					case "UsuarioCreacion":
						 $valor = "".$userTool."";
						 break;
					case "UsuarioActualizacion":
						 $valor = "".$userTool."";
						 break;
                    default:
					     $valor ="";
					     break;
				}	
                  return $valor; 				
		 }
		 
		 if(get("metodo") == "editar_formulario"){
		
				switch ($campo) {
					case "FechaHoraActualizacion":
						 $valor = "'".$fechaHoraSrv."'";
						 break;

					case "UsuarioActualizacion":
						 $valor = "".$userTool."";
						 break;
                    default:
					     $valor ="";
					     break;
				}	
                  return $valor; 				
		 }		 
		  
		if(get("metodo") == "sysTabletDet"){
		   if ($campo == "sys_tabla"){
		   $valor = "'".get("codigoSysTabla")."'";
		   }else{$valor ="";}
		   return $valor; 
		 }
			
		if(get("metodo") == "sysformdet2"){
		   if ($campo == "Form"){
		   $valor = "'".get("codigoForm")."'";
		   }else{$valor ="";}
		   return $valor; 
		 }	
		 if(get("metodo") == "menu_empresa_det"){
		   if ($campo == "Menu"){
		   $valor = "'".get("Menu")."'";
		   }else{$valor ="";}
		   return $valor; 
		 }			 
	}

	function p_before($codigo){
	// W("MUESTRA CODIGO ".$codigo);
	// return "hola";
	}			
			
	if(get("TipoDato") == "texto"){
		if(get("transaccion") == "UPDATE"){
			if(get("metodo") == "editar_formulario"){   
			     p_gf("editar_formulario",$vConex,get("codigoForm"));
					$msg =Msg("El proceso fue cerrado correctamente","C");
						W("
						<script>
						\$popupEC.close();
						</script>");
						 WE($msg);
				 }					
	
	    }
		
		if(get("transaccion") == "INSERT"){
			if(get("metodo") == "SysFomr1"){  
			    pro_sysform();
				
				W("Codigo :: ".post("Codigo"));
				$msg =Msg("El proceso fue cerrado correctamente","C");
				W("
				<script>
				\$popupEC.close();
				</script>");
				 WE($msg);
			}
		}	
	}
	
   if(get("transaccion") == "DELETE"){
			if(get("metodo") == "sys_tipo_input"){DReg("sys_tipo_input","Codigo","'".get("codigo")."'",$vConex);datosAlternos("CreacionTipoDato");}
	}		
			
   exit();
}

   function insert_row_form($key){
		global $vConex,$enlace, $enlacePopup,$fechaHoraSrv;
	   		 
			 $FormularioName=$key->Formulario;
			 $TablaName=$key->Tabla;
			
             $Campo=$key->Campo;
			 $Alias=$key->Alias;
			 $TipoOuput=$key->TipoOuput;
			 $TipoInput=$key->TipoInput;
			 $Visible=$key->Visible;
			 $Correlativo=$key->Correlativo;
			 $AutoIncrementador=$key->AutoIncrementador;
			 $Posicion=$key->Posicion;
			 $TamanoCampo=$key->TamanoCampo;
			 $Form=$key->Form;
			 $OpcionesValue=$key->OpcionesValue;
			 $TablaReferencia=$key->TablaReferencia;
			 $MaximoPeso=$key->MaximoPeso;
			 $AliasB=$key->AliasB;
			 $CtdaCartCorrelativo=$key->CtdaCartCorrelativo;
			 $CadenaCorrelativo=$key->CadenaCorrelativo;
			 $InsertP=$key->InsertP;
			 $UpdateP=$key->UpdateP;
			 $Validacion=$key->Validacion;
			 $read_only=$key->read_only;
			 $TipoValor=$key->TipoValor;
			 $PlaceHolder=$key->PlaceHolder;
			 $Edicion=$key->Edicion;
			 $Event_hidden_field=$key->Event_hidden_field;
			 $destiny_upload=$key->destiny_upload;
			 $video_control=$key->video_control;
			 $video_destiny_platform=$key->video_destiny_platform;
			 $UsuarioCreacion=$key->UsuarioCreacion;
			 $UsuarioActualizacion=$key->UsuarioActualizacion;
			 $FechaHoraActualizacion=$key->FechaHoraActualizacion;
			 $FechaHoraCreacion=$key->FechaHoraCreacion;
			
			 $CodigoAjax=$key->CodigoAjax;
		 
		 $Q_Existe="SELECT  Codigo FROM  sys_form  WHERE Codigo='{$FormularioName}' ";
         $Rg=fetchOld($Q_Existe);
         $exist=$Rg['Codigo'];		 
		 if($exist<>$FormularioName){

		 $Q_Formulario1=" INSERT  INTO sys_form 
				SET Codigo='{$FormularioName}' 
				, Descripcion='Form_{$FormularioName} '
				, DescripcionExtendida= '{$FormularioName}' 
				, Tabla= '{$TablaName}'
				, Estado= 'Activo'
				, FechaHoraCreacion='{$fechaHoraSrv}'
				, UsuarioCreacion= {$UsuarioCreacion}
				, UsuarioActualizacion={$UsuarioActualizacion}
				, FechaHoraActualizacion='{$fechaHoraSrv}' 
				, tipo_estilo=''  
			";	
		
		 xSQL($Q_Formulario1, $vConex);
		 }	
         $cod_sys_form_det = numerador("sys_form_det", 5, "");	
		/// INSERTAR EL DETALLE DEL FORMULARIO
					$Q_DetalleFormulario=" INSERT INTO  sys_form_det 
						SET
						Codigo={$cod_sys_form_det}
						,NombreCampo='{$Campo}' 
						,Alias= '{$Alias}'
						,TipoInput= '{$TipoInput}'
						,TipoOuput= '{$TipoOuput}'
						,TamanoCampo={$TamanoCampo}
						,Form= '{$FormularioName}'
						,Visible= '{$Visible}'
						,TablaReferencia=  '{$TablaReferencia}'
						,OpcionesValue=  '{$OpcionesValue}'
						,MaximoPeso=  {$MaximoPeso}
						,AliasB=  '{$AliasB}'
						,CtdaCartCorrelativo=  {$CtdaCartCorrelativo}
						,CadenaCorrelativo=  '{$CadenaCorrelativo}'
						,Validacion=  '{$Validacion}'
						,InsertP=  {$InsertP}
						,UpdateP=  {$UpdateP}
						,Correlativo={$Correlativo}
						,Posicion= {$Posicion} 
						,AutoIncrementador= '{$AutoIncrementador}'
						,read_only= '{$read_only}'
						,TipoValor= '{$TipoValor}'
						,PlaceHolder='{$PlaceHolder}'
						,Edicion= '{$Edicion}'
						,Event_hidden_field='{$Event_hidden_field}'
						,destiny_upload= '{$destiny_upload}'
						,video_control='{$video_control}'
						,video_destiny_platform='{$video_destiny_platform}'
						
						,FechaHoraActualizacion='{$fechaHoraSrv}'
						,FechaHoraCreacion='{$fechaHoraSrv}'

				                 ";
			        xSQL($Q_DetalleFormulario, $vConex);	
			
}

function CrearFormualrio($arg){
	global $vConex,$enlace, $enlacePopup;
	switch ($arg) {
		case "NewCreate":					
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?CrearFormualrio=Create', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			break;
		case "Create":

			$path = "";
			$uRLForm ="Guardar]".$enlace."?metodo=SysFomr1&transaccion=INSERT]PanelInferior]F]}";			
			$form = c_form("",$vConex,"SysFomr1","CuadroA",$path,$uRLForm,'','');
            $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           		 
            $menu_titulo = tituloBtnPn("<span>CREAR FORMULARIO</span><p>Llene los campos</p>", $botones, 'auto', 'TituloA');
    
			WE("
                <div style='width:500px;' id='update-program-settings'>
                {$Close}
                    {$menu_titulo}
                    {$form}
                    <div class='clear'></div>
                </div>
            ");
			break;	

						
		default:
			 $valor ="";
			 break;
	}	
				
	    WE($s);

}

function formAdmin($arg){
	global $vConex,$enlace, $enlacePopup,$fechaHoraSrv;
	switch ($arg) {
		case "RCrear":
		
			$accion = get("Accion");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?formAdmin=Crear&Accion={$accion}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			break;
		case "Crear":

			$path = "";
			$uRLForm ="Guardar]".$enlace."?metodo=SysFomr1&transaccion=INSERT]PanelInferior]F]}";			
			$form = c_form("",$vConex,"SysFomr1","CuadroA",$path,$uRLForm,'','');
            $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
		    // $btn .= "Eliminar]./PanelControlEmpresa.php?EliminarPrograma={$accion}&Codigo={$Codigo}&AlmacenPrograma={$AlmacenCod}]panelB-R}";
        	// $botones = Botones($btn, 'botones1',"");
            $menu_titulo = tituloBtnPn("<span>CREAR FORMULARIO</span><p>Llene los campos</p>", $botones, 'auto', 'TituloA');
    
			WE("
                <div style='width:500px;' id='update-program-settings'>
                {$Close}
                    {$menu_titulo}
                    {$form}
                    <div class='clear'></div>
                </div>
            ");
			break;	

		case "REditar":
		

			$codigoForm = get("codigoForm");
			$html = "	
                        <script> openPopupURI('".$enlacePopup."?formAdmin=Editar&codigoForm={$codigoForm}&AlmacenPrograma={$AlmacenCod}&Codigo={$Codigo}', {modal:true, closeContent:null}); </script>
                    ";
            WE($html);
			break;
		 case "Editar":
		
			$codigoForm = get("codigoForm");		
         
			$path = "";
			$uRLForm ="Actualizar]".$enlace."?metodo=editar_formulario&transaccion=UPDATE&codigoForm=".$codigoForm."]PanelInferior]F]}";
			
			$form = c_form("",$vConex,"editar_formulario","CuadroA",$path,$uRLForm,$codigoForm,'Codigo');
            $Close = "<div class='btnOcultaPanel' onclick=\$popupEC.close(); >X</div>";
           
			// $sql = "SELECT Codigo FROM sys_form WHERE Codigo = '{$codigoForm}'";
			// $rg = fetchOld($sql);
			// $formName = $rg["Codigo"];
			
            $menu_titulo = tituloBtnPn("<span>EDITAR FORMULARIO</span><p> ".$formName."</p>", $botones, 'auto', 'TituloA');
    
			WE("
                <div style='width:500px;' id='update-program-settings'>
                {$Close}
                    {$menu_titulo}
                    {$form}
                    <div class='clear'></div>
                </div>
            ");						  
			break;	
		//  Importar Formulario
		case "ImportarFormulario":

		$path = array('DescripcionExtendida' => "../../system/_export");
		$path2=$path['DescripcionExtendida'];
		$titulo   = tituloBtnPn("<span></span><p> Importación de Formulario </p>","", 'Auto', 'TituloA');
		$uRLForm = "<i class='icon-refresh'></i> Procesar Archivo]" . $enlace . "?formAdmin=ImportarF&Path={$path2}]div_Imp]F]}";
		$form = c_form_adp('', $vConex, 'ImportarFormulario', 'CuadroA',$path, $uRLForm, '', "",'');
        $Formulario = "<div class='PanelMsgAccion'>" . $titulo . $form ."<div>";
        WE($Formulario); 

	   break;
			
		 case "ImportarF":
		 $Archivo=post('DescripcionExtendida');
		 if(empty($Archivo)){
			$Mensaje = "<div style='display:block;font-family: Open Sans;background-color:#f44336;font-size: 1em;color:#FFFFFF; padding:4px;text-align:center;'> No se ha encontrado ningún archivo Excel que procesar..!!! </div>";
			WE($Mensaje); 
		}
		 $Path=get('Path');
		 $NombreArchivo=$Path."/{$Archivo}";
		 $excel=LeerExcelImport($NombreArchivo);
		 $VarJson = json_decode($excel);
		 $name_temp = "";
		
		foreach($VarJson as $key ){
			 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			if($name_temp!= $key->Formulario){
				// codigo consulta nombre tabla
				$name_temp=$key->Formulario;
				
				$Q_ExisteTable="SELECT  Codigo FROM  sys_tabla  WHERE Codigo='{$key->Tabla}' ";
				$RgT=fetchOld($Q_ExisteTable);
				
				if(!$RgT){// preguntar existencia tabla 
					// WE("TABLA NO EXISTE NO SE PUEDE IMPORTAR");
					$Mensaje = "<div style='display:block;font-family: Open Sans;background-color:#f44336;font-size: 1em;color:#FFFFFF; padding:4px;text-align:center;'> La tabla especificada no existe, No se puede realizar la Importación.!!! </div>";
					WE($Mensaje);
				}else{
					// consulta nombre del formulario
					$Q_Existe="SELECT  Codigo FROM  sys_form  WHERE Codigo='{$key->Formulario}' ";
                    $RgF=fetchOld($Q_Existe);
					
					if($RgF){// preguntar existencia formulario
					    // WE("FORMULARIO YA EXISTE  NO SE PUEDE IMPORTAR");
				 	 $Mensaje = "<div style='display:block;font-family: Open Sans;background-color:#f44336;font-size: 1em;color:#FFFFFF; padding:4px;text-align:center;'> El Formulario ya exíste, No se puede realizar la Importación..!!! </div>";
					 WE($Mensaje);
					}else{
						insert_row_form($key);
					}
				}				
				
			}else{
				insert_row_form($key);
				$name_temp=$key->Formulario;
			}
			
		}
  		  // WE("IMPORTACION EXITOSA");
	 $Mensaje = "<div style='display:block;font-family: Open Sans;background-color:#34a853;font-size: 1em;color:#FFFFFF; padding:4px;text-align:center;'>La Importación fue realizada con ÉXITO..!!! </div>";
     WE($Mensaje);
					
	break;
	
	
        // Exportar Formulario			
    	case "ExportarFrom":
		$codigoForm = get("codigoForm");	
		
       $Q_fromulario="   
					SELECT
					SF.Codigo  AS 'Formulario'
					,SF.Tabla
					,SFD.NombreCampo AS 'Campo'
					,SFD.Alias
					,SFD.TipoOuput
					,SFD.TipoInput
					,SFD.TamanoCampo
					,SFD.Visible
					,SFD.TablaReferencia
					,SFD.OpcionesValue
					,SFD.MaximoPeso
					,SFD.AliasB
					,SFD.CtdaCartCorrelativo
					,SFD.Validacion
					,SFD.InsertP
					,SFD.UpdateP
					,SFD.CadenaCorrelativo
					,SFD.CadenaCorrelativo
					,SFD.Form
					,SFD.Visible
					,SFD.Correlativo 
					,SFD.AutoIncrementador 
					,SFD.Posicion
					,SFD.read_only
					,SFD.TipoValor
					,SFD.PlaceHolder
					,SFD.Edicion 
					,SFD.Event_hidden_field
					,SFD.destiny_upload
					,SFD.video_control
					,SFD.video_destiny_platform
					,SFD.UsuarioCreacion
					,SFD.UsuarioActualizacion 
					,SFD.FechaHoraActualizacion
					,SFD.FechaHoraCreacion
					,SFD.Codigo AS CodigoAjax 
					FROM sys_form_det  AS SFD
					INNER JOIN  sys_form AS SF ON SFD.Form=SF.Codigo
					WHERE SFD.Form = '{$codigoForm}' 
					ORDER BY Posicion;  ";

	   ExportExcelSimple($Q_fromulario,$vConex,'Reporte Formulario',$datos);
	   $Mensaje = "<div style='display:block;font-family: Open Sans;background-color:#34a853;font-size: 1em;color:#FFFFFF; padding:4px;text-align:center;'>La exportación  del formulario fue realizada con ÉXITO..!!! </div>";
       WE($Mensaje);
		
		break;
        
		default:
			 $valor ="";
	    break;
	}	
				
	    WE($s);

}

function pro_sysform(){
  global $vConex, $fechaHoraSrv, $userTool;

	$sql = 'SELECT Codigo,Descripcion FROM sys_tabla WHERE  Codigo = "'.post("Tabla").'" ';
	$rg = rGT($vConex,$sql);
	$codigo = $rg["Codigo"];	
	if($codigo != ""){
	// WE(post("Tabla"));
		$vSQL = 'SELECT Codigo,Descripcion,TipoCampo,sys_tabla  FROM  sys_tabla_det WHERE  sys_tabla = "'.post("Tabla").'" ';
		$consulta = mysql_query($vSQL, $vConex);
		while ($r= mysql_fetch_array($consulta)) {
		$cod_sys_form_det= numerador("sys_form_det",5,"");		
		
			$sql = 'INSERT  INTO sys_form_det (Codigo,NombreCampo,Alias,TipoInput,TipoOuput,Form,Visible,
			TamanoCampo,FechaHoraCreacion,FechaHoraActualizacion,UsuarioCreacion,UsuarioActualizacion) 
			VALUES ("'.$cod_sys_form_det .'","'.$r["Descripcion"].'","'.$r["Descripcion"].'","'.cmn($r["TipoCampo"]).'","text","'.post("Codigo").'","SI",130,
			"'.$fechaHoraSrv.'","'.$fechaHoraSrv.'",'.$userTool.','.$userTool.')';

			xSQL($sql,$vConex); 
		
		}
		
		p_gf("SysFomr1",$vConex,"");
		

	}else{
	    WE("La Tabla No existe".post("Tabla"));
	}
}


?>