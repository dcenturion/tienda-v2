'use strict';

/**
 *  Require jquery
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main TextArea Class
 *
 *  @param {Object} settings
 */

var TextArea = function(settings) {
    //Private vars
    var ta = this,
    defaults = {
        //Params
        id: null,
        labelText : null,
        name: "defaultName",
        value : null,
        width: TextArea.WIDTH_MATCH_PARENT
    };
    
    //Class vars
    ta.d = $.extend(true, {}, defaults, settings),
    ta.n = {
        textArea : $("<div>").attr({"class":"input-field col s" + ta.d.width}),
            textarea: $("<textarea>").attr({"class":"materialize-textarea", "name":ta.d.name}).text((ta.d.value)? ta.d.value : ""),
            label: $("<label>").hide()
    };
    /*
    CSS
    textArea    : input-field, col, s[1-12]
    textarea    : materialize-textarea
    */
    
    //Render Node
    $(ta.n.textArea)
    .append(ta.n.textarea)
    .append(ta.n.label);
    
    //Appling settings
    //If exist id apply on input
    if(ta.d.id){
        $(ta.n.textarea).attr({"id":ta.d.id});
    }
    //If labelText exists show label and set the value
    if(ta.d.labelText){
        $(ta.n.label)
        .attr({"for":ta.d.id})
        .show()
        .text(ta.d.labelText);
    }
    //If value exists value active label
    if(ta.d.value && ta.d.value.trim()){
        $(ta.n.label).addClass("active");
    }
};

TextArea.WIDTH_MATCH_PARENT = 12,
TextArea.WIDTH_HALF_PARENT = 6;

/*
id:         String
labelText:  String
name:       String
value:      String
width:      Integer -> [1-12]
*/