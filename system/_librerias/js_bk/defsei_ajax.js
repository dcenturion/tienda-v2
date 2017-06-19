	function FormularioCompras(sUrl,formid,sDivCon) 
	{	
			var FormR = document.getElementById(formid);
			var longitudFormulario = FormR.elements.length;
			var cadenaFormulario = "";
			var sepCampos="";
			for (var i=0; i <= FormR.elements.length-1;i++) {
			
				if (FormR.elements[i].type == "hidden") {
					cadenaFormulario += sepCampos+FormR.elements[i].name+'='+encodeURI(FormR.elements[i].value);
					sepCampos="&";
				}		
			
			}
			var iframe = document.getElementById(sDivCon);
			iframe.setAttribute("src", sUrl+'?'+cadenaFormulario);			
	}
	
	
	function ResetIframe(){
	  		var iframe = document.getElementById("CarritoB");
	  		var sDiv = document.getElementById("PnlA");
			// alert(sDiv.className);
			if( sDiv.className == "Carrito" ){
			     iframe.setAttribute("style", "width:100px;");   
			     sDiv.setAttribute("class", "CarritoOclt");   
			}else{
			     iframe.setAttribute("style", "width:280px;");	
                 sDiv.setAttribute("class", "Carrito");				 
			}

	
	}


	