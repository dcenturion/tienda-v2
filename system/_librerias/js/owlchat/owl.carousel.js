/* 
 * Require jquery
 * Require jquery.contentcarousel
 *  
 @author Aaron Nuñez
 @version 0.0.1
 */

(function ($, window) {

    'use strict';

    /**
     * Main OwlCarousel Class
     *
     * @param {Object} settings
     */
    $.OwlCarousel = function (settings) {
        //CONSTANTS
        var c = this,
        defaults = {
            // Callbacks
            afterClose: function () {},
            beforeClose: function () {},
            //Params
            items: [
                {
                    imageURI        : "imageURI",
                    title           : "title",
                    subtitle        : "subtitle",
                    description     : "description",
                    color           : "color",
                    redirectURI     : "subtitle"
                }
            ]
        };
        
        //VARS
        c.d = $.extend(true, {}, defaults, settings),
        c.items = [];
        
        //METHODS
        c.addItem = function(objectItem){
            var oi = objectItem,
            item = {
                node : {
                    item                        : $("<div>").attr({"class":"ca-item"}),
                        main                    : $("<div>").attr({"class":"ca-item-main"}),
                            mImg                : $("<img>").attr({"src":oi.imageURI}),
                            mTitle              : $("<h3>").text(oi.title),
                            mSubtitle           : $("<h4>"),
                                sQuote          : $("<span>").attr({"class":"ca-quote"}).html("&ldquo;"),
                                sText           : $("<span>").text(oi.subtitle),
                            mMore               : $("<a>").attr({"class":"ca-more","href":"#"}).text("Leer más..."),
                        wrapper                 : $("<div>").attr({"class":"ca-content-wrapper"}),
                            wContent            : $("<div>").attr({"class":"ca-content"}),
                                cTitle          : $("<h6>").text(oi.title),
                                cClose          : $("<a>").attr({"class":"ca-close","href":"#"}).html("<i class='icon-remove-sign'></i>"),
                                cContent        : $("<div>").attr({"class":"ca-content-text"}).text(oi.description),
                                cOption         : $("<div>").attr({"class":"ca-content-control"}),
                                    oRegister   : $("<div>").attr({"class":"option"}).text("Matricularse")
                }
            };
            
            //Render Node
            $(item.node.item)
            .append(item.node.main)
            .append(item.node.wrapper);
            $(item.node.main)
            .append(item.node.mImg)
            .append(item.node.mTitle)
            .append(item.node.mSubtitle)
            .append(item.node.mMore);
            $(item.node.wrapper)
            .append(item.node.wContent);
            $(item.node.mSubtitle)
            .append(item.node.sQuote)
            .append(item.node.sText);
            $(item.node.wContent)
            .append(item.node.cTitle)
            .append(item.node.cClose)
            .append(item.node.cContent)
            .append(item.node.cOption);
            $(item.node.cOption)
            .append(item.node.oRegister);
            
            //Add item to array of items
            c.items.push(item);
        },
        c.setContainer = function(containerId){
            var content = $("<div>").attr({"class":"ca-wrapper"});
            
            c.items.forEach(function(item){
                content.append(item.node.item);
            });
            
            $(containerId).addClass("ca-container").append(content);
            $(containerId).contentcarousel();
        };
        
        //PRIVATE METHODS
        function isEmpty(obj) {
            for(var prop in obj) {
                if(obj.hasOwnProperty(prop))
                    return false;
            }

            return true;
        }
        
        //FOREACH ITEMS
        c.d.items.forEach(function(item){
            c.addItem(item);
        });
    };
}(jQuery, window));
//extend d