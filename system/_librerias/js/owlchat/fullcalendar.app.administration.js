$("#calendar").text("Cargando... espere porfavor...");

//First get events to view
$.get("/system/_vistas/gad_cursos_simples.php?Calendar=load", function(initData){
    var $calendar = $("#calendar").empty();
    
    //private vars
    var removeEvent = function(eventIdToDelete){
        var indexToDelete = null;
        
        $.each(initData.events, function(index, event){
            if(eventIdToDelete === event.id){
                indexToDelete = index;
            }
        });
        
        initData.events.splice(indexToDelete, 1);
    },
    showAllEvents = function(){
        //Remove all current events
        $calendar.fullCalendar("removeEvents");
        
        //Render all events
        $.each(initData.events, function(index, event){
            //render de event
            $calendar.fullCalendar("renderEvent", event);
        });
    },
    showCourseEvents = function(){
        //Remove all current events
        $calendar.fullCalendar("removeEvents");
        
        //Render events from this calendar_id
        $.each(initData.events, function(index, event){
            if(event.calendarId === CALENDAR_ID){
                //render de event
                $calendar.fullCalendar("renderEvent", event);
            }
        });
    };
    
    var $dropDownButtons = $("<div>")
    .attr({"class":"dropdown-container"})
    .append($("<div>").attr({"class":"dropdown-option"}).text("Ver todos los eventos").click(showAllEvents))
    .append($("<div>").attr({"class":"dropdown-option"}).text("Ver eventos en este curso").click(showCourseEvents));
    
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
                    openPopupURI("/system/_vistas/gad_cursos_simples.php?Calendar=newEvent&ProgramaAlmacen=" + ADMINISTRATION_CALENDAR_PROGRAMA_ALMACEN + "&CursoAlmacen=" + ADMINISTRATION_CALENDAR_CURSO_ALMACEN, {
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
                                sendForm("/system/_vistas/gad_cursos_simples.php?Calendar=newEventRecord&ProgramaAlmacen=" + ADMINISTRATION_CALENDAR_PROGRAMA_ALMACEN + "&CursoAlmacen=" + ADMINISTRATION_CALENDAR_CURSO_ALMACEN,"Form_administration_new_event", function(data){
                                    var json = JSON.parse(data);
                                    
                                    //add to data events
                                    initData.events.push(json.event);
                                    
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
            },
            newCourseEvent: {
                text: "Crear evento en el curso",
                click: function() {
                    openPopupURI("/system/_vistas/gad_cursos_simples.php?Calendar=newCourseEvent", {
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
                                sendForm("/system/_vistas/gad_cursos_simples.php?Calendar=newCourseEventRecord&ProgramaAlmacen=" + ADMINISTRATION_CALENDAR_PROGRAMA_ALMACEN + "&CursoAlmacen=" + ADMINISTRATION_CALENDAR_CURSO_ALMACEN,
                                "Form_administration_new_event_att", function(data){
                                    var json = JSON.parse(data);
                                    
                                    //add to data events
                                    initData.events.push(json.event);
                                    
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
            },
            filterCalendar: {
                text: "Búsqueda",
                click: function() {
                    if($(this).attr("data-display")){
                        $(this).removeAttr("data-display");
                        $dropDownButtons.hide();
                    }else{
                        $(this).attr("data-display", true);
                        $dropDownButtons.show().appendTo(this);
                    }
                }
            }
        },
        events: initData.events,
        header: {
            left    : "today prev,next",
            center  : "title",
            right   : "newEvent newCourseEvent,filterCalendar agendaDay,month"
        },
        //Callbacks
        eventClick: function(calEvent, jsEvent, view) {
            openPopupURI("/system/_vistas/gad_cursos_simples.php?Calendar=editEvent&eventId=" + calEvent.id, {
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
                        sendForm("/system/_vistas/gad_cursos_simples.php?Calendar=editEventRecord&eventId=" + calEvent.id, "Form_administration_new_event_att-UPD", function(data){
                            var json = JSON.parse(data);
                            
                            //delete from data events
                            removeEvent(json.event.id);
                            
                            //Remove the event
                            $calendar.fullCalendar("removeEvents", [json.event.id]);

                            //add to data events
                            initData.events.push(json.event);

                            //Render the update event
                            $calendar.fullCalendar("renderEvent", json.event);

                            $popup.close();
                        });
                    });

                    //button delete
                    $content.find(".Botonera").eq(1)
                    .click(function(){
                        sendForm("/system/_vistas/gad_cursos_simples.php?Calendar=deleteEvent&eventId=" + calEvent.id, "Form_administration_new_event_att-UPD", function(eventId){
                            $calendar.fullCalendar("removeEvents", [eventId]);
                            
                            //delete from data events
                            removeEvent(eventId);
                            
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
            
            openPopupURI("/system/_vistas/gad_cursos_simples.php?Calendar=newEvent&ProgramaAlmacen=" + ADMINISTRATION_CALENDAR_PROGRAMA_ALMACEN + "&CursoAlmacen=" + ADMINISTRATION_CALENDAR_CURSO_ALMACEN, {
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
                        sendForm("/system/_vistas/gad_cursos_simples.php?Calendar=newEventRecord&ProgramaAlmacen=" + ADMINISTRATION_CALENDAR_PROGRAMA_ALMACEN + "&CursoAlmacen=" + ADMINISTRATION_CALENDAR_CURSO_ALMACEN,"Form_administration_new_event", function(data){
                            var json = JSON.parse(data);
                            
                            //add to data events
                            initData.events.push(json.event);
                            
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
}, "json");