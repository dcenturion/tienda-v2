'use strict';

/**
 *  Require materialize.js
 *  Require jquery.popup.js
 *  Require jquery
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main Zilli Class
 *  @param  {Object} settings
 *  @return {Object}
 */

function Zilli(settings){
    var z = this,
    defaults = {
        //Params
        path : "js/zilli/"
    };
    
    //Class vars
    z.d = $.extend(true, {}, defaults, settings),
    z.dependencies = [
        "Button.js",
        "CheckBox.js",
        "CheckBoxGroup.js",
        "DatePicker.js",
        "EditText.js",
        "Form.js",
        "RadioButton.js",
        "RadioButtonGroup.js",
        "RowLayout.js",
        "SelectBox.js",
        "Switch.js",
        "TextArea.js",
        "timerpicker.js"
    ],
    z.init = function(callback){
        var dependencies = z.dependencies;
        //Load dependencies
        loadDependencie(dependencies, callback);
    };
    
    //PRIVATE VARS
    //Load dependencies files function
    function loadDependencie(dependencies, callback){
        var dependencieURI = dependencies.shift();

        if(dependencieURI){
            $.getScript(z.d.path + dependencieURI, function(){
                loadDependencie(dependencies, callback);
            });
        }else{
            callback();
        }
    }
}

var Z = new Zilli({
    path : "/system/_librerias/js/lib/zilli/"
});