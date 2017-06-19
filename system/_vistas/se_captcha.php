<?php
session_start();
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');

error_reporting(E_ERROR);

$enlace = "_vistas/se_login.php";
$cnPDO = PDOConnection();


$titulo = tituloBtnPn('<span>VERFICA LOS DATOS</span>
<p>  </p>', $btn, 'auto', 'TituloALMB');
			
echo '  
<div class="panelMsj" id="panelMsj" >
    <div class="panelMsj panelCentralMsj alert-success">
	 <a href="#" class="btn_cerrar" onclick=panelAdm(\'panelMsj\',\'Cierra\'); >x</a>
     '.$titulo.'
	 <div>
	 <p>El usuarios y contraseña que has ingresado no son válidos.<BR> !! VUELVE A INTENTAR!!<img src="./_imagenes/jaguar.jpg"> <p>
	 
	 </div>
    </div>
</div>';

?>
