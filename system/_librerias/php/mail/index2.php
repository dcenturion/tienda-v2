<?php 
$destinatario = "dancen_15@hotmail.com"; 
$asunto = "Este mensaje de prueba"; 
$cuerpo = ' 
<html> 
<head> 
<title>Prueba de correo electronico</title> 
</head> 
<body> 
<h1>Hola amigos!</h1> 
<p> 
<b>Correo electrónico de prueba</b>
</p> 
</body> 
</html> 
'; 

//Envío en formato HTML 
$headers = "MIME-Version: 1.0\r\n"; 
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

//Dirección del remitente 
$headers .= "From: Pepito < pepito@mydomain.com>\r\n"; 

//Dirección de respuesta (Puede ser una diferente a la de pepito@mydomain.com)
$headers .= "Reply-To: mariano@mydomain.com\r\n"; 

//Ruta del mensaje desde origen a destino 
$headers .= "Return-path: holahola@mydomain.com\r\n"; 

//direcciones que recibián copia 
$headers .= "Cc: maria@mydomain.com\r\n"; 

//Direcciones que recibirán copia oculta 
$headers .= "Bcc: pepe@pepe.com, juan@juan.com\r\n"; 

mail($destinatario,$asunto,$cuerpo,$headers) 
?>