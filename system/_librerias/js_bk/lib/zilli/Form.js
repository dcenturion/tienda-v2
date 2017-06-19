'use strict';

/**
 *  Require jquery
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main EditText Class
 *
 *  @param {Object} settings
 */

var Form = function(settings) {
    //Private vars
    var f = this,
    defaults = {
        containerId : null, 
        elements    : [],
        id          : "defaultFormId",
        requestURI  : null,
        params      : {},
        width       : 500
    },
    done = function(){},
    $popup = null;
    
    //Class vars
    f.d = $.extend(true, {}, defaults, settings),
    f.elements = {},
    f.n = {
        form : $("<form>").attr({"id":f.d.id})
    },
    //Methods
    f.setContainer = function($container){
        $container.append(f.n.form);
        
        //Initializing elements
        $.each(f.elements, function(key, element){
            var type = element.type,
            elem = element.object;
            
            switch(type){
                case "SelectBox":
                case "DatePicker":
                case "TimePicker":
                    elem.init();
                    break;
            }
        });
    },
    f.close = function(){
        if($popup){
            $popup.close();
        }
    },
    f.get = function(nameElement){
        return f.elements[nameElement].object;
    },
    f.open = function(){
        var $container = null;
        
        if(f.d.containerId){
            $container = $(f.d.containerId);
        }else{
            //Create a popup
            //First create a volatil id
            var ramId = f.d.id + "-ram-" + (Math.floor(Math.random() * 99 + 1)),
            $ramContainer = $("<div>").append(                
                $("<div>").attr({"id": ramId})
            );
            
            $popup = new $.Popup({
                content         : $ramContainer, 
                modal           : true,
                closeContent    : null
            });
            
            $popup.open();
            
            $container = $("#" + ramId).parent();
            $("#" + ramId).remove();
        }
        
        f.setContainer($container);
        
        //First resize
        handleResize();
        
        //If popup exists center... because the content is dinamycally
        if($popup){
            $popup.center();
        }
        
        return f;
    },
    f.done = function(callback){
        //Return data, status, xhr
        done = callback;
        
        return f;
    },
    f.send = function(){
        if(!f.d.requestURI){
            return;
        }
        
        //Disable submit button
        f.n.form.find("button[type=submit]").attr("disabled", true);
        
        var form = f.n.form[0],
        elements = form.elements,
        params = {};
        
        $.each(elements, function(){
            var element = this,
            success = true,
            value = null;
            
            switch (element.type) {
                case "file":
                case "hidden":
                case "number":
                case "password":
                case "text":
                case "textarea":
                case "select-one":
                case "select-multiple":
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
                case "button":
                case "submit":
                case "reset":
                    success = false;
                    break;
            }

            if(success && element.name){
                value = value.replace(/'/g, '"');
                params[element.name] = value;
            }
        });
        
        //Each elements
        $.each(f.elements, function(key, element){
            var type = element.type,
            elem = element.object;
            
            switch(type){
                case "DatePicker":
                    params[elem.n.input.attr("name")] = elem.n.input.attr("data-date");
                    break;
            }
        });
        
        params = $.extend(true, {}, f.d.params, params);
        
        //Start POST request
        $.post(f.d.requestURI, params, function(data, status, xhr){ 
            console.log(data);
            //Enable submit button
            f.n.form.find("button[type=submit]").removeAttr("disabled");
            
            //Execute done function
            done(data, status, xhr);
        }, "json")
        .fail(function(xhr){
            //Fail request
            console.log(xhr.responseText);
        });
        
        return f;
    };
    
    /*
    CSS
    */
    
    //Appling settings
    $.each(f.d.elements, function(index, element){
        var rowLayout = new RowLayout();
        var elem = null,
        $elem = null,
        elemProperties = $.extend(true, {},element.properties, {name:element.name, id:element.name});
        
        switch(element.type){
            case "EditText":
                elem = new EditText(elemProperties);
                //Set EditText node
                $elem = elem.n.editText;
                break;
            case "Button":
                elem = new Button(elemProperties);
                //Set Button node
                $elem = elem.n.button;
                break;
            case "CheckBox":
                elem = new CheckBox(elemProperties);
                //Set CheckBox node
                $elem = elem.n.checkBox;
                break;
            case "CheckBoxGroup":
                elem = new CheckBoxGroup(elemProperties);
                //Set CheckBoxGroup node
                $elem = elem.n.checkBoxGroup;
                break;
            case "DatePicker":
                elem = new DatePicker(elemProperties);
                //Set DatePicker node
                $elem = elem.n.datePicker;
                break;
            case "RadioButton":
                elem = new RadioButton(elemProperties);
                //Set RadioButton node
                $elem = elem.n.radioButton;
                break;
            case "RadioButtonGroup":
                elem = new RadioButtonGroup(elemProperties);
                //Set RadioButtonGroup node
                $elem = elem.n.radioButtonGroup;
                break;
            case "SelectBox":
                elem = new SelectBox(elemProperties);
                //Set TextArea node
                $elem = elem.n.selectBox;
                break;
            case "Switch":
                elem = new Switch(elemProperties);
                //Set Switch node
                $elem = elem.n.switch;
                break;
            case "TextArea":
                elem = new TextArea(elemProperties);
                //Set TextArea node
                $elem = elem.n.textArea;
                break;
            case "TimePicker":
                elem = new TimePicker(elemProperties);
                //Set TimePicker node
                $elem = elem.n.editText;
                break;
        }
        //Adding nodeElement to rowLayout
        $(rowLayout.n.rowLayout).append($elem);
        
        //Adding rowLayout to Form
        $(f.n.form).append(rowLayout.n.rowLayout);
        
        //Adding [elem] to array of elements
        f.elements[element.name] = {
            type    : element.type,
            object  : elem
        };
    });
    
    //Private vars
    var handleResize = function(){
        if(!$popup){
            return;
        }

        var wWidth = $(window).width(),
        defaultWidth = 500,
        conditionalWidth = (f.d.width >= defaultWidth)? f.d.width : defaultWidth;
        
        if(wWidth > conditionalWidth){
            f.n.form.css({width: (conditionalWidth - 50)});
        }else{
            f.n.form.css({width: (wWidth - 50)});
        }
    };
    
    //EVENTS
    //On resize window
    $(window).resize(function(){
        handleResize();
    });
    
    //On submit
    f.n.form.on("submit", function(e){
        //Avoid the default send
        e.preventDefault();
        
        f.send();
    });
};

/*
containerId : String
id          : String
width       : Integer
-----------------------------
elements:   Array
[
    {
        type: "EditText",
        properties: {
            id: "txtId",
            labelText : "Campo de Prueba"
        }
    }
]
*/