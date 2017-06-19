/*
 * This file require AjaxZilli.js
 * This file require zilli.js
 * This file require Jquery.js
 */

function parseScript(strcode) {
  var scripts = new Array();         // Array which will store the script's code
  
  // Strip out tags
  while(strcode.indexOf("<script") > -1 || strcode.indexOf("</script") > -1) {
    var s = strcode.indexOf("<script");
    var s_e = strcode.indexOf(">", s);
    var e = strcode.indexOf("</script", s);
    var e_e = strcode.indexOf(">", e);
    
    // Add to scripts array
    scripts.push(strcode.substring(s_e+1, e));
    // Strip from strcode
    strcode = strcode.substring(0, s) + strcode.substring(e_e+1);
  }
  
  // Loop through every script collected and eval it
  for(var i=0; i<scripts.length; i++) {
    try {
      eval(scripts[i]);
    }
    catch(ex) {
      // do what you want here when a script fails
    }
  }
} 
 
function componentes(form){

    var form = form,
        elements = form.elements,
        cadenaFormulario = "";
        var _y = "&";
        _SQS.each(elements, function(elem){
			if(elem.getAttribute('data-CBI')!==true && elem.name){
	
				//console.log(elem);
				
				var responseValue, success = true;
				
				switch (elem.type) {
					case "text":
					case "password":
					case "email":
					case "email":
					case "submit":
					case "hidden":
					case "number":
						responseValue = elem.value;
						break;
					case "textarea":
						var sTextAreaValue, sTextAreaValueB;
						
						sTextAreaValue = document.getElementById(elem.name + "-Edit");
						
						if (sTextAreaValue !== null) {
							sTextAreaValueB = sTextAreaValue.innerHTML;
						} else {
							sTextAreaValueB = elem.value;
						}
						responseValue = sTextAreaValueB;
						break;
					case "file":
						if (elem.value !== "") {
							var sPath = elem.getAttribute('ruta');
						}
						responseValue = elem.value;
						break;
					case "checkbox":
					case "radio":
						if (elem.checked) {
							responseValue = elem.value;
						} else {
							success = false;
						}
						break;
				}
				if (elem.tagName === "SELECT") {
					responseValue = elem.value;
				}
				
				if (success) {
				    // responseValue = responseValue.replace(/'/g, '"').replace(/&nbsp;/g, "<1001>").replace(/&/g, " ");
					cadenaFormulario += _y + elem.name + '=' + encodeURI(responseValue);
				}			
			}	
        });
    return cadenaFormulario;	
	
}

function mkAjaxObject() {
    var xmlHttp = null;
    if (window.ActiveXObject) {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (window.XMLHttpRequest) {
        xmlHttp = new XMLHttpRequest();
    }

    return xmlHttp;
}

function requestAjax(url, callback, method, params) {
    params = (!params) ? null : params;
    method = (!method) ? 'GET' : method;

    var AjaxObj = mkAjaxObject();
    AjaxObj.onreadystatechange = function () {
        if (AjaxObj.readyState === 4) {
            switch (AjaxObj.status) {
                case 200:
                    callback(AjaxObj);
                    break;
                case 404:
                    console.log("Error: La pagina no existe");
                    break;
                case 500:
                    console.log("Error: El servidor no responde");
                    break;
                default:
                    console.log("Error: Error desconocido");
                    break;
            }
        }
    };

    AjaxObj.open(method, url, true);
    if (method === 'POST') {
        AjaxObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    }
    AjaxObj.send(params);
}

function AjaxGET(url, block_id, callback) {
    if (typeof block_id === "string") {
        block_id = _SQS.id(block_id);
    }
    
    if (block_id) {
        $(block_id).html("<div class='loading'><div class='img'></div><div class='text'>Cargando...</div></div>");
        
        requestAjax(url, function (Ajax) {
            $(block_id).html(Ajax.responseText);
            if (callback) {
                callback(Ajax);
            }
        });
    } else {
        console.log("El bloque con _id " + block_id + " no existe");
    }
}

function AjaxGET_TEXT(url, callback) {
    requestAjax(url, function (Ajax) {
        callback(Ajax.responseText);
    });
}

function AjaxPOST_TEXT(url, param_string, callback) {
    requestAjax(url, function (Ajax) {
        callback(Ajax.responseText);
    }, "POST", param_string);
}

function sendForm(url, formId, callback, dataType) {
    var form = typeof formId === "string" ? document.getElementById(formId) : formId,
    formElements = form.elements,
    params = {};
    
    $.each(formElements, function(i, element){
        if(element.name){
            var value, success = true;
            
            switch (element.type) {
                case "button":
                case "file":
                case "hidden":
                case "number":
                case "password":
                case "select-one":
                case "submit":
                case "text":
                case "textarea":
                    value = element.value;
                    break;
                case "checkbox":
                case "radio":
                    if (element.checked) {
                        value = element.value;
                    } else {
                        success = false;
                    }
                    break;
            }
            
            if(success){
                var elementName = element.name;
                value = value.replace(/'/g, '"').replace(/&nbsp;/g, "<1001>").replace(/&/g, " ");

                switch (element.type){
                    case "checkbox":
                        if(elementName.match(/\[\]/)){
                            elementName = elementName.replace(/\[\]/, "");
                            
                            if(!params[elementName]){
                                params[elementName] = [];
                            }
                            
                            params[elementName].push(value);
                        }else{
                            params[elementName] = value;
                        }
                        break;
                    default:
                        params[elementName] = value;
                        break;
                }
            }
        }
    });
    
    var disabledElements = function(disabled){
        $(formElements).each(function(){
            switch (this.type) {
                    case "button":
                    case "checkbox":
                    case "file":
                    case "select-one":
                    case "radio":
                    case "submit":
                    case "textarea":
                        $(this).attr("disabled", disabled);
                        break;
                    case "hidden":
                    case "number":
                    case "password":
                    case "text":
                        $(this).attr("readonly", disabled);
                        break;
                }
        });
    };
    
//    console.log(params);
    
    //prepare elements to wait the request
    var $message = $("<div>").text("Enviando...");
    $(form).prepend($message);
    disabledElements(true);
    
    $.post(url, params, function(responseText){
        $message.remove();
        disabledElements(false);
        
        callback(responseText);
    }, dataType)
    .fail(function(xhr, errorType, errorMessage){
        console.log(errorMessage);
        console.log(xhr.responseText);
        
        switch(errorType){
            case "parsererror":
                console.log("Error in parser, type " + dataType);
                break;
        }
        
        $message.text("Ah ocurrido un error...");
        setTimeout(function(){
            $message.fadeIn(function(){
                $message.remove();
                disabledElements(false);
            });
        }, 2000);
    });
}



/* Forms Tools */
function mkFormPopUp(IdFormPopUp, btn_close_display) {
    var FormPP = _SQS.id(IdFormPopUp);
    if (FormPP) {
        clog("El Formularion PopUp que desea crear ya existe...");
        return FormPP;
    }
    //FPP: Form PopUp
    var attrElem = {
        "id": IdFormPopUp
    };
    var attrStyle = {
        "background-color": "rgba(0,0,0,0.5)",
        "display": "none",
        "min-height": "100%",
        "position": "absolute",
        "top": "0em",
        "left": "0em",
        "width": "100%",
        "z-index": "1000"
    };
    var FPU = _SQE.mk("div", attrElem, attrStyle);

    var attrStyleContent = {
        "background-color": "#FFF",
        "display": "block",
        "position": "relative",
        "top": "5em",
        "left": "14em",
        "width": "1000px",
        "z-index": "1001"
    };
    var contentFPU = _SQE.mk("div", {"id": IdFormPopUp + "-F"}, attrStyleContent);

    var attrStyleBtnClose = {
        "background-color": "#006484",
        "color": "#FFF",
        "cursor": "pointer",
        "padding": "0.5em 0.8em",
        "position": "absolute",
        "top": "-0.8em",
        "right": "-0.8em",
        "z-index": "1003"
    };

    var btnCloseFPU = _SQE.mk("div", null, attrStyleBtnClose);
    btnCloseFPU.innerHTML = "Cerrar";
    btnCloseFPU.onclick = function () {
        if ($) {
            $("#" + this.parentNode.parentNode.id).fadeOut(1000);
        } else {
            _SQE.delElemet(this.parentNode.parentNode);
        }
    };

    var attrElemResponse = {
        "id": IdFormPopUp + "-FPP"
    };
    var responseFPU = _SQE.mk("div", attrElemResponse);

    if (btn_close_display) {
        _SQE.addChild(contentFPU, btnCloseFPU);
    }
    _SQE.addChild(contentFPU, responseFPU);
    _SQE.addChild(FPU, contentFPU);

    var body = _SQS.tag("body")[0];
    _SQE.addChild(body, FPU);
    return FPU;
}

function showFormPopUp(IdFormPopUp, url, btn_close_display) {
    if (typeof IdFormPopUp === "string") {
        IdFormPopUp = mkFormPopUp(IdFormPopUp, btn_close_display);
    }
    if (IdFormPopUp) {
        if ($) {
            $("#" + IdFormPopUp.id + "-F").css({
                "top": (document.body.scrollTop + 40) + "px",
                "left": ((document.body.scrollWidth - $("#" + IdFormPopUp.id + "-F").width()) / 2) + "px"
            });
            $("#" + IdFormPopUp.id).fadeIn(1000);
        } else {
            var attrStyle = {
                "display": "block"
            };
            _SQE.addStyleAttributes(IdFormPopUp, attrStyle);
        }
        AjaxGET(url, IdFormPopUp.id + "-FPP");
    } else {
        clog("El Formulario PopUp con Id " + IdFormPopUp.id + " no existe...");
    }
}

/* Funciones de seleccion de elementos */
//SQS: SonQo Selection -- Seleccion de Elementos
var _SQS = {
    class: function (className, parentNode) {
        if (!parentNode) {
            parentNode = document;
        }
        return parentNode.getElementsByClassName(className);
    },
    each: function (Elements, callback) {
        for (var i = 0; i < Elements.length; i++) {
            callback(Elements[i], i);
        }
    },
    id: function (idElement) {
        return document.getElementById(idElement);
    },
    tag: function (tagName, parentNode) {
        if (!parentNode) {
            parentNode = document;
        }
        return parentNode.getElementsByTagName(tagName);
    },
    search: function (tagName, attributes, parentNode) {
        if (!parentNode) {
            parentNode = document;
        }
        
        var nodes = _SQS.tag(tagName, parentNode),
        resp_nodes = [];
        
        _SQS.each(nodes, function(node){
            for (var attr in attributes) {
                var rgx = new RegExp(attributes[attr]);
                
                if (node[attr].match(rgx)) {
                    resp_nodes.push(node);
                }
            }
        });
        
        if (resp_nodes.length) {
            console.log("Elementos encontrados: " + resp_nodes.length);
            return resp_nodes;
        } else {
            console.log("No se encontraron elementos");
            return null;
        }
    }
};
// SQE: SonQo Elements -- Manejo de Elementos 
var _SQE = {
    addAttributes: function (Element, attributesElement) {
        for (var attr in attributesElement) {
            Element.setAttribute(attr, attributesElement[attr]);
        }
    },
    addChild: function (Element, childElement) {
        Element.appendChild(childElement);
    },
    addClass: function (Element, className) {
        if (!_SQE.classExists(Element, className)) {
            Element.className += " " + className;
        }
    },
    addStyleAttributes: function (Element, styleAttributesElement) {
        for (var attr in styleAttributesElement) {
            Element.style[attr] = styleAttributesElement[attr];
        }
    },
    classExists: function (Element, className) {
        return Element.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
    },
    delElemet: function (Element) {
        Element.parentNode.removeChild(Element);
    },
    insertAfter: function (Element, newElement) {
        if (Element.nextSibling) {
            Element.parentNode.insertBefore(newElement, Element.nextSibling);
        } else {
            Element.parentNode.appendChild(newElement);
        }
    },
    insertBefore: function (Element, newElement) {
        Element.parentNode.insertBefore(newElement, Element);
    },
    mk: function (TagName, attributesElement, styleAttributesElement) {
        var element = document.createElement(TagName);
        if (attributesElement) {
            _SQE.addAttributes(element, attributesElement);
        }
        if (styleAttributesElement) {
            _SQE.addStyleAttributes(element, styleAttributesElement);
        }
        return element;
    },
    removeClass: function (Element, className) {
        if (_SQE.classExists(Element, className)) {
            var exp = new RegExp('(\\s|^)' + className + '(\\s|$)');
            Element.className = Element.className.replace(exp, "");
        }
    }
};

/**
 * 
 * Request asynchronous js file
 *
 * @param  {string} file
 * @param  {function} callback
 * 
 * @return {void}
 */

function include(file, callback) {
    if (!file) {
        return;
    }

    if (!callback) {
        callback = function () {};
    }

    var scripts = _SQS.search("script", { src : file });

    if(scripts){
        callback();
        return;
    }

    var head = window.document.head;
    var script = _SQE.mk("script");

    script.src = file;
    script.type = 'text/javascript';
    script.onload = callback;
    //IE
    script.onreadystatechange = function () {
        if (this.readyState === 'complete') {
            callback();
        }
    };

    head.appendChild(script);
}

/*-------------------------------
 
    ZILLI.JS
 
    Extension plugin for jQuery
 
    @author Todd Francis
    @version 0.1

 -------------------------------*/

(function ($) {

    'use strict';
    $.id = function (id) {
        return $("#" + id);
    };
}(jQuery));






				 
function seleccionaProducto(idProducto,entidad) {
	
	    var ruta = '/system/_vistas/se_pedidos.php?Main=CapturaPedido';
	    var parametros = {
			'idProducto': idProducto,
			'entidad': entidad,
		};
		
		alertify.log("Procesando Datos..");
	
		$.ajax({
			data: parametros,
			url: ruta,
			type: 'get',
			async: true,
			success: function(response) {
				// alertify.success(response);
				var l_a_json = eval('(' + response + ')');
				var mensaje = l_a_json["msj"];
				var nroItem = l_a_json["nro_articulos"];
				var codPedido = l_a_json["codPedido"];
				
				
				var id_btn_compras = "btn_"+idProducto;
			    $("#"+id_btn_compras+"").attr("href","/informacion-pago/entidad/"+entidad+"/pedido/"+codPedido+"");
			    $("#"+id_btn_compras+"").attr("style","background-color:#ff999a;");
			    $("#"+id_btn_compras+"").html("IR A PAGAR");
								
				alertify.success(mensaje);
				$('#indicador-pedido').html(nroItem);
				$('#indicador-pedido').fadeIn(1500);
			}
		});

}
						 
function seleccionaProductoListaPreferencias(idProducto,entidad,cod_cliente) {
	
	    var ruta = '/system/_vistas/se_lista_preferencias.php?Main=capturaArticulo';
	    var parametros = {
			'idProducto': idProducto,
			'entidad': entidad,
			'cod_cliente': cod_cliente
			
		};
		
		// alertify.log("Procesando Datos..");
	
		$.ajax({
			data: parametros,
			url: ruta,
			type: 'get',
			async: true,
			success: function(response) {
		
				alertify.success(response);
			}
		});

}

function consultas(empresa,codPedido,idProducto,cod_cliente) {
	
	    var ruta = '/system/_vistas/se_consulta_tienda.php?Main=Enviar&empresa='+empresa+'&codPedido='+codPedido+'&idProducto='+idProducto+'&cod_cliente='+cod_cliente+'';

		$.ajax({                        
           type: "POST",                 
           url: ruta,                     
           data: $("#formularioConsultas").serialize(), 
           success: function(data)             
           {
			    alertify.success(data);
				// $('#msjAjax').html(data);
				$("#comment").val("");
				
				// var explode = function(){
				    // location.href="/producto-programa-educativo/id/<?= $idProducto; ?>/entidad/<?php echo $empresa; ?>";
				// };
				// setTimeout(explode, 3000);
           }
        });	
}

function conexionProeducative(empresa,producto,tipoProducto){
	
	    var parametros = {
			'empresa': empresa,
			'producto': producto,
			'tipoProducto': tipoProducto
		};	  
	    
		$("#btn_"+producto+"").html("... Cargando datos");
		
	    var ruta = '/system/_vistas/se_conexion_proeducative.php?Main=Conexion';
		$.ajax({
			data: parametros,
			url: ruta,
			type: 'get',
			async: true,
			success: function(response) {
				
				var l_a_json = eval('(' + response + ')');
				var SesionIdCliente = l_a_json["SesionIdCliente"];
				var Email = l_a_json["Email"];
				var EntidadCreadora = l_a_json["EntidadCreadora"];
				var IdProducto = l_a_json["IdProducto"];
				var TipoProducto = l_a_json["TipoProducto"];

				var url ="http://owlgroup.local/system/redirect.php?SesionIdCliente="+SesionIdCliente+"&Email="+Email+"&EntidadCreadora="+EntidadCreadora+"&IdProducto="+IdProducto+"&TipoProducto="+TipoProducto+""; 
				
				$("#btn_"+producto+"").html('<i class="glyphicon glyphicon-hand-up"></i><strong>Confirmar acceso </strong>');
				$("#btn_"+producto+"").attr("style", "background-color: #3fcf44");
				$("#btn_"+producto+"").attr("target", "_blank");
				$("#btn_"+producto+"").attr("href",url);
				$("#btn_"+producto+"").attr("onclick","actualizaPageMC('"+empresa+"');");
				

           }
        });		
}

function actualizaPageMC(empresa){
	
	location.href="/compras-realizadas/entidad/"+empresa+"";
				
}


/*
 * Envío de todos los valores capturados desde la pasarela
 * registra y solicita un proceso (Paypal)
 */

function FinalizaCompra(tipopago){
	
		$('#msgloading')[0].textContent='Esperando Culqi...';

		$.post( '/_librerias/php/Culqi/CulqiController.php' ,
		{"method":"processrequest"
		,"name":rr.usrNombres
		,"lastname":rr.usrApellidos
		,"email":rr.usrEmail
		,"phone":rr.Telefono
		,"address":rr.client_address
		,"total":rr.TotalPrecio
		,"id_proform":rr.CodProforma
		,"id_client":rr.CodCliente
		,"ctasuscripcion":sell.data.CtaSuscripcion}
		).done(function(rsp){
			
			rsp = JSON.parse(rsp);
			if(rsp.success){
					// Código del comercio
					checkout.codigo_comercio   = rsp.data.codigo_comercio;
					// La informacion_venta es el contenido del parámetro que recibiste en la creación de la venta.
					checkout.informacion_venta = rsp.data.informacion_venta;
					// Activa el botón de pago, al darle click mostrará el formulario de pago
					checkout.abrir();
			}else{
				if(rsp.id_error==3){
					// duplicated sell
					$('#msgloading')[0].textContent='Ha surgido un error en la compra , vuelva a intentarlo por favor.';

					setTimeout(function(){
						window.location.href = '/shoppingcart/'+ rsp.new_proform.entity +'/'+ rsp.new_proform.id +'/'+ rsp.new_proform.session;
					},2000);
					
				}else{
					alert("Este servicio no esta disponible por ahora. Inténtelo más tarde por favor.");
				}
				
				$('#loading').hide();
			}											

		}).fail(function(jqXHR, exception) {
			console.log(jqXHR+' '+exception);
		});
}

//#########################################################################################

