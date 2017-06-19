/** este archivo trabaja con owleditorial **/
function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    if (response.status === 'connected') {
      testAPI();
    } else if (response.status === 'not_authorized') {
      console.log('Please log into this app.');
    } else {
      console.log("Please log into Facebook.");
    }
  }

  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });

      FB.init({
	    appId      : '1641921439386431',
	    cookie     : true, 
	                       
	    xfbml      : true, 
	    version    : 'v2.2'
	  });

  }



  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/es_ES/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));


  function testAPI() {
    FB.api('/me', function(response) {
      if(response.email!=undefined){
        var codalmacen 	= $('#codAlmacen').val();
        var codid 		= $('#codId').val();
        var urlempresa 	= $('#urlEmpresa').val();

        var nombre 	= response.name ;
        var res 	= nombre.split(" ");
        var lng 	= res.length - 1  ;

        var apellidos 	= '';
        var nombres 	= '';

        for (i = 0; i <= lng  ; i++) {

          if(i<lng){
            apellidos=apellidos+res[i]+' ';
          }else{
          nombres=res[i];
          }
          
        }
        $("[name='Nombres']")	.val(nombres+' '+apellidos);
        $("[name='Usuario']")	.val(response.email);
        $("[name='Contrasena']").val('pass123'); 			// pass por defecto
        /*
        console.log('codid:'+codid+' codalmacen:'+codalmacen+' urlempresa:'+urlempresa);
        console.log('email:'+response.email);
        console.log('nombres:'+nombres);
        console.log('apellidos:'+apellidos);*/
        /**# Este método debe tener la entidad que os está llamando*/
          fya_enviaFormAjax();
        //enviaFormS('{"sUrl":"https://archivos.owlgroup.org/system/epub/fya.php?metodo=usuarios&transaccion=INSERT&CodAlmacen='+codalmacen+'&docid='+codid+'&urlEmpresa='+urlempresa+'&lgnfb=1","formid":"Form_form_new_usuario_b","sDivCon":"formularioreg","sIdCierra":""}');
        
      	
      }
    });
  }




























/*fyupanquia*/
//10.09.15 update