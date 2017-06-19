
function PopupService(settings){
	var f = this, url_request = settings.urlRequest, btn_modal1 = _SQS.id("btn_modal1");
	
    // btn_modal1.onclick = function(){
			
		// AjaxGET_TEXT(url_request+"/id/entro", function(responseText){
			// console.log(responseText);
			// parseScript(responseText);
			
			// formMsj = _SQS.id("modal1");
			// formMsj.innerHTML = responseText;
			
		    // cardalert = _SQS.id("modal1");
		    // cardalert.setAttribute("style","display:block;z-index:1300");
			// console.log(responseText);

		
			 
		// });
	// };
	
	// var nombre = _SQS.id("Nombre").value;
		
    $(".btneditar").click(function(){
		var oID = $(this).attr("id");
	
		AjaxGET_TEXT(url_request+"/id/"+oID, function(responseText){
			
			
			formMsj = _SQS.id("form_edit_popup");
			formMsj.innerHTML = responseText;
			
	
			console.log(responseText);
			 
		});		
		
	    alert('Evento click sobre un button con class="nombre3"'+oID);
	});	
}


// state counter
var state = 0;



function PopupServiceB(settings){
	var f = this, url_request = settings.urlRequest, btn_crear_modal = _SQS.id("btn_crear_modal");
	
    btn_crear_modal.onclick = function(){
		
		var currentLocation = window.location;

		var state = 0;
		sObj = { "state": state, "message":"por aca" },
		title = "title",
		url = "?transacion=add";
		
		// replace current state
		history.pushState(sObj, title, url);

		AjaxGET_TEXT(url_request+"/form/crear", function(responseText){
		
			parseScript(responseText);
			
			formMsj = _SQS.id("modal1");
			formMsj.innerHTML = responseText;
			
		    cardalert = _SQS.id("modal1");
		    cardalert.setAttribute("style","display:block;z-index:1300");
			console.log(responseText);

		
			 
		});
	};
	
			
    $(".btn_reporte").click(function(){
		var oID = $(this).attr("id");
	    	
		formMsj = _SQS.id("modal1");
		formMsj.innerHTML = '<div class="circle"></div>';
		AjaxGET_TEXT(url_request+"/form/update/id/"+oID, function(responseText){
			
			
			formMsj = _SQS.id("modal1");
			formMsj.innerHTML = responseText;
		    
			cardalert = _SQS.id("modal1");
		    cardalert.setAttribute("style","display:block;z-index:1300");

			tpe = _SQS.id("transparencia");
			tpe.setAttribute("style","z-index: 1002; display: block; opacity: 0.5;");
					
			console.log(responseText);
			 
		});		
		
	    // alert('Evento click sobre un button con class="nombre3"'+oID);
	});	

}

function validaCliente(){
	
		var nombre = _SQS.id("Nombre").value;
		var descripcion = _SQS.id("Descripcion").value;
		
		if(nombre =="" || descripcion ==""  ){
			
			cardalert = _SQS.id("card-alert");
		    cardalert.setAttribute("style","display:block");
			
			formMsj = _SQS.id("msj_form");
			formMsj.innerHTML = "<p>DEBE LENAR LOS CAMPOS</p>";
			
			return "error";
		}else{
			
			
			return "correcto";			
		}
	
}

function cierraPanel(id){
	cardalert = _SQS.id(id);
	cardalert.setAttribute("style","display:none");	
	$(".lean-overlay").remove();
}

function enviarForm(iDForm,uRl){
	var cV, form = _SQS.id(iDForm);
	cV = validaCliente();
	var cmp_form = componentes(form);
	
	if(cV !== "error"){
	
		AjaxPOST_TEXT(uRl, cmp_form, function(responseText){
			console.log(responseText);
			var data = JSON.parse(responseText);
			if(data.mensaje =="correcto"){
				Materialize.toast('Ejecución correcta', 4000,'',function(){});
				location.href = "/adminservice";
			}else{
				cardalert = _SQS.id("card-alert");
				cardalert.setAttribute("style","display:block");
				
				formMsj = _SQS.id("msj_form");
				formMsj.innerHTML = "<p>Sus datos son incorrectos</p>";				
			}					 
		});
		
	}
}

function FormService(setings){
			
alert("url_request hhhh "+url_request);
	var f = this, url_request = setings.urlRequest, form = _SQS.id(setings.id), btn = _SQS.id("btn_guardar"),
	btnb = _SQS.id("btn_guardarB");
	
	console.log("url_request hhhh "+url_request);
	
	btn.onclick = function(){
		
		validaCliente();
		f.send();
	};

	btnb.onclick = function(){
		validaCliente();
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
				
                location.href = "/adminservice";		
				
			}else{
				
				cardalert = _SQS.id("card-alert");
				cardalert.setAttribute("style","display:block");
				
				formMsj = _SQS.id("msj_form");
				formMsj.innerHTML = "<p>Sus datos son incorrectos</p>";				
			}					 
		});		
	};
	
}
		