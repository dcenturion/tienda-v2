function Login(settings){
	var f = this, url_request = settings.urlRequest, form = _SQS.id(settings.id);
	
	form.elements.btn_enviar.onclick = function(){
		var name = _SQS.id("name").value;
		var email = _SQS.id("email").value;
		var phone = _SQS.id("phone").value;
		var message = _SQS.id("message").value;
		
		console.log(name);
		if(name =="" || email =="" || phone =="" ){
			formMsj = _SQS.id("msj_form");
			formMsj.setAttribute("class","msjCorrecto");
			formMsj.innerHTML = "DEBE LENAR LOS CAMPOS";
			return;
		}
		console.log("paso la validacion");
		f.send();
	};
	//Methods
	f.send = function(){
		
		if(!url_request){
			console.log("You don't established the url of request");
			return;
		}
		var cmp_form = componentes(form);
		
		AjaxPOST_TEXT(url_request, cmp_form, function(responseText){
			console.log(responseText);
			var data = JSON.parse(responseText);
			if(data.mensaje =="correcto"){
				
				formMsj = _SQS.id("msj_form");
				formMsj.setAttribute("class","msjCorrecto");
				formMsj.innerHTML = "SUS DATOS FUERON ENVIADOS";
				
				_SQS.id("name").value = "";						
				_SQS.id("email").value = "";						
				_SQS.id("phone").value = "";			
				_SQS.id("message").value = "";			
			}					 
		});		
	};
	
}
		