/*
 * This file require Jquery.js
 */

function getImagePresentation(filename){
    var fileExts = ["docx", "doc", "xls", "xlsx", "ppt", "pptx", "mp3", "pdf"],
    fileExtension = filename.split(".").pop().toLowerCase(),
    imageName = "file_icon.png",
    index = fileExts.indexOf(fileExtension);

    switch(index){
        case 0:
        case 1:
            imageName = "word_icon.png";
            break;
        case 2:
        case 3:
            imageName = "excel_icon.png";
            break;
        case 4:
        case 5:
            imageName = "ppt_icon.png";
            break;
        case 6:
            imageName = "mp3_icon.png";
            break;
        case 7:
            imageName = "pdf_icon.png";
            break;
    }

    return "/system/_imagenes/" + imageName;
}

function chatBox(receiver, transmitter){
    //CONSTANTS
    var c = this;
    
    //Events
    var onfocus = function(){},
    onclose = function(){};
    
    // Private vars
    var newMessageAnimationInterval = null,
    newMessageAnimationStatus = true;
    
    //PUBLIC VARS
    c.transmitter = transmitter,
    c.receiver = receiver,
    c.chat_id = null,
    c.messageBoxes = [],
    c.node = null,
    c.focused = false,
    c.showed = false,
    c.disableRecoveryOption = false;
    c.isPreparedToUpload = false,
    c.filesToUpload = [];
    
    //PUBLIC METHODS
    c.addMessageBox = function(messagebox, init){
        if(init){
            c.messageBoxes.splice(0, 0, messagebox);
            $(messagebox.node.messagebox).insertAfter(c.node.cControl);
            
            //Hide date details
            $(".m_d_date", c.node.content).each(function(){
                $(this).hide();
            });
        }else{
            //Hide date details
            $(".m_d_date", c.node.content).each(function(){
                $(this).hide();
            });
            
            c.messageBoxes.push(messagebox);
            $(c.node.content).append(messagebox.node.messagebox);
            
            if(!c.focused && c.showed && messagebox.orentation === messagebox.MESSAGE_OUT){
                c.setNewMessageAnimation();
            }
        }
        
        var dateLocaleStringOptions = {weekday: "long", year: "numeric", month: "long", day: "numeric", hour: "numeric", minute: "numeric", hour12:"false"},
        dateString = (new Date(messagebox.date_sent)).toLocaleString("es-ES", dateLocaleStringOptions);
        
        //Add tooltip
        tooltip($(messagebox.node.img_message), $(messagebox.node.messagebox), dateString);
        
        messagebox.onLoad = function(){
            c.scroller();
        };
    },
    c.appendTo = function(panel){
        $(c.node.chatbox).appendTo(panel);
    },
    c.setNewMessageAnimation = function(){
        if(newMessageAnimationInterval){
            c.clearNewMessageAnimation();
        }
        
        newMessageAnimationInterval = setInterval(function(){
            if(newMessageAnimationStatus){
                newMessageAnimationStatus = false;
                
                c.node.chatbox.addClass("blink");
            }else{
                newMessageAnimationStatus = true;
                
                c.node.chatbox.removeClass("blink");
            }
        }, 1000);
    },
    c.clearNewMessageAnimation = function(){
        clearInterval(newMessageAnimationInterval);
        
        c.node.chatbox.removeClass("blink");
    },
    c.hide = function(){
        $(c.node.chatbox)
        .removeAttr("data-showed")
        .hide();
        
        c.showed = false;
    },
    c.minimize = function(){
        $(c.node.chatbox).attr("data-minimize", true);
        $(c.node.body).hide();
    },
    c.maximize = function(){
        $(c.node.chatbox).removeAttr("data-minimize");
        $(c.node.body).show();
    },
    c.scroller = function(){
        $(c.node.content).animate({"scrollTop": c.node.content[0].scrollHeight}, "fast");
    },
    c.setRightPosition = function(right){
        $(c.node.chatbox).css({"right": right});
    },
    c.show = function(){
        $(c.node.chatbox)
        .attr("data-showed", true)
        .show("fast");
        
        c.showed = true;
    },
    c.close = function(callback){
        onclose = callback;
        
        return c;
    },
    c.onsendmessage = function(){},
    c.onrecoverymessages = function(){},
    c.focus = function(callback){
        onfocus = callback;
        
        return c;
    };
    
    // Private methods
    var prepareFilesToUpload = function(files){
        if(files.length){
            if(files.length === 1){
                var file = files[0];
                file.isImage = false;
                
                try{
                    var extensionFounded = imagesPermitted.find(function(extension) {
                        return mime[ file.type ].extensions.find(function(ext) {
                            return ext === extension;
                        });
                    });
                    
                    file.isImage = typeof extensionFounded === "string";
                }catch(e){
                    // Error ocurred when mime type isn't in mimedb
                }
                
                c.isPreparedToUpload = true;
                c.node.cUploadPreview.show();
                c.node.cUploadPreview.find(".upload-preview-control").show();
                c.node.cUploadPreview.find(".upload-preview-image").show();

                c.filesToUpload = [];
                c.filesToUpload.push(file);
                
                if(file.isImage){
                    c.node.cUploadPreview.find(".upload-preview-message-box").hide();
                    
                    var oReader = new FileReader();

                    oReader.onload = function(e){
                        c.node.cUploadPreview.find(".upload-preview-image img").attr("src", e.target.result);
                    };

                    oReader.readAsDataURL(file);
                }else{
                    var imagePresentation = getImagePresentation(file.name);
                    
                    c.node.cUploadPreview.find(".upload-preview-message-box").text(file.name).show();
                    c.node.cUploadPreview.find(".upload-preview-image img").attr("src", imagePresentation);
                }
            }else{
                c.node.cUploadPreview.show();
                c.node.cUploadPreview.find(".upload-preview-message-box").text("No se han podido adjuntar los archivos, debes adjuntarlos uno por uno").show();
                setTimeout(function(){ 
                    if(!c.isPreparedToUpload){
                        c.node.cUploadPreview.hide();
                    }
                    c.node.cUploadPreview.find(".upload-preview-message-box").hide();
                }, 5000);
            }
        }
    },
    tooltip = function ($hoveredNode, $relativeNode, tooltipInformation){
        var n = {
            tooltip     : $("<div>").attr("class", "tooltip"),
                arrow   : $("<div>").attr("class", "arrow"),
                content : $("<div>").attr("class", "content").text(tooltipInformation)
        };

        //Render node
        n.tooltip
        .append(n.arrow)
        .append(n.content);

        n.tooltip.insertAfter(c.node.chatbox);

        $hoveredNode.hover(function(){
            n.tooltip.show();
            
            var xPosition = $relativeNode.offset().left,
            yPosition = $relativeNode.offset().top;

            n.tooltip
            .css({
                right   : $(window).width() - xPosition + 10,
                top     : yPosition
            });
        }, function(){
            n.tooltip.hide();
        });
    };
    
    c.node = {
        chatbox                     : $("<div>").attr({"class":"chatbox"}),
            header                  : $("<div>").attr({"class":"header"}),
                title               : $("<div>").attr({"class":"title"}),
                    indicator       : $("<div>").attr({"class":"indicator"}),
                    tText           : $("<div>").attr({"class":"text"}),
                hControl            : $("<div>").attr({"class":"control"}),
                    oClose          : $("<a>").attr({"class":"option", href:"javascript:void(0)", title: "Cerrar chat"}),
                    oSettings       : $("<a>").attr({"class":"option", href:"javascript:void(0)", title: "Opciones"}),
                clear               : $("<div>").attr({"class":"clear"}),
            body                    : $("<div>").attr({"class":"body"}),
                content             : $("<div>").attr({"class":"content"}).css('height', '250px'),
                    cControl        : $("<div>").attr({"class":"control"}),
                        oRecovery   : $("<div>").attr({"class":"option"}),
                cUploadMessage      : $("<div>").attr({"class":"upload-message"}),
                cUploadPreview      : $("<div>").attr({"class":"upload-preview"}),
                input               : $("<div>").attr({"class":"input"}),
                    iInputFile      : $("<input>").attr({type: "file"}),
                    iText           : $("<textarea>").attr({"class":"text emoji", "contenteditable":"true"}),
                    iUploadButton   : $("<a>").attr({href:"javascript:void(0)"}).html("<img src='/system/_librerias/js/emoticons/basic/button-file.png'>"),
                    iPhotoButton    : $("<a>").attr({href:"javascript:void(0)"}).html("<img src='/system/_librerias/js/emoticons/basic/button-photo.png'>"),
                    iLikeButton     : $("<a>").attr({href:"javascript:void(0)"}).css({"float": "right"}).html("<img src='/system/_librerias/js/emoticons/basic/button-like.svg'>")
    };

    //Renderizando el control de usuario
    var chatbox_title = c.receiver.name + " " + c.receiver.last_name;
    chatbox_title = chatbox_title.substring(0, 22);
    chatbox_title += (chatbox_title.length >= 22)? "..." : "";

    $(c.node.tText).text(chatbox_title);
    $(c.node.oClose).html("<i class='icon-remove'></i>");
    $(c.node.oSettings).html("<i class='icon-cog'></i>");
    $(c.node.oRecovery).text("Recuperar mensajes...");
    
    // Add suboptions
    c.node.oSettings.append("\
    <div class='dropdown-list'>\
        <a href='javascript:void(0)'><div class='dropdown-option'>Añadir contacto al chat...</div></a>\
        <a href='javascript:void(0)'><div class='dropdown-option'>Añadir archivos</div></a>\
    </div>");
    
    //Evaluando el estado del contacto para el chatbox
    if(!receiver.connected){
        $(c.node.indicator).hide();
    }

    $(c.node.header)
    .append(c.node.title)
    .append(c.node.hControl)
    .append(c.node.clear);
    $(c.node.body)
    .append(c.node.cUploadMessage)
    .append(c.node.cUploadPreview)
    .append(c.node.content)
    .append(c.node.input);
    $(c.node.content)
    .append(c.node.cControl);
    $(c.node.input)
    .append(c.node.iInputFile)
    .append(c.node.iText)
    .append(c.node.iUploadButton)
    .append(c.node.iPhotoButton)
    .append(c.node.iLikeButton);
    $(c.node.title)
    .append(c.node.indicator);
    $(c.node.title)
    .append(c.node.tText);
    $(c.node.hControl)
//    .append(c.node.oSettings)
    .append(c.node.oClose);
    $(c.node.cControl)
    .append(c.node.oRecovery);
    $(c.node.chatbox)
    .append(c.node.header);
    $(c.node.chatbox)
    .append(c.node.body);

    
    c.node.events = function() {
//        var initialHeightText = c.node.iText.outerHeight(true);
        var initialHeightText = c.node.iText.get(0).scrollHeight;
        var initialHeightContent = c.node.content.outerHeight(true);
        
        c.node.iText.on("input cut drop paste", function(){
//            var height = c.node.iText.outerHeight(true) + c.node.iText.scrollTop();
            var height = c.node.iText.get(0).scrollHeight;
            
            if(c.node.iText.val().trim()){
                if(height < 70){
                    c.node.content.css("height", initialHeightContent - ( height - initialHeightText));
                    c.node.iText.css("height", height);
                }
            }else{
                c.node.content.css("height", initialHeightContent);
                c.node.iText.css("height", initialHeightText);
            }
        });
        c.node.iText.on("keydown", function(e){
            var keyCode = e.keyCode || e.which;
            
            if(keyCode === 13 && !e.shiftKey){
                c.node.content.css("height", initialHeightContent);
                c.node.iText.css("height", initialHeightText);
            }
        });
    };

    // Setting upload message html content
    c.node.cUploadMessage.html("\
    <div class='upload-message-icon-content'>\
        <div class='upload-message-icon'><i class='icon-upload-alt'></i></div>\
    </div>\
    <div class='upload-message-box'>Suelta los archivos para poder subirlos de inmediato y enviarlos</div>\
    ");
    
    // Setting upload preview html content
    c.node.cUploadPreview.html("\
    <div class='upload-preview-control'>\
        <div class='upload-preview-control-option close'><i class='icon-remove'></i></div>\
    </div>\
    <div class='upload-preview-image'>\
        <img alt='image result'>\
        <div class='upload-preview-button'><i class='icon-ok-sign'></i></div>\
    </div>\
    <div class='upload-preview-message-box'>Default message</div>\
    ");
    
    //MAXIMIZE - MINIMIZE CHATBOX
    $(c.node.title).click(function(){
        if($(c.node.chatbox).attr("data-minimize")){
            c.maximize();
        }else{
            c.minimize();
        }
    });
    //HIDE CHATBOX
    $(c.node.oClose).click(function(){
        //Clear animation interval
        c.clearNewMessageAnimation();
        
        onclose();
    });
    //DROPDOWN OPTIONS
    $(c.node.oSettings).click(function(){
        console.log("click");
    });
    //SENDMESSAGE CHATBOX
    $(c.node.iText)
    .on("keydown", function(e){
        switch(true){
            case e.keyCode === 13 && !e.shiftKey:
                e.preventDefault();
                
                var message = $(this).val();

                if(message.trim()){
                    $(this).val(null);

                    c.onsendmessage(message);
                }
                break;
        }
    })
    .focus(function(){
        //Change focused status
        c.focused = true;
        
        //Clear animation interval
        c.clearNewMessageAnimation();
        
        onfocus();
    })
    .blur(function(e){
        //Change focused status
        c.focused = false;
    });
    //RECOVERY OLD MESSAGES
    $(c.node.oRecovery).click(function(){
        c.onrecoverymessages();
    });
    //SET EVENT TO SCROLL FOR SHOW RECOVERY OLD MESSAGES
    $(c.node.content).scroll(function(){
        if(this.scrollTop === 0 &&  this.scrollHeight > (this.offsetHeight * 2) && c.disableRecoveryOption === false){
            $(c.node.oRecovery).fadeIn(500);
        }else{
            $(c.node.oRecovery).hide();
        }
    });
    // SET EVENT TO UPLOAD FILES
    c.node.iUploadButton.click(function(){
        c.node.iInputFile.removeAttr("accept").click();
    });
    // SET EVENT TO UPLOAD IMAGES
    c.node.iPhotoButton.click(function(){
        c.node.iInputFile.attr("accept", "image/*").click();
    });
    // SET EVENT TO SEND LIKE
    c.node.iLikeButton.click(function(){
        window.socket.emit("message", {
            "receiver"      : c.receiver.email,
            "transmitter"   : c.transmitter.email,
            "message"       : "(Y)",
            "type"          : "text"
        });
    });
    // SET EVENT TO CATCH FILE IN INPUT FILE
    c.node.iInputFile.on("change", function(e){
        var files = this.files;
        
        prepareFilesToUpload(files);
    });
    // SET EVENT TO CATCH FILES DROPPED
    $(c.node.cUploadMessage).on("dragleave", function(e){
        $(c.node.cUploadMessage).hide();
    });
    
    $(c.node.body).on("drag dragend dragover dragenter dragleave drop", function(e){
        // dragstart isn't included
        e.preventDefault();
        e.stopPropagation();
    })
    .on("dragover dragenter", function(e){
        $(c.node.cUploadMessage).show();

        if(c.isPreparedToUpload){
            c.node.cUploadPreview.find(".upload-preview-control .close").click();
        }
    })
    .on("dragend drop mouseleave", function(e){
        // dragleave isn't included because upload message is in front
        $(c.node.cUploadMessage).hide();
    })
    .on("drop", function(e){
        var files = e.originalEvent.dataTransfer.files;
        
        prepareFilesToUpload(files);
    });
    // CLOSE PREVIEW
    c.node.cUploadPreview.find(".upload-preview-control .close").click(function(){
        c.isPreparedToUpload = false;
        c.node.cUploadPreview.hide();
        c.node.cUploadPreview.find(".upload-preview-control").hide();
        c.node.cUploadPreview.find(".upload-preview-image").hide();
    });
    // UPLOAD FILE
    c.node.cUploadPreview.find(".upload-preview-image .upload-preview-button").click(function(){
        c.isPreparedToUpload = false;
        c.node.cUploadPreview.hide();
        c.node.cUploadPreview.find(".upload-preview-control").hide();
        c.node.cUploadPreview.find(".upload-preview-image").hide();
        
        var file = c.filesToUpload.pop();
        
        var messagebox = new messageBox("emptyMessage", c.receiver, c.transmitter);
        messagebox.setOrentation(messagebox.MESSAGE_ON);
        
        var imagePreloader = $("<div>").attr({"class":"image-preloader"});
        var image = $("<img>").css({"opacity":"0.5"});
        
        imagePreloader.append(image);
        
        if(file.isImage){
            var oReader = new FileReader();

            oReader.onload = function(e){
                image.attr("src", e.target.result);
            };

            oReader.readAsDataURL(file);
        }else{
            image.attr("src", getImagePresentation(file.name));
        }
        
        $(messagebox.node.text_message).empty().append(imagePreloader);
        
        c.addMessageBox(messagebox);
        c.scroller();
        
        $.post("/system/_vistas/upload.php", {
            PREPARE_UPLOAD  : true,
            name            : file.name,
            size            : file.size,
            token           : window.tokenToUpload
        }, function(data){
            if(data.success){
                var form = new FormData(),
                xhr = new XMLHttpRequest();

                form.append("file", file);
                form.append("token", window.tokenToUpload);
                
                xhr.onload = function(e){
                    var data = null;
                    
                    try{
                        data = JSON.parse(e.target.responseText);
                        console.log(data);
                    }catch(err){
                        console.log(e.target.responseText);
                    }
                    
                    if(data){
                        if(data.success){
                            messagebox.node.messagebox.remove();
                            
                            var type = data.file.isImage ? "image" : "file";
                            
                            window.socket.emit("message", {
                                "receiver"      : c.receiver.email,
                                "transmitter"   : c.transmitter.email,
                                "message"       : data.file.physicsUri,
                                "type"          : type
                            });
                        }else{
                            messagebox.node.messagebox.remove();
                            
                            //Add error message
                            c.node.cUploadPreview.show();
                            c.node.cUploadPreview.find(".upload-preview-message-box").text(data.message).show();
                            setTimeout(function(){ 
                                if(!c.isPreparedToUpload){
                                    c.node.cUploadPreview.hide();
                                }
                                c.node.cUploadPreview.find(".upload-preview-message-box").hide();
                            }, 5000);
                        }
                    }else{
                        messagebox.node.messagebox.remove();
                        
                        //Add error message
                        c.node.cUploadPreview.show();
                        c.node.cUploadPreview.find(".upload-preview-message-box").text("Ah ocurrido un error al intentar subir el archivo, por favor vuelva a intentarlo...").show();
                        setTimeout(function(){ 
                            if(!c.isPreparedToUpload){
                                c.node.cUploadPreview.hide();
                            }
                            c.node.cUploadPreview.find(".upload-preview-message-box").hide();
                        }, 5000);
                    }
                };
                
                xhr.open("POST", "/system/_vistas/upload.php");
                xhr.send(form);
            }else{
                messagebox.node.messagebox.remove();
                
                //Add error message
                c.node.cUploadPreview.show();
                c.node.cUploadPreview.find(".upload-preview-message-box").text(data.message).show();
                setTimeout(function(){ 
                    if(!c.isPreparedToUpload){
                        c.node.cUploadPreview.hide();
                    }
                    c.node.cUploadPreview.find(".upload-preview-message-box").hide();
                }, 5000);
            }
        }, "json")
        .fail(function(xhr){
            //Fail request
            console.log(xhr.responseText);
        });
    });
    //SET TIMER FOR DATELINKS
    setInterval(function(){
        for(var x in c.messageBoxes){
            var messagebox = c.messageBoxes[x],
            date_sent = messagebox.date_sent;

            if(date_sent){
                var date = new Date(date_sent),
                date_string = prettyDate(date);

                $(messagebox.node.m_d_date).text(date_string);
            }
        }
    }, 1000);
};

function messageBox(message, receiver, transmitter, type){
    //CONSTANTS
    var m = this;
    m.MESSAGE_ON = 1,
    m.MESSAGE_OUT = 0;
    
    m.orentation = m.MESSAGE_OUT;
    
    m.date_sent = new Date(), // String or Date
    m.message = message, // String
    m.type = type === "text" || type === "image" || type === "file" ? type : "text";
    m.transmitter = transmitter, //User Object
    m.receiver = receiver, //User Object
    m.node = null;
    
    //PUBLIC METHODS
    m.setOrentation = function(movement){
        m.orentation = movement;
        
        switch(movement){
            case this.MESSAGE_ON:
                _SQE.addClass(this.node.messagebox, "out");
                break;
            case this.MESSAGE_OUT:
                _SQE.addClass(this.node.messagebox, "in");
                break;
        }
    },
    m.onLoad = function(){};
    
    m.node = {
        messagebox  : _SQE.mk("div", {"class":"messagebox"}),
            img_message : _SQE.mk("img", {"class":"img_message"}),
            arrow       : _SQE.mk("div", {"class":"arrow"}),
            message     : _SQE.mk("div", {"class":"message"}),
                text_message    : _SQE.mk("div", {"class":"text_message"}),
            clear_one   : _SQE.mk("div", {"class":"clear"}),
            message_detail  : _SQE.mk("div", {"class":"message_detail"}),
                m_d_date        : _SQE.mk("div", {"class":"m_d_date"}),
            clear_two   : _SQE.mk("div", {"class":"clear"})
    };
    
    // Setting type message
    if(m.type === "image"){
        var hyperlink = $("<a>").attr({href: m.message, title: m.message});
        var imagePreloader  = $("<div>").attr({"class":"image-preloader"});
        var imageToLoad     = $("<img>").attr({"src": m.message});
        
        hyperlink.append(imagePreloader);
        
        imageToLoad.load(function(){
            imagePreloader.css("backgroundImage", "none").append(imageToLoad);
            
            m.onLoad.call(m);
        });
        
        $(m.node.text_message).empty().append(hyperlink);
    }else if(m.type === "text"){
        // Valide type var
        if(typeof m.message === "string"){
            message = parsemessage(this.message.replace('<', '&lt;').replace('>', '&gt;'));
        }
        
        $(m.node.text_message).html(message);
    }else if(m.type === "file"){
        var filename = m.message.split("/").pop();
        var imagePresentation = getImagePresentation(filename);
        
        var hyperlink  = $("<a>").attr({href: m.message, title: m.message}).html("\
        <div class='predownloader'>\
            <div class='image-icon'><img src='" + imagePresentation + "'></div>\
            <span>" + filename + "</span>\
        </div>\
        ");
        
        $(m.node.text_message).empty().append(hyperlink);
    }
    
    //Renderizando el control de usuario
    $(m.node.img_message).attr({"src":m.transmitter.urlimage});
    $(m.node.m_d_date).text("Hace un momento");
    
    $(m.node.messagebox)
    .append(m.node.img_message)
    .append(m.node.arrow)
    .append(m.node.message)
    .append(m.node.clear_one)
    .append(m.node.message_detail)
    .append(m.node.clear_two);
    $(m.node.message)
    .append(m.node.text_message);
    $(m.node.message_detail)
    .append(m.node.m_d_date);
    
    m.node.messagebox.onclick = function(e){
        // Avoid display data for download file
        if(m.type === "text"){
            var display = $(this).attr("data-display");

            if(display){
                $(this).removeAttr("data-display");
                $(m.node.m_d_date).fadeOut();
            }else{
                $(this).attr("data-display", true);
                $(m.node.m_d_date).fadeIn();
            }
        }
    };
}
