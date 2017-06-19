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