var fb = {
    // This is called with the results from from FB.getLoginStatus().
    statusChangeCallback: function (response) {
        if (response.status === 'connected') {
            AjaxGET_TEXT("/system/_vistas/se_login.php?Login=fb&urlid=" + window.urlid, function(response){
                var data = JSON.parse(response);
                
                if(data.success){
                    if(data.options){
                        AjaxGET("/system/_vistas/se_login.php?Form=form_option&urlid=" + window.urlid, window.form);
                    }else{
                        window.location.href = data.url_redirect;
                    }
                }else{
                    console.log(data.message);
                }
            });
        }else{
            //User no authorized login
        }
    },
    checkLoginState: function () {
        FB.login(function (response) {
            fb.statusChangeCallback(response);
        });
    },
    init : function(){
        include("//connect.facebook.net/en_US/sdk.js", function(){
            FB.init({
                appId   : '896547983768673',
                cookie  : true,
                xfbml   : true,
                version : 'v2.4'
            });
            
            fb.checkLoginState();
        });
    }
};

//FB.api('/me?fields=email,name', function (response){});