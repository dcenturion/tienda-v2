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

var EditText = function(settings) {
    //Private vars
    var et = this,
    defaults = {
        //Params
        disabled : false,
        id: null,
        labelText : null,
        name: "defaultName",
        type: "text",
        validate: false,
        value : null,
        width: EditText.WIDTH_MATCH_PARENT
    };
    
    //Class vars
    et.d = $.extend(true, {}, defaults, settings),
    et.n = {
        editText : $("<div>").attr({"class":"input-field col s" + et.d.width}),
            input: $("<input>").attr({"type":et.d.type, "name":et.d.name, "value":et.d.value}),
            label: $("<label>").hide()
    };
    /*
    CSS
    editText    : input-field, col, s[1-12]
    input       : validate
    */
    
    //Render Node
    $(et.n.editText)
    .append(et.n.input)
    .append(et.n.label);
    
    //Appling settings
    //If disabled is true disable the input
    if(et.d.disabled){
        $(et.n.input).attr({"disabled":true});
    }
    //If exist id apply on input
    if(et.d.id){
        $(et.n.input).attr({"id":et.d.id});
    }
    //If labelText exists show label and set the value
    if(et.d.labelText){
        $(et.n.label)
        .attr({"for":et.d.id})
        .show()
        .text(et.d.labelText);
    }
    //If validate is true appliyng validate on field
    if(et.d.validate){
        $(et.n.input).addClass("validate");
    }
    //If value exists value active label
    if(et.d.value && et.d.value.trim()){
        $(et.n.label).addClass("active");
    }
};

EditText.WIDTH_MATCH_PARENT = 12,
EditText.WIDTH_HALF_PARENT = 6;

/*
disabled:   Boolean
id:         String
labelText:  String
name        String
type:       String  -> [text, email, password]
validate:   Boolean
value       String
width:      Integer -> [1-12]
*/