function Evaluation(settings){
    //Private vars
    var e = this,
    defaults = {
        //Params
        id              : null,
        description     : "Default description",
        course: {
            name    : "Default name course",
            id      : null
        },
        student         : null,
        time            : null,
        timeOfTolerance : null,
        groups          : null,
        trackingData    : null,
        
        optionalButtonId    : null,
        container           : null
    };
    
    var index = null,
    ONPREV = 0,
    ONNEXT = 1,
    timerInterval = null,
    URItoSaveData = "/system/_vistas/gad_cursos_actividades.php";
    
    e.groups = [];

    //Class vars
    e.d = $.extend(true, {}, defaults, settings),
    e.node = e.d.container.html($("#eva-app-template").html()).find("div[class^=eva]").eq(0),
    e.messageNode = e.node.find("span.eva-title-message").eq(0),
    e.toleranceWasApplied = false;
    
    e.move = function(movement){
        var currentIndex = index;
        var indexToSend = currentIndex;
        
        if(movement === ONNEXT){
            var limit = e.d.groups.length - 1;
            
            if(currentIndex === limit){
                return;
            }
            
            currentIndex++;
        }else if(movement === ONPREV){
            if(!currentIndex){
                return;
            }
            
            currentIndex--;
        }
        
        if(e.setIndex(currentIndex)){
            e.sendGroup(indexToSend);
        }
    },
    e.setIndex = function(indexValue){
        if(indexValue >= 0 && indexValue < e.d.groups.length){
            index = indexValue;
            
            if(indexValue === e.d.groups.length - 1){
                e.node.find("div.eva-button").eq(1).hide();
                e.node.find("div.eva-button").eq(2).show();
            }else{
                e.node.find("div.eva-button").eq(1).show();
                e.node.find("div.eva-button").eq(2).hide();
            }
        
            e.node.find("div.eva-group-container").hide();
            e.node.find("div.eva-group-container").eq(indexValue).fadeIn();
            e.node.find("input.eva-paginator-value").val(indexValue + 1);

            var currentTotalQuestions = 0,
            totalQuestions = questionNumber - 1;

            for(var i = 0; i <= indexValue; i++){
                currentTotalQuestions += e.d.groups[i].questions.length;
            }

            e.node.find("div.eva-questions-indicator").text("Pregunta " + currentTotalQuestions + " / " + totalQuestions);
            
            return true;
        }else{
            alert("Por favor ingrese un valor válido...");
            
            return false;
        }
    },
    e.init = function(){
        e.node.find("div.eva-group-container").hide();
        
        e.setIndex(0);
        
        function format(time){
            return ((time < 10)? "0" : "") + time;
        }
        
        timerInterval = setInterval(function(){
            if(e.d.time < 0){
                e.send();
                return;
            }
            
            e.d.time = e.d.time - 1;
            
            var date = new Date();
            date.setHours(0);
            date.setMinutes(0);
            date.setSeconds(0);
            
            date.setSeconds(e.d.time);
            
            var timeString = (date.getHours()? format(date.getHours()) + ":" : "") + format(date.getMinutes()) + ":" + format(date.getSeconds());
            
            e.node.find("span.eva-time-value").text(timeString);
            
            if(!e.d.time){
                if(e.d.timeOfTolerance && !e.toleranceWasApplied){
                    e.node.find("div.eva-time").css("color", "#FF5C5C");
                    
                    e.d.time = e.d.timeOfTolerance;
                    
                    e.toleranceWasApplied = true;
                }else{
                    e.send();
                }
            }
        }, 1000);
    },
    e.send = function(){
        var popup = new $.Popup({
            content         : $("<div>").html("<i class='icon-upload-alt'></i><span id='eva-alert-message'>Finalizando el examen...</span>"),
            modal           : true,
            closeContent    : ""
        });
        
        popup.open();
        
        var evaluationQuestions = [];
        e.groups.forEach(function(group){
            group.questions.forEach(function(question){
                evaluationQuestions.push(question.d.id);
            });
        });

        $.post(URItoSaveData + "?ProcesoActividadesExamen=finish_evaluation", {
            dataEvaluation: {
                evaluationId        : e.d.id,
                evaluationQuestions : evaluationQuestions
            }
        }, function(data){
            
            if(data.success){
                $("#eva-alert-message").html("<i class='icon-ok'> Se envio correctamente");
                
                //fast patch
                setTimeout(function(){
                    popup.close();
                },1000);

                setTimeout(function(){
                    clearInterval(timerInterval);                        
                                            
                    var $optionalButton = $(e.d.optionalButtonId);
                    
                    if($optionalButton.length){
                        $optionalButton.click();
                    }else{
                        $("#activity").click();
                    }
                    
                    if(data.message){
                        if(data.certificado == 'ok'){

                            urlCertificado = '';
                            certificado = 'existe';
                            if (data.tipo == 'Personalizado') {
                                urlCertificado = './_vistas/gad_programas_simples.php?ProgramaSimple=DescargarCertificado&Codigo='+data.programaCod+'&CodigoAlmacen='+data.almacenCod+'&Alumno='+data.alumno;
                            }
                            if (data.tipo == 'Prefijo') {
                                urlCertificado = './_vistas/gad_programas_simples.php?ProgramaSimple=DescargarCertificadoDefault&Codigo='+data.programaCod+'&CodigoAlmacen='+data.almacenCod+'&Alumno='+data.alumno;
                            }
                            if (data.tipo == '') {
                                certificado = 'noexiste';
                            }

                            if (certificado == 'existe') {
                                var certificadoPopup = new $.Popup({
                                content         : $("<div>")
                                    .append($("<h2>").text("Información de la evaluación").css({"text-align":"center"}))
                                    .append($("<p>").text(data.message).css({"padding":"1em"}))
                                    .append($("<div>").css({"text-align":"center"})
                                        .append($("<h4>").text("Has Completado el Programa " + data.titulo_prog).css({"margin-bottom": "25px"}))
                                        .append($("<a>").text("Ver Certificado").attr("href", urlCertificado ).attr("target", "_blank").css({"text-decoration": "none"}).css({"background": "#DF0209"}).css({"padding": "8px"}).css({"color": "white"}))
                                    )
                                    
                                });
                            }
                            if (certificado == 'noexiste'){
                                var certificadoPopup = new $.Popup({
                                content         : $("<div>")
                                    .append($("<h2>").text("Información de la evaluación"))
                                    .append($("<p>").text(data.message).css({"padding":"1em"}))
                                });
                            }
                            

                            certificadoPopup.open();

                        }else{

                            var messagePopup = new $.Popup({
                                content         : $("<div>")
                                    .append($("<h2>").text("Información de la evaluación"))
                                    .append($("<p>").text(data.message).css({"padding":"1em"}))
                            });
                            
                            messagePopup.open();                               
                        }
                        if(data.certificado == 'Ninguno'){

                            urlCertificado = '';
                            certificado = 'existe';
                            if (data.tipo == 'Personalizado') {
                                urlCertificado = './_vistas/gad_programas_simples.php?ProgramaSimple=DescargarCertificado&Codigo='+data.programaCod+'&CodigoAlmacen='+data.almacenCod+'&Alumno='+data.alumno;
                            }
                            if (data.tipo == 'Prefijo') {
                                urlCertificado = './_vistas/gad_programas_simples.php?ProgramaSimple=DescargarCertificadoDefault&Codigo='+data.programaCod+'&CodigoAlmacen='+data.almacenCod+'&Alumno='+data.alumno;
                            }
                            if (data.tipo == '') {
                                certificado = 'noexiste';
                            }

                            if (certificado == 'existe') {
                                var certificadoPopup = new $.Popup({
                                content         : $("<div>")
                                    .append($("<h2>").text("Información de la evaluación").css({"text-align":"center"}))
                                    .append($("<p>").text(data.message).css({"padding":"1em"}))
                                    .append($("<div>").css({"text-align":"center"})
                                        .append($("<h4>").text("Has Completado el Programa ").css({"margin-bottom": "25px"}))
                                        
                                    )
                                    
                                });
                            }
                            if (certificado == 'noexiste'){
                                var certificadoPopup = new $.Popup({
                                content         : $("<div>")
                                    .append($("<h2>").text("Información de la evaluación"))
                                    .append($("<p>").text(data.message).css({"padding":"1em"}))
                                });
                            }
                            

                            certificadoPopup.open();

                        }
                    }

                    

                }, 1500);
            }
        }, "json").fail(function(httpRequestData, error){
            $("#eva-alert-message").html("<i class='icon-remove'> Error al intentar finalizar la evaluación...");
            
            setTimeout(function(){
                popup.close();
//                    e.send();
            }, 2000);
            
            console.log(httpRequestData.responseText);
        });
    },
    e.sendGroup = function(indexValue, callback){
        var group = e.groups[indexValue],
        dataToSave = {
            evaluationId        : e.d.id,
            timeInSeconds       : e.d.time,
            toleranceWasApplied : e.toleranceWasApplied,
            questions           : []
        };
        
        group.questions.forEach(function(question, index){
            var questionDataToSave = {
                type: question.d.type,
                id  : question.d.id,
                answers: []
            };
            
            switch(question.d.type){
                case "open":
                    var textValue = question.answers[0].node.find("textarea").eq(0).val();
                    
                    if(textValue){
                        questionDataToSave.answers.push(textValue);
                    }
                    break;
                case "selective":
                case "multiselective":
                    question.answers.forEach(function(answer){
                        if(answer.node.find("input").get(0).checked){
                             questionDataToSave.answers.push(answer.d.id);
                        }
                    });
                    break;
            }
            
            dataToSave.questions.push(questionDataToSave);
        });
        
        e.messageNode.html("<i class='icon-refresh'></i> Sincronizando...");
        e.messageNode.css({display: "inline-block"});
        
        $.post(URItoSaveData + "?ProcesoActividadesExamen=save_step", {dataStep: dataToSave}, function(success){

            if(success){
                if(callback && typeof callback === "function"){
                    callback();
                }
                
                e.messageNode.html("<i class='icon-ok'></i> Cambios guardados");
                setTimeout(function(){
                    e.messageNode.fadeOut();
                }, 2000);
            }
        }, "json").fail(function(httpRequestData, error){
            e.messageNode.addClass("error").html("<i class='icon-remove'></i> Error al intentar guardar los cambios");
            setTimeout(function(){
                e.messageNode.removeClass("error").fadeOut();
            }, 2000);
            
            console.log(httpRequestData.responseText);
        });
    };
    
    //Appling settings
    var questionNumber = 1;
    e.d.groups.forEach(function(groupData){
        var group = new Group({
            questions: groupData.questions
        });
        
        group.questions.forEach(function(question){
            question.node.find("span.eva-question-number").text(questionNumber);
            questionNumber++;
        });
        
        e.node.find("div.eva-viewer").append(group.node);
        
        e.groups.push(group);
    });
    
    e.node.find("span.eva-paginator-total").text(e.d.groups.length);
    
    //set data tracking
    e.groups.forEach(function(group){
        group.questions.forEach(function(question){
            if(question.d.id in e.d.trackingData){
                var data = e.d.trackingData[question.d.id];
                
                switch(question.d.type){
                    case "open":
                        question.answers[0].node.find("textarea").eq(0).val(data);
                        break;
                    case "selective":
                    case "multiselective":
                        question.answers.forEach(function(answer){
                            if($.inArray(answer.d.id, data) !== -1){
                                answer.node.find("input").get(0).checked = true;
                                answer.node.find("input").eq(0).parent().addClass("checked");
                            }
                        });
                        break;
                }
            }
        });
    });

    //set course title
    e.node.find("span.eva-title-value").text(e.d.course.name + " - " + e.d.description);

    //set events
    var onnoption = function(movement){
        return function(){
            e.move(movement);
        };
    };

    e.node.find("div.eva-button").eq(0).click(onnoption(ONPREV));
    e.node.find("div.eva-button").eq(1).click(onnoption(ONNEXT));
    e.node.find("div.eva-button").eq(2).click(function(){
        e.sendGroup(e.groups.length - 1, function(){

            var arrayQtions = [];

            e.groups.forEach(function(value){

                value.d.questions.forEach(function(value){

                    arrayQtions.push(value.id);

                });
            });

            $.post(URItoSaveData + "?ProcesoActividadesExamen=get_unansweredQuestions", {
                dataQuestions: {
                    unansweredQuestions : arrayQtions
                }
            }, function(data){

                var msg = "";

                if (data.length > 0){

                    msg += "<style>" + 
                    ".app-confirm .app-confirm-message {" +                       
                        "height: 20em;" +
                        "overflow-y: scroll; }" +

                    ".app-confirm .app-confirm-message {" +                       
                        "max-height: 15em;" +
                    "}" +

                    ".popup_cont {" +
                        "top: 25% !important;" +
                        "left: 20% !important;" +
                        "right: 20% !important;" +
                    "}" +

                    ".app-confirm .app-confirm-message {" +
                        "max-height: 10em;" +
                    "}" +

                    "</style>";

                    msg += "</br> <p style='font-size: 0.8em; text-align: center;'> Tiene preguntas sin responder: </p>";                    

                    msg += "<table style='margin: 1em 1em 1em 1em; width: 96%;'>"+
                              "<tr>"+
                                "<th style='font-size: 0.8em;' scope='col'>Pregunta</th>"+
                                "<th style='font-size: 0.8em;' scope='col'>Puntaje</th>"+
                                "<th style='font-size: 0.8em;' scope='col'>Respondida</th>"+
                              "</tr>";                              

                data.forEach(function(value){                    

                    msg += "<tr>";

                        msg += "<td style='font-size: 0.7em;'>" + value.Descripcion + "</td>";

                        msg += "<td style='font-size: 0.7em;'>" + value.CantidadNota + "</td>";

                        if (value.checked == 0){
                            msg += "<td style='font-size: 0.7em;'> <i class='icon-exclamation-sign'> No marcada</i> </td>";
                        }

                    msg += "</tr>";

                });             

                    msg += "</table>";

                }

                var conf = new Confirm({
                    message : "¿Desea finalizar la evaluación?",
                    confirm : function(){
                            e.send();
                        }
                });
                
                conf.open();
                
                $( ".app-confirm-message" ).append( $.parseHTML(msg) );

            }, "json").fail(function(httpRequestData, error){

                console.log(httpRequestData.responseText);

            });
        });
    });
    e.node.find("input.eva-paginator-value").on("keyup", function(ev){
        if($(this).val().trim()){
            var value = +$(this).val() - 1;
            var indexToSend = index;
            
            if(ev.keyCode === 13 && !isNaN(value) && index !== value){
                if(e.setIndex(value)){
                    e.sendGroup(indexToSend);
                }
            }else if(ev.keyCode === 27){
                $(this).val("");
            }
        }
    });
    $("#Pizarra").bind("DOMSubtreeModified", function(){
        if(!$(this).find(".eva-container").length){
            console.log("Leave the evaluation...");
            clearInterval(timerInterval);
            $(this).unbind("DOMSubtreeModified");
        }
    });
}

function Group(settings){
    //Private vars
    var g = this,
    defaults = {
        //Params
        questions: null
    };
    
    g.questions = [];
    
    //Class vars
    g.d = $.extend(true, {}, defaults, settings),
    g.node = $("<div>").html($("#eva-group-template").html()).find("div[class^=eva]").eq(0);
    
    //Appling settings
    g.d.questions.forEach(function(questionData){
        var question = new Question({
            id          : questionData.id,
            type        : questionData.type,
            description : questionData.description,
            answers     : questionData.answers
        });
        
        g.node.append(question.node);
        
        g.questions.push(question);
    });
}

function Question(settings){
    //Private vars
    var q = this,
    defaults = {
        //Params
        id: null,
        type: "defaultType",
        description: "defaultDescription",
        answers: null
    };
    
    q.answers = [];
    
    //Class vars
    q.d = $.extend(true, {}, defaults, settings),
    q.node = $("<div>").html($("#eva-question-template").html()).find("div[class^=eva]").eq(0);

    //Appling settings
    switch(q.d.type){
        case "selective":
        case "multiselective":
            q.d.answers.forEach(function(answerData){
                var answer = new Answer({
                    id: answerData.id,
                    description: answerData.description
                });

                q.node.find("div.eva-question-viewer").append(answer.node);

                q.answers.push(answer);
            });
            break;
        case "open":
            var answer = new Answer();

            q.node.find("div.eva-question-viewer").append(answer.node);

            q.answers.push(answer);
            break;
    }
    
    //set question title
    q.node.find("span.eva-question-description").html(q.d.description);
    
    //each answers
    switch(q.d.type){
        case "open":
            var $input = $("<textarea>");
            answer.node.append($input);
            break;
        case "selective":
            q.answers.forEach(function(answer){
                var $input = $("<input>").attr({"type":"radio", "name":"eva-answer-radio-" + q.d.id});
                answer.node.append($input).append(answer.d.description);
                
                $input.on("change", function(){
                    q.answers.forEach(function(answer){
                        answer.node.find("input[type=radio]").parent().removeClass("checked");
                    });
                    
                    $(this).parent().addClass("checked");
                });
            });
            break;
        case "multiselective":
            q.answers.forEach(function(answer){
                var $input = $("<input>").attr({"type":"checkbox", "name":"eva-answer-checkbox-" + q.d.id});
                answer.node.append($input).append(answer.d.description);
                
                $input.on("change", function(){
                    if(this.checked){
                        $(this).parent().addClass("checked");
                    }else{
                        $(this).parent().removeClass("checked");
                    }
                });
            });
            break;
    }
}

function Answer(settings){
    //Private vars
    var a = this,
    defaults = {
        //Params
        id          : null,
        description : "defaultDescription"
    };
    
    //Class vars
    a.d = $.extend(true, {}, defaults, settings),
    a.node = $("<div>").html($("#eva-answer-template").html()).find("div[class^=eva]").eq(0);
}

function Confirm(settings){
    //Private vars
    var c = this,
    defaults = {
        //Params
        message     : "defaultMessage",
        confirm     : null,
        cancel      : null
    };
    
    //Class vars
    c.d = $.extend(true, {}, defaults, settings),
    c.open = function(){
        var popup = new $.Popup({
            content         : $("<div>").append(c.node),
            modal           : true,
            closeContent    : "",
            afterOpen       : function(){
                var $content = $("#app-confirm-message");
                
                //set events
                $content.find("button").eq(0).click(function(){
                    popup.close();
                    typeof c.d.confirm === "function" && c.d.confirm();
                });
                
                $content.find("button").eq(1).click(function(){
                    popup.close();
                    typeof c.d.cancel === "function" && c.d.cancel();
                });
            }
        });
        
        popup.open();
    },
    c.node = $("<div>").html($("#app-confirm-template").html()).find("div[class=app-confirm]").eq(0);

    //applying settings
    c.node.attr("id", "app-confirm-message");
    c.node.find("div.app-confirm-message").text(c.d.message);
}

function renderEvaluation(data, optionalButtonId){
    var evaluation = new Evaluation({
        id              : data.id,
        description     : data.description,
        course          : data.course,
        student         : data.student,
        time            : data.time,
        timeOfTolerance : data.timeOfTolerance,
        groups          : data.groups,
        trackingData    : data.trackingData,
        container       : $("#examen_evaluacion_body"),
        optionalButtonId: optionalButtonId
                
    });
    
    evaluation.init();
}
