'use strict';

/**
 *  Require jquery
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main RowLayout Class
 *
 *  @param {Object} settings
 */
var RowLayout = function(settings) {
    //Private vars
    var rl = this,
    defaults = {};
    
    //Class vars
    rl.d = $.extend(true, {}, defaults, settings),
    rl.n = {
        rowLayout : $("<div>").attr({"class":"row"})
    };
    /*
    CSS
    rowLayout   : row
    */
};