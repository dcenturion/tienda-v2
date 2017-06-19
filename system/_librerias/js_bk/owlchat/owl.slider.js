/*
 * This file require Jquery.js
 */
function Slider(settings){
    //Private vars
    var s = this,
    defaults = {
        //Params
        container : null,
        numElements: 5
    },
    sliderElements = [],
    backSliderElements = [],
    indexNode = null,
    index = null,
    ONPREV = 0,
    ONNEXT = 1;
    
    s.d = $.extend(true, {}, defaults, settings),
    s.addSliderElement = function(sliderElement){
        var se = sliderElement;
        
        sliderElements.push(se);
        backSliderElements = sliderElements;
        
        s.list();
    },
    s.list = function(){
        //empty index and owlslider
        s.node.index.html(null);
        
        indexNode = [],
        index = 0;
        
        if(backSliderElements.length){
            backSliderElements.forEach(function(sliderElement, i){
                var se = sliderElement,
                percentWidth = 100 / s.d.numElements;
                
                //set the with percent, it is here because i can change the s.d.numElements
                se.node.element.css("width", percentWidth + "%");
                
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
        }else{
            s.node.control.parent().hide();
            
            console.log("There aren't elements...");
        }
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
        index               : $("<div>").attr({"class":"owlslider-index"}),
        btnSearch              : $("<div>").attr({"class":"search"}),
        txtSearch              : $("<div>").attr({"class":"txt-search"}),
            text            : $("<input>").attr({"type":"text", "placeholder":"Buscar..."})
    };
    
    //render
    s.d.container
    .append(s.node.owlslider)
    .append(s.node.control)
    .append(s.node.index)
    .append(s.node.btnSearch)
    .append(s.node.txtSearch);
    s.node.control
    .append(s.node.optionprev)
    .append(s.node.optionnext);
    s.node.txtSearch
    .append(s.node.text);
    
    //Applying settings
    s.node.optionprev.html("<i class='icon-chevron-left'></i>");
    s.node.optionnext.html("<i class='icon-chevron-right'></i>");
    s.node.btnSearch.html("<i class='icon-search'></i>");
    
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
    //btnSearch CLICK EVENT
    s.node.btnSearch.click(function(){
        var displayData = $(this).attr("data-display");
        
        if(displayData){
            $(this).removeAttr("data-display");
            s.node.txtSearch.fadeOut();
        }else{
            $(this).attr("data-display", true);
            s.node.txtSearch.fadeIn();
        }
    });
    //txtSearch KEYUP EVENT
    s.node.text.on("keyup", function(){
        var textMatch = $(this).val().toLowerCase(),
        rgx = new RegExp(textMatch, "g");
        
        backSliderElements = [];

        sliderElements.forEach(function(SliderElement){
            var se = SliderElement,
            lowerTitle = se.d.title.toLowerCase();
            
            //Mejorar el algoritmo para listar los slider elements
            if(lowerTitle.match(rgx)){
                backSliderElements.push(se);
                
                se.node.element.show();
            }else{
                se.node.element.hide();
            }
        });
        
        s.list();
    });
}

function SliderElement(settings){
    //Private vars
    var e = this,
    defaults = {
        //Params
        title       : "defaultTitle",
        imageURI    : "http://vidalassociats.cat/wp-content/uploads/empresa2.jpeg"
    },
    sliderOptions = [],
    onclick = function(){};
    
    e.d = $.extend(true, {}, defaults, settings),
    e.addSliderOption = function(sliderOption){
        sliderOptions.push(sliderOption);
    },
    e.setView = function(viewMode){
        switch(viewMode){
            case "course":
                //Don't show image
                e.node.element.css("height", "12em");
                //Modify css class presentation of title node
                e.node.title.addClass("course-mode");
                //Change position to bar and content
                e.node.bar.insertBefore(e.node.text).html("<i class='icon-tasks'></i>");
                break;
        }
    },
    e.click = function(callback){
        onclick = callback;
    },
    e.node = {
        element             : $("<div>").attr({"class":"element"}),
            desc            : $("<div>").attr({"class":"desc","style": e.d.style}),
                pic         : $("<div>").attr({"class":"pic"}),
                    img     : $("<img>").attr({"src":e.d.imageURI}),
                title       : $("<div>").attr({"class":"title"}),
                    text    : $("<div>").attr({"class":"text","style": e.d.styleText}).text(e.d.title),
                    bar     : $("<div>").attr({"class":"bar"})
    };
    
    //render
    e.node.element
    .append(e.node.desc);
    e.node.desc
    .append(e.node.pic)
    .append(e.node.title);
    e.node.pic
    .append(e.node.img);
    e.node.title
    .append(e.node.text)
    .append(e.node.bar);
    
    //Applying settings
    if(!e.d.imageURI){
        e.setView("course");
    }
    
    //element CLICK EVENT
    e.node.element.click(function(){
        if(!sliderOptions.length){
            onclick();
            return;
        }else if(sliderOptions.length === 1){
            window.open(sliderOptions[sliderOptions.length - 1].node.title.attr("href"));
            return;
        }
        
        //Show slider options in a popup
        var nodeOption = 
        $("<div>")
        .attr({"class":"adcourse"})
        .append(
            $("<div>")
            .attr({"class":"title"})
            .text(e.d.title)
        )
        .append(
            $("<div>")
            .attr({"class":"subtitle"})
            .text("Mis Cursos:")
        );
        
        sliderOptions.forEach(function(so, index){
            nodeOption.append(so.node.option);
            
            if(!((index + 1) % 3)){
                nodeOption.append($("<div>").attr({"class":"clear"}));
            }
        });
        
        //Clear the float left when finally the process to append options
        nodeOption.append($("<div>").attr({"class":"clear"}));

        (new $.Popup({ 
            content : $("<div>").append(nodeOption)
        })).open();
    });
}

function SliderOption(settings){
    var o = this,
    defaults = {
        //Params
        title       : "defaultTitle",
        description : "defaultDescription",
        href        : "defaultURL"
    };
    
    o.d = $.extend(true, {}, defaults, settings),
    o.node = {
        option          : $("<div>").attr({"class":"option", "title":o.d.title}),
            title       : $("<a>").attr({"class":"title", "href":o.d.href}),
            // $('#link_other a').attr('target', '_blank');
            description : $("<div>").attr({"class":"description"})
    };
    
    //Applying settings
    o.node.title.text(o.d.title);
    o.node.description.text(o.d.description);
    
    //render
    o.node.option
    .append(o.node.title)
    .append(o.node.description);
}
