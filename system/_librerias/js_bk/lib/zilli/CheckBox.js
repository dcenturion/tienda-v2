'use strict';

/**
 *  Require jquery
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main CheckBox Class
 *
 *  @param {Object} settings
 */

var CheckBox = function(settings) {
    //Private vars
    var cb = this,
    defaults = {
        //Params
        checked         : false,
        disabled        : false,
        effectFilledIn  : true,
        id              : null,
        labelText       : "defaultLabelText",
        name            : "defaultName",
        value           : null, //If it's null checkbox return on
        width           : CheckBox.WIDTH_MATCH_PARENT
    };
    
    //Class vars
    cb.d = $.extend(true, {}, defaults, settings),
    cb.n = {
        checkBox    : $("<div>").attr({"class":"col s" + cb.d.width}),
            input   : $("<input>").attr({"type":"checkbox", "id":cb.d.id, "name":cb.d.name, "value":cb.d.value}),
            label   : $("<label>").attr({"for":cb.d.id}).text(cb.d.labelText)
    };
    
    /*
    CSS
    checkBox    : input-field, col, s[1-12]
    input       : filled-in
    */
   
   //Render CheckBox
    $(cb.n.checkBox)
    .append(cb.n.input)
    .append(cb.n.label);
   
    //Configure options
    if(cb.d.checked){
        cb.n.input.attr("checked", true);
    }

    if(cb.d.disabled){
        cb.n.input.attr("disabled", true);
    }
    
    if(cb.d.effectFilledIn){
        cb.n.input.addClass("filled-in");
    }
};

CheckBox.WIDTH_HALF_PARENT = 6,
CheckBox.WIDTH_MATCH_PARENT = 12;

/*
checked         Boolean
disabled        Boolean
effectWithGap   Boolean
id              String
labelText       String
name            String
value           String
width:          Integer -> [1-12]
*/