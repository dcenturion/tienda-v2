'use strict';

/**
 *  Require jquery
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main SelectBox Class
 *
 *  @param {Object} settings
 */

var SelectBox = function(settings) {
    //Private vars
    var sb = this,
    defaults = {
        //Params
        disabled : false,
        id: null,
        indexOption : 0,
        labelText : null,
        name: "defaultName",
        options : [
            {
                disabled    : false,
                value       : "ItemValueDefault"
            }
        ],
        width: SelectBox.WIDTH_MATCH_PARENT
    };
    
    //Class vars
    sb.d = $.extend(true, {}, defaults, settings),
    sb.n = {
        selectBox   : $("<div>").attr({"class":"input-field col s" + sb.d.width}),
            select  : $("<select>").attr({"id":sb.d.id, "name":sb.d.name}),
            label   : $("<label>").hide()
    },
    sb.init = function(){
        //Init select
        $("#" + sb.d.id).material_select();
    };
    /*
    CSS
    selectBox    : input-field, col, s[1-12]
    */
    
    //Render Node
    $(sb.n.selectBox)
    .append(sb.n.select)
    .append(sb.n.label);
    
    //Configure items
    $.each(sb.d.options, function(index, option){
        var nodeOption = $("<option>").text(option.value);
        
        if(option.disabled){
            nodeOption.attr("disabled", true);
        }
        
        if(sb.d.indexOption === index){
            nodeOption.attr("selected", true);
        }
        
        $(sb.n.select)
        .append(nodeOption);
    });
    
    //Appling settings
    //If disabled is true disable the input
    if(sb.d.disabled){
        $(sb.n.select).attr({"disabled":true});
    }
    //If exist id apply on input
    if(sb.d.id){
        $(sb.n.select).attr({"id":sb.d.id});
    }
    //If labelText exists show label and set the value
    if(sb.d.labelText){
        $(sb.n.label)
        .attr({"for":sb.d.id})
        .show()
        .text(sb.d.labelText);
    }
};

SelectBox.WIDTH_MATCH_PARENT = 12,
SelectBox.WIDTH_HALF_PARENT = 6;

/*
disabled:   Boolean
id:         String
indexOption Integer
labelText:  String
name        String
options     Array of {}
width:      Integer -> [1-12]
*/