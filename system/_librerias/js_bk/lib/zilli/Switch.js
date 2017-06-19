'use strict';

/**
 *  Require jquery
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main Switch Class
 *
 *  @param {Object} settings
 */

var Switch = function(settings) {
    //Private vars
    var s = this,
    defaults = {
        //Params
        checked             : false,
        disabled            : false,
        disableLabelText    : "disableLabelTextDefault",
        enableLabelText     : "enableLabelTextDefault",
        name                : "defaultName"
    };
    
    //Class vars
    s.d = $.extend(true, {}, defaults, settings),
    s.n = {
        switch          : $("<div>").attr({"class":"switch"}),
            label       : $("<label>"),
                input   : $("<input>").attr({"type":"checkbox", "name":s.d.name}),
                lever   : $("<span>").attr({"class":"lever"})
    };
    
    /*
    CSS
    switch  : switch
    lever   : lever
    */
   
   //Render CheckBox
    $(s.n.switch)
    .append(s.n.label);
    $(s.n.label)
    .append(s.d.disableLabelText)
    .append(s.n.input)
    .append(s.n.lever)
    .append(s.d.enableLabelText);
   
    //Configure options
    if(s.d.checked){
        s.n.input.attr("checked", true);
    }
    
    if(s.d.disabled){
        s.n.input.attr("disabled", true);
    }
};

/*
disabled            Boolean
disableLabelText    String
enableLabelText     String
name                String
*/