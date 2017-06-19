/*
 * This file require Jquery.js
 */
function PanelBox(settings){
    var p = this,
    defaults = {
        //Params
        container: null
    },
    options = {};
    
    p.d = $.extend(true, {}, defaults, settings),
    p.select = function(key){
        var option = options[key];
        
        if(option.selected){
            return;
        }
        
        $.each(options, function(__key, option){
            option.nodePanel.hide();
            option.nodeNav.removeClass("selected");
            option.selected = false;
        });
        
        option.nodePanel.fadeIn(1000);
        option.nodeNav.addClass("selected");
        option.selected = true;
        option.callback();
    },
    p.addOption = function(settings){
        var defaults = {
            //Params
            key         : "defaultKey",
            title       : "defaultTitle",
            callback    : function(){}
        },
        merge = $.extend(true, {}, defaults, settings);

        //create node option to nav
        var nodeNav = $("<div>")
        .attr({"class":"option"})
        .text(merge.title)
        .click(function(){
            p.select(merge.key);
        });
        
        p.node.nav.append(nodeNav);
        
        //create node panel to container
        var panelNode = $("<div>").attr({"class":"content", "id":merge.key});
        
        p.d.container.append(panelNode);
        
        options[merge.key] = {
            callback    : merge.callback,
            nodeNav     : nodeNav,
            nodePanel   : panelNode,
            selected    : false
        };
    },
    p.node = {
        nav     : $("<div>").attr({"class":"nav"})
    };
    
    p.d.container.html("");
    
    //render
    p.d.container.append(p.node.nav);
}