'use strict';

/**
 *  Require jquery
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main Button Class
 *
 *  @param {Object} settings
 */

var Button = function(settings) {
    //Private vars
    var b = this,
    defaults = {
        //Params
        disabled    : false,
        name: "defaultName",
        text        : "null",
        type        : "button",
        waveEffect  : true,
        waveLight   : true,
        width       : Button.WIDTH_MATCH_PARENT
    };
    
    //Class vars
    b.d = $.extend(true, {}, defaults, settings),
    b.n = {
        button  : $("<button>").attr({"class":"btn col s" + + b.d.width, "name":b.d.name, "type": b.d.type}).text(b.d.text)
    };
    /*
    CSS
    button  : btn, waves-effect, waves-light, col, s[1-12]
    */
    
    //Appling settings
    //If disabled is true disable the input
    if(b.d.disabled){
        $(b.n.button).attr({"disabled":true});
    }
    //If waveEffect is true add class
    if(b.d.waveEffect){
        $(b.n.button).addClass("waves-effect");
    }
    //If waveLight is true add class
    if(b.d.waveEffect){
        $(b.n.button).addClass("waves-light");
    }
};

Button.WIDTH_MATCH_PARENT = 12,
Button.WIDTH_HALF_PARENT = 6;
Button.WIDTH_AUTO = 1;

/*
disabled    : Boolean
type        : String  -> [button, reset, submit]
waveEffect  : Boolean
waveLight   : Boolean
*/