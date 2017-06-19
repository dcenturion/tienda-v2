/*
 * This file require AjaxZilli.js
 * This file require zilli.js
 * This file require Jquery.js
 */
function Login(settings){
    var f = this,
    url_request = settings.urlRequest,
    url_captcha = settings.urlImg,
    label = {
        title           : "Herramienta de desarrollo",
        subtitle        : "",
        lbluser         : "Usuario",
        lblpassword     : "Contraseña",
        lblcaptcha      : "Ingrese las letras en blanco",
        btnsign         : "Iniciar Sesión"
    };
    //w3-btn w3-padding w3-teal
    f.formbox = _SQS.id(settings.id),
    f.node = {
          head        : _SQE.mk("div", {"class":"sysdc-container sysdc-teal","style":"text-align:center"}),
                title       : _SQE.mk("h2"),
            form     : _SQE.mk("form", {"class":"sysdc-container sysdc-card-4","action": "javascript:void(null);"}),
              
                subtitle    : _SQE.mk("h4"),
                headmessage : _SQE.mk("div", {"class":"sysdc-container sysdc-red","style":"text-align:center"}),
                    message     : _SQE.mk("h6"),
                lbluser     : _SQE.mk("label" ,{"class":"sysdc-text-grey"}),
                espacio     : _SQE.mk("p"),
                espacio1     : _SQE.mk("p"),
                espacio2     : _SQE.mk("p"),
                espacio3     : _SQE.mk("p"),
                espacio4     : _SQE.mk("p"),
                txtuser     : _SQE.mk("input", {"class":"sysdc-input", "type":"text", "name":"user"}),
                lblpassword : _SQE.mk("label" ,{"class":"sysdc-text-grey"}),
                txtpassword : _SQE.mk("input", {"class":"sysdc-input","type":"password", "name":"password"}),
                img         : _SQE.mk("img"),
                lblcaptcha  : _SQE.mk("label" ,{"class":"sysdc-text-grey"}),
                txtcaptcha  : _SQE.mk("input", {"class":"sysdc-input","type":"text", "name":"captcha"}),
                btnsign     : _SQE.mk("input", {"class":"sysdc-btn sysdc-padding sysdc-teal","type":"submit", "value": label.btnsign,"style":"width:100%"})
    };
    
    //Render
    $(f.node.headmessage).hide();
    $(f.node.img).hide();
    $(f.node.lblcaptcha).hide();
    $(f.node.txtcaptcha).hide();
    
    $(f.node.title).text(label.title);
    $(f.node.subtitle).text(label.subtitle);
    $(f.node.lbluser).text(label.lbluser);
    $(f.node.lblpassword).text(label.lblpassword);
    $(f.node.lblcaptcha).text(label.lblcaptcha);
    
 //   _SQE.addChild(f.node.form, f.node.head);
    _SQE.addChild(f.node.head, f.node.title);
    _SQE.addChild(f.node.form, f.node.subtitle);
    _SQE.addChild(f.node.form, f.node.headmessage);
    _SQE.addChild(f.node.headmessage, f.node.message);
    _SQE.addChild(f.node.form, f.node.lbluser);    
    _SQE.addChild(f.node.form, f.node.txtuser);
    _SQE.addChild(f.node.form, f.node.espacio1);
    _SQE.addChild(f.node.form, f.node.lblpassword);
    _SQE.addChild(f.node.form, f.node.txtpassword);
    _SQE.addChild(f.node.form, f.node.espacio2);
    _SQE.addChild(f.node.form, f.node.img);
    _SQE.addChild(f.node.form, f.node.espacio3);
    _SQE.addChild(f.node.form, f.node.lblcaptcha);
    _SQE.addChild(f.node.form, f.node.espacio4);
    _SQE.addChild(f.node.form, f.node.txtcaptcha);
    _SQE.addChild(f.node.form, f.node.espacio);
    _SQE.addChild(f.node.espacio, f.node.btnsign);
   // _SQE.addChild(f.node.form, f.node.btnsign);

    _SQE.addChild(f.formbox, f.node.head);
    _SQE.addChild(f.formbox, f.node.form);
    
    //Events
    f.node.btnsign.onclick = function(){
        f.send();
    };
    
    //Methods
    f.send = function(){
        if(!url_request){
            console.log("You don't established the url of request");
            return;
        }
        
        var form = f.node.form,
        elements = form.elements,
        param_string = "";
        
        _SQS.each(elements, function(elem){
            var value, success = false;
            
            switch (elem.type) {
                case "text":
                case "password":
                    value = elem.value;
                    success = true;
                    break;
            }
            
            if(success){
                value = value.replace(/'/g, '"').replace(/&/g, " ");
                param_string += "&" + elem.name + "=" + encodeURI(value);
            }
        });
   
        AjaxPOST_TEXT(url_request, param_string, function(responseText){
			        console.log(responseText);
            var data = JSON.parse(responseText);
         
            if(data.success){
                window.location.href = data.url_redirect; 
            }else{
               
                if(data.message != 'Ingrese su usuario'){
                    $(f.node.headmessage).text(data.message);
                    $(f.node.headmessage).fadeIn(1000);
                              
                }
                
                if(data.err){
                    console.log(data.err);
                }
                
                if(data.captcha){
                    $(f.node.txtpassword).val("");
                    
                    f.node.img.src = url_captcha;
                    $(f.node.img).show();
                    $(f.node.lblcaptcha).show();
                    $(f.node.txtcaptcha).show();
                }
            }
        });
    };
    
    f.send();
};
