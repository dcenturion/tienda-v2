$("#calendar").text("Cargando... espere porfavor...");

//First get events to view
$.get("/system/_vistas/gad_agenda.php?Event=load", function(data){
    var $calendar = $("#calendar").empty();
    
    //private vars
    
    //Init calendar
    $calendar.fullCalendar({
        //Settings
        dayNames : ("Domingo_Lunes_Martes_Miércoles_Jueves_Viernes_Sábado").split("_"),
        dayNamesShort : ("Dom_Lun_Mar_Mie_Jue_Vie_Sáb").split("_"),
        firstDay : 1,
        editable: true,
        lang: "es",
        monthNames : ("Enero_Febrero_Marzo_Abril_Mayo_Junio_Julio_Agosto_Septiembre_Octubre_Noviembre_Diciembre").split("_"),
        monthNamesShort : ("Ene_Feb_Mar_Abr_May_Jun_Jul_Ago_Sep_Oct_Nov_Dic").split("_"),
        selectable: true,
        selectHelper: true,
        timeFormat: "h(:mm)a",
        customButtons: {
            newEvent: {
                text: "Crear evento",
                click: function() {
                    openPopupURI("/system/_vistas/gad_agenda.php?Event=newEvent", {
                        afterOpen:  function(){
                            var $popup = this,
                            $content = $("#form-new-event-administration");
                            
                            $content.find("input[name=start_hour]").val(moment(new Date()).format("HH:mm:ss"));
                            $content.find("input[name=final_hour]").val(moment(new Date()).format("HH:mm:ss"));
                            $content.find("input[name=color]").val(moment(new Date()).format("YYYY-MM-DD"));
                            $content.find("input[name=status]").val(moment(new Date()).format("YYYY-MM-DD"));
                            
                            //create event
                            $content.find(".Botonera").eq(0)
                            .click(function(){
                                sendForm("/system/_vistas/gad_cursos_simples.php?Calendar=newEventRecord","Form_administration_new_event_att", function(data){
                                    var json = JSON.parse(data);
                                    
                                    //Render the new event
                                    $calendar.fullCalendar("renderEvent", json.event);
                                    
                                    $popup.close();
                                    
                                    if(json.eventURI){
                                        (new $.Popup({
                                            content: $("<div>").append('Ir al evento en Google Calendar <a href="' + json.eventURI + '" target="_blank"> Click aquí</a>')
                                        })).open();
                                    }
                                });
                            });
                            
                            //button cancel
                            $content.find(".Botonera").eq(1)
                            .click(function(){
                                $popup.close();
                            });
                        },
                        modal: true
                    });
                }
            }
        },
        events: data.events,
        header: {
            left    : "today prev,next",
            center  : "title",
            right   : "newEvent agendaDay,month"
        },
        //Callbacks
        eventClick: function(calEvent, jsEvent, view) {
            openPopupURI("/system/_vistas/gad_agenda.php?Event=editEvent&eventId=" + calEvent.id, {
                afterOpen:  function(){
                    var $popup = this,
                    $content = $("#form-edit-event-administration");
                    
                    $content.find("input[name=color]").val(calEvent.start.format("YYYY-MM-DD"));
                    $content.find("input[name=status]").val((calEvent.end)? calEvent.end.format("YYYY-MM-DD") : calEvent.start.format("YYYY-MM-DD"));
                    
                    //if event is from other user
                    if($content.find("h1 p").text().indexOf("Este evento le pertenece a") !== -1){
                        $content.find(".Botonera").eq(0).find("button").attr("disabled", true);
                        $content.find(".Botonera").eq(1).find("button").attr("disabled", true);
                    }
                    
                    //update event
                    $content.find(".Botonera").eq(0)
                    .click(function(){
                        sendForm("/system/_vistas/gad_agenda.php?Event=editEventRecord&eventId=" + calEvent.id, "Form_administration_new_event_att-UPD", function(data){
                            var json = JSON.parse(data);
                            
                            //Remove the event
                            $calendar.fullCalendar("removeEvents", [json.event.id]);

                            //Render the update event
                            $calendar.fullCalendar("renderEvent", json.event);

                            $popup.close();
                        });
                    });

                    //button delete
                    $content.find(".Botonera").eq(1)
                    .click(function(){
                        sendForm("/system/_vistas/gad_agenda.php?Event=deleteEvent&eventId=" + calEvent.id, "Form_administration_new_event_att-UPD", function(eventId){
                            $calendar.fullCalendar("removeEvents", [eventId]);
                            
                            $popup.close();
                        }, "json");
                    });
                    
                    //button cancel
                    $content.find(".Botonera").eq(2)
                    .click(function(){
                        $popup.close();
                    });
                },
                modal: true
            });
        },
        eventMouseover: function(event, jsEvent, view){
            var $bubble = $("<div>").html(window.fc_bubble_template).children().eq(0);
            
            $bubble.appendTo("body");
            
            $bubble.find(".fc-bubble-title").text(event.title);
            $bubble.find(".fc-bubble-description").text(event.description);
            
            var $bubbleArrow = $bubble.find(".fc-bubble-arrow");
            
            $bubble.css({
                left    : (jsEvent.clientX - ($bubble.outerWidth() / 2)) + "px",
                top     : (jsEvent.clientY - $bubble.outerHeight() - $bubbleArrow.outerHeight() - 10) + "px"
            });
            
            $bubbleArrow.css("left", $bubble.outerWidth() / 2);
            
            event.bubble = $bubble;
        },
        eventMouseout: function(event, jsEvent, view){
            if(event.bubble){
                event.bubble.remove();
            }
        },
        select: function(start, end) {
            var timeZoneMinutesDiff = (new Date()).getTimezoneOffset();
            
            start._d.setMinutes(start._d.getMinutes() + timeZoneMinutesDiff);
            end._d.setMinutes(end._d.getMinutes() + timeZoneMinutesDiff);
            
            //decrease endDate one day
            end._d.setDate(end._d.getDate() - 1);
            
            var dateStartStr = moment(start).format("YYYY-MM-DD"),
            dateEndStr = moment(end).format("YYYY-MM-DD");
            
            openPopupURI("/system/_vistas/gad_agenda.php?Event=newEvent", {
                afterOpen:  function(){
                    var $popup = this,
                    $content = $("#form-new-event-administration");
                    
                    $content.find("input[name=start_hour]").val((start._d.getHours())? moment(start._d).format("HH:mm:ss") : moment(new Date()).format("HH:mm:ss"));
                    $content.find("input[name=final_hour]").val((end._d.getHours())? moment(end._d).format("HH:mm:ss") : moment(new Date()).format("HH:mm:ss"));
                    $content.find("input[name=color]").val(dateStartStr);
                    $content.find("input[name=status]").val(dateEndStr);
                    
                    //create event
                    $content.find(".Botonera").eq(0)
                    .click(function(){
                        sendForm("/system/_vistas/gad_agenda.php?Event=newEventRecord","Form_administration_new_event_att", function(data){
                            var json = JSON.parse(data);
                            
                            //Render the new event
                            $calendar.fullCalendar("renderEvent", json.event);

                            $popup.close();

                            if(json.eventURI){
                                (new $.Popup({
                                    content: $("<div>").append('Ir al evento en Google Calendar <a href="' + json.eventURI + '" target="_blank"> Click aquí</a>')
                                })).open();
                            }
                        });
                    });

                    //button cancel
                    $content.find(".Botonera").eq(1)
                    .click(function(){
                        $popup.close();
                    });
                },
                modal: true
            });
        }
    });
    
    //Confirm view of events
    $.post("/system/_vistas/gad_agenda.php?EventMethod=confirm_view");
    
    //Show pending events
    $.each(data.events, function(index, event){
        console.log(event);
    });
    
    //get data to know if user configure google account
    $.get("/system/_vistas/gad_agenda.php?UserConfig=google_account_exists", function(valide){
        if(valide){
            var popup = new $.Popup({
                afterOpen: function(){
                    $("#ad-caldendar-option-accept").click(function(){
                        $.post("/system/_vistas/gad_agenda.php?UserConfig=google_account_enable_calendar", function(){
                            popup.close();
                        }, "json");
                    });
                    
                    $("#ad-caldendar-option-decline").click(function(){
                        popup.close();
                    }); 
                } 
            });
            
            popup.open("/system/_vistas/gad_agenda.php?UserConfig=google_account_request_permission");
        }
    }, "json");
}, "json");