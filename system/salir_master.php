<?php
session_start();
require_once('_librerias/php/funciones.php');
require_once('_librerias/php/conexiones.php');

error_reporting(E_ERROR);

$vConex = conexSys();

//Delete the unique session var
unset($_SESSION["master_access"])

?>
<script>
    window.location.href = "/system/admin_master.php";
</script>