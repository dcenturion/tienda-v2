function ImagenTemproral(evt, sId, sPath, sUrl, formid) {

    var insertar_en = document.querySelector("#" + sId + " ul");
    var files = evt.target.files; // FileList object
    //var input = document.getElementById ("files");
    var imagenG = "";
    // Obtenemos la imagen del campo "file".
    for (var i = 0, f; f = files[i]; i++) {
        //if (!f.type.match('image.*')) {
        //continue;
        //}
        insertar_en.innerHTML = "";
        var reader = new FileReader();
        reader.onload = (function (theFile) {
            return function (e) {
                // Insertamos la imagen
                imagenG = e.target.result;
                var arcDiv = document.createElement("li");
                arcDiv.setAttribute('style', 'width:8%;float:left;');
                arcDiv.innerHTML = "<img  src='" + imagenG + "' width='30px' height='30px' />";
                insertar_en.appendChild(arcDiv);

            };
        })(f);

        reader.readAsDataURL(f);

        // var botonEnv = document.createElement("li");
        // botonEnv.setAttribute('style',sId+"-p-li");
        // botonEnv.setAttribute('id','width:10%;float:left;');		
        // botonEnv.innerHTML = "<a href=''>Subir</a>";
        // insertar_en.appendChild(botonEnv);

        var archivo = document.createElement("li");
        archivo.setAttribute('style', 'width:87%;float:left;');
        //archivo.innerHTML = f.name + " - (<b>" + f.type + "</b>) ->" + f.size;
        var nSizeArc = (f.size / 1024);
        var nSize = nSizeArc.toFixed(2);
        archivo.innerHTML = f.name + ", Peso : " + nSize + " kb";
        insertar_en.appendChild(archivo);
    }

    subeImagen(sUrl, formid, sId + "-MS", sPath, sId);

}

function recorrerTabla(tableReg, id) {
    var table = document.getElementById(tableReg + '-T');
    for (var i = 1; i < table.rows.length; i++)
    {
        codFila = table.rows[i].getAttribute('id');
        linea = document.getElementById(codFila);
        if (id != codFila) {
            linea.setAttribute('style', false);
        }
    }
}

function mostrarReloj(idinput, idgadget) {
    var $input = $("#" + idinput),
    $gadget = $("#" + idgadget);
    
    $gadget.show();
    
    if($gadget.html()){
        return;
    }
    
    $(document).click(function(e){
        if(!$gadget.parent().find($(e.target)).length){
            $gadget.hide();
        }
    });
    
    var htmlString = '\
    <div class="date-selects">\
        <select class="hour-select"></select>\
        <select class="minute-select"></select>\
        <select class="meridiem-select"></select>\
    </div>\
    <div class="date-buttons">\
        <input class="success-button" type="button" value="Aceptar">\
        <input class="close-button" type="button" value="Cancelar">\
    </div>\
    ';
    
    $gadget.html(htmlString);
    
    var $hourSelect = $gadget.find(".hour-select"),
    $minuteSelect = $gadget.find(".minute-select"),
    $meridiemSelect = $gadget.find(".meridiem-select");
    
    function setTimeValue(hour, minute){
        var meridiem;
        
        if(hour === 0){
            meridiem    = "am";
            hour        = 12;
        }else{
            meridiem    = hour >= 12 ? "pm" : "am";
            hour        = hour > 12 ? hour - 12 : hour;
        }
        
        $hourSelect.val(formatHM(hour));
        $minuteSelect.val(formatHM(minute));
        $meridiemSelect.val(meridiem);
    }
    
    var curDate = new Date(),
    hours = ("01_02_03_04_05_06_07_08_09_10_11_12").split("_"),
    minutes = [],
    meridiems = ("am_pm").split("_");
    
    for(var i = 0; i < 60; i++){
        minutes.push(i);
    }
    
    hours.forEach(function(hourString){
        $hourSelect.append($("<option>").attr("value", hourString).text(hourString));
    });
    
    minutes.forEach(function(minuteString){
        $minuteSelect.append($("<option>").attr("value", formatHM(minuteString)).text(formatHM(minuteString)));
    });
    
    meridiems.forEach(function(meridiemString){
        $meridiemSelect.append($("<option>").attr("value", meridiemString).text(meridiemString));
    });
    
    //events
    $gadget.find(".success-button").click(function(){
        var hour    = +$hourSelect.val(),
        minute      = $minuteSelect.val(),
        meridiem    = $meridiemSelect.val();
        
        if(meridiem === "am" && hour === 12){
            hour = 0;
        }else if(meridiem === "pm" && hour < 12){
            hour += 12;
        }
        
        $input.val(formatHM(hour) + ":" + minute + ":00");
        
        $gadget.hide();
    });
    
    $gadget.find(".close-button").click(function(){
        $gadget.hide();
    });
    
    //if exists dateString in the input
    var regexTime = /^([0-2][0-9]):([0-5][0-9]):([0-5][0-9])$/;
    
    if($input.val().trim() && $input.val().match(regexTime)){
        var values = $input.val().split(':');
        
        values = values.map(function(elem){
            return +elem;
        });
        
        setTimeValue(+values[0], +values[1]);
    }else{
        setTimeValue(curDate.getHours(), curDate.getMinutes());
    }
}

function formatHM(horaminstr) {
    if (+horaminstr < 10) {
        return "0" + +horaminstr;
    } else {
        return +horaminstr;
    }
}

function SelectCurrentOption(ElementSelect) {
    var indexObject = ElementSelect.selectedIndex;
    var MxOptions = ElementSelect.childNodes;
    var ValueSelected = MxOptions[indexObject].value;
    return ValueSelected;
}
function gadgetDate(idinput, idgadget) {
    var $input = $("#" + idinput),
    $gadget = $("#" + idgadget);
    
    $gadget.show();
    
    if($gadget.html()){
        return;
    }
    
    $(document).click(function(e){
        if(!$gadget.parent().find($(e.target)).length){
            $gadget.hide();
        }
    });
    
    var htmlString = '\
    <div class="date-selects">\
        <select class="year-select"></select>\
        <select class="month-select"></select>\
        <select class="day-select"></select>\
    </div>\
    <div class="date-buttons">\
        <input class="success-button" type="button" value="Aceptar">\
        <input class="close-button" type="button" value="Cancelar">\
    </div>\
    ';
    
    $gadget.html(htmlString);
    
    var $yearSelect = $gadget.find(".year-select"),
    $monthSelect = $gadget.find(".month-select"),
    $daySelect = $gadget.find(".day-select");
    
    function isleapYear(year) {
        if (year % 4 === 0 && (year % 100 !== 0) || (year % 400 === 0)) {
            return true;
        } else {
            return false;
        }
    }
    
    function renderDaySelect(){
        $daySelect.empty();
        
        if(isleapYear($yearSelect.val())){ 
            numberOfDayForMonth[1] = 29;
        };
        
        for(var i = 1; i <= numberOfDayForMonth[$monthSelect.val()]; i++){
            var textDay = i < 10 ? "0" + i : i;
            
            $daySelect.append($("<option>").attr("value", i).text(textDay));
        }
    }
    
    var curDate = new Date(),
    monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
    numberOfDayForMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    
    for(var i = curDate.getFullYear() + 20; i >= curDate.getFullYear() - 100; i--){
        $yearSelect.append($("<option>").attr("value", i).text(i));
    }
    
    monthNames.forEach(function(monthName, index){
        $monthSelect.append($("<option>").attr("value", index).text(monthName));
    });
    
    //events
    $yearSelect.change(function(){
        if($monthSelect.val() === "1"){
            renderDaySelect();
        }
    });
    
    $monthSelect.change(function(){
        renderDaySelect();
    });
    
    $gadget.find(".success-button").click(function(){
        var dayValue = +$daySelect.val(),
        textDay = dayValue < 10 ? "0" + dayValue : dayValue,
        monthValue = +$monthSelect.val() + 1,
        textMonth = monthValue < 10 ? "0" + monthValue : monthValue;
        
        $input.val($yearSelect.val() + "-" + textMonth + "-" + textDay);
        $gadget.hide();
    });
    
    $gadget.find(".close-button").click(function(){
        $gadget.hide();
    });
    
    //if exists dateString in the input
    var regexDate = /^(\d{4})-(0[1-9]|1[0-2])-([0-3][0-9])$/;
    
    if($input.val().trim() && $input.val().match(regexDate)){
        var values = $input.val().split('-');
        
        values = values.map(function(elem){
            return +elem;
        });
        
        $yearSelect.val(values[0]);
        $monthSelect.val(values[1] - 1);
        renderDaySelect();
        $daySelect.val(values[2]);
    }else{
        $yearSelect.val(curDate.getFullYear());
        $monthSelect.val(curDate.getMonth());
        renderDaySelect();
        $daySelect.val(curDate.getDate());
    }
}

function checkAll(formId, check) {
    var Formulario = document.getElementById(formId);
    for (var i in Formulario.elements) {

        if (Formulario.elements[i].type === 'checkbox') {
            if (check.checked) {
                Formulario.elements[i].checked = true;
            } else {
                Formulario.elements[i].checked = false;
            }
        }
    }
}

function include(file, callback) {
    if (typeof callback !== "function") {
        callback = function () {
        };
    }
    if (typeof file !== "string") {
        file = "";
    }
    var head = document.getElementsByTagName("head")[0];
    var script = document.createElement('script');
    script.src = file;
    script.type = 'text/javascript';
    //real browsers
    script.onload = callback;
    //Internet explorer
    script.onreadystatechange = function () {
        if (this.readyState === 'complete') {
            callback();
        }
    };
    head.appendChild(script);
}

// BEGIN: Recurso de Evaluación
function showPanel(RecAcadem) {

    muestra_oculta('body-recurso', 'block');
    muestra_oculta('panelItem' + RecAcadem, 'none');
    muestra_oculta('item' + RecAcadem, 'none');
    muestra_oculta('btn-iniciar-re', 'none');
    muestra_oculta('btn-terminar-re', 'block');
    var limit = document.getElementById("liveclock").innerHTML
    var parselimit = limit.split(":")
    parselimit = parselimit[0] * 3600 + parselimit[1] * 60 + parselimit[2] * 1
    begintimer(parselimit);
    document.getElementById("mi-estado").innerHTML = 'En Proceso';
    window.onbeforeunload = confirmExit;
}

function endExamen() {
    if (confirm('¿Desea concluir su examen? Una vez aceptado, ya no retomarlo')) {
        document.getElementById("send_recurso").click()
    }
}

function muestra_oculta(id, estado) {
    var el = document.getElementById(id).style.display = estado;
}

function confirmExit()
{
    return "Usted inició una evalución. Se recomienda que termine este exámen, caso contrario se anulará dicho examen y no tendra nota... ¡Si Ud. ya realizó su evaluación omita este mensaje!";
}

function begintimer(parselimit) {
    if (!document.images)
        return
    if (parselimit == 1) {
        document.getElementById("liveclock").innerHTML = '00:00:00'
        alert('Su tiempo concluyo: su Nota sera dada automáticamente');
        document.getElementById('send_recurso').click();
    }
    else {
        parselimit -= 1
        curhor = Math.floor(parselimit / 3600)
        curmin = parselimit % 3600
        curmin = Math.floor(curmin / 60)
        cursec = parselimit % 60
        if (curmin != 0)
            curtime = curhor + ":" + curmin + ":" + cursec + " "
        else
            curtime = curhor + ":00:" + cursec + " "
        document.getElementById("liveclock").innerHTML = curtime
        window.status = curtime
        // setTimeout("begintimer("+parselimit+")",1000)
    }
}

function downhomework() {
    muestra_oculta('upload-homework', 'block');
}

function enviaForm2(url, Forms, Panel, N) {
    var times = document.getElementById("liveclock").innerHTML
    if (times != '00:00:00') {
        if (confirm('Esta seguro de enviar este examen?')) {
            //alert(url+'---'+Forms+'---'+Panel);
            enviaForm(url, Forms, Panel, N);
        }
    } else {
        enviaForm(url, Forms, Panel, N);
    }
}
// FIN: Recurso de Evaluación

function shovListVideo() {
    var elements = document.getElementsByClassName('hs-sc');
    //elements.style.display='none';  
    muestra_oculta('hs-sc', 'block');
    muestra_oculta('hs-sc-an', 'none');
    muestra_oculta('hs-sc-per', 'none');
}
function shovListAnuncio() {
    var elements = document.getElementsByClassName('hs-sc');
    //elements.style.display='none';  
    muestra_oculta('hs-sc-an', 'block');
    muestra_oculta('hs-sc', 'none');
    muestra_oculta('hs-sc-per', 'none');
}
function cajaPersona() {
    var elements = document.getElementsByClassName('hs-sc');
    //elements.style.display='none';  
    muestra_oculta('hs-sc-per', 'block');
    muestra_oculta('hs-sc', 'none');
    muestra_oculta('hs-sc-an', 'none');
}
function escritorio() {
    muestra_oculta('hs-sc-per', 'none');
    muestra_oculta('hs-sc', 'none');
    muestra_oculta('hs-sc-an', 'none');
    closeIntroProg()
}


function introProg(codigo) {

    muestra_oculta('hs-sc', 'none');
    var m_int = document.getElementById('m-int');

    var cant_int = document.getElementById('m-int').innerHTML;

    if (cant_int != '' && cant_int != 0) {
        if (cant_int == 1) {
            m_int.style.display = 'none';
        }
        cant_int = cant_int - 1;
        document.getElementById('m-int').innerHTML = cant_int;
    }

    var caja = document.getElementById('cuerpo');
    caja.style.background = '#000';
    caja.style.opacity = '0.8';
    muestra_oculta('introduccion-programa', 'block');
    muestra_oculta('hs-sc', 'none');

    enviaVista('./miscursos.php?show=si&Codigo=1' + codigo, 'ci-pr-bd', '');

}

function closeIntroProg() {
    muestra_oculta('introduccion-programa', 'none');
    muestra_oculta('fondoTransparente', 'none');
    var caja = document.getElementById('cuerpo');
    caja.style.background = 'none';
    caja.style.opacity = '1';
}

function vermas(codigo) {
    muestra_oculta('vmenos-' + codigo, 'block');
    muestra_oculta('vmas-' + codigo, 'none');
}

function vermenos(codigo) {
    muestra_oculta('vmas-' + codigo, 'block');
    muestra_oculta('vmenos-' + codigo, 'none');
}

function anuncio2(codigo) {
    var estado = document.getElementById('sp-v-' + codigo).innerHTML;
    if (estado == 'NO') {
        cant = document.getElementById('m-msm').innerHTML;
        if (cant != '' && cant != 0) {
            if (cant == 1) {
                muestra_oculta('m-msm', 'none')
            }
            cant = cant - 1;
            document.getElementById('m-msm').innerHTML = cant;
        }
        document.getElementById('sp-v-' + codigo).innerHTML = 'SI'
        document.getElementById('v-' + codigo).style.background = 'white';

        enviaVista('./miscursos.php?anuncio=si&Codigo=' + codigo, 'glob-anuncio', '');
    }
}

function popup(url, ancho, alto) {

    var opciones = "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=" + ancho + ",height=" + alto + ",top=85,left=140";

    window.open(url, '', opciones);
    return;
}

function activateFullscreen(elemento, arrayAtriB, arrayAtriC) {

    var element = document.getElementById(elemento);

    arrayAtriCA = arrayAtriC.split('|');
    arrayAtribA = arrayAtriB.split('|');
    for (var i = 1; i < arrayAtribA.length; i++)
    {
        arrayAS = arrayAtribA[i].split('-');
        element.setAttribute(arrayAS[0], arrayAS[1]);

    }

    var lista = document.querySelectorAll("#" + arrayAtribA[0] + " " + arrayAtriCA[0] + "");

    for (var i = 1; i < arrayAtriCA.length; i++)
    {
        arrayCS = arrayAtriCA[i].split('-');
        lista[0].setAttribute(arrayCS[0], arrayCS[1]);

    }

    var BtnOpen = document.getElementById(elemento + 'BtnOpen');
    BtnOpen.setAttribute('style', 'display:none;');

    var BtnBtnClose = document.getElementById(elemento + 'BtnClose');
    BtnBtnClose.setAttribute('style', 'display:block;');

    if (element.requestFullScreen) {
        element.requestFullScreen();
    } else if (element.mozRequestFullScreen) {
        element.mozRequestFullScreen();
    } else if (element.webkitRequestFullScreen) {
        // element.setAttribute(atributo,valorAtrib);
        element.webkitRequestFullScreen();
    }
}

function LimpiaDiv(IdDiv) {
    document.getElementById(IdDiv).innerHTML = "";
}

function exitFullscreen(elemento, arrayAtriB, arrayAtriC) {
    var element = document.getElementById(elemento);

    arrayAtriCA = arrayAtriC.split('|');
    arrayAtribA = arrayAtriB.split('|');
    for (var i = 1; i < arrayAtribA.length; i++)
    {
        arrayAS = arrayAtribA[i].split('-');
        element.setAttribute(arrayAS[0], arrayAS[1]);

    }

    var lista = document.querySelectorAll("#" + arrayAtribA[0] + " " + arrayAtriCA[0] + "");

    for (var i = 1; i < arrayAtriCA.length; i++)
    {
        arrayCS = arrayAtriCA[i].split('-');
        lista[0].setAttribute(arrayCS[0], arrayCS[1]);

    }

    var BtnOpen = document.getElementById(elemento + 'BtnOpen');
    BtnOpen.setAttribute('style', 'display:block;');

    var BtnBtnClose = document.getElementById(elemento + 'BtnClose');
    BtnBtnClose.setAttribute('style', 'display:none;');

    console.log(document.fullScreenEnabled);
    console.log(document.fullScreenEnabled);
    if (document.cancelFullScreen) {
        document.cancelFullScreen();
    } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
    } else if (document.webkitCancelFullScreen) {
        document.webkitCancelFullScreen();
    }
}


// Events
document.addEventListener("fullscreenchange", function (e) {
    console.log("fullscreenchange event! ", e);
});
document.addEventListener("mozfullscreenchange", function (e) {
    console.log("mozfullscreenchange event! ", e);
});
document.addEventListener("webkitfullscreenchange", function (e) {
    console.log("webkitfullscreenchange event! ", e);
});

//OWL TEXTAREA
function OWLEditor(node, prefix){
    //cb : Controller Buttons
    //te : Text Editor
    var $cb = $("#CTAE_OWL_SUIT_" + prefix),
    $te = $(node);
    
    //Scan image to set form for resize img
    scanImagesToResize($te);
    
    //Void container
    $cb.empty();

    //Remove event
    $te.removeAttr('onfocus');
    
    //Buttons properties
    var b = {
        bold            : { command : "bold", icon : "<i class='icon-bold'></i>", title: "Negrita" },
        italic          : { command : "italic", icon : "<i class='icon-italic'></i>", title: "Cursiva" },
        underline       : { command : "underline", icon : "<i class='icon-underline'></i>", title: "Subrayado" },
        strikethrough   : { command : "strikeThrough", icon : "<i class='icon-strikethrough'></i>", title: "Tachado" },
        forecolor       : { command : "foreColor", icon : "<i class='icon-tint'></i> Fuente", title: "Color de fuente" },
        backcolor       : { command : "backColor", icon : "<i class='icon-tint'></i> Resaltado", title: "Color de resaltado" },
        fontsize        : { command : "fontSize", icon : "<i class='icon-font'></i> Tamaño", title: "Tamaño de fuente" },
        sup             : { command : "superScript", icon : "x<sup style='font-size:0.5em'>2</sup>", title: "Superíndice" },
        sub             : { command : "subScript", icon : "x<sub style='font-size:0.5em'>2</sub>", title: "Subíndice" },
        ul              : { command : "insertUnorderedList", icon : "<i class='icon-list-ul'></i>", title: "Insertar lista sin orden" },
        ol              : { command : "insertOrderedList", icon : "<i class='icon-list-ol'></i>", title: "Insertar lista ordenada" },
        
        left            : { command : "justifyLeft", icon : "<i class='icon-align-left'></i>", title: "Alinear a la izquierda" },
        center          : { command : "justifyCenter", icon : "<i class='icon-align-center'></i>", title: "Alinear al centro" },
        right           : { command : "justifyRight", icon : "<i class='icon-align-right'></i>", title: "Alinear a la derecha" },
        full            : { command : "justifyFull", icon : "<i class='icon-align-justify'></i>", title: "Justificar" },
        
        del             : { command : "delete", icon : "<i class='icon-hdd'></i> Borrador", title: "Borrar" },
        undo            : { command : "undo", icon : "<i class='icon-undo'></i>", title: "Deshacer" },
        redo            : { command : "redo", icon : "<i class='icon-repeat'></i>", title: "Rehacer" },
        
        link            : { command : "CreateLink", icon : "<i class='icon-link'></i>", title: "Insertar link" },
        image           : { command : "insertImage", icon : "<i class='icon-picture'></i>", title: "Insertar Imagen" },
        images          : { command : "insertMultiImage", icon : "<i class='icon-upload'></i> <i class='icon-picture'></i>", title: "Subir Imagen" },
        table           : { command : "insertTable", icon : "<i class='icon-table'></i>", title: "Insertar Tabla" }
    };
    
    //functions
    var getText = function (){
        var selection = window.getSelection();

        if(!selection.isCollapsed){
            return selection.toString();
        }
    },
    isURI = function(URI){
        //Filter URI
        var URIrgx = new RegExp("^(http|https|ftp)\://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(:[a-zA-Z0-9]*)?/?([a-zA-Z0-9\-\._\?\,\'/\\\+&%\$#\=~])*$");
        
        if(URI.match(URIrgx)){
            return true;
        }else{
            return false;
        }
    };
    
    //Create buttons
    $.each(b, function(key, props){
        //Create button
        var $btn = $("<button>").attr({"title":props.title}).html(props.icon);
        
        //Append button to controller
        $cb.append($btn);
        
        var callback = null;
        
        switch(key){
            case "forecolor":
            case "backcolor":
                //Create input color
                var $color = $("<input>")
                .attr({"type":"color"})
                .css({
                    "border": "none",
                    "height": 0,
                    "width": 0
                })
                .insertAfter($btn)
                .on("change", function(){
                    document.execCommand(props.command, false, $(this).val());
                });
                
                callback = function(){
                    $color
                    .focus()
                    .click();
                };
                break;
            case "fontsize":
                //Declare sizes
                var size = {
                    1: "Tamaño 1",
                    2: "Tamaño 2",
                    3: "Tamaño 3",
                    4: "Tamaño 4",
                    5: "Tamaño 5",
                    6: "Tamaño 6",
                    7: "Tamaño 7"
                };

                var $list = $("<div>")
                .attr({"class":"owl-editor-suit"})
                .css({"left": $btn.position().left, "position": "absolute", "z-index":1})
                .hide()
                .insertAfter($btn);

                $.each(size, function(size, text){
                    //Create option
                    var $font = $("<font>").attr({"size": size}).text(text);

                    $("<a>")
                    .attr({"href":"#"})
                    .css({"display":"block"})
                    .append($font)
                    .appendTo($list)
                    .click(function(e){
                        e.preventDefault();
                        
                        document.execCommand(props.command, false, size);
                        
                        $btn.removeAttr("data-showed");
                        $list.hide();
                    });
                });
                
                callback = function(){
                    if($btn.attr("data-showed")){
                        $btn.removeAttr("data-showed");
                        $list.hide();
                    }else{
                        $btn.attr("data-showed", true);
                        $list.show();
                    }
                };
                break;
            case "link":
                callback = function(){
                    var text = getText(),
                    URI = prompt("URL:");
                    
                    if(!URI){
                        return;
                    }else if(!isURI(URI)){
                        alert("La URL proporcinada es errónea");
                        return;
                    }
                    
                    if(text){
                        var $link = $("<a>").attr("href", URI).text(text);
                        
                        document.execCommand("insertHTML", false, $("<div>").append($link).html());
                    } else {
                        document.execCommand(props.command, false, URI);
                    }
                };
                break;
            case "image":
                callback = function(){
                    var URI = prompt("URL de Imagen:");
            
                    if(!URI){
                        return;
                    }else if(!isURI(URI)){
                        alert("La URL proporcinada es errónea");
                        return;
                    }
                    
                    document.execCommand(props.command, false, URI);
                    
                    scanImagesToResize($te);
                };
                break;
            case "images":
                var $uploadController = $("#" + prefix + "_UIT");
                
                //If upload controller no exists hide button
                if(!$uploadController.length){
                    $btn.hide();
                }
                
                callback = function(){
                    if($btn.attr("data-showed")){
                        $btn.removeAttr("data-showed");
                        //hide
                        $uploadController.hide();
                    }else{
                        $btn.attr("data-showed", true);
                        //show
                        $uploadController.show();
                    }
                };
                break;
            case "table":
                callback = function(e){
                    var $table = $("<table>").attr("width", "100%"),
                    row =  + prompt("Filas", 1),
                    column = + prompt("Columnas", 1);
            
                    if(!row && !column){
                        return;
                    }
                    
                    for(var i = 0; i < row; i++){
                        var $row = $("<tr>");

                        for(var j = 0; j < column; j++){
                            var $column = $("<td>").css("width",(100 / column)  + "%");

                            $row.append($column);
                        }

                        $table.append($row);
                    }
                    
                    document.execCommand("insertHTML", false, $("<div>").append($table).html());
                };
                break;
            default:
                callback = function(){
                    document.execCommand(props.command, false, null);
                };
                break;
        }
        
        $btn.click(callback);
        
        //Add aditional function
        $btn.click(function(){ 
            $te.focus();
        });
    });
    
    //SET ON PASTE
    $te.bind("paste", function (e) {
        e.preventDefault();
        var beforeData = $te.html();
        
        // Access the clipboard using the api
        var pastedData = e.originalEvent.clipboardData.getData('text');

        $te.html(beforeData + pastedData);
    });
}

function scanImagesToResize($node) {
    $node.find("img").click(function(e){
        var $img = $(this),
        n = {
            shadow              : $("<div>").attr({"class":"shadow-layer"}),
                form            : $("<div>").attr({"class":"resize-form"}).draggable(),
                    lblWidth    : $("<label>").text("Ancho"),
                    inpWidth    : $("<input>").attr({"type":"number", "step":10}).val($img.width()),
                    lblHeight   : $("<label>").text("Alto"),
                    inpHeight   : $("<input>").attr({"type":"number", "step":10}).val($img.height()),
                    btnAccept   : $("<button>").text("Aceptar")
        };
        
        //Applying styles
        n.form
        .css({
            "top": e.clientY,
            "left": e.clientX
        });
        
        //Render the DOM
        n.shadow
        .append(
            n.form
            .append(n.lblWidth)
            .append(n.inpWidth)
            .append(n.lblHeight)
            .append(n.inpHeight)
            .append(n.btnAccept)
        )
        .appendTo("body");

        //Applying events
        n.inpWidth.on("keyup change input", a);
        n.inpHeight.on("keyup change input", a);

        n.btnAccept.click(function () {
            n.shadow.remove();
        });

        function a() {
            var w = n.inpWidth.val(),
            h = n.inpHeight.val();

            if (w !== 0 && h !== 0) {
                $img.css({
                    "width" : w,
                    "height": h
                });
            }
        }
    });
}

//FUNCION DE REPORTE
function setColor_ReportRow(idReporte, NomClaseFilaAplicar, ClaseBuscar) {
    var table = document.getElementById(idReporte);
    if (table) {
        var tr = table.getElementsByTagName('tr');
        for (var fila = 0; fila < tr.length; fila++) {
            var ArrayClaseBuscada = tr[fila].getElementsByClassName(ClaseBuscar);
            if (ArrayClaseBuscada.length > 0) {
                tr[fila].setAttribute('class', NomClaseFilaAplicar);
            }
        }
    } else {
        console.log("La tabla con ID: " + idReporte + " no existe...");
    }
}
//FUNCION PARA DESHABILITAR checkbox
function handle_checkbox(obj, NameCheck, DisableValue, Check) {
    //cur : Current(Actual)
    var cur_form = obj;
    if (typeof cur_form === "string") {
        cur_form = document.getElementById(cur_form);
    }
    while (cur_form.tagName !== "FORM") {
        cur_form = cur_form.parentNode;
        if (cur_form.tagName === "BODY") {
            console.log("ERROR: No se pudo encontrar el formulario padre...");
            return;
        }
    }
    var elems = cur_form.elements;
    for (var x in elems) {
        if (elems[x].name === NameCheck) {
            if (Check) {
                elems[x].checked = true;
            }
            elems[x].disabled = DisableValue;
        }
    }
}
//FUNCION PARA MOSTRAR DESCRIPCION data-description
function showDataDescripcion(obj) {
    obj.removeAttribute("onmouseover");
    var div = document.createElement("div");
    div.style["position"] = "relative";
    var panelMsg = document.createElement("div");
    panelMsg.setAttribute("class", "PanelMensajeAlerta");
    panelMsg.style["width"] = "auto";
    panelMsg.style["top"] = (obj.offsetHeight + 5) + "px";
    var triangle = document.createElement("div");
    triangle.setAttribute("class", "triangle_PM");
    var title = document.createElement("div");
    title.setAttribute("class", "title_PM");
    title.innerHTML = obj.getAttribute("data-description");
    panelMsg.appendChild(triangle);
    panelMsg.appendChild(title);
    div.appendChild(panelMsg);
    obj.appendChild(div);
    obj.onmouseover = function () {
        this.appendChild(div);
    };
    obj.onmouseout = function () {
        this.removeChild(div);
    };
}

function redireccionar(url)
{
    location.href = url;
}

function dwn_ocl(archivo,paht_base){
    location.href = paht_base+"/articulos/"+archivo;
    // alert("hola");
}

function screenFull(){
     var marioVideo = document.getElementById("resource_pizarra");

		
            if (marioVideo.requestFullscreen) {
                marioVideo.requestFullscreen();
            }
            else if (marioVideo.msRequestFullscreen) {
                marioVideo.msRequestFullscreen();
            }
            else if (marioVideo.mozRequestFullScreen) {
                marioVideo.mozRequestFullScreen();
            }
            else if (marioVideo.webkitRequestFullScreen) {
                marioVideo.webkitRequestFullScreen();
                /*
                    *Kept here for reference: keyboard support in full screen
                    * marioVideo.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
                */
            }
    
}	
/*
 function ver(id)
 {
 location.href = url;
 }
 
 */


/* Class Editor*/
