'use strict';

/**
 *  Require jquery
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main RadioButtonGroup Class
 *
 *  @param {Object} settings
 */

var RadioButtonGroup = function(settings) {
    //Private vars
    var rbg = this,
    defaults = {
        //Params
        name    : "defaultName",
        options : [
            {
                checked     : false,
                disabled    : false,
                labelText   : "defaultLabelText",
                value       : "defaultValue"
            }
        ],
        width   : RadioButtonGroup.WIDTH_MATCH_PARENT
    };
    
    //Class vars
    rbg.d = $.extend(true, {}, defaults, settings),
    rbg.n = {
        radioButtonGroup    : $("<div>").attr({"class":"col s" + rbg.d.width})
    };
    
    //Configure options
    $.each(rbg.d.options, function(index, option){
        var _id = (rbg.d.name + "-" + index);
        
        var defaultProperties = {name: rbg.d.name, id: _id},
        mergeProperties = $.extend(true, {}, option, defaultProperties),
        radioButton = new RadioButton(mergeProperties);
        
        //Add to CheckBoxGroup
        $(rbg.n.radioButtonGroup).append(radioButton.n.radioButton);
    });
};

RadioButtonGroup.WIDTH_MATCH_PARENT = 12,
RadioButtonGroup.WIDTH_HALF_PARENT = 6;

/*
name        String
options     Array of {}
*/