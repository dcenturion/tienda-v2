var url_domain_current = "https://" + document.domain;
//help comment
CALENDAR_ID = null;
ADMINISTRATION_CALENDAR_PROGRAMA_ALMACEN    = null;
ADMINISTRATION_CALENDAR_CURSO_ALMACEN       = null;

function parseScript(strcode) {
    var scripts = new Array();         // Array which will store the script's code

    // Strip
    while (strcode.indexOf("<script") > -1 || strcode.indexOf("</script") > -1) {
        var s = strcode.indexOf("<script");
        var s_e = strcode.indexOf(">", s);
        var e = strcode.indexOf("</script", s);
        var e_e = strcode.indexOf(">", e);

        // Add to scripts array
        scripts.push(strcode.substring(s_e + 1, e));
        // Strip from strcode
        strcode = strcode.substring(0, s) + strcode.substring(e_e + 1);
    }

    // Loop through every script collected and eval it
    for (var i = 0; i < scripts.length; i++) {
        try {
            eval(scripts[i]);
        }
        catch (ex) {
            // do what you want here when a script fails
        }
    }
}

function controlaActivacionPaneles(sUrls, sTipoAjax) {
    sMatriUrls = sUrls.split('|');
    var sCuerpo = document.getElementById("cuerpo");
    sCuerpo.innerHTML = "";
    for (i = 0; i < sMatriUrls.length - 1; i++) {
        var xFactor = sMatriUrls[i].split('[');
        sPanel = xFactor[0];
        sClass = xFactor[1];
        sTiempo = xFactor[3];
        creaDiv(sPanel, sClass);
        setTimeout(function () {
            cargaContenido(sUrls);
        }, sTiempo);
    }
}


function controlaActivacionPanelesLogin(sUrls, sTipoAjax) {
    sMatriUrls = sUrls.split('|');
    var sCuerpo = document.getElementById("cuerpo");
    sCuerpo.innerHTML = "";
    for (i = 0; i < sMatriUrls.length - 1; i++) {
        console.log(sMatriUrls[i]);
        var xFactor = sMatriUrls[i].split('[');
        sPanel = xFactor[0];
        sClass = xFactor[1];
        sTiempo = xFactor[3];
        creaDiv(sPanel, sClass);
        setTimeout(function () {
            cargaContenido(sUrls);
        }, sTiempo);
    }
}

function creaDiv(sDivHijo, sClass) {

    var sCuerpo = document.getElementById("cuerpo");
    var sPanel = document.createElement('div');
    sPanel.innerHTML = "";
    sPanel.setAttribute('id', sDivHijo);
    sPanel.setAttribute('class', sClass);
    sCuerpo.appendChild(sPanel);

}

function cargaContenido(sUrls) {
    sMatriUrls = sUrls.split('|');
    for (i = 0; i < sMatriUrls.length - 1; i++) {
        var xFactor = sMatriUrls[i].split('[');

        sPanel = xFactor[0];
        var sPanelP = document.getElementById(sPanel);
        var contenido = sPanelP.innerHTML;
        if (contenido == "") {
            var sId = sPanel;
            sUrl = xFactor[2];
            sTAjax = xFactor[4];
            break;
        } else {

        }
    }

    traeDatos(sUrl, sId, true)
    // setInterval(function(){alert("hola mundo"+sId);}, 20000 );

}

function creaObjecto(sB) {
    var chartData;
    var sVI = sB.split("]");
    var instanciaA = new Object();
    instanciaA["Panel"] = "" + sVI[0] + "";
    instanciaA["Tiempo"] = parseInt(sVI[1]);
    instanciaA["url"] = parseInt(sVI[2]);
    instanciaA["Funcion"] = function () {
    };
    chartData = sVI[1];
    return chartData;
}

function panelAdm(panel, accion) {
    var vpanel = document.getElementById(panel);
    if (accion == "Abre") {
        vpanel.setAttribute('class', 'panel-Float');
    } else {
        vpanel.setAttribute('class', 'panelCerrado');
    }

}

function panelAdmB(panel, accion, ClassAbrir) {
    var vpanel = document.getElementById(panel);

    if (accion == "Abre") {
        if (ClassAbrir != "") {
            vpanel.setAttribute('class', ClassAbrir);
        } else {
            vpanel.setAttribute('class', 'panel-Float');
        }
    } else {
        vpanel.setAttribute('class', 'panelCerrado');
    }

}



function subeImagen(sUrl, formid, sDivCon, sPath, sIdFile) { //aaa1
    document.getElementById(sDivCon).innerHTML = '<img src="./_imagenes/loading.gif" width="50px"><div class="loading">Cargando...</div>';

    var fileInput = document.getElementById(sIdFile);
    var file = fileInput.files[0];

    var formData = new FormData();
    formData.append("file", file);

    var ParametrosAjax = [];
    ParametrosAjax[3] = sUrl;
    ParametrosAjax[5] = sDivCon;
    
    var xhr = crearXMLHttpRequest();
    xhr.open('POST', sUrl + "&TipoDato=archivo&path=" + sPath + "&formId=" + formid + "&campo=" + sIdFile, true);
    xhr.onreadystatechange = function () {
        procesarEventos(xhr, ParametrosAjax);
    };
    xhr.send(formData);
    return true;
}

function InsBefore(elem, newelem) {
    var padre = elem.parentNode;
    padre.insertBefore(newelem, elem);
}

function getFormOfElement(elem) {
    var parentform = elem.parentNode;
    while (parentform.tagName !== 'FORM') {
        parentform = parentform.parentNode;
    }
    return parentform;
}

function CreaNodo(Tag, Atributos) {

    var Nodo = document.createElement(Tag);
    if (Atributos["Clase"]) {
        Nodo.setAttribute('class', Atributos["Clase"]);
    }
    if (Atributos["Id"]) {
        Nodo.setAttribute('id', Atributos["Id"]);
    }
    if (Atributos["Estilo"]) {
        Nodo.setAttribute('style', Atributos["Estilo"]);
    }
    if (Atributos["Html"]) {
        Nodo.innerHTML = Atributos["Html"];
    }
    if (Atributos["AClick"]) {
        Nodo.setAttribute('onclick', Atributos["AClick"]);
    }
    return Nodo;
}

function ActualizaNodo(IdTag, Atributos) {

    var Nodo = document.getElementById(IdTag);
    if (Atributos["Clase"]) {
        Nodo.setAttribute('class', Atributos["Clase"]);
    }
    if (Atributos["Id"]) {
        Nodo.setAttribute('id', Atributos["Id"]);
    }
    if (Atributos["Estilo"]) {
        Nodo.setAttribute('style', Atributos["Estilo"]);
    }
    if (Atributos["AClick"]) {
        Nodo.setAttribute('onclick', Atributos["AClick"]);
    }
    // NodoHijo2.setAttribute('onclick',"DelCBI_OpcCBI(this,'" + IdControl + "')");
    if (Atributos["Html"] != "Lleno") {
        Nodo.innerHTML = Atributos["Html"];
    }

    return Nodo;
}

function DesapareceMsg(IdPanel) {

    var VpanelMsg = ActualizaNodo(IdPanel, {Clase: "PanelAlerta"});

}

function  PanelMsgForm(IdPanel, b, Msg) {
    // alert(IdPanel);
    var VpanelMsg = ActualizaNodo(IdPanel, {Clase: "s", Html: ""});
    var PadreCA = CreaNodo("div", {Clase: "", Id: "ContenedorMsgB" + b + "", Estilo: "position:relative;"});
    var PadreCB = CreaNodo("div", {Clase: "PanelAlertaC", Id: "ContenedorMsgB2" + b + "", Html: Msg});
    var PadreCB2 = CreaNodo("div", {Clase: "Triangulo", Id: "ContenedorMsgBB2" + b + ""});
    var PadreCB3 = CreaNodo("div", {Clase: "BtnCerrarMsg", Id: "ContenedorMsgBB3" + b + "", Html: "x", AClick: "DesapareceMsg('" + IdPanel + "');"});

    PadreCA.appendChild(PadreCB);
    PadreCA.appendChild(PadreCB2);
    PadreCA.appendChild(PadreCB3);
    VpanelMsg.appendChild(PadreCA);

}

function enviaForm(ParametrosAjax) {

    formid = ParametrosAjax[2];
    sUrl = ParametrosAjax[3];
    campoFinal = ParametrosAjax[4];
    sDivCon = ParametrosAjax[5];
    sIdCierra = ParametrosAjax[6];
    ParametrosInput = ParametrosAjax[7];
    UrlPrimerBtn = ParametrosAjax[8];

    if (sIdCierra !== "") {
        panelAdm(sIdCierra, "Cierra");
    }

    var Formulario = document.getElementById(formid);
    var form_elements = Formulario.elements;
    var cadenaFormulario = "";
    var _y = "&";
    
    for (var i = 0; i < form_elements.length; i++) {
        var elem = form_elements[i], responseValue, success = true;
        if (elem.getAttribute('data-CBI') !== true && elem.name) {
        
            // alert(elem.type);
            switch (elem.type) {
                case "text":
                case "password":
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
                responseValue = responseValue.replace(/'/g, '"').replace(/&nbsp;/g, "<1001>").replace(/&/g, " ");
                cadenaFormulario += _y + elem.name + '=' + encodeURI(responseValue);
            }
        }
    }

    document.getElementById(sDivCon).innerHTML = '<img src="./_imagenes/loading.gif" width="50px"><div class="loading">Cargando...</div>';

    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("POST", sUrl + "&TipoDato=texto&formId=" + formid, true);
    sAjaxMotor.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=ISO-8859-1');
    sAjaxMotor.onreadystatechange = function () {
        procesarEventos(sAjaxMotor,ParametrosAjax)
    };
    sAjaxMotor.send(cadenaFormulario);
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
    
    console.log(params);
    
    //prepare elements to wait the request
    var $message = $("<div>").text("Enviando...");
    $(form).prepend($message);
    disabledElements(true);
    
    $.post(url, params, function(responseText){
        $message.remove();
        disabledElements(false);
        
        callback(responseText);
    }, dataType);
}

function ValidaFormulario(IdForm) {

    var Formulario = document.getElementById(IdForm);
    var form_elements = Formulario.elements;
    var cadenaFormulario = "";
    var _y = "&";

    for (var i = 0; i < form_elements.length; i++) {
        var elem = form_elements[i], responseValue, success = true;
        if (elem.getAttribute('data-valida') !== "") {
            if (elem.getAttribute('data-valida') !== null) {
                elem.getAttribute('data-valida');
            }

        }
    }
    //exit();


}


function enviaReg(id, url, panel, id_table)
{
    if (id_table != "") {
        var linea = document.getElementById(id);
        linea.style.backgroundColor = 'rgba(224,224,224,0.6)';
        recorrerTabla(id_table, id);
    }
    document.getElementById(panel).innerHTML = '<img src="./_imagenes/loading.gif" width="50px"><div class="loading">Cargando...</div>';
    
    var ParametrosAjax = [];
    ParametrosAjax[3] = url;
    ParametrosAjax[5] = panel;
    
    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("GET", url, true);
    sAjaxMotor.onreadystatechange = function () {
        procesarEventos(sAjaxMotor, ParametrosAjax);
    }
    sAjaxMotor.send(null);
}


function AjaxData(url, panel)
{
    var ParametrosAjax = [];
    ParametrosAjax[3] = url;
    ParametrosAjax[5] = panel;
    
    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("GET", url, true);
    sAjaxMotor.onreadystatechange = function () {
        procesarEventos(sAjaxMotor, ParametrosAjax);
    }
    sAjaxMotor.send(null);
}



function sendRow(row, url, panel)
{
    var rows = row.parentNode.childNodes;
    for (var i = 0; i < rows.length; i++) {
        rows.item(i).style.backgroundColor = "";
    }
    row.style.backgroundColor = 'rgba(224,224,224,0.6)';
    var ParametrosAjax = [];
    ParametrosAjax[3] = url;
    ParametrosAjax[5] = panel;
    
    document.getElementById(panel).innerHTML = '<img src="./_imagenes/loading.gif" width="50px"><div class="loading">Cargando...</div>';
    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("GET", url, true);
    sAjaxMotor.onreadystatechange = function () {
        procesarEventos(sAjaxMotor, ParametrosAjax)
    }
    sAjaxMotor.send(null);
}

function stopPropagacion(event) {
    var event = event || window.event;
    event.stopPropagation();
}

function sendLink(event, url, panel)
{
    document.getElementById(panel).innerHTML = '<img src="./_imagenes/loading.gif" width="50px"><div class="loading">Cargando...</div>';
    var ParametrosAjax = [];
    ParametrosAjax[3] = url;
    ParametrosAjax[5] = panel; 
    
    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("GET", url, true);
    sAjaxMotor.onreadystatechange = function () {
        procesarEventos(sAjaxMotor, ParametrosAjax);
    };
    sAjaxMotor.send(null);
    event.preventDefault();
}

function enviaVista(url, panel, sIdCierra) {
    // alert("url  " + url + "  paenl "+panel);
    // exit();

    if (document.getElementById(panel)) {
        if (sIdCierra !== "") {
            panelAdm(sIdCierra, "Cierra");
        }
        document.getElementById(panel).innerHTML = '<img src="./_imagenes/loading.gif" width="50px"><div class="loading">Cargando...</div>';
        
        var ParametrosAjax = [];
        ParametrosAjax[3] = url;
        ParametrosAjax[5] = panel; 

        var sAjaxMotor = false;
        sAjaxMotor = crearXMLHttpRequest();
        sAjaxMotor.open("GET", url, true);
        sAjaxMotor.onreadystatechange = function () {
            procesarEventos(sAjaxMotor,ParametrosAjax);
        };
        sAjaxMotor.send(null);
    } else {
        console.log('No existe Elemento con Id: ' + panel);
    }
}

function enviaVistaMenu(url, panel, idUL, idLI) {
    // alert("url  " + url + "  paenl "+panel);
    // exit();

	$('#'+idUL+' li').each(function(indice, elemento) {
		var idLis = $(elemento).attr("id");
	    $("#"+idLis+"").attr("style","background-color:#fff");
		$("#"+idLis+", a").attr("style","background-color:#fff;");
		$("#"+idLis+" .icon").attr("style","color: #bebcbe;font-weight: 300;");
	    console.log('El elemento con el índice '+indice+' contiene '+$(elemento).attr("id"));
	});
	
	$("#"+idLI+"").attr("style","background-color:#edecf1");
	$("#"+idLI+" a").attr("style","background-color:#edecf1;color: #2196F3;font-weight: 700;");
	$("#"+idLI+" .icon").attr("style","color:#0e7fd9;font-weight: 700;");
	
    if (document.getElementById(panel)) {
       
        document.getElementById(panel).innerHTML = '<img src="./_imagenes/loading.gif" width="50px"><div class="loading">Cargando...</div>';
        
        var ParametrosAjax = [];
        ParametrosAjax[3] = url;
        ParametrosAjax[5] = panel; 

        var sAjaxMotor = false;
        sAjaxMotor = crearXMLHttpRequest();
        sAjaxMotor.open("GET", url, true);
        sAjaxMotor.onreadystatechange = function () {
            procesarEventos(sAjaxMotor,ParametrosAjax);
        };
        sAjaxMotor.send(null);
    } else {
        console.log('No existe Elemento con Id: ' + panel);
    }
}


function sendPageCustionario(url, idform, idpanel) {
    var form = document.getElementById(idform),
            elements = form.elements,
            num_elements = 0,
            num_success = 0;

    var toType = function (obj) {
        return ({}).toString.call(obj).match(/\s([a-z|A-Z]+)/)[1].toLowerCase()
    };
//    var Mx=[]; Mx.push(elements); console.log(Mx);
    for (var x in elements) {
        console.log(toType(elements[x]));
//        console.log(elements[x]);

        var elem = elements[x];

        switch (toType(elem)) {
            case "radionodelist":
                num_elements++;
                if (elem.length > 0) {
                    var bool_success = false;
                    var num_item = elem.length;
                    for (var i = 0; i < num_item; i++) {
                        var aux_elem = elem[i];
                        if (aux_elem.checked) {
                            bool_success = true;
                        }
                    }
                    if (bool_success) {
                        num_success++;
                    }
                }
                break;
        }
    }
    
    var ParametrosAjax = [];
    ParametrosAjax[0] = "";
    ParametrosAjax[1] = "";
    ParametrosAjax[2] = idform;
    ParametrosAjax[3] = url;
    ParametrosAjax[4] = "";
    ParametrosAjax[5] = idpanel;
    ParametrosAjax[6] = "";
    ParametrosAjax[7] = "";
    ParametrosAjax[8] = "";
    if (num_success === num_elements) {
        enviaForm(ParametrosAjax);
    } else {
    }
}

function ConexAjax(url, panel)
{
    var sAjaxMotor = false;
    // this.val_function;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("GET", url, true);
    sAjaxMotor.onreadystatechange = function () {
        procesarEventosSC(sAjaxMotor, panel, url);
    }
    sAjaxMotor.send(null);

    // return val_function;
}

function procesarEventosSC(sAjaxMotor, divContenido, url)
{
    window.status = url;
    var detalles = document.getElementById(divContenido);
    if (sAjaxMotor.readyState === 4) {
        switch (sAjaxMotor.status) {
            case 200:
                detalles.innerHTML = sAjaxMotor.responseText;

                parseScript(sAjaxMotor.responseText);
                break
            case 404:
                document.getElementById(containerid).innerHTML = "ERROR: La página no existe<br>" + url;
                break
            case 500:
                document.getElementById(containerid).innerHTML = "ERROR: Del servidor<br />" + page_request.status + page_request.responseText;
                break
            default:
                document.getElementById(containerid).innerHTML = "ERROR: Desconocido<br />" + page_request.status + page_request.responseText;
                break
        }
    }
    // alert(vareturn);
    // return vareturn;
}


function traeDatos(url, divContenido, tipoAjax)
{
    var ob;
    divContenidoA = divContenido;
    cargarData(url, tipoAjax, divContenido);
}

function cargarData(url, tipoAjax, divContenido)
{
    document.getElementById(divContenido).innerHTML = '<img src="./_imagenes/loading.gif" width="50px"><div class="loading">Cargando...</div>';
    var ParametrosAjax = [];
    ParametrosAjax[3] = url;
    ParametrosAjax[5] = divContenido; 
   
    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.onreadystatechange = function () {
        procesarEventos(sAjaxMotor, ParametrosAjax);
    };
    sAjaxMotor.open("GET", url, tipoAjax);
    sAjaxMotor.send(null);

}

function procesarEventos(sAjaxMotor,ParametrosAjax)
{

    sUrl = ParametrosAjax[3];
    sDivCon = ParametrosAjax[5];
    ParametrosInput = ParametrosAjax[7];
    UrlPrimerBtn = ParametrosAjax[8];
    window.status = sUrl;
    ///////////////nnnnnnnnnnnnnnnnnnnnnnnnnnnn
    var detalles = document.getElementById(sDivCon);
        // alert("bbb");        
    if (sAjaxMotor.readyState === 4) {
    
        switch (sAjaxMotor.status) {
            case 200:
                detalles.innerHTML = sAjaxMotor.responseText;
                parseScript(sAjaxMotor.responseText);
                        
                if(ParametrosInput !== undefined){
                   var ParametrosInput = ParametrosInput.toString();
                    var ParametrosInputR = ParametrosInput.split(',');      
                    LanzaValorBA(ParametrosInputR[0], ParametrosInputR[1], ParametrosInputR[2], ParametrosInputR[4], UrlPrimerBtn, ParametrosInputR[3]);
                    panelAdmB('PanelForm-Oculto','Cierra');
                }
                
                break
            case 404:
                document.getElementById(containerid).innerHTML = "ERROR: La página no existe<br>" + url;
                break
            case 500:
                document.getElementById(containerid).innerHTML = "ERROR: Del servidor<br />" + page_request.status + page_request.responseText;
                break
            default:
                document.getElementById(containerid).innerHTML = "ERROR: Desconocido<br />" + page_request.status + page_request.responseText;
                break
        }
    }
}


function crearXMLHttpRequest()
{
    var xmlHttp = null;
    if (window.ActiveXObject)
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    else
    if (window.XMLHttpRequest)
        xmlHttp = new XMLHttpRequest();
    return xmlHttp;
}


//***************************************
//Funciones comunes a todos los problemas
//***************************************
function addEvent(elemento, nomevento, funcion, captura)
{
    if (elemento.attachEvent)
    {
        elemento.attachEvent('on' + nomevento, funcion);
        return true;
    }
    else
    if (elemento.addEventListener)
    {
        elemento.addEventListener(nomevento, funcion, captura);
        return true;
    }
    else
        return false;
}

var xmlhttp;
function AbrirFichero(fichXML)
{

    var xmlDoc = undefined;
    try
    {
        alert("1");
        if (document.all) //IE
        {
            xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
        }
        else //firefox
        {
            xmlDoc = document.implementation.createDocument("", "", null);
        }
        xmlDoc.async = false;
        xmlDoc.load(fichXML);


    }
    catch (e)
    {

        try { //otros safari, chrome

            var xmlhttp = new window.XMLHttpRequest();
            xmlhttp.open("GET", fichXML, false);
            alert("conf");
            xmlhttp.send(null);

            xmlDoc = xmlhttp.responseXML.documentElement;
            return xmlDoc;
        }
        catch (e)
        {
            return undefined;
        }

    }
    return xmlDoc;
}

function panelAdmA(panel, boton, msg) {
    var vpanel = document.getElementById(panel);
    var vBoton = document.getElementById(boton);
    // alert(vpanel.className);
    if (vpanel.className == "panelCerrado") {
        vBoton.innerHTML = "Cerrar";
        vpanel.setAttribute('class', 'panel-Abierto');
    } else {
        vBoton.innerHTML = msg;
        vpanel.setAttribute('class', 'panelCerrado');
    }
}

function AbreCierra(panel, panelB) {
    var vpanel = document.getElementById(panel);
    var vpanelb = document.getElementById(panelB);
    // alert(vpanel.className);
    if (vpanel.className == "panelCerrado") {
        // vBoton.innerHTML = "Cerrar";
        vpanel.setAttribute('class', 'panel-Abierto');
        vpanelb.setAttribute('class', 'panelCerrado');
    } else {
        // vBoton.innerHTML = msg;
        vpanelb.setAttribute('class', 'panel-Abierto');
        vpanel.setAttribute('class', 'panelCerrado');
    }
}

function MuestraPanel(panel, ClaseAbre) {
    var vpanel = document.getElementById(panel);
    // alert(vpanel.className);
    if (vpanel.className == "panelCerrado") {
        // vBoton.innerHTML = "Cerrar";
        vpanel.setAttribute('class', 'panel-Abierto');
    } else {
        vpanel.setAttribute('class', 'panelCerrado');
    }
}

function enviaRegBuscar(id, panel)
{
    // alert(panel);
    var inputVal = document.getElementById(panel);
    inputVal.value = id;
    var nL = panel.length; // numeroLetras = 10
    nL = nL - 2;
    var tr = document.getElementById(id);
    var panelConId = panel.substring(0, nL);
    var campoDesc = document.getElementById(panelConId + "_DSC");
    campoDesc.innerHTML = tr.cells[0].innerHTML + ' ' + tr.cells[1].innerHTML;
    panelAdm(panelConId, "Cierra");

}

function enviaFormRD(sUrl, formid, sDivCon, urlRedirecionamineto)
{
    // if(sIdCierra != "" ){panelAdm(sIdCierra,"Cierra");}
    var Formulario = document.getElementById(formid);
    //alert(formid+" - "+Formulario);
    // return false;
    var longitudFormulario = Formulario.elements.length;
    var cadenaFormulario = "";
    var sepCampos = "";
    for (var i = 0; i <= Formulario.elements.length - 1; i++) {

        if (Formulario.elements[i].type == "text") {
            cadenaFormulario += sepCampos + Formulario.elements[i].name + '=' + encodeURI(Formulario.elements[i].value);
            sepCampos = "&";
        }
        if (Formulario.elements[i].type == "password") {
            cadenaFormulario += sepCampos + Formulario.elements[i].name + '=' + encodeURI(Formulario.elements[i].value);
            sepCampos = "&";
        }
        if (Formulario.elements[i].type == "textarea") {
            var sTextAreaValue = document.getElementById(Formulario.elements[i].name + "-Edit");
            alert(sTextAreaValue.innerHTML);
            cadenaFormulario += sepCampos + Formulario.elements[i].name + '=' + encodeURI(sTextAreaValue.innerHTML);
            sepCampos = "&";
        }
        if (Formulario.elements[i].type == "submit") {
            cadenaFormulario += sepCampos + Formulario.elements[i].name + '=' + encodeURI(Formulario.elements[i].value);
            sepCampos = "&";
        }
        if (Formulario.elements[i].type == "hidden") {
            cadenaFormulario += sepCampos + Formulario.elements[i].name + '=' + encodeURI(Formulario.elements[i].value);
            sepCampos = "&";
        }
        if (Formulario.elements[i].type == "file") {
            // alert(Formulario.elements[i].value);
            if (Formulario.elements[i].value !== "") {
                var sObjectForm = document.getElementById(Formulario.elements[i].id);
                var sPath = sObjectForm.getAttribute('ruta');
                // alert(Formulario.elements[i].id);
                // subeImagen(sUrl,formid,sDivCon,sPath,Formulario.elements[i].id);
            }
            //alert(Formulario.elements[i].value);

            cadenaFormulario += sepCampos + Formulario.elements[i].name + '=' + encodeURI(Formulario.elements[i].value);
            sepCampos = "&";
        }
        if (Formulario.elements[i].type == "checkbox") {
            if (Formulario.elements[i].checked) {
                cadenaFormulario += sepCampos + Formulario.elements[i].name + '=' + encodeURI(Formulario.elements[i].value);
                sepCampos = "&";
            }
        }
        if (Formulario.elements[i].type == "radio") {
            if (Formulario.elements[i].checked) {
                cadenaFormulario += sepCampos + Formulario.elements[i].name + '=' + encodeURI(Formulario.elements[i].value);
                sepCampos = "&";
            }
        }
        if (Formulario.elements[i].tagName == "SELECT") {
            cadenaFormulario += sepCampos + Formulario.elements[i].name + '=' + encodeURI(Formulario.elements[i].value);
            sepCampos = "&";
        }
    }

    document.getElementById(sDivCon).innerHTML = '<img src="./_imagenes/loading.gif" width="20px">Cargando ...';

    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("POST", sUrl + "&TipoDato=texto&formId=" + formid, true);
    sAjaxMotor.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=ISO-8859-1');
    var sUrlRD = sUrl.split("?");
    sAjaxMotor.onreadystatechange = function () {
        procesarEventosR(sAjaxMotor, sDivCon, sUrl, urlRedirecionamineto, sUrlRD[1] + "&" + cadenaFormulario)
    }
    sAjaxMotor.send(cadenaFormulario);

}
function procesarEventosR(sAjaxMotor, divContenido, url, urlRD, cadenaFormulario)
{
    window.status = url;
    var detalles = document.getElementById(divContenido);
    if (sAjaxMotor.readyState == 4)
    {
        switch (sAjaxMotor.status) {
            case 200:
                var divCont = sAjaxMotor.responseText;
                var cadenaNew = divCont.indexOf('REDIRECCIONAAJAX');
                // alert(cadenaNew);
                if (cadenaNew != -1) {
                    // lurlRD = urlRD;
                    // alert(urlRD+"?"+cadenaFormulario);
                    location.href = urlRD + "&" + cadenaFormulario;
                    return false;
                }

                detalles.innerHTML = divCont;
                break
            case 404:
                document.getElementById(containerid).innerHTML = "ERROR: La página no existe<br>" + url;
                break
            case 500:
                document.getElementById(containerid).innerHTML = "ERROR: Del servidor<br />" + page_request.status + page_request.responseText;
                break
            default:
                document.getElementById(containerid).innerHTML = "ERROR: Desconocido<br />" + page_request.status + page_request.responseText;
                break
        }
    }

}

function upload(inputFile, url, path, formId, indicatorimg) {
    if (!inputFile.files[0]) {
        return;
    }
    var oTimer = 0;
    var iBytesUploaded = 0;
    var iBytesTotal = 0;
    var iPreviousBytesLoaded = 0;

    function secondsToTime(secs) {
        var hr = Math.floor(secs / 3600);
        var min = Math.floor((secs - (hr * 3600)) / 60);
        var sec = Math.floor(secs - (hr * 3600) - (min * 60));

        if (hr < 10) {
            hr = "0" + hr;
        }
        if (min < 10) {
            min = "0" + min;
        }
        if (sec < 10) {
            sec = "0" + sec;
        }
        if (hr) {
            hr = "00";
        }
        return hr + ':' + min + ':' + sec;
    }
    ;

    function bytesToSize(bytes) {
        var sizes = ['Bytes', 'KB', 'MB'];
        if (bytes === 0) {
            return 'n/a';
        }
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
    }
    ;

    if (window.File && window.FileReader && window.FileList && window.Blob) { //aaa1
        inputFile.disabled = true;
        iPreviousBytesLoaded = 0;
        var oFile = inputFile.files[0];

        var datafile = {dataurl: null, name: null};

        var oReader = new FileReader();
        oReader.onload = (function (thefile) {
            return function (e) {
                datafile.dataurl = e.target.result;
                datafile.name = thefile.name;
            }
        })(oFile);
        oReader.readAsDataURL(oFile);

        var urlVUP = url + "&VUP=Y&filedata=" + inputFile.getAttribute('filedata');//console.log(urlVUP);
        var sAjaxMotor = crearXMLHttpRequest();
        sAjaxMotor.onreadystatechange = function () {
            if (sAjaxMotor.readyState === 4) {
                switch (sAjaxMotor.status) {
                    case 200:
                        var JSONresp = sAjaxMotor.responseText;
                        var response = JSON.parse(JSONresp);
                        var tiposVUP = response.filedata.tipos;

                        var sizeVUP = parseFloat(response.filedata.maxfile, 10) * 1024 * 1024;
                        var ext_oFile = oFile.name.split('.').pop();

                        console.log("Extension de archivo: " + ext_oFile);
                        if (tiposVUP.indexOf(ext_oFile) !== -1) {
                            console.log(ext_oFile + " extension permitido: CORRECTO");
                            if (oFile.size < sizeVUP) {
                                console.log(oFile.size + " < " + sizeVUP + ": CORRECTO");

                                var form = new FormData();
                                var xhr = new XMLHttpRequest();

                                form.append('file', oFile);
                                form.append('path', path);
                                form.append('formId', formId);
                                form.append('filedata', inputFile.getAttribute('filedata'));
                                form.append('campo', inputFile.getAttribute('name'));

                                if (!indicatorimg) {
                                    if (ext_oFile === "jpg") {
                                        CroopImage(inputFile, oFile, datafile, inputFile, url, path, formId);
                                        return;
                                    }
                                }
                                if (indicatorimg) {
                                    form.append('x1', indicatorimg.x1);
                                    form.append('y1', indicatorimg.y1);
                                    form.append('x2', indicatorimg.x2);
                                    form.append('y2', indicatorimg.y2);
                                }

                                xhr.upload.addEventListener('progress', uploadProgress, false);
                                xhr.addEventListener('load', uploadFinish, false);
                                xhr.addEventListener('error', uploadError, false);
                                xhr.addEventListener('abort', uploadAbort, false);
                                xhr.open('POST', url);
                                xhr.send(form);

                                oTimer = setInterval(doInnerUpdates, 300);
                            } else {
                                inputFile.disabled = false;
                                alert("** Tu archivo supera el tamaño permitido **");
                            }
                        } else {
                            inputFile.disabled = false;
                            alert("** Solo se permite archivos con extensión: (*." + tiposVUP.join(' *.') + ")");
                        }
                        break;
                    case 404:
                        alert("ERROR: La página no existe");
                        break;
                    case 500:
                        alert("ERROR: Del servidor");
                        break;
                    default:
                        alert("ERROR: Desconocido");
                        break;
                }
            }
        };
        sAjaxMotor.open("GET", urlVUP, true);
        sAjaxMotor.send();
    } else {
        console.log('El API del control de Archivo no es soportado por todos los buscadores');
        return;
    }

    var mensaje = document.getElementById('msg-' + inputFile.id);
    var inputHidden = document.getElementById(inputFile.id + '-id');

    mensaje.querySelector('#upload_response').style.display = 'none';
    mensaje.querySelector('#progress_percent').innerHTML = '';
    var oProgress = mensaje.querySelector('#progress');
    var oContentProgress = mensaje.querySelector('#content-progress');
    oContentProgress.style.display = 'block';
    oProgress.style.display = 'block';
    oProgress.style.width = '0px';

    function doInnerUpdates() {

        var iCB = iBytesUploaded;
        var iDiff = iCB - iPreviousBytesLoaded;

        // if nothing new loaded - exit
        if (iDiff == 0)
            return;

        iPreviousBytesLoaded = iCB;
        iDiff = iDiff * 2;
        var iBytesRem = iBytesTotal - iPreviousBytesLoaded;
        var secondsRemaining = iBytesRem / iDiff;

        // update speed info
        var iSpeed = iDiff.toString() + 'B/s';
        if (iDiff > 1024 * 1024) {
            iSpeed = (Math.round(iDiff * 100 / (1024 * 1024)) / 100).toString() + 'MB/s';
        } else if (iDiff > 1024) {
            iSpeed = (Math.round(iDiff * 100 / 1024) / 100).toString() + 'KB/s';
        }

        mensaje.querySelector('#speed').innerHTML = iSpeed;
        mensaje.querySelector('#remaining').innerHTML = ' | ' + secondsToTime(secondsRemaining);

    }

    function uploadProgress(e) {
        if (e.lengthComputable) {
            iBytesUploaded = e.loaded;
            iBytesTotal = e.total;
            var iPercentComplete = Math.round(e.loaded * 100 / e.total);
            var iBytesTransfered = bytesToSize(iBytesUploaded);

            mensaje.querySelector('#progress_percent').innerHTML = iPercentComplete.toString() + '%';
            mensaje.querySelector('#progress').style.width = (iPercentComplete * 4).toString() + 'px';
            mensaje.querySelector('#b_transfered').innerHTML = iBytesTransfered;
            if (iPercentComplete === 100) {
                var oUploadResponse = mensaje.querySelector('#upload_response');
                oUploadResponse.innerHTML = 'Please wait...processing';
                oUploadResponse.style.display = 'block';
            }
        }
    }

    function uploadFinish(e) {
        inputFile.disabled = false;
        var msg = '', bi, responseText = e.target.responseText;
        try {
            var response = JSON.parse(responseText);
            inputHidden.value = response.codigo;
            msg = response.msg;
            bi = response.img_upload_url;
        } catch (e) {
            msg = responseText;
        }
        var img_preview = document.getElementById(inputFile.id + "_preview");
        console.log(bi);
        img_preview.src = bi;
        var oUploadResponse = mensaje.querySelector('#upload_response');
        oUploadResponse.innerHTML = msg;
        oUploadResponse.style.display = 'block';
        clearInterval(oTimer);

    }

    function uploadError(e) {
//        console.log('uploadError');
    }

    function uploadAbort(e) {
//        console.log('uploadError');
    }

}

function CroopImage(oFile, ext_oFile, datafile, inputFile, url, path, formId) {
    var Mx = [];
    Mx.push(document);
    console.log(Mx);
    var shadow = document.createElement("div");
    shadow.style["position"] = "fixed";
    shadow.style["background-color"] = "rgba(0,0,0,0.3)";
    shadow.style["top"] = "0em";
    shadow.style["left"] = "0em";
    shadow.style["width"] = "100%";
    shadow.style["height"] = "100%";
    shadow.style["z-index"] = "1000";

    var content_app = document.createElement("div");
    content_app.style["position"] = "absolute";
    content_app.style["left"] = "0px";
    content_app.style["top"] = "0px";
    content_app.style["max-height"] = "90%";

    var content_img = document.createElement("div");
    content_img.style["position"] = "relative";

    var img = new Image();
    img.id = "CurrentImageCropp";
    img.src = datafile.dataurl;

    var cropp_area = document.createElement("div");
    cropp_area.style["position"] = "absolute";
    cropp_area.style["left"] = "0em";
    cropp_area.style["top"] = "0em";
    cropp_area.style["width"] = "100%";
    cropp_area.style["height"] = "100%";
    cropp_area.style["z-index"] = "1020";

    //area_cut
    var ac = document.createElement("div");
//    ac.style["background-color"]="rgba(0,100,0,0.5)";
    ac.style["position"] = "absolute";
    ac.style["left"] = "0px";
    ac.style["top"] = "0px";
    ac.style["width"] = "10px";
    ac.style["height"] = "10px";
    ac.style["z-index"] = "1010";

    var side_north = document.createElement("div");
    side_north.style["background-color"] = "rgba(0,0,0,0.7)";
    side_north.style["position"] = "absolute";
    side_north.style["left"] = "0px";
    side_north.style["top"] = "0px";
    side_north.style["width"] = "100%";
    side_north.style["height"] = "0px ";
    side_north.style["z-index"] = "1010";

    var side_south = document.createElement("div");
    side_south.style["background-color"] = "rgba(0,0,0,0.7)";
    side_south.style["position"] = "absolute";
    side_south.style["left"] = "0px";
    side_south.style["bottom"] = "0px";
    side_south.style["width"] = "100%";
    side_south.style["height"] = "0px ";
    side_south.style["z-index"] = "1010";

    var side_east = document.createElement("div");
    side_east.style["background-color"] = "rgba(0,0,0,0.7)";
    side_east.style["position"] = "absolute";
    side_east.style["left"] = "0px";
    side_east.style["top"] = "0px";
    side_east.style["width"] = "0px";
    side_east.style["height"] = "0px";
    side_east.style["z-index"] = "1010";

    var side_west = document.createElement("div");
    side_west.style["background-color"] = "rgba(0,0,0,0.7)";
    side_west.style["position"] = "absolute";
    side_west.style["right"] = "0px";
    side_west.style["top"] = "0px";
    side_west.style["width"] = "0px";
    side_west.style["height"] = "0px";
    side_west.style["z-index"] = "1010";

    //btn send
    var btn_send = document.createElement("input");
    btn_send.style["padding"] = "5px 10px";
    btn_send.setAttribute("type", "submit");
    btn_send.setAttribute("value", "Enviar");

    var btn_cancel = document.createElement("input");
    btn_cancel.style["padding"] = "5px 10px";
    btn_cancel.setAttribute("type", "button");
    btn_cancel.setAttribute("value", "Cancelar");

    btn_send.onclick = function (e) {
        var JSONdatacrop = {
            "x1": ac.offsetLeft,
            "y1": ac.offsetTop,
            "x2": side_west.offsetLeft,
            "y2": side_south.offsetTop
        }
        upload(inputFile, url, path, formId, JSONdatacrop);
        shadow.parentNode.removeChild(shadow);
    }

    btn_cancel.onclick = function (e) {
        inputFile.disabled = false;
        shadow.parentNode.removeChild(shadow);
    }

    content_img.appendChild(img);
    //add sides
    content_img.appendChild(side_north);
    content_img.appendChild(side_south);
    content_img.appendChild(side_east);
    content_img.appendChild(side_west);

    content_img.appendChild(ac);
    content_img.appendChild(cropp_area);

    content_app.appendChild(content_img);
    content_app.appendChild(btn_send);
    content_app.appendChild(btn_cancel);

    shadow.appendChild(content_app);

    inputFile.parentNode.appendChild(shadow);

    //Centrando area de cortado
    content_app.style["left"] = ((shadow.offsetWidth / 2) - (content_img.offsetWidth / 2)) + "px";
    content_app.style["top"] = ((shadow.offsetHeight / 2) - (content_img.offsetHeight / 2)) + "px";

    var mousemovemethod = function (e) {
        //apca : Absolute Position cropp area
        var apca = getAbsoluteElementPosition(cropp_area);
        var Ypos = e.clientY - apca.top;
        var Xpos = e.clientX - apca.left;

        ac.style["width"] = (Xpos - ac.offsetLeft) + "px";
        ac.style["height"] = (Ypos - ac.offsetTop) + "px";

        //sides
        side_north.style["height"] = (Ypos - ac.offsetHeight) + "px";
        side_south.style["height"] = (cropp_area.offsetHeight - (ac.offsetTop + ac.offsetHeight)) + "px";
        side_east.style["height"] = (ac.offsetHeight) + "px";
        side_east.style["top"] = (Ypos - ac.offsetHeight) + "px";
        side_east.style["width"] = (ac.offsetLeft) + "px";
        side_west.style["top"] = (Ypos - ac.offsetHeight) + "px";
        side_west.style["height"] = (ac.offsetHeight) + "px";
        side_west.style["width"] = (cropp_area.offsetWidth - (ac.offsetLeft + ac.offsetWidth)) + "px";
    }
    var mousedownmethod = function (e) {
        btn_send.setAttribute("value", "Aceptar y Cortar");

        var apca = getAbsoluteElementPosition(cropp_area);
        var Ypos = e.clientY - apca.top;
        var Xpos = e.clientX - apca.left;

        ac.style["left"] = Xpos + "px";
        ac.style["top"] = Ypos + "px";

        //sides
        side_north.style["height"] = Ypos + "px";
        side_south.style["height"] = (cropp_area.offsetHeight - (ac.offsetTop + ac.offsetHeight)) + "px";
        side_east.style["height"] = (ac.offsetHeight) + "px";
        side_east.style["top"] = (Ypos) + "px";
        side_east.style["width"] = (ac.offsetLeft) + "px";
        side_west.style["top"] = (Ypos) + "px";
        side_west.style["height"] = (ac.offsetHeight) + "px";
        side_west.style["width"] = (cropp_area.offsetWidth - (ac.offsetLeft + ac.offsetWidth)) + "px";
    }

    cropp_area.onmousedown = function (e) {
        mousedownmethod(e);
        cropp_area.onmousemove = mousemovemethod;
        cropp_area.onmouseup = function (e) {
            cropp_area.onmousemove = null;
        }
        cropp_area.onmouseout = function (e) {
            cropp_area.onmousemove = null;
        }
    }
}

function SelectAnidadoId(obj, url, NomC, idinput) {
    var indexObject = obj.selectedIndex;
    var MxOptions = obj.childNodes;
    var ValueSelected = MxOptions[indexObject].value;

    url = url + "&NomCH=" + NomC + "&Codigo=" + ValueSelected;

    var IdDetalle = document.getElementById(idinput);

    IdDetalle.innerHTML = "";
    var option = document.createElement("option");
    option.innerHTML = "Cargando...";
    IdDetalle.appendChild(option);

    var xmlhttp = crearXMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            IdDetalle.innerHTML = xmlhttp.responseText;
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function AjaxDataParm(Url, IdPanel, ConsultaCampos) {
    // alert(Url+"  "+IdPanel+"   "+ConsultaCampos);
    var CamposSelect = ConsultaCampos.split(',');
    var IdCampoSelect = CamposSelect[0];
    var PosicionSelectB = document.getElementById(IdCampoSelect).options;
    if (PosicionSelectB === undefined) {
        var ValorIdSelect = document.getElementById(IdCampoSelect).value;
    } else {
        var PosicionSelect = document.getElementById(IdCampoSelect).options.selectedIndex;
        var ValorIdSelect = document.getElementById(IdCampoSelect).options[PosicionSelect].value;
    }

    AjaxData(Url + '&' + IdCampoSelect + '=' + ValorIdSelect, IdPanel);

}

function ValidaCampos(CamposInput, UrlR, CodForm, NombreCampo) {

    if (CamposInput != "") {
        var Cadena = CamposInput.split(',');
        var UrlCadena = UrlR.split('?');
        CadenaNew = "?";
        for (var i = 0; i < Cadena.length; i++) {
            var ValorIdCmp = document.getElementById(Cadena[i]).value;
            CadenaNew += Cadena[i] + "=" + ValorIdCmp + "&";
        }
        var CodFormURL = CodForm.replace("-UPD", "");
        var Url = UrlCadena[0] + CadenaNew + "TipoDato=texto&transaccion=VALIDACION&metodo=" + CodFormURL + "&NombreCampo=" + NombreCampo + "";
        ValorAjaxG = FAjax(Url, 'CmpValidacion--' + NombreCampo + '', CodForm, WAjaxValidacion);
    }
}


function WAjaxValidacion(e, Url, Panel, Form) {

    var a = e.responseText;
    var Mp = MatrisProtocolo(Panel);
    var ContenidoValidacion = document.getElementById("ContenedorValidacion" + Form + "");
    var PanelBotenera = document.getElementById("PanelBtn-" + Form);
    var ContenidoValidacionValue = document.getElementById("ContenedorValidacion" + Form + "").value;
    var ContenidoValidacionVGeneral = document.getElementById("ContenedorValidacion-Gen" + Form + "");
    var ContenidoValidacionValueGeneral = document.getElementById("ContenedorValidacion-Gen" + Form + "").value;
    // alert(Mp[0]);
    if (Mp[0] == 1) {// si es 1 hay un error

        var vpanel = document.getElementById(Panel);
        var Msg = vpanel.innerHTML;
        vpanel.innerHTML = "";
            // alert(Panel);
        PanelMsgForm(Panel, "1", Mp[1]);

        NuevaCadena = Mp[0] + "|" + Mp[1] + "|" + Panel;
        var Coincidencia = Buscar(ContenidoValidacionValue, NuevaCadena);
        var CoincidenciaR = Redondear(Coincidencia);
        if (CoincidenciaR < 1 && CoincidenciaR !== 0) {
            ContenidoValidacion.value = ContenidoValidacionValue + NuevaCadena + "}";
        }

    } else {

        var vpanel = document.getElementById(Panel);
        vpanel.setAttribute('class', 'PanelAlerta');

        var CadenaValidacion = "";
        var CadenaS = Segmentar(ContenidoValidacionValue, '}');
        for (var i = 0; i < CadenaS.length - 1; i++) {
            var CadenaSB = Segmentar(CadenaS[i], '|');

            if (CadenaSB[2] == Panel) {
                // alert("encontro");
            } else {
                CadenaValidacion += CadenaS[i] + '}';
            }
        }
        ContenidoValidacion.value = CadenaValidacion;

        //////////sssssssssss
        var CadenaSC = Segmentar(ContenidoValidacionValueGeneral, ',');
        for (var b = 0; b < CadenaSC.length - 1; b++) {
            if (CadenaSC[b] == Panel) {
                ContenidoValidacionVGeneral.value = ContenidoValidacionValueGeneral.replace(CadenaSC[b] + ",", "");
            }
        }
    }
}

function FAjax(Url, Panel, Form, callback) {

    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("GET", Url, true);
    sAjaxMotor.onreadystatechange = function () {
        var detalles = document.getElementById(Panel);
        if (sAjaxMotor.readyState === 4) {
            if (sAjaxMotor.status == 200) {
                detalles.innerHTML = sAjaxMotor.responseText;
                callback(sAjaxMotor, Url, Panel, Form);
            }
        }
    }
    sAjaxMotor.send(null);

}


function Segmentar(Cadena, Simbolo) {
    var Valor = Cadena.split(Simbolo);
    return Valor;
}


function Redondear(Valor) {
    Valor = Math.round(Valor);
    return Valor;
}


function Buscar(CadenaOrigen, CadenaBuscar) {

    var CadenaOrigen = CadenaOrigen.toLowerCase();
    var CadenaBuscar = CadenaBuscar.toLowerCase();
    var n = CadenaOrigen.indexOf(CadenaBuscar);
    return n;

}


function MatrisProtocolo(Panel) {

    var PanelValores = document.getElementById(Panel);

    PanelValoresHTML = PanelValores.innerHTML;
    PanelValoresSeg = PanelValoresHTML.split('<defsei>');
    PanelValoresSegB = PanelValoresSeg[1].split('</defsei>');
    PanelValoresSegC = PanelValoresSegB[0].split(']');
    return PanelValoresSegC;

}


function LanzaValorBA(Panel, MultiSelec, IdControl, IdForm, UrlRaiz, CamposValidacion) {
    // alert("0");       
    var PanelValores = document.getElementById(Panel);
    PanelValoresHTML = PanelValores.innerHTML;
    PanelValoresSeg = PanelValoresHTML.split('<defsei>');
    PanelValoresSegB = PanelValoresSeg[1].split('</defsei>');
    PanelValoresSegC = PanelValoresSegB[0].split(']');
    SeleccionaItems(IdControl, PanelValoresSegC[0], PanelValoresSegC[1], MultiSelec);
    Campo = IdControl.split('--');
    // alert(" IdForm: "+IdForm+"   UrlRaiz : "+UrlRaiz+"    CamposValidacion  : "+CamposValidacion);    
    ValidaCampos(CamposValidacion, UrlRaiz, IdForm, Campo[1]);
    // alert("2");
}


// var RespCBI='';
// var ContMultiSelect=0;
function BusquedaAuto(ObBA, IdControl, MultiSelec, urlCaida, ConsultaCampos, IdForm, CamposValidacion, NameCampo) {
    BusquedaAccion(ObBA, IdControl, MultiSelec, urlCaida, ConsultaCampos, IdForm, CamposValidacion, NameCampo);
}


function BusquedaAccion(objCBI, IdControl, MultiSelec, urlCaida, ConsultaCampos, IdForm, CamposValidacion, NameCampo) {

    var ValueCampo = objCBI.value;
    var NumeroCaracteres = ValueCampo.length;
    var result_busqueda_int = document.getElementById('Pnl-' + IdControl);
    ContenidoPanelResultado = result_busqueda_int.innerHTML;
    
    var Panel_Referencia = document.getElementById('Pnl-' + IdControl + '-view');
    Panel_Referencia.setAttribute('style', 'display:block;');

    if (ConsultaCampos != "") {
        var CamposSelect = ConsultaCampos.split(',');
        var IdCampoSelect = CamposSelect[0];
        var PosicionSelectB = document.getElementById(IdCampoSelect).options;
        if (PosicionSelectB === undefined) {
            var ValorIdSelect = document.getElementById(IdCampoSelect).value;
        } else {
            var PosicionSelect = document.getElementById(IdCampoSelect).options.selectedIndex;
            var ValorIdSelect = document.getElementById(IdCampoSelect).options[PosicionSelect].value;
        }
    }
    
    var tecla = validar(event);
    var Url = urlCaida + '&' + IdControl + '=' + ValueCampo + '&NumeroCar=' + NumeroCaracteres + '&' + IdCampoSelect + '=' + ValorIdSelect;

    if (NumeroCaracteres === 0) {
        if (tecla === 8) {
            Panel_Referencia.innerHTML = "";
        }
    }

    ResultadoR = (NumeroCaracteres % 3);

    if (tecla === 8) {
        // if (ResultadoR === 0) {
            // FAjaxB(Url, 'Pnl-' + IdControl + '', IdControl, ConsultaCampos, MultiSelec, IdForm, CamposValidacion, NameCampo, WAjaxBusquedaAutomatica);
        // }

    } else {
        if (ResultadoR === 0) {
            FAjaxB(Url, 'Pnl-' + IdControl + '', IdControl, ConsultaCampos, MultiSelec, IdForm, CamposValidacion, NameCampo, WAjaxBusquedaAutomatica);
        }
    }

}

function WAjaxBusquedaAutomatica(e, Url, Panel, IdControl, Campos, MultiSelec, IdForm, CamposValidacion, NameCampo) {

    var CampoTetxt = document.getElementById(IdControl);
    var ValueCampo = CampoTetxt.value;
    var ValueCampoArray = ValueCampo.split(' ');

    var Panel_Busqueda = document.getElementById('Pnl-' + IdControl);
    ContenidoPanelResultado = Panel_Busqueda.innerHTML;

    // alert(ContenidoPanelResultado);
    var Panel_Busqueda_Resultado = document.getElementById('Pnl-' + IdControl + '-view');
    Panel_Busqueda_Resultado.innerHTML = '';

    var items = ContenidoPanelResultado.split(']');
    CadenaNew = "";
    for (var i = 0; i < items.length - 1; i++) {

        var ItemSegmento = items[i].split('|');
        var cadena = ItemSegmento[0] + ' ' + ItemSegmento[1];
        // cadena = cadena.toLowerCase();
        // ValueCampo = ValueCampo.toLowerCase();
        BusqeudaResultadoA = ItemSegmento[1].toLowerCase();
        for (var j = 0; j < ValueCampoArray.length; j++) {

            BusqeudaResultado = BusqeudaResultadoA.replace(ValueCampoArray[j].toLowerCase(), "<h class='ItemSelectSearchL' >" + ValueCampoArray[j].toLowerCase() + "</h>");
        }

        CadenaNew += ItemSegmento[0] + '|' + ItemSegmento[1] + ']';

        var NodoHijo2 = document.createElement('div');
        // NodoHijo2.setAttribute('style','background-color:red;padding:6px 10px;cursor:pointer;');
        NodoHijo2.setAttribute('class', 'ItemSelectSearch');
        NodoHijo2.setAttribute('id', 'OS-' + ItemSegmento[0]);
        NodoHijo2.setAttribute('onclick', "SeleccionaItems('" + IdControl + "','" + ItemSegmento[0] + "','" + ItemSegmento[1] + "','" + MultiSelec + "'); ValidaCampos('" + CamposValidacion + "','" + Url + "','" + IdForm + "','" + NameCampo + "'); ");

        NodoHijo2.innerHTML = BusqeudaResultado;
        Panel_Busqueda_Resultado.appendChild(NodoHijo2);

    }
    Panel_Busqueda.innerHTML = CadenaNew;
    // alert(" d "+Campos);
}

function FAjaxB(Url, Panel, Form, Campos, MultiSelec, IdForm, CamposValidacion, NameCampo, callback) {
    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("GET", Url, true);
    sAjaxMotor.onreadystatechange = function () {
        var detalles = document.getElementById(Panel);
        if (sAjaxMotor.readyState === 4) {
            if (sAjaxMotor.status == 200) {
                detalles.innerHTML = sAjaxMotor.responseText;
                callback(sAjaxMotor, Url, Panel, Form, Campos, MultiSelec, IdForm, CamposValidacion, NameCampo);
            }
        }
    }
    sAjaxMotor.send(null);
}


function validar(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    return tecla;
}



function SeleccionaItems(PanelInputV, ItemId, ValorItem, MultiSelec) {

    var PanelInput = document.getElementById('PnlA-' + PanelInputV);
    var InputValue = document.getElementById(PanelInputV);
    var IdCampo = PanelInputV.split('--');

    ValidaNodo = document.querySelector('#PnlA-' + PanelInputV + ' #SubPanelB-' + IdCampo[1] + '-' + ItemId);
    var ValorInputBusqueda = "";

    if (ValidaNodo == null) {

        if (MultiSelec == "UniNivel") {

            var TotItems = PanelInput.childNodes.length;
            for (var i = 0; i < TotItems - 1; i++) {
                // alert("a");
                NHijosId = PanelInput.childNodes[i].id;
                PanelItemInput = "PInPrimario-" + PanelInputV;
                // alert("PanelItemInput " +PanelItemInput+"  ===   nh "+NHijosId);

                if (NHijosId == PanelItemInput) {

                } else {
                    var NodoItemB = document.getElementById(NHijosId);
                    // alert(NodoItemB);
                    PanelInput.removeChild(NodoItemB);
                }
            }
        }
        // alert("1");
        var NodoHijo2 = document.createElement('div');
        // NodoHijo2.setAttribute('style','background-color:green;float:left;padding:4px;margin:0px 3px;position:relative;padding: 4px 20px 4px 8px;');                     
        NodoHijo2.setAttribute('class', 'ItemSelectB');
        NodoHijo2.setAttribute('id', 'SubPanelB-' + IdCampo[1] + '-' + ItemId);
        NodoHijo2.innerHTML = ValorItem;
        PanelInput.appendChild(NodoHijo2);
        // alert("2");
        var NodoHijo3 = document.createElement('div');
        NodoHijo3.setAttribute('class', 'BotonCerrar');
        NodoHijo3.setAttribute('onclick', "EliminaItems('" + PanelInputV + "','" + ItemId + "','" + ValorItem + "')");
        NodoHijo3.innerHTML = 'x';
        NodoHijo2.appendChild(NodoHijo3);
        // alert("3");
        PanelInput.insertBefore(NodoHijo2, PanelInput.childNodes[0]);

        var IdCampo = PanelInputV.split('--');

        var ValueCampoA = document.getElementById(IdCampo[1]);
        if (MultiSelec == "UniNivel") {
            ValueCampoA.value = ItemId;
        } else {
            ValueCampoA.value = ValueCampoA.value + ',' + ItemId;
        }

        var PaneResultadoItems = document.getElementById('Pnl-' + PanelInputV + '-view');
        PaneResultadoItems.setAttribute('style', 'display:none;');

        InputValue.value = "";
    }

}

function EliminaItems(PanelInputV, ItemId, ValorItem) {
    var IdCampo = PanelInputV.split('--');
    var ValueCampoA = document.getElementById(IdCampo[1]);
    ValueAnterior = ValueCampoA.value;
    Cadena = "";
    var items = ValueAnterior.split(',');
    for (var i = 1; i < items.length; i++) {
        if (items[i] == ItemId) {
        } else {
            Cadena += "," + items[i];
        }
    }
    ValueCampoA.value = Cadena;
    var BoxItems = document.getElementById('SubPanelB-' + IdCampo[1] + '-' + ItemId);
    BoxItems.parentNode.removeChild(BoxItems);
}


/*
 - Que mapee:
 * CBI-NombreForm_collection_busqueda_int -> Para agregar nodos
 * CBI-NombreForm_result_busqueda_int     -> Para displayar resultados de Busqueda
 * CBI-NombreForm_txt_response            -> Para guardar mi array de Codigos de Coleccion 
 */

var RespCBI = '';
var ContMultiSelect = 0;
function CBI_start(objCBI, IdControl, MultiSelec, urlCaida) {
    ContMultiSelect = 0;
    objCBI.removeAttribute('onclick');
    objCBI.removeAttribute('onfocus');
    objCBI.onkeyup = CBI_search(objCBI, IdControl, MultiSelec, urlCaida);
}

function CBI_search(objCBI, IdControl, MultiSelec, urlCaida) {
    return function () {
        var result_busqueda_int = document.getElementById('CBI-' + IdControl + '_result_busqueda_int');
        var posobjCBI = getAbsoluteElementPosition(objCBI);
        var sizeobjCBI = getSizeElement(objCBI);
//        result_busqueda_int.style.top=(posobjCBI.top + sizeobjCBI.height) + "px";
//        result_busqueda_int.style.left=posobjCBI.left + "px";
        //alert(objCBI.getAttribute('id'));
        //Objetos Contenedores HTML
        var ObjCBIValue = objCBI.value;
        var lenvalue = ObjCBIValue.length;
        if (ObjCBIValue !== "") {
            if (MultiSelec === 1 || (MultiSelec === 0 && ContMultiSelect < 1)) {//1: true 0:false
                if (lenvalue >= 1 && lenvalue <= 3) {
                    //alert('Consulta SQL')
                    //gad_cursos.php?Cursos=CBI&IdControl=CtrlCBI1&Criterio_CtrlCBI1=AAAAA
                    var URLSolicitud = urlCaida + '&IdControl=' + IdControl + '&Criterio_' + IdControl + '=' + ObjCBIValue;
                    //alert(URLSolicitud);
                    LoadAjaxCBI(URLSolicitud, IdControl);
                } else {
                    var PanelId = document.getElementById('CBI-' + IdControl + '_result_busqueda_int');
                    PanelId.innerHTML = '';
                    var RAMRespTEXT = RespCBI;
                    var auxRAMRespTEXT = '';
                    //alert(RAMRespTEXT);
                    var items = RAMRespTEXT.split(']');
                    //alert(items.length);
                    for (var i = 0; i < items.length - 1; i++) {
                        var ObjCodDes = items[i].split('|');

                        if ((ObjCodDes[1].toLowerCase()).indexOf(ObjCBIValue.toLowerCase()) !== -1) { //Evaluando la descripcion
                            auxRAMRespTEXT += items[i] + ']';
                        }
                        //                if((items[i].toLowerCase()).indexOf(ObjCBIValue.toLowerCase())!==-1){
                        //                    auxRAMRespTEXT+=items[i] + ']';
                        //                }
                    }
                    ConvertOpcCBI(auxRAMRespTEXT, IdControl);
                }
            } else {
                result_busqueda_int.innerHTML = "";
                var DivOpcCBI = document.createElement('div');
                DivOpcCBI.setAttribute('class', "CBI_result_OPC");
                DivOpcCBI.innerHTML = "Solo puedes escoger una Opción...";
                result_busqueda_int.appendChild(DivOpcCBI);
            }
        } else {
            result_busqueda_int.innerHTML = "";
        }
    };
}

function CBI_OpcCBI(ObjOption, IdControl) {
    var collection_busqueda_int = document.getElementById('CBI-' + IdControl + '_collection_busqueda_int');
    var result_busqueda_int = document.getElementById('CBI-' + IdControl + '_result_busqueda_int');
    var txt_response = document.getElementById('CBI-' + IdControl + '_txt_response');
    //Input De busqueda
    var txt_search = document.getElementById('CBI-' + IdControl + '_txt_search');
    //<div id='nodo1' data-Codigo='CodigoSQL1'>DescripcionSQL1</div>
    //
    //////////////////////////////////////////////////////////////////////////////////
    var data_codigo = ObjOption.getAttribute('data-codigo');
    var data_descripcion = ObjOption.getAttribute('data-descripcion');
    //////////////////////////////////////////////////////////////////////////////////
    var NodoContenedor = document.createElement('div');
    NodoContenedor.setAttribute('class', "CBI_OpcCBI");
    var NodoHijo = document.createElement('div');
    NodoHijo.setAttribute('class', "TextCBI_OpcCBI");
    NodoHijo.setAttribute('data-codigo', data_codigo);
    NodoHijo.setAttribute('data-descripcion', data_descripcion);
    NodoHijo.innerHTML = data_descripcion;
    var NodoHijo2 = document.createElement('div');
    NodoHijo2.setAttribute('onclick', "DelCBI_OpcCBI(this,'" + IdControl + "')");
    NodoHijo2.setAttribute('class', "DelCBI_OpcCBI");
    NodoHijo2.setAttribute('data-codigo', data_codigo);
    NodoHijo2.innerHTML = 'x';

    NodoContenedor.appendChild(NodoHijo);
    NodoContenedor.appendChild(NodoHijo2);
    collection_busqueda_int.insertBefore(NodoContenedor, txt_search);
    ContMultiSelect++;

    var MxresponseValues = (!txt_response.value.trim())? [] : txt_response.value.split("|");
    MxresponseValues.push(data_codigo);
    
    txt_response.value = MxresponseValues.join("|");
    txt_search.value = '';
    txt_search.focus();
    txt_search.onkeyup();
}

function DelCBI_OpcCBI(objOpcCBIDEL, IdControl) {
    var padreOpcContent = objOpcCBIDEL.parentNode;
    var padre_collection_busqueda = padreOpcContent.parentNode;
    padre_collection_busqueda.removeChild(padreOpcContent);
    //Eliminando del value
    var txt_response = document.getElementById('CBI-' + IdControl + '_txt_response');
    //alert(txt_response.value);
    var CodigoDELETE = objOpcCBIDEL.getAttribute('data-codigo');
    var RAMRespTEXT = txt_response.value;
    var auxRAMRespTEXT = '';
    var items = RAMRespTEXT.split('|');
    for (var i = 0; i < items.length - 1; i++) {
        if ((items[i].toLowerCase()).indexOf(CodigoDELETE.toLowerCase()) < 0) {
            auxRAMRespTEXT += items[i] + '|';
        }
    }
    txt_response.value = auxRAMRespTEXT;
    ContMultiSelect--;
    ConvertOpcCBI(auxRAMRespTEXT, IdControl);
}

function ConvertOpcCBI(ResponseText, IdControl) {
    var result_busqueda_int = document.getElementById('CBI-' + IdControl + '_result_busqueda_int');
    var items = ResponseText.split(']');
    for (var i = 0; i < 6; i++) { //for(var i=0;i<items.length-1;i++){
        var OpcCBI = items[i].split('|');

        var txt_response = document.getElementById('CBI-' + IdControl + '_txt_response');
        var RAMRespTEXT = txt_response.value;
        if ((RAMRespTEXT.toLowerCase()).indexOf(OpcCBI[0].toLowerCase()) < 0) {
            var DivOpcCBI = document.createElement('div');
            DivOpcCBI.setAttribute('class', "CBI_result_OPC");
            DivOpcCBI.setAttribute('ondblclick', "CBI_OpcCBI(this,'" + IdControl + "')");
            DivOpcCBI.setAttribute('data-codigo', OpcCBI[0]);
            DivOpcCBI.setAttribute('data-descripcion', OpcCBI[1]);
            DivOpcCBI.innerHTML = OpcCBI[1];
            result_busqueda_int.appendChild(DivOpcCBI);
        }
    }
}

function LoadAjaxCBI(url, IdControl) {
    var PanelId = document.getElementById('CBI-' + IdControl + '_result_busqueda_int');
    var objAjax = crearXMLHttpRequest();
    objAjax.onreadystatechange = function () {
        if (objAjax.readyState === 4 && objAjax.status === 200) {
            PanelId.innerHTML = '';
            RespCBI = objAjax.responseText;
            ConvertOpcCBI(objAjax.responseText, IdControl);
            //alert(RespCBI);
        } else if (objAjax.readyState === 4 && objAjax.status === 404) {
            PanelId.innerHTML = "Error! Pagina no existe...";
        } else {
            PanelId.innerHTML = "Cargando...";
        }
    };
    objAjax.open("GET", url, true);
    objAjax.send();
}

/*
 * 
 */
function TextAreaAutoSize(ObjTextArea) {
//    console.log(ObjTextArea.value);
    ObjTextArea.style["height"] = 'auto';
    ObjTextArea.style["height"] = ObjTextArea.scrollHeight + 'px';
}

//Funciones para objetos HTML
function getAbsoluteElementPosition(element) {
    if (typeof element === "string") {
        element = document.getElementById(element);
    }
    if (!element) {
        return {top: 0, left: 0};
    }
    var y = 0;
    var x = 0;
    while (element.offsetParent) {
        x += element.offsetLeft;
        y += element.offsetTop;
        element = element.offsetParent;
    }
    return {top: y, left: x};
}

function getCursorPosition() {
    return {top: event.clientY, left: event.clientX};
}

function getSizeElement(element) {
    if (typeof element === "string") {
        element = document.getElementById(element);
    }
    var size = {height: 0, width: 0};
    if (element.offsetHeight) {
        size.height = element.offsetHeight;
    } else if (element.style.pixelHeight) {
        size.height = element.style.pixelHeight;
    }
    if (element.offsetWidth) {
        size.width = element.offsetWidth;
    } else if (element.style.pixelWidth) {
        size.width = element.style.pixelWidth;
    }
    return size;
}

//AUXILIAR CALENDAR FULL
/* SHOW FORM */
function showForm(URL) {
    btnDescGroup = false;
    btnListContact = false;
    btnDescContact = true;
    //alert(URL);
    $('#FondoForm').fadeIn(500);
    var w_height = $(window).height();
    var w_width = $(window).width();
    var ff_height = $('#FondoFormContent').height();
    var ff_width = $('#FondoFormContent').width();
    $('#FondoFormContent').css({
        "left": (w_width - (w_width / 2) - (ff_width / 2)) + "px",
        "top": '100px'
//        "top":(w_height-(w_height/2)-(ff_height/2)) + "px"
    });
    enviaVista(URL, 'FondoFormContent', '');
}

function closeForm(idform) {
    if (idform) {
        $("#" + idform).fadeOut(500);
    } else {
        $('#FondoForm').fadeOut(500);
    }
}
/* FIN SHOW FORM */

/* Upload Multifile */
var is_process_upload_owl = false;
var owl_file_wait = [];
var cont_id_owl_upload = 1;
function uploadOwl(inputFile, url, path, formId, file) {
    //Si el input contiene mas de un archivo...
    if (document.getElementById(inputFile).files.length > 1) {
        console.log(document.getElementById(inputFile).files.length + " Archivos");
        for (var i = 1; i < document.getElementById(inputFile).files.length; i++) {
            //ADD new upload control
            var new_input_owl = document.createElement("div");
            new_input_owl.setAttribute("class", "input-owl");
            new_input_owl.style["background-size"] = "40px";
            new_input_owl.style["background-image"] = "url('" + url_domain_current + "/system/_imagenes/load.gif')";

            var new_id_input_file = 'owl_upload_input' + cont_id_owl_upload;
            cont_id_owl_upload++;

            var input_file = document.createElement("input");
            input_file.setAttribute("id", new_id_input_file);
            input_file.setAttribute("type", "file");
            input_file.disabled = true;

            var input_file_hidden = document.createElement("input");
            input_file_hidden.setAttribute("id", new_id_input_file + '-id');
            input_file_hidden.setAttribute("type", "hidden");

            new_input_owl.appendChild(input_file);
            new_input_owl.appendChild(input_file_hidden);

            if (i > 1) {
                var lastid = 'owl_upload_input' + (cont_id_owl_upload - 2);
                document.getElementById(lastid).parentNode.parentNode.insertBefore(new_input_owl, document.getElementById(lastid).parentNode);
                //console.log('lastId:' + lastid); 
            } else {
                document.getElementById(inputFile).parentNode.parentNode.insertBefore(new_input_owl, document.getElementById(inputFile).parentNode);
            }

            //Añadiendo a Cola
            var json_upload_owl_aux = {
                inputFile: new_id_input_file,
                url: url,
                path: path,
                formId: formId,
                file: document.getElementById(inputFile).files[i]
            };
            owl_file_wait.push(json_upload_owl_aux);
        }
        console.log(owl_file_wait.length + " archivos en espera...");
    }
    //Si hay achivos en espera...
    if (is_process_upload_owl) {
        var uploadfile = document.getElementById(inputFile);
        uploadfile.parentNode.style["background-size"] = "40px";
        uploadfile.parentNode.style["background-image"] = "url('" + url_domain_current + "/system/_imagenes/load.gif')";
        var json_upload_owl = {
            inputFile: inputFile,
            url: url,
            path: path,
            formId: formId,
            file: null
        };
        owl_file_wait.push(json_upload_owl);
        console.log("El archivo esta en la cola de espera...");
        return;
    }
    var msg_upload = document.getElementById('msg_upload_owl');
    is_process_upload_owl = true;
    console.log("Upload: Iniciado...");

    msg_upload.querySelector('#progress_bar_content').style["display"] = "block";
    msg_upload.querySelector('#det_upload_owl').style["display"] = "block";
    msg_upload.querySelector('#det_bupload_owl').style["display"] = "block";

    var inputHidden = document.getElementById(inputFile + '-id');
    inputFile = document.getElementById(inputFile);
    inputFile.removeAttribute("onchange");

    //global vars
    var Timer = null;
    var iBytesUploaded = 0;
    var iBytesTotal = 0;
    var iPreviousBytesLoaded = 0;

    function secondsToTime(secs) {
        var hr = Math.floor(secs / 3600);
        var min = Math.floor((secs - (hr * 3600)) / 60);
        var sec = Math.floor(secs - (hr * 3600) - (min * 60));

        if (hr < 10) {
            hr = "0" + hr;
        }
        if (min < 10) {
            min = "0" + min;
        }
        if (sec < 10) {
            sec = "0" + sec;
        }
        if (hr) {
            hr = "00";
        }
        return hr + ':' + min + ':' + sec;
    }
    ;

    function bytesToSize(bytes) {
        var sizes = ['Bytes', 'KB', 'MB'];
        if (bytes === 0) {
            return 'n/a';
        }
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
    }
    ;

    if (window.File && window.FileReader && window.FileList && window.Blob) {
        inputFile.disabled = true;
        iPreviousBytesLoaded = 0;
        var oFile = (file) ? file : inputFile.files[0];

        var oReader = new FileReader();
        oReader.onload = function (e) {

        };
        oReader.readAsDataURL(oFile);

        var urlVUP = url + "&VUP=Y&filedata=" + inputFile.parentNode.parentNode.getAttribute('data-filedata');

        inputFile.parentNode.style["background-size"] = "40px";
        inputFile.parentNode.style["background-image"] = "url('" + url_domain_current + "/system/_imagenes/load.gif')";
        var sAjaxMotor = crearXMLHttpRequest();
        sAjaxMotor.onreadystatechange = function () {
            if (sAjaxMotor.readyState === 4) {
                switch (sAjaxMotor.status) {
                    case 200:
                        var JSONresp = sAjaxMotor.responseText;
                        var response = JSON.parse(JSONresp);
                        var tiposVUP = response.filedata.tipos;

                        var sizeVUP = parseFloat(response.filedata.maxfile, 10) * 1024 * 1024;
                        var ext_oFile = oFile.name.split('.').pop();

                        console.log("Extension de archivo: " + ext_oFile);
                        if (tiposVUP.indexOf(ext_oFile) !== -1) {
                            console.log(ext_oFile + " extension permitido: CORRECTO");
                            if (oFile.size < sizeVUP) {
                                console.log(oFile.size + " < " + sizeVUP + ": CORRECTO");

                                var form = new FormData();
                                var xhr = new XMLHttpRequest();

                                form.append('file', oFile);
                                form.append('path', path);
                                form.append('formId', formId);
                                form.append('filedata', inputFile.parentNode.parentNode.getAttribute('data-filedata'));
                                form.append('campo', inputFile.getAttribute('name'));

                                xhr.upload.addEventListener('progress', uploadProgress, false);
                                xhr.addEventListener('load', uploadFinish, false);
                                xhr.addEventListener('error', uploadError, false);
                                xhr.addEventListener('abort', uploadAbort, false);
                                xhr.open('POST', url);
                                xhr.send(form);

                                Timer = setInterval(doInnerUpdates, 300);
                            } else {
                                inputFile.disabled = false;
                                inputFile.parentNode.style["display"] = "none";
                                alert("** Tu archivo supera el tamaño permitido **");
                                is_process_upload_owl = false;
                                add_newInputOwl();
                                exits_file_wait();
                            }
                        } else {
                            inputFile.disabled = false;
                            inputFile.parentNode.style["display"] = "none";
                            alert("** Solo se permite archivos con extensión: (*." + tiposVUP.join(' *.') + ")");
                            is_process_upload_owl = false;
                            add_newInputOwl();
                            exits_file_wait();
                        }
                        break;
                    case 404:
                        console.log("ERROR: La página no existe");
                        break;
                    case 500:
                        console.log("ERROR: Del servidor");
                        break;
                    default:
                        console.log("ERROR: Desconocido");
                        break;
                }
            }
        };
        sAjaxMotor.open("GET", urlVUP, true);
        sAjaxMotor.send();
    } else {
        console.log('El API del control de Archivo no es soportado por todos los buscadores');
        return;
    }

    function doInnerUpdates() {
        console.log("doInnerUpdates");
        var iCB = iBytesUploaded;
        var iDiff = iCB - iPreviousBytesLoaded;

        // if nothing new loaded - exit
        if (iDiff === 0) {
            return;
        }

        iPreviousBytesLoaded = iCB;
        iDiff = iDiff * 2;
        var iBytesRem = iBytesTotal - iPreviousBytesLoaded;
        var secondsRemaining = iBytesRem / iDiff;

        // update speed info
        var iSpeed = iDiff.toString() + 'B/s';
        if (iDiff > 1024 * 1024) {
            iSpeed = (Math.round(iDiff * 100 / (1024 * 1024)) / 100).toString() + 'MB/s';
        } else if (iDiff > 1024) {
            iSpeed = (Math.round(iDiff * 100 / 1024) / 100).toString() + 'KB/s';
        }

        msg_upload.querySelector('#speed').innerHTML = 'Velocidad: ' + iSpeed;
        msg_upload.querySelector('#remaining').innerHTML = 'Tiempo restante: ' + secondsToTime(secondsRemaining);

    }

    function uploadProgress(e) {
        console.log("uploadProgress");
        if (e.lengthComputable) {
            iBytesUploaded = e.loaded;
            iBytesTotal = e.total;
            var iPercentComplete = Math.round(e.loaded * 100 / e.total);
            var iBytesTransfered = bytesToSize(iBytesUploaded);

            msg_upload.querySelector('#progress_percent').innerHTML = iPercentComplete.toString() + '%';
            msg_upload.querySelector('#progress_owl').style.width = iPercentComplete.toString() + '%';
            msg_upload.querySelector('#b_transfered').innerHTML = iBytesTransfered + ' Subidos...';
            if (iPercentComplete === 100) {
                var oUploadResponse = msg_upload.querySelector('#upload_response');
                oUploadResponse.innerHTML = 'Por favor espere...';
                oUploadResponse.style.display = 'block';
            }
        }
    }

    function uploadFinish(e) {
        console.log("uploadFinish");
        inputFile.disabled = true;
        var msg = '';
        var responseText = e.target.responseText;
        try {
            var response = JSON.parse(responseText);
            inputHidden.value = response.codigo;
            msg = response.msg;

            var div_nombre_file = document.createElement('div');
            div_nombre_file.setAttribute("class", "name_file");
            div_nombre_file.innerHTML = response.filenameNew;
            inputFile.parentNode.insertBefore(div_nombre_file, inputFile);

            var div_delete_file = document.createElement('div');
            div_delete_file.setAttribute("class", "delete_file");
            div_delete_file.innerHTML = "X";
            inputFile.parentNode.insertBefore(div_delete_file, inputFile);
            div_delete_file.onclick = function () {
                var file_name_delete = response.filenameNew;
                var upload_input_response = document.getElementById("upload_input_response");
                var mx_name_files = upload_input_response.value.split("|");
                mx_name_files.splice(mx_name_files.indexOf(file_name_delete), 1);
                upload_input_response.value = mx_name_files.join("|");
                console.log("Input de respuesta despues de Delete: " + mx_name_files.join("|"));
                inputFile.parentNode.parentNode.removeChild(inputFile.parentNode);

                var chkfiledelete = document.getElementById("filechk-" + response.filenameNew);
                chkfiledelete.parentNode.removeChild(chkfiledelete);
            };

            inputFile.setAttribute("title", response.filenameNew);

            /* Evaluando la Imagen de presentacion */
            var ext_array = ["docx", "doc", "xls", "xlsx", "ppt", "pptx", "mp3"];
            var ext_array_img = ["jpg", "png", "gif"];
            var oFile = (file) ? file : inputFile.files[0];
            var ext_oFile = oFile.name.split('.').pop();

            var url_background = null;
            var index_extension = ext_array.indexOf(ext_oFile);
            var index_extension_img = ext_array_img.indexOf(ext_oFile);
            if (index_extension !== -1) {
                inputFile.parentNode.style["background-size"] = "75% 75%";
                switch (index_extension) {
                    case 0:
                    case 1:
                        url_background = "url('" + url_domain_current + "/system/_imagenes/word_icon.png')";
                        break;
                    case 2:
                    case 3:
                        url_background = "url('" + url_domain_current + "/system/_imagenes/excel_icon.png')";
                        break;
                    case 4:
                    case 5:
                        url_background = "url('" + url_domain_current + "/system/_imagenes/ppt_icon.png')";
                        break;
                    case 6:
                        url_background = "url('" + url_domain_current + "/system/_imagenes/mp3_icon.png')";
                        break;
                }
            } else if (index_extension_img !== -1) {
                inputFile.parentNode.style["background-size"] = "100% 100%";
                url_background = "url('" + response.img_upload_url + "')";
            } else {
                inputFile.parentNode.style["background-size"] = "75% 75%";
                url_background = "url('" + url_domain_current + "/system/_imagenes/file_icon.png')";
            }
            inputFile.parentNode.style["background-image"] = url_background;
            /* Fin de Imagen de Presentacion */
        } catch (e) {
            msg = responseText;
        }

        var oUploadResponse = msg_upload.querySelector('#upload_response');
        oUploadResponse.innerHTML = msg;
        oUploadResponse.style.display = 'block';

        //Añadiendo cola de archivos subidos...
        var new_file_check = document.createElement("div");
        new_file_check.setAttribute("class", "new_file_check");
        new_file_check.setAttribute("id", "filechk-" + response.filenameNew);
        new_file_check.innerHTML = '&check; ' + oFile.name;
        var upload_size = document.createElement('div');
        upload_size.setAttribute("class", "upload_size");
        upload_size.innerHTML = bytesToSize(oFile.size);
        var div_clean = document.createElement("div");
        div_clean.setAttribute("class", "clean");

        new_file_check.appendChild(upload_size);
        new_file_check.appendChild(div_clean);

        oUploadResponse.parentNode.appendChild(new_file_check);

        //Añadiendo archivo al input de respuesta
        var upload_input_response = document.getElementById("upload_input_response");
        if (upload_input_response.value.trim() !== "") {
            var mx_name_files = upload_input_response.value.split("|");
            mx_name_files.push(response.filenameNew);
            upload_input_response.value = mx_name_files.join("|");
            console.log("Input de respuesta: " + mx_name_files.join("|"));
        } else {
            upload_input_response.value = response.filenameNew;
        }

        clearInterval(Timer);
        is_process_upload_owl = false;
        msg_upload.querySelector('#progress_bar_content').style["display"] = "none";
        console.log("Upload finalizado...");

        console.log("Hay " + owl_file_wait.length + " archivos en espera...");
        //ADD new upload control
        add_newInputOwl();
        exits_file_wait();
    }
    //Mis funciones
    function add_newInputOwl() { //Agregar si no hay ningun archivo en espera
        if (owl_file_wait.length === 0) {
            var new_input_owl = document.createElement("div");
            new_input_owl.setAttribute("class", "input-owl");

            var new_id_input_file = 'owl_upload_input' + cont_id_owl_upload;
            cont_id_owl_upload++;

            var input_file = document.createElement("input");
            input_file.setAttribute("id", new_id_input_file);
            input_file.setAttribute("type", "file");
            input_file.setAttribute("title", "Elegir un Archivo");
            input_file.setAttribute("multiple", "");
            input_file.onchange = function () {
                uploadOwl(new_id_input_file, url, path, formId);
            };
            var input_file_hidden = document.createElement("input");
            input_file_hidden.setAttribute("id", new_id_input_file + '-id');
            input_file_hidden.setAttribute("type", "hidden");

            new_input_owl.appendChild(input_file);
            new_input_owl.appendChild(input_file_hidden);
            inputFile.parentNode.parentNode.insertBefore(new_input_owl, inputFile.parentNode);
        }
    }
    function exits_file_wait() {
        if (owl_file_wait.length > 0) {
            var new_JsonUpload = owl_file_wait.shift();
            uploadOwl(new_JsonUpload.inputFile, new_JsonUpload.url, new_JsonUpload.path, new_JsonUpload.formId, new_JsonUpload.file);
        }
    }

    function uploadError(e) {
        console.log("uploadError");
//        console.log('uploadError');
    }

    function uploadAbort(e) {
        console.log("uploadAbort");
//        console.log('uploadError');
    }
}

Upload.MULTI_ITEM   = 1;
Upload.MONO_ITEM    = 0;

function Upload(settings){
    //Validate if upload API is supported for the browser
    if(!(window.File && window.FileReader && window.FileList && window.Blob)){
        console.log("La API controlador de archivos no es sopotado por este buscador...");
        return;
    }
    console.log("entro por acá ");
    //PRIVATE VARS
    var u = this,
        defaults = {
            //Params
            id      : null,
            name    : "defaultName",
            preFiles: [
                {
                    name    : null, //Name file
                    URI     : null  //URI Presentation
                }
            ],
            token   : null,
            type    : Upload.MONO_ITEM,
            URI     : "/system/_vistas/upload.php",

            form    : null
        },
        files       = [], //nameFiles loaded
        onload      = function(){},
        Item = function(settings){ //Class Item
            var i = this,
                defaults = {
                    imageURI    : false,
                    title       : null
                };

            i.d = $.extend(true, {}, defaults, settings),
                i.n = {
                    item        : $("<div>").attr({"class":"item", "title": i.d.title}),
                    image   : $("<img>").attr({"src": i.d.imageURI}),
                    loader  : $("<div>").attr({"class":"loader"}),
                    close   : $("<div>").attr({"class":"btn-close"}).html("<i class='icon-remove'></i>"),
                    progress    : $("<div>").attr({"class":"progress"}),
                    bar     : $("<div>").attr({"class":"bar"})
                };
            //Public methods
            i.close = function(callback){
                i.n.close.click(callback);
            },
                i.ready = function(){
                    //Add class to close button for show
                    i.n.close.addClass("showable");

                    //Hide loader
                    i.n.loader.hide();
                };

            //Render the DOM
            i.n.item
                .append(i.n.image)
                .append(i.n.loader)
                .append(i.n.close)
                .append(
                i.n.progress
                    .append(i.n.bar)
            );
        },
        Row = function(settings){
            var r = this,
                defaults = {
                    size    : null,
                    title   : null,
                    type    : Row.TYPE_SUCCESS
                };

            r.d = $.extend(true, {}, defaults, settings),
                r.n = {
                    row             : $("<div>").attr({"class":"row-detail"}).text(r.d.title),
                    size        : $("<div>").attr({"class":"size"}).text(r.d.size),
                    indicator   : $("<div>").attr({"class":"indicator"})
                };

            //Render the DOM
            r.n.row
                .append(r.n.size)
                .append(r.n.indicator);

            //Applying settings
            if(r.d.type){
                r.n.indicator.html("<i class='icon-ok'></i>");
            }else{
                r.n.row.addClass("error");
                r.n.indicator.html("<i class='icon-remove'></i>");
            }

            if(!r.d.size){
                r.n.size.hide();
            }
        },
        bytesToSize = function(bytes) {
            var sizes = ['Bytes', 'KB', 'MB'];

            if (bytes === 0) {
                return 'n/a';
            }
            var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));

            return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
        },
        getTypeFile = function(nameFile){
            //Check img presentation
            var fileExts = ["docx", "doc", "xls", "xlsx", "ppt", "pptx", "mp3", "jpg", "png", "gif"],
                fileExtension = nameFile.split(".").pop(),
                imageURI = null,
                imageName = null,
                fileType = null,
                index = fileExts.indexOf(fileExtension);

            if(index !== -1){
                switch(index){
                    case 0:
                    case 1:
                        imageName = "word_icon.png";
                        fileType = "WORD";
                        break;
                    case 2:
                    case 3:
                        imageName = "excel_icon.png";
                        fileType = "EXCEL";
                        break;
                    case 4:
                    case 5:
                        imageName = "ppt_icon.png";
                        fileType = "PPT";
                        break;
                    case 6:
                        imageName = "mp3_icon.png";
                        fileType = "MP3";
                        break;
                    case 7:
                    case 8:
                    case 9:
                        imageName = "image_icon.png";
                        fileType = "IMAGE";
                        break;
                }
            }else{
                imageName = "file_icon.png";
                fileType = "OTHER";
            }

            imageURI = "/system/_imagenes/" + imageName;

            return {
                imageURI    : imageURI,
                fileType    : fileType
            };
        };

    Row.TYPE_ERROR = 0;
    Row.TYPE_SUCCESS = 1;

    //CLASS VARS
    u.d = $.extend(true, {}, defaults, settings),
        u.n = {
            upload              : $("<div>").attr({"class":"upload"}),
            content         : $("<div>").attr({"class":"upload-content"}),
            item        : $("<div>").attr({"class":"item control"}),
            input   : $("<input>").attr({"type":"file"}),
            clear       : $("<div>").css({"clear":"both"}),
            detail          : $("<div>").attr({"class":"upload-detail"}),
            body    : $("<div>").attr({"class":"body"}),
            value    : $("<input>").attr({"type":"hidden", "name": u.d.name})
        };

    //CLASS METHODS
    u.open  = function(){
        if(!u.d.id){
            console.warn("Don't established a container id");
        }else{
            $(u.d.id).append(u.n.upload);
        }

        return u;
    },
        u.addRowError = function(title, message){
            var row = new Row({ size: message, title: title, type: Row.TYPE_ERROR});

            u.n.body.append(row.n.row);

            setTimeout(function(){
                row.n.row.fadeOut({
                    complete: function(){
                        row.n.row.remove();
                    }
                });
            }, 5000);
        },
        u.addRowMessage = function(title, message){
            var row = new Row({ size: message, title: title, type: Row.TYPE_SUCCESS});

            u.n.body.append(row.n.row);

            return row;
        },
        u.addFile = function(nameFile){
            var imageURI = getTypeFile(nameFile).imageURI;

            //Create Item
            var item = new Item({
                imageURI    : imageURI,
                title       : nameFile
            });

            //Create Row
            var row = u.addRowMessage(nameFile, "En proceso...");

            //Add new item before the input Item
            item.n.item.insertBefore(u.n.item);

            item.close(function(){
                //Remove DOM item
                item.n.item.remove();

                //Remove DOM row
                row.n.row.remove();

                //get newFileName
                var newNameFile = row.d.title;

                u.deleteNameFile(newNameFile);
            });

            var returnData = {
                item    : item,
                row     : row
            };

            return returnData;
        },
        u.addNameFile = function(nameFile){
            //Add fileName to files
            files.push(nameFile);

            //Set value FileNames in inputValue
            u.n.value.val(files.join("|"));

            //See if Upload support one item
            if(u.d.type === Upload.MONO_ITEM){
                u.n.item.hide();
            }
        },
        u.deleteNameFile = function(nameFile){
            //Delete fileName from files
            files.splice(files.indexOf(nameFile), 1);

            //Set value FileNames in inputValue
            u.n.value.val(files.join("|"));

            //See if Upload support one item
            if(u.d.type === Upload.MONO_ITEM){
                u.n.item.fadeIn();
            }
        },
        u.load = function(callback){
            onload = callback;
        };

    //Render the DOM
    u.n.upload
        .append(
        u.n.content
            .append(
            u.n.item
                .append(u.n.input)
        )
            .append(u.n.clear)
    )
        .append(
        u.n.detail
            .append(u.n.body)
            .append(u.n.value)
    );

    //Applying settings
    if(u.d.type === Upload.MULTI_ITEM){
        u.n.input.attr("multiple", true);
    }

    if(u.d.preFiles.length){
        u.d.preFiles.forEach(function(prefile){
            //Evalue if prefile.name is null for default or is empty
            if(prefile.name){
                //Add file
                var fileData = u.addFile(prefile.name),
                    item = fileData.item,
                    row = fileData.row;

                item.ready();

                //Change message row
                row.n.size.text("El archivo ya ha sido subido con exito");

                //Add nameFile to files
                u.addNameFile(prefile.name);

                //Add src to image if prefile.URI exists
                if(prefile.URI){
                    item.n.image.attr("src", prefile.URI);
                }
            }
        });
    }

    //Applying events
    u.n.input.on("change", function(e){
        //Hide Item upload
        if(u.d.type === Upload.MONO_ITEM){
            u.n.item.fadeOut();
        }

        var input = this;

        $.each(input.files, function(key, file){
            $.post(u.d.URI, {
                PREPARE_UPLOAD  : true,
                name            : file.name,
                size            : file.size,
                token           : u.d.token
            }, function(data){
                console.log(data);
//                return;
                if(data.success){
				
                    file.isVideo = data.isVideo;
                    file.destinyVideoServer = $('#' + u.d.form + ' input[name=destiny-video-server]:checked').val();

                    if(file.isVideo && file.destinyVideoServer === "owlserver"){
                        //Add file
                        var fileData = u.addFile(file.name),
                            item = fileData.item,
                            row = fileData.row;

                        var fileReader = new FileReader();
                        fileReader.readAsDataURL(file);

                        var formData = new FormData();
                        var xhr = new XMLHttpRequest();

                        formData.append("uploadVideo", file);

                        xhr.processData = false;
                        xhr.contentType = false;
                        xhr.open('POST', 'https://multimedia.owlgroup.org/upload', true);
                        xhr.setRequestHeader('x-access-token', data.token);
                        xhr.upload.onprogress = function (e) {
                            if(e.lengthComputable){
                                var loaded = e.loaded || e.position,
                                    total = e.total || e.totalSize,
                                    percent = Math.round((loaded / total) * 100),
                                    transfered = bytesToSize(loaded);

                                //Show calculations
                                item.n.bar.css("width", percent + "%");
                            }
                        };
                        xhr.onerror = function (e) {
                            console.log('error while trying to upload');
                        };
                        xhr.onload = function () {
                            console.log(xhr.responseText);
                        };
                        xhr.onloadstart = function(e){
                            item.n.progress.show();
                        };
                        xhr.send(formData);

                        var socket = io('https://multimedia.owlgroup.org/', {'force new connection': true, secure: true});

                        socket.on('progress', function(data){
                            //Change message row
                            row.n.size.text(data);
                        });

                        socket.on('progressLine', function(progress){
                            //Show calculations of convert video
                            item.n.bar.css("width", progress + "%");

                            if(progress === 100){
                                item.n.progress.hide();
                            }
                        });

                        socket.on('detalle', function(video){
                            var newNameFile = video.params.embed + "||" + video.params.mp4 + "||" + file.name;

                            item.ready();

                            //Change message row
                            row.n.size.text("Cambiando la privacidad del video...");

                            //Set thumbail
                            item.n.image.attr("src", video.params.thumbnail);

                            //Add nameFile to files
                            u.addNameFile(newNameFile);

                            //Start a new proccess for change video privacity
                            $.ajax({
                                data    : {
                                    id          : video.id,
                                    token       : data.token,
                                    estado      : "Publico"
                                },
                                url         : "https://multimedia.owlgroup.org/description",
                                method      : "POST",
                                headers     : {"Accept": "application/json"}
                            })
                                .done(function(data){
                                    row.n.size.text(bytesToSize(file.size));

                                    file.newName = newNameFile;

                                    onload(file);
                                });
                        });
                    }else{
                        //Add file
                        var fileData = u.addFile(file.name),
                            item = fileData.item,
                            row = fileData.row;

                        var fileReader = new FileReader();
                        fileReader.readAsDataURL(file);

                        var form = new FormData(),
                            xhr = new XMLHttpRequest();

                        form.append("file", file);
                        form.append("token", u.d.token);
                        form.append("form", u.d.form);
                        form.append("field", u.d.name);

                        xhr.onabort = function(e){

                        };
                        xhr.onerror = function(e){

                        };
                        xhr.onload = function(e){

                            var data = null;

                            try{
                                data = JSON.parse(e.target.responseText);
                                console.log(data);
                            }catch(err){
                                console.log(e.target.responseText);
                            }

                            if(data){
                                if(data.success){
                                    var newNameFile = data.file.name;

                                    item.ready();

                                    //Change message row
                                    row.n.size.text(bytesToSize(file.size));

                                    //Add nameFile to files
                                    u.addNameFile(newNameFile);

                                    //Change title row in defaultData
                                    row.d.title = newNameFile;

                                    //Evalue fileType to present Image
                                    var fileType = getTypeFile(newNameFile).fileType;

                                    if(fileType === "IMAGE" && data.file.uri){
                                        item.n.image.attr("src", data.file.uri);
                                    }

                                    file.newName = newNameFile;

                                    onload(file);
                                }else{
                                    //Remove DOM item
                                    item.n.item.remove();

                                    //Remove DOM row
                                    row.n.row.remove();

                                    //Add error message
                                    u.addRowError(file.name, data.message);

                                    if(u.d.type === Upload.MONO_ITEM){
                                        u.n.item.fadeIn();
                                    }
                                }
                            }else{
                                //Remove DOM item
                                item.n.item.remove();

                                //Remove DOM row
                                row.n.row.remove();

                                //Add error message
                                u.addRowError(file.name, "Ah ocurrido un error al intentar subir el archivo, por favor vuelva a intentarlo...");

                                if(u.d.type === Upload.MONO_ITEM){
                                    u.n.item.fadeIn();
                                }
                            }
                        };
                        xhr.onloadend = function(e){

                        };
                        xhr.onloadstart = function(e){
                            item.n.progress.show();
                        };
                        xhr.upload.onprogress = function(e){
                            if(e.lengthComputable){
                                var loaded = e.loaded || e.position,
                                    total = e.total || e.totalSize,
                                    percent = Math.round((loaded / total) * 100),
                                    transfered = bytesToSize(loaded);

                                //Show calculations
                                item.n.bar.css("width", percent + "%");

                                //speed 
                                //remaining

                                if(percent === 100){
                                    item.n.progress.hide();
                                }
                            }
                        };
                        xhr.ontimeout = function(e){

                        };

                        xhr.open('POST', u.d.URI);
                        xhr.send(form);
                    }
                }else{
                    u.addRowError(file.name, data.message);

                    if(u.d.type === Upload.MONO_ITEM){
                        u.n.item.fadeIn();
                    }
                }
            }, "json")
                .fail(function(xhr){
                    //Fail request
                    console.log(xhr.responseText);
                });
        });
    });
}

/* Upload Multifile, UIT: Upload Image TextArea */
var is_process_UIT = false;
var UIT_file_wait = [];
var cont_id_UIT = 1;
function uploadUIT(inputFile, url, path, formId, IdentificadorTextArea, file) {
    //Si el input contiene mas de un archivo...
    if (document.getElementById(inputFile).files.length > 1) {
        console.log(document.getElementById(inputFile).files.length + " Archivos");
        for (var i = 1; i < document.getElementById(inputFile).files.length; i++) {
            //ADD new upload control
            var new_input_owl = document.createElement("div");
            new_input_owl.setAttribute("class", "input-owl");
            new_input_owl.style["background-size"] = "40px";
            new_input_owl.style["background-image"] = "url('" + url_domain_current + "/system/_imagenes/load.gif')";

            var new_id_input_file = 'owl_upload_input' + cont_id_UIT;
            cont_id_UIT++;

            var input_file = document.createElement("input");
            input_file.setAttribute("id", new_id_input_file);
            input_file.setAttribute("type", "file");
            input_file.disabled = true;

            var input_file_hidden = document.createElement("input");
            input_file_hidden.setAttribute("id", new_id_input_file + '-id');
            input_file_hidden.setAttribute("type", "hidden");

            new_input_owl.appendChild(input_file);
            new_input_owl.appendChild(input_file_hidden);

            if (i > 1) {
                var lastid = 'owl_upload_input' + (cont_id_UIT - 2);
                document.getElementById(lastid).parentNode.parentNode.insertBefore(new_input_owl, document.getElementById(lastid).parentNode);
                //console.log('lastId:' + lastid); 
            } else {
                document.getElementById(inputFile).parentNode.parentNode.insertBefore(new_input_owl, document.getElementById(inputFile).parentNode);
            }

            //Añadiendo a Cola
            var json_upload_owl_aux = {
                inputFile: new_id_input_file,
                url: url,
                path: path,
                formId: formId,
                IdentificadorTextArea: IdentificadorTextArea,
                file: document.getElementById(inputFile).files[i]
            };
            UIT_file_wait.push(json_upload_owl_aux);
        }
        console.log(UIT_file_wait.length + " archivos en espera...");
    }
    //Si hay achivos en espera...
    if (is_process_UIT) {
        var uploadfile = document.getElementById(inputFile);
        uploadfile.parentNode.style["background-size"] = "40px";
        uploadfile.parentNode.style["background-image"] = "url('" + url_domain_current + "/system/_imagenes/load.gif')";
        var json_upload_owl = {
            inputFile: inputFile,
            url: url,
            path: path,
            formId: formId,
            IdentificadorTextArea: IdentificadorTextArea,
            file: null
        };
        UIT_file_wait.push(json_upload_owl);
        console.log("El archivo esta en la cola de espera...");
        return;
    }
    var msg_upload = document.getElementById('msg_upload_owl');
    is_process_UIT = true;
    console.log("Upload: Iniciado...");

    msg_upload.querySelector('#progress_bar_content').style["display"] = "block";
    msg_upload.querySelector('#det_upload_owl').style["display"] = "block";
    msg_upload.querySelector('#det_bupload_owl').style["display"] = "block";

    var inputHidden = document.getElementById(inputFile + '-id');
    inputFile = document.getElementById(inputFile);
    inputFile.removeAttribute("onchange");

    //global vars
    var Timer = null;
    var iBytesUploaded = 0;
    var iBytesTotal = 0;
    var iPreviousBytesLoaded = 0;

    function secondsToTime(secs) {
        var hr = Math.floor(secs / 3600);
        var min = Math.floor((secs - (hr * 3600)) / 60);
        var sec = Math.floor(secs - (hr * 3600) - (min * 60));

        if (hr < 10) {
            hr = "0" + hr;
        }
        if (min < 10) {
            min = "0" + min;
        }
        if (sec < 10) {
            sec = "0" + sec;
        }
        if (hr) {
            hr = "00";
        }
        return hr + ':' + min + ':' + sec;
    }
    ;

    function bytesToSize(bytes) {
        var sizes = ['Bytes', 'KB', 'MB'];
        if (bytes === 0) {
            return 'n/a';
        }
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
    }
    ;

//aaaaa
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        inputFile.disabled = true;
        iPreviousBytesLoaded = 0;
        var oFile = (file) ? file : inputFile.files[0];

        var oReader = new FileReader();
        oReader.onload = function (e) {

        };
        oReader.readAsDataURL(oFile);

        var urlVUP = url + "&VUP=Y&filedata=" + inputFile.parentNode.parentNode.getAttribute('data-filedata');

        inputFile.parentNode.style["background-size"] = "40px";
        inputFile.parentNode.style["background-image"] = "url('" + url_domain_current + "/system/_imagenes/load.gif')";
        var sAjaxMotor = crearXMLHttpRequest();
        sAjaxMotor.onreadystatechange = function () {
            if (sAjaxMotor.readyState === 4) {
                switch (sAjaxMotor.status) {
                    case 200:
                        var JSONresp = sAjaxMotor.responseText;
                        var response = JSON.parse(JSONresp);
                        var tiposVUP = response.filedata.tipos;

                        var sizeVUP = parseFloat(response.filedata.maxfile, 10) * 1024 * 1024;
                        var ext_oFile = oFile.name.split('.').pop();

                        console.log("Extension de archivo: " + ext_oFile);
                        if (tiposVUP.indexOf(ext_oFile) !== -1) {
                            console.log(ext_oFile + " extension permitido: CORRECTO");
                            if (oFile.size < sizeVUP) {
                                console.log(oFile.size + " < " + sizeVUP + ": CORRECTO");

                                var form = new FormData();
                                var xhr = new XMLHttpRequest();

                                form.append('file', oFile);
                                form.append('path', path);
                                form.append('formId', formId);
                                form.append('filedata', inputFile.parentNode.parentNode.getAttribute('data-filedata'));
                                form.append('campo', inputFile.getAttribute('name'));

                                xhr.upload.addEventListener('progress', uploadProgress, false);
                                xhr.addEventListener('load', uploadFinish, false);
                                xhr.addEventListener('error', uploadError, false);
                                xhr.addEventListener('abort', uploadAbort, false);
                                xhr.open('POST', url);
                                xhr.send(form);

                                Timer = setInterval(doInnerUpdates, 300);
                            } else {
                                inputFile.disabled = false;
                                inputFile.parentNode.style["display"] = "none";
                                alert("** Tu archivo supera el tamaño permitido **");
                                is_process_UIT = false;
                                add_newInputOwl();
                                exits_file_wait();
                            }
                        } else {
                            inputFile.disabled = false;
                            inputFile.parentNode.style["display"] = "none";
                            alert("** Solo se permite archivos con extensión: (*." + tiposVUP.join(' *.') + ")");
                            is_process_UIT = false;
                            add_newInputOwl();
                            exits_file_wait();
                        }
                        break;
                    case 404:
                        console.log("ERROR: La página no existe");
                        break;
                    case 500:
                        console.log("ERROR: Del servidor");
                        break;
                    default:
                        console.log("ERROR: Desconocido");
                        break;
                }
            }
        };
        sAjaxMotor.open("GET", urlVUP, true);
        sAjaxMotor.send();
    } else {
        console.log('El API del control de Archivo no es soportado por todos los buscadores');
        return;
    }

    function doInnerUpdates() {
        console.log("doInnerUpdates");
        var iCB = iBytesUploaded;
        var iDiff = iCB - iPreviousBytesLoaded;

        // if nothing new loaded - exit
        if (iDiff === 0) {
            return;
        }

        iPreviousBytesLoaded = iCB;
        iDiff = iDiff * 2;
        var iBytesRem = iBytesTotal - iPreviousBytesLoaded;
        var secondsRemaining = iBytesRem / iDiff;

        // update speed info
        var iSpeed = iDiff.toString() + 'B/s';
        if (iDiff > 1024 * 1024) {
            iSpeed = (Math.round(iDiff * 100 / (1024 * 1024)) / 100).toString() + 'MB/s';
        } else if (iDiff > 1024) {
            iSpeed = (Math.round(iDiff * 100 / 1024) / 100).toString() + 'KB/s';
        }

        msg_upload.querySelector('#speed').innerHTML = 'Velocidad: ' + iSpeed;
        msg_upload.querySelector('#remaining').innerHTML = 'Tiempo restante: ' + secondsToTime(secondsRemaining);

    }

    function uploadProgress(e) {
        console.log("uploadProgress");
        if (e.lengthComputable) {
            iBytesUploaded = e.loaded;
            iBytesTotal = e.total;
            var iPercentComplete = Math.round(e.loaded * 100 / e.total);
            var iBytesTransfered = bytesToSize(iBytesUploaded);

            msg_upload.querySelector('#progress_percent').innerHTML = iPercentComplete.toString() + '%';
            msg_upload.querySelector('#progress_owl').style.width = iPercentComplete.toString() + '%';
            msg_upload.querySelector('#b_transfered').innerHTML = iBytesTransfered + ' Subidos...';
            if (iPercentComplete === 100) {
                var oUploadResponse = msg_upload.querySelector('#upload_response');
                oUploadResponse.innerHTML = 'Por favor espere...';
                oUploadResponse.style.display = 'block';
            }
        }
    }

    function uploadFinish(e) {
        console.log("uploadFinish");
        inputFile.disabled = true;
        var msg = '';
        var responseText = e.target.responseText;
        try {
            var response = JSON.parse(responseText);
            inputHidden.value = response.codigo;
            msg = response.msg;

            var div_nombre_file = document.createElement('div');
            div_nombre_file.setAttribute("class", "name_file");
            div_nombre_file.innerHTML = response.filenameNew;
            inputFile.parentNode.insertBefore(div_nombre_file, inputFile);

            var div_delete_file = document.createElement('div');
            div_delete_file.setAttribute("class", "delete_file");
            div_delete_file.innerHTML = "X";
            inputFile.parentNode.insertBefore(div_delete_file, inputFile);
            div_delete_file.onclick = function () {
                var file_name_delete = response.filenameNew;
                var upload_input_response = document.getElementById("upload_input_response");
                var mx_name_files = upload_input_response.value.split("|");
                mx_name_files.splice(mx_name_files.indexOf(file_name_delete), 1);
                upload_input_response.value = mx_name_files.join("|");
                console.log("Input de respuesta despues de Delete: " + mx_name_files.join("|"));
                inputFile.parentNode.parentNode.removeChild(inputFile.parentNode);

                var chkfiledelete = document.getElementById("filechk-" + response.filenameNew);
                chkfiledelete.parentNode.removeChild(chkfiledelete);
            };

            inputFile.setAttribute("title", response.filenameNew);

            /* Evaluando la Imagen de presentacion */
            var ext_array = ["docx", "doc", "xls", "xlsx", "ppt", "pptx", "mp3"];
            var ext_array_img = ["jpg", "png", "gif"];
            var oFile = (file) ? file : inputFile.files[0];
            var ext_oFile = oFile.name.split('.').pop();

            var url_background = null;
            var index_extension = ext_array.indexOf(ext_oFile);
            var index_extension_img = ext_array_img.indexOf(ext_oFile);
            if (index_extension !== -1) {
                inputFile.parentNode.style["background-size"] = "75% 75%";
                switch (index_extension) {
                    case 0:
                    case 1:
                        url_background = url_domain_current + "/system/_imagenes/word_icon.png";
                        break;
                    case 2:
                    case 3:
                        url_background = url_domain_current + "/system/_imagenes/excel_icon.png";
                        break;
                    case 4:
                    case 5:
                        url_background = url_domain_current + "/system/_imagenes/ppt_icon.png";
                        break;
                    case 6:
                        url_background = url_domain_current + "/system/_imagenes/mp3_icon.png";
                        break;
                }
            } else if (index_extension_img !== -1) {
                inputFile.parentNode.style["background-size"] = "100% 100%";
                url_background = response.img_upload_url;
            } else {
                inputFile.parentNode.style["background-size"] = "75% 75%";
                url_background = url_domain_current + "/system/_imagenes/file_icon.png";
            }
            inputFile.parentNode.style["background-image"] = "url('" + url_background + "')";

            var TextArea = document.getElementById(IdentificadorTextArea + "-Edit");
            TextArea.focus();
            document.execCommand("insertImage", false, url_background);
            TextArea.focus();
            scanImagesToResize($(TextArea));
            /* Fin de Imagen de Presentacion */
        } catch (e) {
            msg = responseText;
        }

        var oUploadResponse = msg_upload.querySelector('#upload_response');
        oUploadResponse.innerHTML = msg;
        oUploadResponse.style.display = 'block';

        //Añadiendo cola de archivos subidos...
        var new_file_check = document.createElement("div");
        new_file_check.setAttribute("class", "new_file_check");
        new_file_check.setAttribute("id", "filechk-" + response.filenameNew);
        new_file_check.innerHTML = '&check; ' + oFile.name;
        var upload_size = document.createElement('div');
        upload_size.setAttribute("class", "upload_size");
        upload_size.innerHTML = bytesToSize(oFile.size);
        var div_clean = document.createElement("div");
        div_clean.setAttribute("class", "clean");

        new_file_check.appendChild(upload_size);
        new_file_check.appendChild(div_clean);

        oUploadResponse.parentNode.appendChild(new_file_check);

        //Añadiendo archivo al input de respuesta
        var upload_input_response = document.getElementById("upload_input_response");
        if (upload_input_response.value.trim() !== "") {
            var mx_name_files = upload_input_response.value.split("|");
            mx_name_files.push(response.filenameNew);
            upload_input_response.value = mx_name_files.join("|");
            console.log("Input de respuesta: " + mx_name_files.join("|"));
        } else {
            upload_input_response.value = response.filenameNew;
        }

        clearInterval(Timer);
        is_process_UIT = false;
        msg_upload.querySelector('#progress_bar_content').style["display"] = "none";
        console.log("Upload finalizado...");

        console.log("Hay " + UIT_file_wait.length + " archivos en espera...");
        //ADD new upload control
        add_newInputOwl();
        exits_file_wait();
    }
    //Mis funciones
    function add_newInputOwl() { //Agregar si no hay ningun archivo en espera
        if (UIT_file_wait.length === 0) {
            var new_input_owl = document.createElement("div");
            new_input_owl.setAttribute("class", "input-owl");

            var new_id_input_file = 'owl_upload_input' + cont_id_UIT;
            cont_id_UIT++;

            var input_file = document.createElement("input");
            input_file.setAttribute("id", new_id_input_file);
            input_file.setAttribute("type", "file");
            input_file.setAttribute("title", "Elegir un Archivo");
            input_file.setAttribute("multiple", "");
            input_file.onchange = function () {
                uploadUIT(new_id_input_file, url, path, formId, IdentificadorTextArea);
            };
            var input_file_hidden = document.createElement("input");
            input_file_hidden.setAttribute("id", new_id_input_file + '-id');
            input_file_hidden.setAttribute("type", "hidden");

            new_input_owl.appendChild(input_file);
            new_input_owl.appendChild(input_file_hidden);
            inputFile.parentNode.parentNode.insertBefore(new_input_owl, inputFile.parentNode);
        }
    }
    function exits_file_wait() {
        if (UIT_file_wait.length > 0) {
            var new_JsonUpload = UIT_file_wait.shift();
            uploadUIT(new_JsonUpload.inputFile, new_JsonUpload.url, new_JsonUpload.path, new_JsonUpload.formId, new_JsonUpload.IdentificadorTextArea, new_JsonUpload.file);
        }
    }

    function uploadError(e) {
        console.log("uploadError");
//        console.log('uploadError');
    }

    function uploadAbort(e) {
        console.log("uploadAbort");
//        console.log('uploadError');
    }
}

function GrantDeleteFilesUpload(IdContentUpload) {
    var inputs_owl = IdContentUpload.getElementsByClassName("input-owl");
    for (var i = 1; i < inputs_owl.length; i++) {
        var btnDelete = inputs_owl[i].getElementsByClassName("delete_file")[0];
        var file_name_delete = inputs_owl[i].getElementsByClassName("name_file")[0].innerHTML;
        AddDeleteEvent(btnDelete, file_name_delete);
    }
    function AddDeleteEvent(ObjDeleteBtnUpload, file_name_delete) {
        ObjDeleteBtnUpload.onclick = function () {
            var upload_input_response = document.getElementById("upload_input_response");
            var mx_name_files = upload_input_response.value.split("|");
            mx_name_files.splice(mx_name_files.indexOf(file_name_delete), 1);
            upload_input_response.value = mx_name_files.join("|");
            console.log("Input de respuesta despues de Delete: " + mx_name_files.join("|"));
            this.parentNode.parentNode.removeChild(this.parentNode);
        };
    }
}

/* js SELECT BOX */
function init_OwlCbo(ObjSelectBox) {
    var osb = ObjSelectBox;
    osb.removeAttribute("onclick");
    var cboresponse = document.getElementById("cboresponse_" + osb.getAttribute("id"));
    var currentOption = osb.getElementsByClassName("current_option")[0];
    var content_cbo_owl_options = osb.getElementsByClassName("content_cbo_owl_options")[0];
    //Displayando Contenido de Opciones
    osb.setAttribute("dsp-opt-cboowl", "true");
    osb.style["background-color"] = "#246B66";
    content_cbo_owl_options.style["display"] = "block";

    //Detectando Options Cbo
    var cbo_items_owl = content_cbo_owl_options.getElementsByClassName("cbo_item_owl");

    for (var i = 0; i < cbo_items_owl.length; i++) {
        select_item_cbo(cbo_items_owl[i]);
    }

    osb.onclick = function () {
        if (this.getAttribute("dsp-opt-cboowl")) {
            this.removeAttribute("dsp-opt-cboowl");
            this.style["background-color"] = "#339791";
            content_cbo_owl_options.style["display"] = "none";
        } else {
            this.setAttribute("dsp-opt-cboowl", "true");
            this.style["background-color"] = "#246B66";
            content_cbo_owl_options.style["display"] = "block";
        }
    };

    function select_item_cbo(ObjItem) {
        ObjItem.onclick = function (e) {
            var data_value_item = this.getAttribute("data-value");
            currentOption.innerHTML = this.getAttribute("data-display");
            //Dando valor al select de Respuesta
            cboresponse.innerHTML = "";
            var option = document.createElement("option");
            option.setAttribute("value", data_value_item);
            cboresponse.appendChild(option);
            cboresponse.selectedIndex = 0;
            //Ocultando o Mostrando datos...
            HideAndShowEventFields(this);
        };
    }
}
//Busca nodo padre segun Tag (GENERAL FUNCTION)
function searchNodeTag(objElem, tagNameNode) {
    var exist = false;
    var parent = objElem.parentNode;
    while (exist === false) {
        if (parent.tagName === tagNameNode) {
            exist = true;
        } else if (parent.tagName === "BODY") {
            console.log("ERROR: No se encontro un NodoPadre de tipo " + tagNameNode + "...");
            return null;
        } else {
            parent = parent.parentNode;
        }
    }
    return parent;
}
//Ocultando o Mostrando datos... (SELECT-BOX)
function HideAndShowEventFields(objelem) {
    if (!objelem) {
        console.log("El elemento no existe... Proceso se cierra...");
        return;
    }
    if (objelem.getAttribute("data-sh")) {
        var sh = objelem.getAttribute("data-sh"); //sh: show|hide
        var e_h_f = objelem.getAttribute("data-e-h-f"); //e_h_f : event hidden fields
        var fields = e_h_f.trim().split("|");
        //var cur_form=cboresponse.form;
        var cur_form = searchNodeTag(objelem, "FORM"); //Formulario Actual
        var elemens = cur_form.elements;
        for (var i = 0; i < elemens.length; i++) {
            if (elemens[i].name) {
                var index_pos = fields.indexOf(elemens[i].name);
                if (index_pos !== -1) {
                    var li = searchNodeTag(elemens[i], "LI");
                    if (li) {
                        if (sh === "show") {
                            li.style["display"] = "block";
                        } else if (sh === "hide") {
                            li.style["display"] = "none";
                        }
                    }
                }
            }
        }
    }
}

/* CheckBox Dinamic */
function init_OwlChk(ObjChkBox) {
    var ochkb = ObjChkBox;
    ochkb.removeAttribute("onclick");
    var cboresponse = document.getElementById("chkresponse_" + ochkb.getAttribute("id"));
    var currentOption = ochkb.getElementsByClassName("current_option")[0];
    var content_chk_owl_options = ochkb.getElementsByClassName("content_chk_owl_options")[0];

    //Detectando Options Chk
    var chk_items_owl = content_chk_owl_options.getElementsByClassName("chk_item_owl");

    //Displayando Contenido de Opciones
    var curOptionRightStyle = currentOption.style["right"];

    currentOption.removeAttribute("style");
    if (curOptionRightStyle) {
        currentOption.style["left"] = "0em";
    } else {
        ochkb.setAttribute("dsp-opt-chkowl", "true");
        currentOption.style["color"] = "#FFF";
        currentOption.style["right"] = "0em";
        currentOption.style["background-color"] = "#339791";
        currentOption.style["border-color"] = "#246B66";

    }
    select_item_chk(chk_items_owl[1]);

    ochkb.onclick = function () {
        if (this.getAttribute("dsp-opt-chkowl")) {
            this.removeAttribute("dsp-opt-chkowl");
            currentOption.removeAttribute("style");
            if (curOptionRightStyle) {
                select_item_chk(chk_items_owl[1]);
            } else {
                select_item_chk(chk_items_owl[0]);
            }
        } else {
            this.setAttribute("dsp-opt-chkowl", "true");
            currentOption.removeAttribute("style");
            currentOption.style["background-color"] = "#339791";
            currentOption.style["border-color"] = "#246B66";
            currentOption.style["color"] = "#FFF";
            currentOption.style["right"] = "0em";
            if (curOptionRightStyle) {
                select_item_chk(chk_items_owl[0]);
            } else {
                select_item_chk(chk_items_owl[1]);
            }
        }
    };

    function select_item_chk(ObjItem) {
        var data_value_item = ObjItem.getAttribute("data-value");
        currentOption.innerHTML = ObjItem.getAttribute("data-display");
        //Dando valor al select de Respuesta
        cboresponse.innerHTML = "";
        var option = document.createElement("option");
        option.setAttribute("value", data_value_item);
        cboresponse.appendChild(option);
        cboresponse.selectedIndex = 0;
        //Ocultando o Mostrando datos...
        HideAndShowEventFields(ObjItem);
    }
}
/* Automatic Save */
function AutomaticSave(path) {
    var newWin = window.open(path);
    setTimeout(SaveAsDefault(), 100);

    function SaveAsDefault() {

    }
}


function AlertaHerramientas(Url, Panel) {
    FAjaxBHerramientas(Url, Panel, ActualizaId);
}

function TraeDatosAJAX(Url, Panel) {
    FAjaxBHerramientas(Url, Panel, GeneraEventos);
}

function GeneraEventos(e, Url, Panel) {

    var PanelMensajeAlerta = document.getElementById("PanelMensajeAlerta_display");

}


function ActualizaId(e, Url, Panel) {

    var CampoTetxt = document.getElementById(Panel);
    var ContenidoBoton = document.getElementById("IconoAlerta");
    var PanelMensajeAlerta = document.getElementById("PanelMensajeAlerta");
    // PanelMensajeAlerta.focus();
    // alert();
    if (CampoTetxt.innerText == 0) {
        ActualizaNodo(Panel, {Estilo: "display:none;", Html: ""});
        ActualizaNodo("IconoAlerta", {Clase: "botIconF2BO", Html: "Lleno"});
        // alert("hola mundo");
    } else {
        ActualizaNodo(Panel, {Estilo: "display:block;", Html: CampoTetxt.innerText});
        ActualizaNodo("IconoAlerta", {Clase: "botIconF2B", Html: "Lleno"});
    }
}

function FAjaxBHerramientas(Url, Panel, callback) {
    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("GET", Url, true);
    sAjaxMotor.onreadystatechange = function () {
        var detalles = document.getElementById(Panel);
        if (sAjaxMotor.readyState === 4) {
            if (sAjaxMotor.status == 200) {
                detalles.innerHTML = sAjaxMotor.responseText;
                callback(sAjaxMotor, Url, Panel);
            }
        }
    }
    sAjaxMotor.send(null);
}

function CierraPanelAlerta(Id) {
    ActualizaNodo(Id, {Estilo: "display:none;", Html: "Lleno"});
}

function AddEventCollapseDiv(node, url, displayId) {
    var $node = $(node),
    $displayNode = $("#" + displayId);
    
    $node
    .removeAttr("onclick")
    .click(function(){
        display();
    });
    
    $(document).click(function(e){
        if(!$displayNode.parent().find($(e.target)).length){
            $node.removeAttr("data-display");
            
            $displayNode.hide();
        }
    });
    
    display();
    
    function display(){
        if(!$node.attr("data-display")){
            $node.attr("data-display", true);
            
            $displayNode.show();
            
            TraeDatosAJAX(url, displayId);
        }else{
            $node.removeAttr("data-display");
            
            $displayNode.hide();
        }
    }
}

function addTimerEvent(idElement, url) {
    var $nodeIndicator = $("#" + idElement).text("0");
    
    function getData() {
        $.get(url, function(num){
            if(num){
                $nodeIndicator.text(num).show();
            }else{
                $nodeIndicator.hide();
            }
        }, "json")
        .fail(function(response){
            console.log(response.responseText);
        });
    };
    
    getData();
    setInterval(getData, 60000);
}

function show_listdiv() {
    //aaa1
}

function showContentProgram(btnShow, idContentProgram) {
    var cp = document.getElementsByClassName("content_product");
    [].forEach.call(cp, function (elcp) {
        elcp.style["display"] = "none";
    });

    var DivContent = btnShow;
    while (DivContent.className !== "blog_post") {
        DivContent = DivContent.parentNode;
        if (DivContent.tagName === "BODY") {
            return;
        }
    }
    var PosDC = getAbsoluteElementPosition(DivContent);
    console.log(PosDC);
    var CP = document.getElementById(idContentProgram);
    var btnClose = CP.getElementsByClassName("btn_close")[0];
    btnClose.onclick = function (e) {
        CP.style["display"] = "none";
    };
//    CP.onmouseleave=function(){
//        CP.style["display"]="none";
//    };

    CP.style["display"] = "inline-block";
    CP.style["top"] = PosDC.top + "px";
    CP.style["left"] = (PosDC.left + DivContent.offsetWidth + 20) + "px";
}

function RedirectPanel(url, panel, time) {
    setTimeout(enviaVista(url, panel, ''), time);
}

function HideShowPanel(obj, idpanel) {
    var dsp = obj.getAttribute("data-dsp");

    if (!dsp) {
        document.getElementById(idpanel).style["display"] = "block";
        obj.setAttribute("data-dsp", "true");
        obj.innerHTML = "<i class='icon-chevron-up'></i>";
    } else {
        document.getElementById(idpanel).style["display"] = "none";
        obj.removeAttribute("data-dsp");
        obj.innerHTML = "<i class='icon-chevron-down'></i>";
    }
}

function clog(elem) {
    console.log(elem);
}


function enviaFormNA(sUrl,formid,sDivCon,sIdCierra){
  
    if(sIdCierra!=="") {
        panelAdm(sIdCierra,"Cierra");
    }

    var Formulario=document.getElementById(formid);
    // alert(" Formulario "+Formulario);
    var form_elements=Formulario.elements;
    var cadenaFormulario="";
    var _y = "&";
            

    for(var i=0;i<form_elements.length;i++){
        var elem=form_elements[i],responseValue,success=true;
        if(elem.getAttribute('data-CBI')!==true && elem.name){

            switch(elem.type){
                case "text":
                case "password":
                case "submit":
                case "hidden":
                case "number":
                    responseValue=elem.value;
                    break;
                 case "textarea":
                    var sTextAreaValue,sTextAreaValueB;
                    sTextAreaValue=document.getElementById(elem.name + "-Edit");
                
                    if(sTextAreaValue){
                        sTextAreaValueB=sTextAreaValue.innerHTML;
                    }else{
                        sTextAreaValueB=elem.value;
                    }
                    responseValue=sTextAreaValueB;
                    break;
                case "file":
                    if(elem.value!==""){
                        var sPath=elem.getAttribute('ruta');
                    }
                    responseValue=elem.value;
                    break;
                case "checkbox":
                case "radio":
                    if(elem.checked){
                        responseValue=elem.value;
                    }else{
                        success=false;
                    }
                    break;
            }

            if (elem.tagName==="SELECT") {
                responseValue=elem.value;
            }

            if(success){
                responseValue=responseValue.replace(/'/g,'"').replace(/&nbsp;/g,"<1001>").replace(/&/g," ");
                cadenaFormulario += _y + elem.name + '=' + encodeURI(responseValue);
            }
        }
    }
    
    // alert("paseo 2");
    document.getElementById(sDivCon).innerHTML = '<img src="./_imagenes/loading.gif" width="50px"><div class="loading">Cargando...</div>';
// alert("paseo 3");
    var sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("POST",sUrl + "&TipoDato=texto&formId=" + formid,true);
    sAjaxMotor.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=ISO-8859-1');
    sAjaxMotor.onreadystatechange=function(){
        procesarEventosNA(sAjaxMotor,sDivCon,sUrl);
    };
    sAjaxMotor.send(cadenaFormulario);
}


function procesarEventosNA(sAjaxMotor, divContenido, url)
{
    window.status = url;
    var detalles = document.getElementById(divContenido);
    if (sAjaxMotor.readyState === 4) {
        switch (sAjaxMotor.status) {
            case 200:
                detalles.innerHTML = sAjaxMotor.responseText;
                parseScript(sAjaxMotor.responseText);
                break
            case 404:
                document.getElementById(containerid).innerHTML = "ERROR: La página no existe<br>" + url;
                break
            case 500:
                document.getElementById(containerid).innerHTML = "ERROR: Del servidor<br />" + page_request.status + page_request.responseText;
                break
            default:
                document.getElementById(containerid).innerHTML = "ERROR: Desconocido<br />" + page_request.status + page_request.responseText;
                break
        }
    }
}

function FAjaxPost(Url,Panel,Form,callback){
    
	var Formulario=document.getElementById(Form);
    var form_elements=Formulario.elements;
    var cadenaFormulario="";
    var _y = "&";       

    for(var i=0;i<form_elements.length;i++){
        var elem=form_elements[i],responseValue,success=true;
            if(elem.getAttribute('data-CBI')!==true && elem.name){
	
					switch(elem.type){
						case "text":
						case "password":
						case "submit":
						case "hidden":
							responseValue =elem.value;

							break;
						case "textarea":
							var sTextAreaValue,sTextAreaValueB;
							sTextAreaValue=document.getElementById(elem.name + "-Edit");
							if(sTextAreaValue){
								sTextAreaValueB=HTML_PaC(sTextAreaValue.innerHTML);
							} else {
								sTextAreaValueB=elem.value;
							}			
							responseValue=sTextAreaValueB;
							break;
						case "file":
							if(elem.value!==""){
								var sPath=elem.getAttribute('ruta');
							}		
							responseValue=elem.value;
							break;
						case "checkbox":
						case "radio":
							if(elem.checked){
								responseValue=elem.value;
							}else{
								success=false;
							}
							break;
				    }
					
				    if (elem.tagName==="SELECT") {
						responseValue=elem.value;
					}
					if(success){
						responseValue=responseValue.replace(/'/g,'"').replace(/&nbsp;/g," ").replace(/&/g," ");
						cadenaFormulario += _y + elem.name + '=' + encodeURI(responseValue);
					}
            }
    }
	
	var sAjaxMotor = false;
	sAjaxMotor = crearXMLHttpRequest();
	sAjaxMotor.open("POST", Url, true);
	sAjaxMotor.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=ISO-8859-1');
	
	sAjaxMotor.onreadystatechange = function() {
		var detalles = document.getElementById(Panel);
		if (sAjaxMotor.readyState === 4) {
			if (sAjaxMotor.status == 200) {
					detalles.innerHTML = sAjaxMotor.responseText;
					callback(sAjaxMotor,Url,Panel,Form);
			}
			if (sAjaxMotor.status == 500) {
									 alert("error de conexion");
			}                          
		}
	}
	sAjaxMotor.send(cadenaFormulario);	
}


function enviaFormS(sParmetro){

    var JsnData = JSON.parse(sParmetro);
    sUrl = JsnData.sUrl;
    formid = JsnData.formid;
    sDivCon = JsnData.sDivCon;
    sIdCierra = JsnData.sIdCierra;
    
    ParametrosInput = JsnData.ParametrosInput;
    UrlPrimerBtn = JsnData.UrlPrimerBtn;
    
    var Formulario=document.getElementById(formid);
    var form_elements=Formulario.elements;
    var cadenaFormulario="";
    var _y = "&";            
    var  UrlCadena = sUrl.split('?');   
    var CodFormURL = formid.replace("Form_", "");
    CodFormURL = CodFormURL.replace("-UPD", "");
    var cont_val = 0;
    var campos_validacion= new Array();
    
    //Arma un array con todos los campos que se deben válidar
    for(var j=0;j<form_elements.length;j++){
        var elemA = form_elements[j],responseValue,success=true;
        if( elemA.getAttribute('data-valida') !== ""  ){
            if( elemA.getAttribute('data-valida') !== null  ){          
                 cont_val += 1; 
                 campos_validacion[cont_val] = elemA.getAttribute('data-valida');   
             }
         }
    } 
    
    //Encuantra los campos a validar y comienza a imprimir sus mensajes de validación
    var cont_val_b = 0;
    for(var i=0;i<form_elements.length;i++){
        var elem=form_elements[i],responseValue,success=true;
        
         //alert(elem.getAttribute('data-valida'));
        if( elem.getAttribute('data-valida') !== ""  ){

           if( elem.getAttribute('data-valida') !== null  ){    
             
                cont_val_b += 1;    
                var ValorIdCmp  = document.getElementById(elem.getAttribute('data-valida')).value; 
                CadenaNew = elem.getAttribute('data-valida')+"="+ValorIdCmp+"&";    
                campoFinal = campos_validacion[cont_val];
                var Url = UrlCadena[0]+"?"+CadenaNew+"TipoDato=texto&transaccion=VALIDACION&metodo="+CodFormURL+"&NombreCampo="+elem.getAttribute('data-valida')+"";    
              
                var ParametrosAjax = new Array(Url,'CmpValidacion--' + elem.getAttribute('data-valida'),formid,sUrl,campoFinal,sDivCon,sIdCierra,ParametrosInput,UrlPrimerBtn);
                
                ValorAjaxG = FAjaxS(ParametrosAjax,WAjaxValidacionS);           
            }
         }
    } 
    
   // si cont_val_b == 0 es porque no hay ningun campo a validar
   
    if(cont_val_b == 0){
        var ParametrosAjax = [];
        ParametrosAjax[0] = "";
        ParametrosAjax[1] = "";
        ParametrosAjax[2] = formid;
        ParametrosAjax[3] = sUrl;
        ParametrosAjax[4] = "";
        ParametrosAjax[5] = sDivCon;
        ParametrosAjax[6] = sIdCierra;
        ParametrosAjax[7] = ParametrosInput;
        ParametrosAjax[8] = UrlPrimerBtn;
        enviaForm(ParametrosAjax);
    }
}

function WAjaxValidacionS(e,ParametrosAjax,Form){

    Url = ParametrosAjax[0];
    Panel = ParametrosAjax[1];

    Form = ParametrosAjax[2];
    sUrl = ParametrosAjax[3];
    campoFinal = ParametrosAjax[4];
    sDivCon = ParametrosAjax[5];
    sIdCierra = ParametrosAjax[6];
    ParametrosInput = ParametrosAjax[7];
    UrlPrimerBtn = ParametrosAjax[8];

    // var a = e.responseText;
    var detalles = document.getElementById(Panel);
    detalles.innerHTML = e.responseText;


    //alert("entro Panel::  "+ Panel ); 
    var Mp = MatrisProtocolo(Panel);

    // var Msg = vpanel.innerHTML;
    // vpanel.innerHTML = "";
        
    if("CmpValidacion--"+campoFinal == Panel){  
        if(Mp[0]==1){
            PanelMsgForm(Panel, "1", Mp[1]);
        }else{      
            enviaForm(ParametrosAjax);
        }
    }else{
        if(Mp[0]==1){
            PanelMsgForm(Panel, "1", Mp[1]);
        }else{
        }
    }       
}

function FAjaxS(ParametrosAjax,callback){

    Url = ParametrosAjax[0];
    Panel = ParametrosAjax[1];
    Form = ParametrosAjax[2];
    sUrl = ParametrosAjax[3];
    campoFinal = ParametrosAjax[4];
    sDivCon = ParametrosAjax[5];
    sIdCierra = ParametrosAjax[6];
    ParametrosInput = ParametrosAjax[7];
    UrlPrimerBtn = ParametrosAjax[8];
                 
    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("GET", Url, true);
    sAjaxMotor.onreadystatechange = function() {
        
        if (sAjaxMotor.readyState === 4) {
            if (sAjaxMotor.status == 200) {
                    callback(sAjaxMotor,ParametrosAjax,Form);
            }
        }
    }
    sAjaxMotor.send(null);  
}



function addPointEvent(url_redirect) {
    var subforum = _SQS.class("subforum_point");

    _SQS.each(subforum, function (elem, index) {
        var json_data = JSON.parse(elem.getAttribute("data-subforum"));
        addStarPoint(elem);
        initPointEvent(elem, json_data, url_redirect);
    });

    function addStarPoint(elem) {
        var html = "";
        for (var i = 0; i < 5; i++) {
            html += "<div class='star_point'><i class='icon-star'></i></div>";
        }
        elem.innerHTML = html;
    }

    function initPointEvent(elem, data, url) {
        var stars = _SQS.class("star_point", elem);

        setPoint(stars, +data.point, +data.summary_point, data.subforum_cod);
        _SQS.each(stars, function (star, index) {
            star.onclick = function () {
                var auxurl = url + "&subforum_cod=" + data.subforum_cod + "&points=" + (index + 1);

                AjaxGET_TEXT(auxurl, function (responseText) {
                    var json_data = JSON.parse(responseText),
                            point = +json_data.point,
                            summary_point = +json_data.summary_point;

                    setPoint(stars, point, summary_point, data.subforum_cod);
                });
            };
        });
    }

    function setPoint(stars, point, summary_point, id_prefix) {
        _SQS.each(stars, function (aux_star, aux_index) {
            if (aux_index < point) {
                _SQE.addClass(aux_star, "point");
            } else {
                _SQE.removeClass(aux_star, "point");
            }
        });

        _SQS.id("point_" + id_prefix).innerHTML = "<i class='icon-star'></i> " + summary_point + " estrella(s)";
    }
}

function addTimerForum(id_string, url_print, url_add_point) {
    var func_success = function () {
        AjaxGET_TEXT(url_print, function (responseText) {
            var json_data = JSON.parse(responseText),
                    html = json_data.html,
                    num = json_data.num_competitors;

            _SQS.id(id_string).innerHTML = html;
            _SQS.id("num_competitor").innerHTML = num + " participante(s)";
            addPointEvent(url_add_point);
        });
    };

    func_success();

    return func_success;
}

var storage = {
    get: function (name_item) {
        return storage.item[name_item];
    },
    set: function (name_item, value_item) {
        storage.item[name_item] = value_item;
    },
    unset: function (name_item) {
        return delete storage.item[name_item];
    },
    item: {}
};
/*
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main comment Object
 */
var comment = {
    /*
    *   Main addTimerComment Function
    *   @param  {String} id_block_comment
    *   @param  {String} id_form
    *   @param  {String} aux
    *   @return {Function}
    */
    addTimerComment: function(id_block_comment, id_form, aux){
        //id_block_comment: id del DOM que contendra los DOM's de los comentarios recuperados por Ajax
        //id_form: id del DOM del formulario
        //aux: Es una URI Auxiliar para realizar el request por AJAX
        var func_success = function (first_request) {
            //first_request : Es el parametro para que define que el primer request por ajax envie un parametro adicional [first_request]
            var form = _SQS.id(id_form),
            //Comment Code list: Lista de codigos de comentario que se recupera a la hora de imprimir el formulario. Ejemplo "1|2|3|4|5|6"
            comment_code_list = form.getAttribute("data-ccl"),
            //URI Print Comment: URI que sirve para que AJAX recupere datos de los comentarios
            url_print_comment = form.getAttribute("data-upc"),
            //URI Print Subcomment: URI que sirve para que AJAX recupere datos de los subcomentarios
            url_print_subcomment = form.getAttribute("data-ups");
            
            //Si no existe una URI auxiliar usar la URI por defecto más la lista de codigos de comentario
            var aux_url = ((aux)? aux : url_print_comment) + "&comment_code_list=" + comment_code_list;
            
            //Si el parametro [first_request] existe añadir un parametro a la URI
            if (first_request) {
                aux_url += "&first_request=true";
            }
            
            //Realizando la petición Ajax
            AjaxGET_TEXT(aux_url, function (responseText) {
                //Renderizando la respuesta y reutilizando las variables [id_block_comment] [id_form]
                comment.render(responseText, id_block_comment, id_form);
            });
        };

        //Mandando una primera solicitud
        func_success(true);
        
        //Retornando la funcion que realiza la solicitud
        return func_success;
    },
    render: function(responseText, id_block_comment, id_form){
        var form = _SQS.id(id_form),
        comment_code_list = form.getAttribute("data-ccl"),
        url_print_comment = form.getAttribute("data-upc"),
        url_print_subcomment = form.getAttribute("data-ups");

        //Convirtiendo la respuesta en objeto JSON
        var json_data = JSON.parse(responseText),
        Mxcomment_cod = json_data.Mxcomment_cod, //["1","2","3"]
        html = json_data.html; //"<div class="comment">...</div>"

        if (Mxcomment_cod.length) {
            //Si comment_code_list no es vacio lo convierto en en un array separado por |
            //Si es vacio es por que no hay comentarios entonces paso un array vacio []
            var mx_cur_code = (comment_code_list.trim()) ? comment_code_list.split("|") : [],
            //Combino el array [mx_cur_code] con el array recuperado por Ajax [Mxcomment_cod]
            mx_merge_code = mx_cur_code.concat(Mxcomment_cod);
            
            //El array combinado lo establezco nuevamente como atributo para el DOM del formulario
            form.setAttribute("data-ccl", mx_merge_code.join("|"));

            //Creo un DOM auxiliar para almacenar los DOM's de los comentarios
            var node_aux = _SQE.mk("div");
            node_aux.innerHTML = html;
            
            //Recupero en un array los comentarios separados por la clase parent_comment
            var new_comments = node_aux.getElementsByClassName("parent_comment"),
            num_comments = new_comments.length;
            
            for (var i = 0; i < num_comments; i++) {
                //Añado el nodo hacia el bloque de comentarios
                _SQS.id(id_block_comment).appendChild(new_comments[0]);

                //Si existe un [url_print_subcomment] creo una URL auxiliar para cada contenedor de subcomentarios de cada comentario
                if (url_print_subcomment) {
                    var aux_url_subcomment = url_print_subcomment + "&comment_cod=" + Mxcomment_cod[i];

                    storage.set("f_subcomment_" + Mxcomment_cod[i], comment.subcomment_function("subcomment_content_" + Mxcomment_cod[i], "form_subcomment_" + Mxcomment_cod[i], aux_url_subcomment));
                    storage.set("interval_" + Mxcomment_cod[i], setInterval(storage.get("f_subcomment_" + Mxcomment_cod[i]), 5000));
                }
            }
        }
    },
    sendComment: function(url, id_form, id_block_comment,down){
        var form = _SQS.id(id_form),
        comment_code_list = form.getAttribute("data-ccl"),
        url_print_comment = form.getAttribute("data-upc"),
        url_print_subcomment = form.getAttribute("data-ups");

        var aux_url = url + "&first_request=true&comment_code_list=" + comment_code_list;
        sendForm(aux_url, id_form, function(responseText){
            comment.render(responseText, id_block_comment, id_form);
            if(down){
                var objDiv = document.getElementById(down);
                objDiv.scrollTop = objDiv.scrollHeight;
            }
        });

    },
    subcomment_function: function(subcomment_content_id, id_form, aux){
        return comment.addTimerComment(subcomment_content_id, id_form, aux);
    }
};

function DonwScroll(id)
{
    var objDiv = document.getElementById(id);
    objDiv.scrollTop = objDiv.scrollHeight;
}


/* Funciones de Consulta Ajax */
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
                    clog("Error: La pagina no existe");
                    break;
                case 500:
                    clog("ERROR: El serbidor no responde");
                    break;
                default:
                    clog("ERROR: Error desconocido");
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
    if (block_id.innerHTML.trim() === "") {
        block_id.innerHTML = "Cargando...";
    }
    if (block_id) {
        requestAjax(url, function (Ajax) {
            block_id.innerHTML = Ajax.responseText;
            if (callback) {
                callback(Ajax);
            }
        });
    } else {
        clog("El elemento " + block_id + " no existe");
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
    search: function (tagName, Attributes, parentNode) {
        if (!parentNode) {
            parentNode = document;
        }
        var MxNodes = _SQS.tag(tagName, parentNode);
        var MxrespNodes = [];
        for (var i = 0; i < MxNodes.length; i++) {
            for (var attr in Attributes) {
                var rgx = new RegExp(Attributes[attr]);
                if (MxNodes[i][attr].match(rgx)) {
                    MxrespNodes.push(MxNodes[i]);
                }
            }
        }
        if (MxrespNodes.length > 0) {
            clog("Elementos encontrados: " + MxrespNodes.length);
            return {exist: true, respNodes: MxrespNodes};
        } else {
            clog("No se encontraron elementos");
            return {exist: false};
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



function MonitorComentarios(url){
// alert(url);
    ValorAjaxG = MonitoreaComentariosAjax(url,ControlaResulComentario);     
}


function MonitoreaComentariosAjax(url,callback){
    var ultimaFecha = document.getElementById("ultimaFecha");
    var sAjaxMotor = false;
    sAjaxMotor = crearXMLHttpRequest();
    sAjaxMotor.open("GET", url+"&ultimaFecha="+ultimaFecha.value.replace(" ", "S")+"", true);
    sAjaxMotor.onreadystatechange = function() {
        
        if (sAjaxMotor.readyState === 4) {
            if (sAjaxMotor.status == 200) {
                    callback(sAjaxMotor,url);
            }
        }
    }
    sAjaxMotor.send(null);  
}


function ControlaResulComentario(e,url){
    var detalles = document.getElementById("msgAlerta");
    detalles.innerHTML = e.responseText;

}

//Tutotiales
function openTutorial(URI, node){
    //URI: document path to show
    var html = '';
    $.ajax({
        url: '/system/desktop.php?ad=tutorial',
        async:false,
        success:function(data){
            html=data;
        }
    })
    var $content = $("<div>")
                    .append( $("<div>").attr({"class":"ad"})
                    .append(html)
    ); 
    
    var popup = new $.Popup({
        content: $content
    });
    
    popup.open();
}

function openTutorial2(URI, node){
    //URI: document path to show
    var html = '';
    $.ajax({
        url: '/system/desktop.php?ad=tutorialVisor&urltutorial='+URI,
        async:false,
        success:function(data){
            html=data;
        }
    })
    var $content = $("<div>")
        .append( $("<div>").attr({"class":"ad"})
            .append(html)
    );

    var popup = new $.Popup({
        content: $content
    });

    popup.open();
}
//Edit course
var $popupEC = null;

function openPopupURI(uri, settings){
    //settings are optional
    $popupEC = new $.Popup(settings);
    $popupEC.open(uri);
}

function tooltip(node){
    var $node = $(node),
    data = $node.attr("data-tooltip");
    
    if(!data){ return; }
    
    var n = {
        tooltip     : $("<div>").attr("class", "tooltip-room"),
            arrow   : $("<div>").attr("class", "arrow"),
            content : $("<div>").attr("class", "content").text(data)
    };
    
    //Render node
    n.tooltip
    .append(n.arrow)
    .append(n.content);
    
    n.tooltip.appendTo("body");
    
    $node.hover(function(){
        var xPosition = $node.offset().left,
        yPosition = $node.offset().top + $node.innerHeight() + 15,
        tooltipWidth = n.tooltip.width();
        
        n.arrow
        .css("left", tooltipWidth / 2);
        
        n.tooltip
        .css({
            left    : xPosition - (tooltipWidth / 2),
            top     : yPosition
        })
        .show();
    }, function(){
        n.tooltip.hide();
    });
}

window.youtube_api_init = false;

function VideoPlayer(settings){
    var vp = this,
    defaults = {
        //params
        id          : null,
        videoId     : null
    };
    
    vp.d = $.extend(true, {}, defaults, settings);

    if(!$('#' + vp.d.id).length){
        console.log('No exists container id');
        return;
    }else if(!vp.d.videoId){
        console.log('No exists videoId');
        return;
    }
    
    vp.node = {
        videoplayer             : $('<div>').attr({class:'videoplayer', oncontextmenu: 'return false'}),
            container           : $('<div>').attr({class:'videoplayer-container'}),
                iframe          : $('<div>').attr({id: vp.d.id + '_iframe'}),
            shadow              : $('<div>').attr({class:'videoplayer-shadow'}),
            controls            : $('<div>').attr({class:'videoplayer-controls'}),
                range           : $('<div>').attr({class:'videoplayer-controls-range'}),
                buttons         : $('<div>').attr({class:'videoplayer-controls-buttons'}),
                    btnPlay     : $('<div>').attr({class:'videoplayer-controls-button'}).html("<i class='icon-pause'></i>"),
                    btnVolume   : $('<div>').attr({class:'videoplayer-controls-button'}).html("<i class='icon-volume-up'></i><div class='volume-range'><div id='volume-range'></div></div>"),
                    btnTime     : $('<div>').attr({class:'videoplayer-controls-button'}).text('--'),
                    btnCog      : $('<div>').attr({class:'videoplayer-controls-button'}).html("<i class='icon-cog'><div class='volume-range' style='width:auto;'><select id='cog-select'></select></div></i>"),
                    btnFull     : $('<div>').attr({class:'videoplayer-controls-button'}).html("<i class='icon-fullscreen'></i>")
    };
    
    //render
    vp.node.videoplayer
    .append(vp.node.container)
    .append(vp.node.shadow)
    .append(vp.node.controls);
    vp.node.container
    .append(vp.node.iframe);
    vp.node.controls
    .append(vp.node.range)
    .append(vp.node.buttons);
    vp.node.buttons
    .append(vp.node.btnPlay)
    .append(vp.node.btnVolume)
    .append(vp.node.btnTime)
    .append(vp.node.btnCog)
    .append(vp.node.btnFull);
    
    //append to container
    $('#' + vp.d.id).append(vp.node.videoplayer);
    
    vp.node.videoplayer.hover(function(){
        vp.node.controls.fadeIn();
    },
    function(){
        vp.node.controls.fadeOut();
    });
    
    var videoPlayerTimeOutControl = null;
    
    vp.node.videoplayer.on('mousemove', function(){
        vp.node.controls.fadeIn();
        clearTimeout(videoPlayerTimeOutControl);
        
        videoPlayerTimeOutControl = setTimeout(function(){
            vp.node.controls.fadeOut();
        }, 3000);
    });
    
    var player,
    done = false,
    paused = false,
    maximize = false,
    convertToTime = function(seconds){
        seconds = parseInt(seconds);
        
        var curDate = new Date();
        curDate.setHours(0);
        curDate.setMinutes(0);
        curDate.setSeconds(0);
        
        curDate.setSeconds(curDate.getSeconds() + seconds);
        
        return (curDate.getHours()? curDate.getHours() + ':' : '') + curDate.getMinutes() + ':' + curDate.getSeconds();
    };

    window.onYouTubeIframeAPIReady = function() {
        window.youtube_api_init = true;
        
        player = new YT.Player(vp.d.id + '_iframe', {
            height: '390',
            width: '640',
            videoId: vp.d.videoId,
            playerVars :{
                rel: 0,
                modestbranding: 1,
                controls: 0,
                showinfo: 0
            },
            events: {
                onReady: function(event) {
                    event.target.playVideo();
                    player.setVolume(100);
                    player.setPlaybackQuality('auto');
                    
                    var clickPauseHandle = function(){
                        if(paused){
                            paused = false;
                            player.playVideo();
                            
                            vp.node.btnPlay.html("<i class='icon-pause'></i>");
                        }else{
                            paused = true;
                            player.pauseVideo();
                            
                            vp.node.btnPlay.html("<i class='icon-play'></i>");
                        }
                    };
                    
                    //set event buttons
                    vp.node.shadow.click(clickPauseHandle);
                    vp.node.btnPlay.click(clickPauseHandle);
                    vp.node.btnFull.click(function(){
                        var node = vp.node.videoplayer.get(0);
                        
                        if(maximize){
                            if(document.exitFullscreen){
                                document.exitFullscreen();
                            }else if(document.msExitFullscreen){
                                document.msExitFullscreen();
                            }else if(document.mozCancelFullScreen){
                                document.mozCancelFullScreen();
                            }else if(document.webkitExitFullscreen){
                                document.webkitExitFullscreen();
                            }
                        }else{
                            if(node.requestFullscreen){
                                node.requestFullscreen();
                            }else if(node.msRequestFullscreen){
                                node.msRequestFullscreen();
                            }else if(node.mozRequestFullScreen){
                                node.mozRequestFullScreen();
                            }else if(node.webkitRequestFullScreen){
                                node.webkitRequestFullScreen();
                            }
                        }
                    });
                    
                    $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange', function(e){
                        maximize = document.fullScreen || document.mozFullScreen || document.webkitIsFullScreen;

                        if(maximize){
                            vp.node.btnFull.html("<i class='icon-resize-small'></i>");
                        }else{
                            vp.node.btnFull.html("<i class='icon-fullscreen'></i>");
                        }
                    });
                                        
                    vp.node.range.slider({
                        change: function(event, ui){
                            if(event.originalEvent){
                                var duration = player.getDuration(),
                                time = ui.value / 100 * duration;
                                
                                player.seekTo(time, true);
                            }
                        }
                    });
                    
                    $("#volume-range").slider({
                        change: function(event, ui){
                            player.setVolume(ui.value);
                        },
                        value: 100
                    });
                    
                    setInterval(function(){
                        var time = player.getCurrentTime(),
                        duration = player.getDuration(),
                        percent = time / duration * 100;
                        
                        vp.node.range.slider('value', percent);
                        
                        vp.node.btnTime.text(convertToTime(duration) + '/' + convertToTime(time));
                    }, 1000);
                },
                onStateChange: function(event){
                    if (event.data == YT.PlayerState.PLAYING && !done) {
                        done = true;
                        console.log('play!');
                        
                        var qualityNames = {
                            tiny    : "144p",
                            small   : "240p",
                            medium  : "360p",
                            large   : "480p"
                        };
                        
                        var qualities = player.getAvailableQualityLevels();
                        qualities.forEach(function(quality){
                            $("#cog-select").prepend(
                                $("<option>").attr({"value": quality}).text((qualityNames[quality])? qualityNames[quality] : quality )
                            );
                        });
                        
                        $("#cog-select").on('change', function(){
                            var quality = $(this).val();
                            
                            player.setPlaybackQuality(quality);
                        });
                    }
                    
                    switch(event.data){
                        case YT.PlayerState.PLAYING:
                            paused = false;
                            
                            vp.node.btnPlay.html("<i class='icon-pause'></i>");
                            break;
                        case YT.PlayerState.ENDED:
                            paused = true;
                            
                            vp.node.btnPlay.html("<i class='icon-play'></i>");
                            break;
                    }
                }
            }
        });
    };
    
    //import youtube api
    $.getScript("https://www.youtube.com/iframe_api", function(){
        if(window.youtube_api_init){
            window.onYouTubeIframeAPIReady();
        }
    });
}

///////////////  Tabla temporal  DC
var data = [];

function dataview(id){
    var dv     = document.getElementById(id).value;
    var Existe = 0;
    for(i=0;i<=data.length;i++){
        if(dv == data[i]){
            Existe = 1;
        }
    }
    if(Existe == 0){
        data.push(dv);
    }else{
        console.log('Existe');
    }
    var html = grilla();
    document.getElementById("gridview").innerHTML = html;
    dv = "";
    console.log(data);
}

function quitar(id){
    delete data[id];
    var html = grilla();
    document.getElementById("gridview").innerHTML = html;

}

function grilla(){
    var html = "";
    html += "<form name=ListUser method=post id=ListUser>";
    html += "<table border=0px cellspacing=0 cellpadding=0 width=100% >";
    html += "<th>Nro</th>";
    html += "<th><a href=# onclick=orGrillaxNombre();> Correo</a></th>";
    html += "<th>Quitar</th>";
    var nro = 1;
    for (i=0;i<=data.length -1;i++){
        if(data[i]){
            html += "<tr><td>"+ nro +"</td><td><input type='hidden' value="+ data[i] +"  name='user[]'> "+ data[i] +"</td><td> <a   href=# onclick=quitar("+ i +");  > <div style='position:relative;'> <div class='Btn-reporte'><div class='botIcRep'><i class='icon-remove''></i>&nbsp;Quitar</div></div>  </div></a></td></tr>";
            nro++;
        }
    }
        html += "</table>";
    html += "</form>";
    return html;
}

function orGrillaxNombre(){
    data.sort();
    var html = grilla();
    document.getElementById("gridview").innerHTML = html;
    //return html;
}
function Limpiar(){

    for(i=0;i<=data.length;i++){
        delete data[i];
    }
    var html = grilla();
    if(document.getElementById("gridview")){
        document.getElementById("gridview").innerHTML = html;
    }

}

var fc_bubble_template = '\
<div class="fc-bubble">\
    <div class="fc-bubble-title">title</div>\
        <div class="fc-bubble-description">\
          description\
        </div>\
    <div class="fc-bubble-arrow"></div>\
</div>';
                                                                                                                                                                                                                                                        
                                                                                                                                                                                                                                   
function cambiar(){
    $("#cambiar_1").removeClass("BotonCatalogo1");
    $("#cambiar_1").addClass("BotonCatalogo3");
    $("#cambiar_2").removeClass("BotonCatalogo3");
    $("#cambiar_2").addClass("BotonCatalogo1");
} 
function cambiar2(){
    $("#cambiar_2").removeClass("BotonCatalogo1");
    $("#cambiar_2").addClass("BotonCatalogo3");
    $("#cambiar_1").removeClass("BotonCatalogo3");
    $("#cambiar_1").addClass("BotonCatalogo1");
}

function courseraSearcher(){
    var $searchForm = $("#coursera-search-form"),
    $resultContainer = $("#coursera-result-container"),
    $selectCoursesContainer = $("#coursera-selected-courses-container"),
    $selectCoursesForm = $("#coursera-selected-courses-form");
    
    var selectedCourseIds = {};

    $selectCoursesForm.find("button").hide();
    
    $selectCoursesForm.on("submit", function(e){
    	e.preventDefault();
        
        sendForm("/system/_vistas/gad_coursera.php?Coursera=sample", this, function(responseText){
            console.log(responseText);
            //do something
        });
    });
    
    $searchForm.on("submit", function(e){
    	e.preventDefault();
    	
        sendForm("/system/_vistas/gad_coursera.php?Coursera=search", this, function(responseText){
            $resultContainer.html(responseText);
            render();
        });
    });
    
    function paintSelectedCourses(){
        if(!$.isEmptyObject(selectedCourseIds)){
            $selectCoursesForm.find("button").show();
            
            $selectCoursesContainer.empty();
            var courseIds = [];
            
            $.each(selectedCourseIds, function(courseId){
                var $resultCourseraItem = $resultContainer.find(".coursera-item[data-id=" + courseId + "]").addClass("selected");
                $resultCourseraItem.find("button[data-item-to-select]").addClass("selected").html("<i class='icon-check'></i> Seleccionado");
                
                courseIds.push(courseId);
                
                $selectCoursesContainer.append(
                    $("<span>").attr({"class":"coursera-item-selected", "data-id": courseId}).text($resultCourseraItem.find(".coursera-item-coursename").text())
                );
            });
            
            $selectCoursesForm.find("input[name=selected-course-ids]").val(courseIds.join(","));
        }else{
            $selectCoursesForm.find("button").hide();
        }
    }
    
    function render(){
        paintSelectedCourses();
        
        $resultContainer.find("button[data-id-to-view]").click(function(){
            var courseId = $(this).attr("data-id-to-view");
            
            $.post("/system/_vistas/gad_coursera.php?Coursera=view", {courseId: courseId}, function(response){
                var $contentTray = $("<div>").html(response);
                
                var popup = new $.Popup({
                    content: $contentTray
                });
                
                popup.open();
            });
        });
        $resultContainer.find("button[data-item-to-select]").click(function(){
            var courseId = $(this).attr("data-id");
            
            if($(this).hasClass("selected")){
                $resultContainer.find(".coursera-item[data-id=" + courseId + "]").removeClass("selected");
                $selectCoursesContainer.find(".coursera-item-selected[data-id=" + courseId + "]").remove();
                $(this)
                .removeClass("selected")
                .html("<i class='icon-plus'></i> Seleccionar");
                
                delete selectedCourseIds[courseId];
            }else{
                selectedCourseIds[courseId] = true;
            }
            
            paintSelectedCourses();
        });
        
        $resultContainer.find("div[data-nav-option] button").click(function(){
            $resultContainer.html("Cargando...");
            
            sendForm("/system/_vistas/gad_coursera.php?Coursera=search&start=" + $(this).attr("data-start"), $searchForm.get(0), function(responseText){
                $resultContainer.html(responseText);
                render();
            });
        });
    }
}
