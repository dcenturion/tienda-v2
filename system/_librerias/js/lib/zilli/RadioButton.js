'use strict';

/**
 *  Require jquery
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main RadioButton Class
 *
 *  @param {Object} settings
 */

var RadioButton = function(settings) {
    //Private vars
    var rb = this,
    defaults = {
        //Params
        checked         : false,
        disabled        : false,
        effectWithGap   : true,
        id              : null,
        labelText       : "defaultLabelText",
        name            : "defaultName",
        value           : "ItemValueDefault",
        width           : RadioButton.WIDTH_MATCH_PARENT
    };
    
    //Class vars
    rb.d = $.extend(true, {}, defaults, settings),
    rb.n = {
        radioButton : $("<div>").attr({"class":"col s" + rb.d.width}),
            input   : $("<input>").attr({"type":"radio", "id":rb.d.id, "name":rb.d.name, "value":rb.d.value}),
            label   : $("<label>").attr({"for":rb.d.id}).text(rb.d.labelText)
    };
    
    /*
    CSS
    radioButton     : input-field, col, s[1-12]
    input           : with-gap
    */
   
   //Render RadioButton
    $(rb.n.radioButton)
    .append(rb.n.input)
    .append(rb.n.label);
   
    //Configure options
    if(rb.d.checked){
        rb.n.input.attr("checked", true);
    }

    if(rb.d.disabled){
        rb.n.input.attr("disabled", true);
    }
    
    if(rb.d.effectWithGap){
        rb.n.input.addClass("with-gap");
    }
};

RadioButton.WIDTH_HALF_PARENT = 6,
RadioButton.WIDTH_MATCH_PARENT = 12;

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