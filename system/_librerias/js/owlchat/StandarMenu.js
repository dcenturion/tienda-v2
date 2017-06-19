/* 
 * This file require Jquery.js
 */
function StandarMenu(){
    this.options = {};
    
    this.addOption = function(optionid, func){
        $(optionid).click(onoption(this, optionid));
        
        this.options[optionid] = func;
    },
    this.selectOption = function(optionid){
        for(var id in this.options){
            $(id).removeClass("selected");
        }
        
        $(optionid).addClass("selected");
        
        this.options[optionid]();
    };
    
    //Vars and private methods
    var onoption = function(_app, keyoption){
        return function(e){
            _app.selectOption(keyoption);
        };
    };
}