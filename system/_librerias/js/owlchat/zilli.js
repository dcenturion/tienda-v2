/* Forms Tools */
function mkFormPopUp(IdFormPopUp, btn_close_display) {
    var FormPP = _SQS.id(IdFormPopUp);
    if (FormPP) {
        clog("El Formularion PopUp que desea crear ya existe...");
        return FormPP;
    }
    //FPP: Form PopUp
    var attrElem = {
        "id": IdFormPopUp
    };
    var attrStyle = {
        "background-color": "rgba(0,0,0,0.5)",
        "display": "none",
        "min-height": "100%",
        "position": "absolute",
        "top": "0em",
        "left": "0em",
        "width": "100%",
        "z-index": "1000"
    };
    var FPU = _SQE.mk("div", attrElem, attrStyle);

    var attrStyleContent = {
        "background-color": "#FFF",
        "display": "block",
        "position": "relative",
        "top": "5em",
        "left": "14em",
        "width": "1000px",
        "z-index": "1001"
    };
    var contentFPU = _SQE.mk("div", {"id": IdFormPopUp + "-F"}, attrStyleContent);

    var attrStyleBtnClose = {
        "background-color": "#006484",
        "color": "#FFF",
        "cursor": "pointer",
        "padding": "0.5em 0.8em",
        "position": "absolute",
        "top": "-0.8em",
        "right": "-0.8em",
        "z-index": "1003"
    };

    var btnCloseFPU = _SQE.mk("div", null, attrStyleBtnClose);
    btnCloseFPU.innerHTML = "Cerrar";
    btnCloseFPU.onclick = function () {
        if ($) {
            $("#" + this.parentNode.parentNode.id).fadeOut(1000);
        } else {
            _SQE.delElemet(this.parentNode.parentNode);
        }
    };

    var attrElemResponse = {
        "id": IdFormPopUp + "-FPP"
    };
    var responseFPU = _SQE.mk("div", attrElemResponse);

    if (btn_close_display) {
        _SQE.addChild(contentFPU, btnCloseFPU);
    }
    _SQE.addChild(contentFPU, responseFPU);
    _SQE.addChild(FPU, contentFPU);

    var body = _SQS.tag("body")[0];
    _SQE.addChild(body, FPU);
    return FPU;
}

function showFormPopUp(IdFormPopUp, url, btn_close_display) {
    if (typeof IdFormPopUp === "string") {
        IdFormPopUp = mkFormPopUp(IdFormPopUp, btn_close_display);
    }
    if (IdFormPopUp) {
        if ($) {
            $("#" + IdFormPopUp.id + "-F").css({
                "top": (document.body.scrollTop + 40) + "px",
                "left": ((document.body.scrollWidth - $("#" + IdFormPopUp.id + "-F").width()) / 2) + "px"
            });
            $("#" + IdFormPopUp.id).fadeIn(1000);
        } else {
            var attrStyle = {
                "display": "block"
            };
            _SQE.addStyleAttributes(IdFormPopUp, attrStyle);
        }
        AjaxGET(url, IdFormPopUp.id + "-FPP");
    } else {
        clog("El Formulario PopUp con Id " + IdFormPopUp.id + " no existe...");
    }
}

/* Funciones de seleccion de elementos */
//SQS: SonQo Selection -- Seleccion de Elementos
var _SQS = {
    class: function (className, parentNode) {
        if (!parentNode) {
            parentNode = document;
        }
        return parentNode.getElementsByClassName(className);
    },
    each: function (Elements, callback) {
        for (var i = 0; i < Elements.length; i++) {
            callback(Elements[i], i);
        }
    },
    id: function (idElement) {
        return document.getElementById(idElement);
    },
    tag: function (tagName, parentNode) {
        if (!parentNode) {
            parentNode = document;
        }
        return parentNode.getElementsByTagName(tagName);
    },
    search: function (tagName, attributes, parentNode) {
        if (!parentNode) {
            parentNode = document;
        }
        
        var nodes = _SQS.tag(tagName, parentNode),
        resp_nodes = [];
        
        _SQS.each(nodes, function(node){
            for (var attr in attributes) {
                var rgx = new RegExp(attributes[attr]);
                
                if (node[attr].match(rgx)) {
                    resp_nodes.push(node);
                }
            }
        });
        
        if (resp_nodes.length) {
            console.log("Elementos encontrados: " + resp_nodes.length);
            return resp_nodes;
        } else {
            console.log("No se encontraron elementos");
            return null;
        }
    }
};
// SQE: SonQo Elements -- Manejo de Elementos 
var _SQE = {
    addAttributes: function (Element, attributesElement) {
        for (var attr in attributesElement) {
            Element.setAttribute(attr, attributesElement[attr]);
        }
    },
    addChild: function (Element, childElement) {
        Element.appendChild(childElement);
    },
    addClass: function (Element, className) {
        if (!_SQE.classExists(Element, className)) {
            Element.className += " " + className;
        }
    },
    addStyleAttributes: function (Element, styleAttributesElement) {
        for (var attr in styleAttributesElement) {
            Element.style[attr] = styleAttributesElement[attr];
        }
    },
    classExists: function (Element, className) {
        return Element.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
    },
    delElemet: function (Element) {
        Element.parentNode.removeChild(Element);
    },
    insertAfter: function (Element, newElement) {
        if (Element.nextSibling) {
            Element.parentNode.insertBefore(newElement, Element.nextSibling);
        } else {
            Element.parentNode.appendChild(newElement);
        }
    },
    insertBefore: function (Element, newElement) {
        Element.parentNode.insertBefore(newElement, Element);
    },
    mk: function (TagName, attributesElement, styleAttributesElement) {
        var element = document.createElement(TagName);
        if (attributesElement) {
            _SQE.addAttributes(element, attributesElement);
        }
        if (styleAttributesElement) {
            _SQE.addStyleAttributes(element, styleAttributesElement);
        }
        return element;
    },
    removeClass: function (Element, className) {
        if (_SQE.classExists(Element, className)) {
            var exp = new RegExp('(\\s|^)' + className + '(\\s|$)');
            Element.className = Element.className.replace(exp, "");
        }
    }
};

/**
 * 
 * Request asynchronous js file
 *
 * @param  {string} file
 * @param  {function} callback
 * 
 * @return {void}
 */

function include(file, callback) {
    if (!file) {
        return;
    }

    if (!callback) {
        callback = function () {};
    }

    var scripts = _SQS.search("script", { src : file });

    if(scripts){
        callback();
        return;
    }

    var head = window.document.head;
    var script = _SQE.mk("script");

    script.src = file;
    script.type = 'text/javascript';
    script.onload = callback;
    //IE
    script.onreadystatechange = function () {
        if (this.readyState === 'complete') {
            callback();
        }
    };

    head.appendChild(script);
}

/*-------------------------------
 
    ZILLI.JS
 
    Extension plugin for jQuery
 
    @author Todd Francis
    @version 0.1

 -------------------------------*/

(function ($) {

    'use strict';
    $.id = function (id) {
        return $("#" + id);
    };
}(jQuery));