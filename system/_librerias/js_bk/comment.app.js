var emailCheck = undefined;
function CommentApp(settings){
    //Private vars
    var comentdelete = [];
    var c = this,
    defaults = {
        //Params
        id      : null,
        course  : null,
        program : null,
        socket  : null,
        perfil  : null,
        email   : null,
        storage : []
    };
    c.subcomments = {};
    c.Iddelete = [];
    
    //Class vars
    c.d = $.extend(true, {}, defaults, settings);

    c.n = renderContainer('mainContainer');
    
    if(c.d.perfil !== 'Alumno' && c.d.perfil !== ""){
        //Render Node
        $(c.n.contain)
        .append(c.n.title)
        .append(c.n.admin)
        .append(c.n.buttonConf)
        .append(
            c.n.form
            .append(c.n.input)
            .append(c.n.contain_id)
            .append(c.n.button)
        ).append(c.n.buttonDelete)
        .append(c.n.body);    
    }else{
            //Render Node
        $(c.n.contain)
        .append(c.n.title)
        .append(c.n.admin)        
        .append(
            c.n.form
            .append(c.n.input)
            .append(c.n.contain_id)
            .append(c.n.button)
        ).append(c.n.body);
    }
    



    if(c.d.perfil !== 'Alumno' && c.d.perfil !== ""){
        var emailControl = {
            titulo          : $('<span>').attr({"class":"description"}).text('Enviar Emails'),
            container       : $('<div>').attr({ class: 'onoffswitch' }),
                input       : $('<input>').attr({type: 'checkbox', name: 'onoffswitch', class: 'onoffswitch-checkbox', id: 'myonoffswitch'}),
                label       : $('<label>').attr({class: 'onoffswitch-label', for: "myonoffswitch"}),
                    inner   : $('<span>').attr({class: 'onoffswitch-inner'}),
                    cambio  : $('<span>').attr({class: 'onoffswitch-switch'})
        }
        if(c.d.email){
            emailControl.input.prop("checked", true);
        }
        if(emailCheck === false || emailCheck === true){
            emailControl.input.prop("checked", emailCheck);
        }
        c.n.admin
            .append(emailControl.titulo)
            .append(
                emailControl.container
                    .append(emailControl.input)
                    .append(
                        emailControl.label
                            .append(emailControl.inner)
                            .append(emailControl.cambio)
                        )
            )

        c.n.admin.change(function(e){
            AjaxGET_TEXT('./_vistas/gad_comentario.php?Comentario=changeStateEmail&AlmacenCurso='+c.d.course+'&AlmacenPrograma='+c.d.program, function(a){
                emailCheck = a === 'true';
            });
        })
    }

    //set data
    c.n.input.val(localStorage["comment" + "-" + c.d.program + "-" + c.d.course]);
    
    //Applying settings
    $(c.d.id).append(c.n.contain);

    //upload_coment

    window.onloand = multiplayer(c.d.course,c.d.program);

    /*
    $.get("/system/_vistas/gad_comentario.php?Comentario=GenerarUpload&AlmacenCurso="+c.d.course+"&AlmacenPrograma="+c.d.program, function(token){
        // Initialize upload
        var upload = new Upload({
            id      : "#upload_coment",
            name    : "uploadFile",
            token   : token
        });
        upload.open();
    }, "json");
*/
    
    //Set events
    // c.n.save.click(function(e){
    //     e.preventDefault();
    //     localStorage.comentario = c.n.input.val();
    // });
    
    c.n.input.change(function(e){
        localStorage["comment" + "-" + c.d.program + "-" + c.d.course] = c.n.input.val();
    });


    c.n.buttonConf.click(function(e){
       
        $(".node-eliminar").css("display","block");
        //node-eliminar","id":"delete-"+data.commentId}).html("<input type=checkbox id="+data.commentId+" >").css("display","none"),
    });

    c.n.buttonDelete.click(function(e){      
       
       $('input[type=checkbox]:checked').each(function(i,val){
             
             if($(this).val()>0){
                c.Iddelete.push($(this).val());                                        
             }             
        });
        c.d.socket.emit("deletecomment", {
            deletecoment : c.Iddelete                      
        });       
        
        $(".node-eliminar").css("display","none");

    });


    c.n.button.click(function(e){
         
        e.preventDefault();

        var message = c.n.input.val().trim();
        var archivo = document.getElementsByName("uploadFile")[0].value;

        if(!message){
            return;
        }
        
        //Disable submit button
        c.n.button.attr("disabled", true);
        if(c.d.perfil !== 'Alumno' && c.d.perfil !== ""){
            AjaxPOST_TEXT('./_vistas/gad_comentario.php?Comentario=sendEmail&AlmacenCurso='+c.d.course+'&AlmacenPrograma='+c.d.program, '&Comentario='+message+'&archivo='+archivo, function(){});
        }else{
            AjaxPOST_TEXT('./_vistas/gad_comentario.php?Comentario=sendEmailCoordinador&AlmacenCurso='+c.d.course+'&AlmacenPrograma='+c.d.program, '&Comentario='+message+'&archivo='+archivo, function(){});
        }

          c.n.input.val(null);
                $("#upload_coment").empty();
                $.get("/system/_vistas/gad_comentario.php?Comentario=GenerarUpload&AlmacenCurso="+c.d.course+"&AlmacenPrograma="+c.d.program, function(token){
                // Initialize upload      
                    var upload = new Upload({
                        id      : "#upload_coment",
                        name    : "uploadFile",
                        token   : token
                    });
                upload.open();
               // upload.deleteNameFile(archivo);        
                }, "json");

        c.d.socket.emit("comment", {
            message : message,
            archivo : archivo
        });
        
      
        localStorage["comment" + "-" + c.d.program + "-" + c.d.course] = "";
    });


  
    c.d.storage.forEach(function(doc){
       
        var comm = new Comment(Object.assign({}, doc, { commentApp: c }));             
               
        c.n.body.append(comm.n.comment);
      
              
    });

    
    // //Class methods
    // c.init = function(){
    //     c.d.socket.emit("init", {
    //         student : c.d.student,    
    //         course  : c.d.course,    
    //         program : c.d.program    
    //     });
    // };
    
    // //Initializing app
    // c.init();
}

function multiplayer(course,program){

    return  $.get("/system/_vistas/gad_comentario.php?Comentario=GenerarUpload&AlmacenCurso="+course+"&AlmacenPrograma="+program, function(token){
        
                var upload = new Upload({
                    id      : "#upload_coment",
                    name    : "uploadFile",
                    token   : token
                });
            upload.open();
            }, "json");

}

function Comment(settings){
    var c = this;
    c.d = settings;
    c.n = renderComment({
        commentId: c.d.commentId,
        date: c.d.date,
        message: c.d.message,
        archivo: c.d.archivo,
        perfil: c.d.perfil,
        courseId: c.d.commentApp.d.course
    });
    //Render Node
    $(c.n.comment)
        .append(
            c.n.body
            .append(
                c.n.photo
                .append(c.n.img)
            )
            .append(
                c.n.detail
                .append(c.n.name)
                .append(c.n.span)
                .append(c.n.date)
                .append(c.n.message)
                .append(c.n.eliminar)
                .append(c.n.cont_arch
                    .append(c.n.ruta
                        .append(c.n.archivo)))
            )
            .append(c.n.clear)
            .append(c.n.content)
        );

    if(!c.d.leido){
        var count = 0;
        $(c.n.comment).toggleClass('no-leido');
        setTimeout(function(){
            $(c.n.comment).toggleClass('no-leido');
        }, 1500)
    }

    var clicked = false;

    // c.n.option.click(function(){
    //     if(clicked){
    //         c.n.content.html('');
    //         clicked = false;
    //     }else{
    //         var subcomment = new SubCommentApp({
    //             content     : c.n.content,
    //             commentId   : c.d.commentId,
    //             socket      : commentSocket
    //         });
    //         clicked = true;
    //         c.d.commentApp.subcomments[c.d.commentId] = subcomment;
    //     }
    // }); 


    //Applying settings
    //Set the nameuser of comment
    var fullname = c.d.studentFullName.toUpperCase();

    if(fullname.length > 20){
        fullname = fullname.substring(0, 20)  + "...";
    }

    c.n.name.text(fullname);
};

function SubCommentApp(settings){
    //Private vars
    var c = this,
    defaults = {
        //Params
        content     : null,
        commentId   : null,
        socket      : null
    };
    
    //Class vars
    c.d = $.extend(true, {}, defaults, settings),
    c.n = renderContainer('subComment');
    
    //Render Node
    $(c.n.contain)
    .append(
        c.n.form
        .append(c.n.input)
        .append(c.n.button)
    )
    .append(c.n.body);
    
    //Applying settings
    c.d.content.append(c.n.contain);
    
    //Set events
    c.n.button.click(function(e){
        e.preventDefault();
        
        var message = c.n.input.val().trim();
        
        if(!message){
            return;
        }
        
        //Disable submit button
        c.n.button.attr("disabled", true);
        
        c.d.socket.emit("subcomment", {
            message     : message,
            commentId   : c.d.commentId
        });
        
        c.n.input.val(null);
    });

    
    //Class methods
    c.init = function(){
        c.d.socket.emit("initSubcomment", c.d.commentId);
    };
    
    //Initializing app
    c.init();
}

function renderComment(data){ 
    //Verify if message contains url
    var regexURI = /((http(s)?:\/\/)?((\w|\d|\-)+\.){1,}\w{2,}(\/(\w|\d|\-|\/)+)*(\w|\d|\.)+(\?)?(\w|\d|\u002d|\/|\.|\.|\&|=|#|:|\+|%)+)/g;
    data.message = data.message.replace(regexURI, '<a target="_blank" href=\'$1\'>$1</a>');
    if (data.archivo != '') {
        adjunto = "Archivo adjunto <i class='icon-download-alt'></i>";
    }else{
         adjunto = "";
    }
    return {
        comment             : $("<div>").attr({"class":"comment", "id": "comment-"+data.commentId}),
            option          : $("<span>").attr({"class":"text-comment"}).text("Responder"),
            body            : $("<div>").attr({"class":"body"}),
                photo       : $("<div>").attr({"class":"photo"}),
                    img     : $("<img>").attr({"src":"/system/_imagenes/icon_user.png"}),
                detail      : $("<div>").attr({"class":"detail"}),
                    name    : $("<div>").attr({"class":"name"}),
                    span    : $("<span>").attr({"class": "perfil"}).text(data.perfil === "Alumno" ? "" : data.perfil),
                    date    : $("<span>").attr({"class":"date"}).text(" " + prettyDate(data.date)),
                    message : $("<div>").attr({"class":"message"}).html(data.message),
                    eliminar : $("<div>").attr({"class":"node-eliminar","id":"delete"}).html("<input type=checkbox id=delete value="+data.commentId+" >").css("display","none"),
                    archivo : adjunto,
                    cont_arch : $("<div>").attr({"class":"archivo_contain"}),
                    ruta    : $("<a>").attr({"href":"https://d2mv2wiw5k8g3l.cloudfront.net/ArchivosEmpresa/file-comment/CU-" + data.courseId + "/" + data.archivo}).attr({"title":data.archivo}), 
                content     : $("<div>").attr({"class": "subcomment"}),
                clear       : $("<div>").css({"clear":"both"})
    }
}

function renderContainer(tipo){

    var general = {
        contain : $("<div>").attr({"class":"comment-app"}),
        form    : $("<form>"),
        admin   : $("<div>").attr({"class":"separate"}),
        input   : $("<textarea>")
    }

    var inpt = document.createElement('input');
        inpt.type="file";

    var hidden = document.createElement('input');
        hidden.type="hidden";
        hidden.name="upload_coment";

    if(tipo === 'mainContainer'){
       return {
            contain         : general.contain,
                title       : $("<div>").attr({"class":"title-comment"}).text("Participa, Debate y Consulta"),
                admin       : general.admin,
                form        : general.form,
                input       : general.input,
                button      : $("<button>").text("Enviar"),
                buttonConf  : $("<div>").attr({"class":"no-config"}).html("<i class=icon-cogs></i>"),
                buttonDelete  : $("<button>").attr({"class":"node-eliminar"}).text("Eliminar").css("display","none"),
                body        : $("<div>").attr({"class":"body"}),
                
                hidden      :hidden,
                inpt        :inpt,
                contain_id     :$("<div>").attr({"id":"upload_coment"}),
                contain_class     :$("<div>").attr({"class":"upload"}),
                contain_upload_contet     :$("<div>").attr({"class":"upload-content"}),
                contain_item_control     :$("<div>").attr({"class":"item control"}).css({"display":"block"}),
                contain_clear     :$("<div>").css({"clear":"both"}),
                contain_detail     :$("<div>").attr({"class":"upload-detail"}),
                contain_body     :$("<div>").attr({"class":"body"})
        }
    }else if(tipo === 'subComment'){
        return {
            contain         : general.contain,
                body        : $("<div>").attr({"class":"body"}),
                form        : general.form,
                    input   : general.input,
                    button  : $("<button>").text("Responder a comentario")
        }
    }
    
}