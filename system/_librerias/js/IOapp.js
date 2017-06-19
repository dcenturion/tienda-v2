/*
 * This file require zilli.js
 * This file require socket.io.js
 * This file require Jquery.js
 */
function _IOapp (panelId, chatBoxPanelId, socket, user, users, uiCase){ 
    users = (users)? users : {};
    
    var io = this,
    moreChatBoxId = [],
    connectedUsers = 0;
    
    var notificationPermission,
    iconURI = "//www.owlgroup.org/system/_imagenes/favicon.ico";
    
    //CONSTANTS
    io.USER_ON = 1,
    io.USER_OUT = 0;
    
    //Events
    var onshow = function(){},
    onhide = function(){};
    
    io.panelid = $.id(panelId),
    io.socket = socket,
    io.navOptions = {},
    io.chatboxpanel = $.id(chatBoxPanelId),
    io.user = user,
    io.showed = false,
    io.node = null,
    io.users = {},
    io.chatBoxes = {},
    io.uiCase = (uiCase)? uiCase : "desktop";
    
    //Imbox Panel
    io.addOption = function(key, title, func){
        //Create option to nav
        var option = $("<div>")
        .attr({"class":"option"})
        .html("<i class='icon-chevron-right'></i> " + title)
        .click(function(){
            io.selectOption(key);
        });
        
        $(io.node.nav).append(option);
        
        io.navOptions[key] = { node: option, func: (func)? func : function(){} };
    },
    io.selectOption = function(key, args){
        $(".option", io.node.nav).removeClass("selected");
        $(io.navOptions[key].node).addClass("selected");
        
        io.navOptions[key].func(args);
    };
    
    //Chat
    io.addUser = function(user){
        //movement : Movimiento es si se valida un movimiento entra(on) , sale(out), si no se asigna solo es para asignarlo a la lista
        user.node = createUserNode(user),
        user.connected = false;
        user.showed = true;
        io.users[user.email] = user;
        
        $(user.node.time)
        .attr({"data-time":user.last_date})
        .css({"color":"rgb(81, 81, 81)"});

        $(io.node.uContainer).append(user.node.user);
        
        //Set value to option contact
        io.node.oContactVal.text("(" + Object.keys(io.users).length + ")");
        
        if(io.uiCase === "room" && (([8, 12, 17]).indexOf(user.profile) !== -1)){
            $(user.node.user)
            .insertBefore(io.node.uMessage);
            
            var prefixNode = $("<div>").attr({"class":"profile"});
            console.log(user.profile);
            switch(user.profile){
                case 8:
                    $(user.node.user).css("background-color", "rgba(255, 255, 0, 0.3)");
                    prefixNode.text("Coordinador(a)");
                    break;
                case 12:
                    $(user.node.user).css("background-color", "rgba(86, 255, 0, 0.3)");
                    prefixNode.text("Profesor(a)");
                    break;
                case 17:
                    $(user.node.user).css("background-color", "rgba(0, 94, 255, 0.25)");
                    prefixNode.text("Soporte Técnico(a)");
                    break;
            }

            //Add Cordinator label
            user.node.name.append(prefixNode);
        }
    },
    io.getChatBoxesShowed = function(){
        return $(".chatbox[data-showed=true]", io.chatboxpanel);
    },
    io.listUsers = function(){
        if(!io.socket){
            $(io.node.uMessage).text("El servicio no está disponible.").show();
            return;
        }
        
        //hide message
        $(io.node.uMessage).hide();
        
        var empty = true;
        $.each(io.users, function(x){
            var user = io.users[x];
            
            if(user.showed){
                empty = false;
                $(user.node.user).show();
            }else{
                $(user.node.user).hide();
            }
        });
        
        if(empty){
            $(io.node.uMessage).text("No hay usuarios disponibles").show();
        }
    },
    io.setUserStatus = function(user, movement){
        if(!io.users[user.email]){
            return;
        }
        
        var node = io.users[user.email].node;
        var chat_id = io.users[user.email].chat_id;
        
        if(movement === io.USER_ON){
            connectedUsers++;
            
            io.users[user.email].connected = true;
            
            $(node.time)
            .html("<div class='online_indicator'></div> En línea")
            .css({"color":"rgb(0, 128, 31)"});
            
            if(!(io.uiCase === "room") && !(([8, 12, 17]).indexOf(user.profile) !== -1)){
                //Put user after the uMessage
                $(io.users[user.email].node.user).insertAfter(io.node.uMessage);
            }
            
            if(chat_id){
                $(io.chatBoxes[chat_id].node.online_indicator).css({"display": ""});
            }
        }else if(movement === io.USER_OUT){
            connectedUsers--;
            
            io.users[user.email].connected = false;
            
            $(node.time)
            .attr({"data-time":user.last_date})
            .css({"color":"rgb(81, 81, 81)"});
            
            if(chat_id){
                $(io.chatBoxes[chat_id].node.online_indicator).hide();
            }
        }
        
        io.node.oAvaibleVal.text("(" + connectedUsers + ")");
    },
    io.show = function(callback){
        onshow = callback;
        
        return io;
    },
    io.hide = function(callback){
        onhide = callback;
        
        return io;
    };
    
    //Vars and private methods
    var createUserNode = function(user){
        var node = {
            user                : $("<div>").attr({"class":"user"}),
                photo           : $("<div>").attr({"class":"photo"}),
                    img         : $("<img>").attr({"src": user.urlimage}),
                desc            : $("<div>").attr({"class":"desc"}),
                    name        : $("<div>").attr({"class":"name"}),
                    time        : $("<div>").attr({"class":"time"}),
                    indicator   : $("<div>").attr({"class":"indicator"}),
                    chatIcon    : $("<div>").attr({"class":"chat-icon", "title":"Estas en una conversación"}).html("<i class='icon-comments'></i>")
        };

        $(node.name).text(user.name + " " + user.last_name);
        $(node.indicator).text("1");
        
        $(node.user)
        .append(node.photo)
        .append(node.desc);
        $(node.photo)
        .append(node.img);
        $(node.desc)
        .append(node.name)
        .append(node.time)
        .append(node.indicator)
        .append(node.chatIcon);

        $(node.desc).click(function(){
            var receiver = user.email,
            transmitter = io.user.email;
            
            //apply features when open chat
            node.user.addClass("in-chat");
            node.chatIcon.show();
            
            if(user.chat_id){
                var chat = getchatBox(user);
                
                if(!chat.showed){
                    //handle chatBox
                    handleChatBox(chat);
                    
                    chat.maximize();
                    chat.scroller();
                }
            }else{
                if(io.socket){
                    io.socket.emit("openchatbox", {
                        "transmitter": transmitter,
                        "receiver": receiver
                    });
                }
            }
        });
        
        return node;
    },
    getchatBox = function(user){
        var chat = io.chatBoxes[user.chat_id];
        
        return chat;
    },
    handleChatBox = function(chatBox, out, auxChatId){
        if(out){
            //Chat Hide
            chatBox.hide();
        
            //reoder chatBoxes
            var chatBoxesShowed = io.getChatBoxesShowed();
            
            chatBoxesShowed.each(function(index){
                var margin = index * 265 + 5;

                $(this).css({"right": margin});
            });
            
            if(moreChatBoxId.length){
                if(auxChatId){
                    moreChatBoxId.splice(moreChatBoxId.indexOf(auxChatId) ,1);
                }
                
                var firstChatId = (auxChatId)? auxChatId : moreChatBoxId.shift(),
                auxChatBox = io.chatBoxes[firstChatId];
                
                $("#more-id-" + firstChatId).remove();
                
                handleChatBox(auxChatBox);
                console.log(moreChatBoxId);
            }
        }else{
            //Chat show
            //Calculate margin to new chatBox
            var numChatBoxes = io.getChatBoxesShowed().length,
            margin = numChatBoxes * 265 + 5,
            wChatBoxes = (numChatBoxes + 1) * 265 + 5,
            wChatBoxPanel = $(io.chatboxpanel).width();
            
            if(wChatBoxPanel > wChatBoxes){
                chatBox.setRightPosition(margin);
                chatBox.show();
            }else{
                //If chat isn't showed and don't exist in moreChatBoxId
                if(moreChatBoxId.indexOf(chatBox.chat_id) === -1){ 
                     //Add chatId to Array
                    moreChatBoxId.push(chatBox.chat_id);

                    //Show controlChat
                    $(io.node.chatControl)
                    .css({"right": margin})
                    .show();
                    
                    //Added option to items
                    $("<div>")
                    .attr({"class":"option","id":"more-id-" + chatBox.chat_id})
                    .text(chatBox.receiver.name + " " + chatBox.receiver.last_name)
                    .appendTo(io.node.iChat)
                    .click(function(){
                        var auxChatBox = null;
                        
                        $.each(io.chatBoxes, function(x){
                            var chatBox = io.chatBoxes[x];
                            
                            if(!auxChatBox && chatBox.showed){
                                auxChatBox = chatBox;
                            }
                        });
                        
                        //Close first chatBox where showed is true and show the current chatBox
                        handleChatBox(auxChatBox, true, chatBox.chat_id);
                        
                        //Show again first ChatBox
                        handleChatBox(auxChatBox);
                    });
                }
            }
            
            $(io.node.text)
            .text(moreChatBoxId.length);
            
            if(!moreChatBoxId.length){
                //Hide controlChat
                $(io.node.chatControl).hide();
            }
        }
        
    }, 
    initialTitle = $("title").text(),
    intervalAnimation = null,
    intervalAnimationStatus = true,
    setTitleAnimation = function(title, message){
        if(intervalAnimation){
            clearTitleAnimation();
        }
        
        intervalAnimation = setInterval(function(){
            if(intervalAnimationStatus){
                intervalAnimationStatus = false;
                
                $("title").text(title);
            }else{
                intervalAnimationStatus = true;
                
                $("title").text(initialTitle);
            }
        }, 1500);
        
        //Sound audio
        io.audio.currentTime = 0;
        io.audio.play();
        
        if(typeof message === "string" && message.trim() && notificationPermission){
            var notification = new window.Notification(title, {
                body: message,
                icon: "//www.owlgroup.org/system/_imagenes/favicon.ico"
            });

            setTimeout(function(){
                notification.close();
            }, 5000);
        }
    },
    clearTitleAnimation = function(){
        clearInterval(intervalAnimation);
        
        $("title").text(initialTitle);
    };
    
    io.node = {
        nav                         : $("<div>").attr({"class": "nav"}),
            user                    : $("<div>").attr({"class": "user"}),
                photo               : $("<div>").attr({"class": "photo"}),
                    img             : $("<img>").attr({"src": user.urlimage}),
                desc                : $("<div>").attr({"class": "desc"}),
        imbox                       : $("<div>").attr({"class": "imbox"}),
            panel                   : $("<div>").attr({"class": "panel"}),
                uContent            : $("<div>").attr({"class": "content"}),
                    uInput          : $("<input>").attr({"type":"text", "placeholder": "Buscar..."}),
                    uBody           : $("<div>").attr({"class": "body"}),
                        uContainer  : $("<div>").attr({"class": "container"}),
                            uMessage: $("<div>").attr({"class": "message"}),
                cContent            : $("<div>").attr({"class": "content"}),
                    cBody           : $("<div>").attr({"class": "body"}),
                        cContainer  : $("<div>").attr({"class": "container"}),
                control             : $("<div>").attr({"class": "control"}),
                    oChat           : $("<div>").attr({"class": "option"}),
                        oChatText   : $("<div>").attr({"class": "text"}),
                    oAvaible        : $("<div>").attr({"class": "option"}),
                        oAvaibleText: $("<div>").attr({"class": "text"}),
                        oAvaibleVal : $("<div>").attr({"class": "text"}),
                    oContact        : $("<div>").attr({"class": "option"}),
                        oContactText: $("<div>").attr({"class": "text"}),
                        oContactVal : $("<div>").attr({"class": "text"}),
        bDisplay                    : $("<div>").attr({"class":"display_button"}),
            bText                   : $("<i>").attr({"class":"icon-chevron-left"}),
        //chatBoxControl            
        chatControl                 : $("<div>").attr({"class":"chatbox"}).width("auto"),
            header                  : $("<div>").attr({"class":"header"}),
                title               : $("<div>").attr({"class":"title"}).css({"padding":"0.5em"}),
                    icon            : $("<i>").attr({"class":"icon-comments"}),
                    text            : $("<div>").attr({"class":"text"}).css({"margin-left":"0.5em"}),
            iChat                   : $("<div>").attr({"class":"item"})
    };
    
    //Renderizando el control de usuario
    var title = io.user.name + " " + io.user.last_name;
    title = title.substring(0, 25);
    title += (title.length >= 25)? "..." : "";

    $(io.node.desc).text(title);
    $(io.node.oChatText).text("Chat");
    $(io.node.oAvaibleText).html(" Disponible <i class='icon-chevron-down'></i>");
    $(io.node.oAvaibleVal).html("(0)");
    $(io.node.oContactText).text(" Contactos");
    $(io.node.oContactVal).text("(0)");
    
    $(io.node.nav)
    .append(io.node.user);
    $(io.node.imbox)
    .append(io.node.panel);
    $(io.node.bDisplay)
    .append(io.node.bText);
    $(io.node.user)
    .append(io.node.photo);
    $(io.node.user)
    .append(io.node.desc);
    $(io.node.panel)
    .append(io.node.uContent);
    $(io.node.panel)
    .append(io.node.cContent);
    $(io.node.panel)
    .append(io.node.control);
    $(io.node.uContent)
    .append(io.node.uInput);
    $(io.node.uContent)
    .append(io.node.uBody);
    $(io.node.cContent)
    .append(io.node.cBody);
    $(io.node.control)
    .append(io.node.oChat);
    $(io.node.control)
    .append(io.node.oAvaible);
    $(io.node.control)
    .append(io.node.oContact);
    $(io.node.photo)
    .append(io.node.img);
    $(io.node.uBody)
    .append(io.node.uContainer);
    $(io.node.cBody)
    .append(io.node.cContainer);
    io.node.oChat
    .append(io.node.oChatText);
    io.node.oAvaible
    .append(io.node.oAvaibleVal)
    .append(io.node.oAvaibleText);
    io.node.oContact
    .append(io.node.oContactVal)
    .append(io.node.oContactText);
    io.node.uContainer
    .append(io.node.uMessage);
    
    $(io.panelid)
    .append(io.node.nav);
    $(io.panelid)
    .append(io.node.imbox);
    $(io.panelid)
    .append(io.node.bDisplay);
    //ChatControl
    $(io.node.chatControl)
    .append(io.node.header)
    .append(io.node.iChat);
    $(io.node.header)
    .append(io.node.title);
    $(io.node.title)
    .append(io.node.icon)
    .append(io.node.text);
    $(io.chatboxpanel)
    .append(io.node.chatControl);
    
    //SET FUNCTION TO CHAT CONTROL
    $(io.node.header).click(function(){
        if($(this).attr("data-showed")){
            $(io.node.iChat).hide();
            $(this).removeAttr("data-showed");
        }else{
            $(io.node.iChat).show();
            $(this).attr("data-showed", true);
        }
    });
    
    //Create tagAudio
    io.audio = document.createElement("audio");
    $(io.audio)
    .attr({"type":"audio/mpeg", "src":"/system/_imagenes/audio.mp3"})
    .appendTo("head");
    
    //Configure Notification Permission
    if ("Notification" in window) {
        switch (window.Notification.permission) { //default,granted,denied
            case "default":
                window.Notification.requestPermission(function (permission) {
                    notificationPermission = permission === "granted";
                });
                break;
            case "granted":
                    notificationPermission = true;
                break;
        }
    } else {
        console.warn("Notification object not supported in this browser");
    }
    
    //Add displayed button [left_layout]
    $(io.node.bDisplay).click(function(){
        if($(this).attr("data-display")){
            $(this)
            .removeAttr("data-display")
            .html("<i class='icon-chevron-left'></i>")
            .attr({"style":null});
            
            $(io.panelid).animate({ "width": "20%" });
            
            io.showed  = true;
            
            onshow();
        }else{
            $(this)
            .attr("data-display", true)
            .html("CHAT <i class='icon-comment'></i>")
            .css({"right":"initial", "left":"100%"});

            $(io.panelid).animate({ "width": "0%" });
            
            io.showed  = false;
            
            onhide();
        }
    });
    
    //SET FUNCTION OPTION CHAT
    $(io.node.oChat).click(function(){
        $(".option", io.node.control).removeClass("selected");
        $(this).addClass("selected");
        
        $(io.node.cContent).show();
        $(io.node.uContent).hide();
        
        //Recovery old chats
        if(io.socket){
            io.socket.emit("oldchat");
        }
    });
    
    //SET FUNCTION OPTION AVAIBLE
    $(io.node.oAvaible).click(function(){
        $(".option", io.node.control).removeClass("selected");
        $(this).addClass("selected");
        
        $(io.node.cContent).hide();
        $(io.node.uContent).show();
        
        $.each(io.users, function(x){
            var user = io.users[x];
            user.showed = (user.connected)? true : false;
        });
        
        io.listUsers();
    });
    
    //SET FUNCTION OPTION CONTACT
    $(io.node.oContact).click(function(){
        $(".option", io.node.control).removeClass("selected");
        $(this).addClass("selected");
        
        $(io.node.cContent).hide();
        $(io.node.uContent).show();
        
        $.each(io.users, function(x){
            io.users[x].showed = true;
        });
        
        io.listUsers();
    });

    //USER'S BODY INPUT FUNCTION CONTROL
    $(io.node.uInput).on("keyup", function(){
        var rgx = new RegExp(this.value.toLowerCase(), "g");
        
        $.each(users, function(x){
            var user = io.users[x],
            lowerName = user.name.toLowerCase(),
            lowerLastName = user.last_name.toLowerCase(),
            fullName = lowerName + " " + lowerLastName;
            
            user.showed = (x.toLowerCase().match(rgx) || lowerName.match(rgx) || lowerLastName.match(rgx) || fullName.match(rgx))? true : false;
        });
        
        io.listUsers();
    });

    if(io.socket){
        //Agregando usuarios mandados por el servidor
        $.each(users, function(x){
            io.addUser(users[x], io.USER_ADD);
        });
        
        //SET TIMER FOR DATELINKS
        setInterval(function(){
            $.each(users, function(x){
                var user = users[x];

                if(!user.connected){
                    var data_time = $(user.node.time).attr("data-time"),
                    date = new Date(data_time),
                    date_string = prettyDate(date);

                    $(user.node.time).text(date_string);
                }
            });
        }, 1000);
        
        //SET EVENT TO NEW USER ONLINE
        io.socket.on("update", function(data){
            var user = data.user,
            movement = data.movement;

            io.setUserStatus(user, movement);
        });

        //SET CLOSE CHAT EVENT FOR ME
        io.socket.on("closechatbox", function(data){
            var chatbox = io.chatBoxes[data.chat_id];
            
            handleChatBox(chatbox, true);
        });

        //SET OPEN CHAT EVENT FOR ME
        io.socket.on("openchatbox", function(data){
            var receiver = io.users[data.receiver],
            transmitter = io.user,
            chatboxpanel = io.chatboxpanel,
            chatbox = io.chatBoxes[data.chat_id];
            
            //apply features when open chat
            receiver.node.user.addClass("in-chat");
            receiver.node.chatIcon.show();
            
            if(!chatbox){
                var chat = new chatBox(receiver, transmitter);
                chat.appendTo(chatboxpanel);

                chat.chat_id = data.chat_id;
                receiver.chat_id = chat.chat_id;
                io.chatBoxes[chat.chat_id] = chat;

                //handle chatBox
                handleChatBox(chat);

                //Set onsendmessage event
                chat.onsendmessage = function(message){
                    io.socket.emit("message", {
                        "receiver": this.receiver.email,
                        "transmitter": this.transmitter.email,
                        "message": message
                    });
                };

                //Set onrecoverymessages event
                chat.onrecoverymessages = function(){
                    var chat_id = this.chat_id,
                    quantity = this.messageBoxes.length;

                    io.socket.emit("oldmessage", {
                        "chat_id" : chat_id,
                        "quantity" : quantity
                    });
                };

                chat
                .close(function(){ //Set onclosechatbox event
                    io.socket.emit("closechatbox", {
                        "receiver": chat.receiver.email,
                        "transmitter": chat.transmitter.email
                    });
                    
                    //apply features when close chat
                    receiver.node.user.removeClass("in-chat");
                    receiver.node.chatIcon.hide();
                })
                .focus(function(){
                    clearTitleAnimation();
                });

                console.log("Haz abierto un chatbox con " + receiver.email);
                //Recovery automatically old messages
                $(chat.node.oRecovery).click();

                chat.node.events();
                // Set placeholder after launch events
                chat.node.iText.attr("placeholder", "Escribe un mensaje...");
                
                $(".text.emoji").emojiarea({
                    buttonLabel: '<img class="emoji-button-image" src="/system/_librerias/js/emoticons/basic/button.png" />',
                    wysiwyg: false
                });
                
                // Verifing if chat was open from contact
                if(data.fromContact){
                    setTitleAnimation(receiver.name + " " + receiver.last_name + " te ha enviado un mensaje...");
                    chat.setNewMessageAnimation();
                }
            }
        });

        //SET EVENT TO LISTENING MESSAGES
        io.socket.on("message", function(datamessage){
            var chatbox = io.chatBoxes[datamessage.chat_id];

            if(chatbox){
                var receiver = io.user,
                transmitter = io.users[datamessage.transmitter],
                on = false;

                if(datamessage.transmitter === io.user.email){
                    receiver = io.users[datamessage.receiver];
                    transmitter = io.user;
                    on = true;
                }else{
                    if(!chatbox.focused){
                        setTitleAnimation(transmitter.name + " " + transmitter.last_name + " te ha enviado un mensaje...", datamessage.message);
                    }
                }
                
                var messagebox = new messageBox(datamessage.message, receiver, transmitter, datamessage.type);
                messagebox.setOrentation((on)? messagebox.MESSAGE_ON : messagebox.MESSAGE_OUT);

                chatbox.show();
                chatbox.addMessageBox(messagebox);
                chatbox.scroller();
            }else{
                var receiver = io.users[datamessage.transmitter].email,
                    transmitter = io.user.email;

                io.socket.emit("openchatbox", {
                    "transmitter"   : transmitter,
                    "receiver"      : receiver,
                    "fromContact"   : true
                });
            }
        });

        //SET EVENT TO RECOVERY OLD MESSAGES
        io.socket.on("oldmessage", function(data){
            var chat_id = data.chat_id,
            messages = data.messages.reverse();
            
            var chatbox = io.chatBoxes[chat_id];
            
            if(messages.length){
                $(messages).each(function(){
                    var receiver = io.user,
                    transmitter = io.users[this.transmitter],
                    on = false;

                    if(this.transmitter === io.user.email){
                        receiver = io.users[this.receiver];
                        transmitter = io.user;
                        on = true;
                    }

                    var messagebox = new messageBox(this.message, receiver, transmitter, this.type);
                    messagebox.setOrentation((on)? messagebox.MESSAGE_ON : messagebox.MESSAGE_OUT);
                    messagebox.date_sent = this.date_sent;

                    chatbox.addMessageBox(messagebox, true);
                });
                
                if(chatbox.messageBoxes.length === messages.length){ chatbox.scroller(); }
            }else{
                chatbox.disableRecoveryOption = true;
                chatbox.node.oRecovery.text("No se han encontrado más mensajes...");
                
                setTimeout(function(){
                    chatbox.node.oRecovery.hide();
                }, 5000);
            }
        });

        //SET EVENT TO RECOVERY OLD CHATS
        io.socket.on("oldchat", function(chats){
            $(io.node.cContainer).html(null);

            chats.forEach(function(chat){
                var receiver = io.users[chat.receiver],
                node = createUserNode(receiver);
                $(node.indicator).remove();

                var message_string = (io.user.email === chat.receiver)? "" : "Yo: ";
                message_string += chat.message.message;

                var ms_len = message_string.length;
                message_string = message_string.substring(0, 25);
                message_string += (ms_len > 25)? "..." : "" ;

                $(node.time)
                .addClass("imbox")
                .text(message_string);

                $(io.node.cContainer).append(node.user);
            });
        });
    }
    
    //List users for default
    $(io.node.oContact).click();
};
