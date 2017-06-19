<?php
session_start();
//import libraries
require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/conexiones.php');
require_once("../_librerias/php/class/owl-login.php");
error_reporting(E_ERROR);
$vConex = conexSys();
$login = new Login();
$login->setURLredirect("/system/master.php");

$login->execute(function($user, $password) {
    global $login, $vConex;

    $password = _crypt($password);

    $Q_U = "
    SELECT 
    AdminCod, 
    Nombres 
    FROM administradores
    WHERE Usuario = '{$user}' 
    AND Contrasena = '{$password}'";
    
    $ObjU = fetchOne($Q_U, $vConex);
    
    if ($ObjU) {
        $_SESSION["master_access"] = $ObjU->AdminCod;
        $login->success();
    } else {
        $login->breaker("El usuario y/o contraseña son inválidos");
    }
});
