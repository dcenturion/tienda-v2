<!DOCTYPE html> 
<html lang='es'>
    <head>
        <title>Herramienta</title>
        <meta charset='UTF-8'>
        <script type='text/javascript' src='/system/_librerias/js/jquery-2.1.1.min.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/owlchat/zilli.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/owlchat/AjaxZilli.js'></script>
        <script type='text/javascript' src='/system/_librerias/js/owl-login.js'></script>
        <!--<link href="/system/_estilos/owl-login.css" rel="stylesheet" type="text/css">-->
        <link rel="stylesheet" href="/system/_estilos/temporary.css" rel="stylesheet" type="text/css">
        <style>
            .form{
                /*min-width: 500px;*/
                /*width: 25%;*/
                    padding-top: 5em;
                    min-width: 27em;
                    width: 25%;
                    margin: 0 auto;
            }
        </style>
    </head>
    <body>
        <div class=" sysdc-container form " id="form"></div>
        <script>
            var login = new Login({
                id          : "form",
                urlRequest  : "/system/_vistas/se_login_master.php",
                urlImg      : "/system/_imagenes/captcha.php"
            });
        </script>
    </body>
</html>