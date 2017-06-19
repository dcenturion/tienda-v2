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

var TimePicker = EditText;

TimePicker.prototype.init = function(){
    this.n.input.clockpicker({
        placement   : 'top',
        align       : 'left',
        autoclose   : true,
        default     : 'now'
    });
};

/*
disabled:   Boolean
id:         String
labelText:  String
name        String
value       String
*/