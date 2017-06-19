function Login(settings){
	var f = this, url_request = settings.urlRequest, form = _SQS.id(settings.id);
	
	form.elements.btn_enviar.onclick = function(){
		var username = _SQS.id("username").value;
		var password = _SQS.id("password").value;
		var rememberme = _SQS.id("remember-me").value;
		
		
		if(username =="" || password =="" || rememberme =="" ){
			cardalert = _SQS.id("card-alert");
		    cardalert.setAttribute("style","display:block");
			
			formMsj = _SQS.id("msj_form");
			formMsj.innerHTML = "<p>DEBE LENAR LOS CAMPOS ee</p>";
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
				
				
                location.href = "/dashboard";		
				
			}else{
				
				cardalert = _SQS.id("card-alert");
				cardalert.setAttribute("style","display:block");
				
				formMsj = _SQS.id("msj_form");
				formMsj.innerHTML = "<p>Sus datos son incorrectos</p>";				
			}					 
		});		
	};
	
}
		