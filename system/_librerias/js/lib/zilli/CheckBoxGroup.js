'use strict';

/**
 *  Require jquery
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main CheckBoxGroup Class
 *
 *  @param {Object} settings
 */

var CheckBoxGroup = function(settings) {
    //Private vars
    var cbg = this,
    defaults = {
        //Params
        name: "defaultName",
        options : [
            {
                checked     : false,
                disabled    : false,
                labelText   : "defaultLabelText"
            }
        ],
        width   : CheckBoxGroup.WIDTH_MATCH_PARENT
    };
    
    //Class vars
    cbg.d = $.extend(true, {}, defaults, settings),
    cbg.n = {
        checkBoxGroup    : $("<div>").attr({"class":"col s" + cbg.d.width})
    };
    
    //Configure options
    $.each(cbg.d.options, function(index, option){
        var _id = (cbg.d.name + "-" + index);
        
        var defaultProperties = {name: _id, id: _id},
        mergeProperties = $.extend(true, {}, option, defaultProperties),
        checkBox = new CheckBox(mergeProperties);
        
        //Add to CheckBoxGroup
        $(cbg.n.checkBoxGroup).append(checkBox.n.checkBox);
    });
};

CheckBoxGroup.WIDTH_MATCH_PARENT = 12,
CheckBoxGroup.WIDTH_HALF_PARENT = 6;

/*
name        String
options     Array of {}
*/