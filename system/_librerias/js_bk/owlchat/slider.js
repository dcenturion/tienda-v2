/*
 * This file require Jquery.js
 */
function Slider(settings){
    //Private vars
    var s = this,
    defaults = {
        //Params
        container : null,
        numElements: 4
    },
    sliderElements = [],
    backSliderElements = null,
    indexNode = null,
    index = null,
    ONPREV = 0,
    ONNEXT = 1;
    
    s.d = $.extend(true, {}, defaults, settings),
    s.addSliderElement = function(sliderElement){
        var se = sliderElement,
        percentWidth = 100 / s.d.numElements;

        //set the with percent
        se.node.element.css("width", percentWidth + "%");
        
        sliderElements.push(se);
        backSliderElements = sliderElements;
        
        s.list();
    },
    s.list = function(){
        //empty index
        s.node.index.html(null);
        s.node.owlslider.html(null);
        
        indexNode = [],
        index = 0;
        
        backSliderElements.forEach(function(sliderElement, i){
            var se = sliderElement;
            
            //Add sliderElement's node
            s.node.owlslider.append(se.node.element);
            
            var mod = i % s.d.numElements;
            var res = i / s.d.numElements;
            
            if(!mod){
                //create node
                var node = $("<div>").attr({"class":"index"}).text(res + 1);
                
                node.click(function(){
                    index = res;
                    
                    s.selectIndex(res);
                });
                
                //Add node to indexNode
                s.node.index.append(node);
                indexNode.push(node);
            }
        });
        
        s.selectIndex(0);
    },
    s.move = function(movement){
        if(movement === ONNEXT){
            var limit = indexNode.length - 1;
            
            if(index === limit){
                return;
            }
            
            index++;
        }else if(movement === ONPREV){
            if(!index){
                return;
            }
            
            index--;
        }
        
        s.selectIndex(index);
    },
    s.selectIndex = function(index){
        //remove class selected in all items and set in selected item
        $.each(indexNode, function(i, jQueryObject){
            if(index === i){
                jQueryObject.addClass("selected");
            }else{
                jQueryObject.removeClass("selected");
            }
        });
        
        var begin = index * s.d.numElements,
        final = begin + s.d.numElements;
        
        backSliderElements.forEach( function(sliderElement, index){
            var se = sliderElement;
            
            if(index >= begin && index < final){
                if(index === final - 1 ){
                    se.node.element.css({"margin-right":"0em"});
                }else{
                    se.node.element.css({"margin-right":""});
                }
                
                se.node.element.fadeIn(500);
            }else{
                se.node.element.hide();
            }
        });
    },
    s.node = {
        owlslider           : $("<div>").attr({"class":"owlslider"}),
        control             : $("<div>").attr({"class":"owlslider-control"}),
            optionprev      : $("<div>").attr({"class":"option prev"}),
            optionnext      : $("<div>").attr({"class":"option next"}),
        index               : $("<div>").attr({"class":"owlslider-index"})
    };
    
    //render
    s.d.container
    .append(s.node.owlslider)
    .append(s.node.control)
    .append(s.node.index);
    s.node.control
    .append(s.node.optionprev)
    .append(s.node.optionnext);
    
    //Applying settings
    s.node.optionprev.html("<i class='icon-chevron-left'></i>");
    s.node.optionnext.html("<i class='icon-chevron-right'></i>");
    
    //PRIVATE VARS AND METHODS
    var onnoption = function(movement){
        return function(){
            s.move(movement);
        };
    };
    
    //PREV ELEMENT BOOTON
    s.node.optionprev.click(onnoption(ONPREV));
    //NEXT ELEMENT BOOTON
    s.node.optionnext.click(onnoption(ONNEXT));
}

function SliderElement(settings){
    //Private vars
    var e = this,
    defaults = {
        //Params
        title       : "defaultTitle",
        description : "defaultDescription",
        time        : "anyTime",
        imageURI    : "http://vidalassociats.cat/wp-content/uploads/empresa2.jpeg"
    };
    
    e.d = $.extend(true, {}, defaults, settings),
    e.node = {
        element             : $("<div>").attr({"class":"element"}),
            pic             : $("<div>").attr({"class":"pic"}),
                img         : $("<img>").attr({"src":e.d.imageURI}),
            desc            : $("<div>").attr({"class":"desc"}),
                title       : $("<div>").attr({"class":"title"}).text(e.d.title),
                    bar     : $("<div>").attr({"class":"bar"}),
                description : $("<div>").attr({"class":"description"}),
                footer      : $("<div>").attr({"class":"footer"}),
                    time    : $("<div>").attr({"class":"info-time"}),
                    button  : $("<a>").attr({"class":"button"})
    };
    
    //render
    e.node.element
    .append(e.node.pic)
    .append(e.node.desc);
    e.node.pic
    .append(e.node.img);
    e.node.desc
    .append(e.node.title)
    .append(e.node.description)
    .append(e.node.footer);
    e.node.title
    .append(e.node.bar);
    e.node.footer
    .append(e.node.time)
    .append(e.node.button);
    
    //Applying settings
    e.node.button.text("Ver más");
    e.node.time.html("<i class='icon-time'></i> " + e.d.time);
    //control de length of the description string
    e.d.description = (e.d.description.length > 190)? e.d.description.substring(0, 190) + "..." : e.d.description;
    e.node.description.text(e.d.description);
    //for clear float right of button
    e.node.button.parent().append("<div class='clear'></div>");
}