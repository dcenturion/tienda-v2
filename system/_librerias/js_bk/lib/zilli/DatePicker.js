'use strict';

/**
 *  Require jquery
 *  Require moment
 *  
 *  @author Aaron Nuñez
 *  @version 0.0.1
 *  
 *  Main DatePicker Class
 *
 *  @param {Object} settings
 */

var DatePicker = function(settings) {
    //Private vars
    var dp = this,
    defaults = {
        //Params
        disabled : false,
        id: null,
        labelText : null,
        name: "defaultName",
        value : null,
        width: DatePicker.WIDTH_MATCH_PARENT
    },
    change = function(){};
    
    //Class vars
    dp.picker = null,
    dp.d = $.extend(true, {}, defaults, settings),
    dp.n = {
        datePicker  : $("<div>").attr({"class":"input-field col s" + dp.d.width}),
            input   : $("<input>").attr({"type":"date", "class":"datepicker", "name":dp.d.name}),
            label   : $("<label>").hide()
    },
    dp.init = function(){
        //Init select
        $("#" + dp.d.id).pickadate({
            selectMonths    : true,
            selectYears     : 100,
            //Strings and Translations
            monthsFull      : ("Enero_Febrero_Marzo_Abril_Mayo_Junio_Julio_Agosto_Septiembre_Octubre_Noviembre_Diciembre").split("_"),
            monthsShort     : ("Ene_Feb_Mar_Abr_May_Jun_Jul_Ago_Sep_Oct_Nov_Dic").split("_"),
            weekdaysFull    : ("Domingo_Lunes_Martes_Miércoles_Jueves_Viernes_Sábado").split("_"),
            weekdaysShort   : ("Dom_Lun_Mar_Mie_Jue_Vie_Sáb").split("_"),
            weekdaysLetter  : ("D_L_M_M_J_V_S").split("_"),
            //Buttons
            today           : "HOY",
            clear           : "LIMPIAR",
            close           : "CERRAR",
            //Callbacks
            onStart         : function(){
                //Set PickerInstance
                dp.picker = this;
                
                //Set value
                var dateValue = (dp.d.value)? new Date(dp.d.value) : new Date();
                this.set("select", dateValue);
            },
            onSet        : function(context){
                var selectDate = context.select;
                if(!(selectDate instanceof Date)){
                    selectDate = new Date(selectDate);
                }
                
                dp.n.input.attr("data-date", moment(selectDate).format("YYYY-MM-DD"));
                
                //Execute change function
                change(selectDate);
            }
        });
    },
    dp.change = function(callback){
        //Return Date object
        change = callback;
        
        return dp;
    };
    /*
    CSS
    datePicker  : input-field, col, s[1-12]
    input       : validate
    */
    
    //Render Node
    $(dp.n.datePicker)
    .append(dp.n.input)
    .append(dp.n.label);
    
    //Appling settings
    //If disabled is true disable the input
    if(dp.d.disabled){
        $(dp.n.input).attr({"disabled":true});
    }
    //If exist id apply on input
    if(dp.d.id){
        $(dp.n.input).attr({"id":dp.d.id});
    }
    //If labelText exists show label and set the value
    if(dp.d.labelText){
        $(dp.n.label)
        .attr({"for":dp.d.id})
        .show()
        .text(dp.d.labelText);
    }
};

DatePicker.WIDTH_MATCH_PARENT = 12,
DatePicker.WIDTH_HALF_PARENT = 6;

/*
disabled:   Boolean
id:         String
labelText:  String
name        String
value       String
width:      Integer -> [1-12]
*/