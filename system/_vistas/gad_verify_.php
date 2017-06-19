<?php
session_start();
error_reporting( E_ERROR );

/* Este archivo representa un terminal que indica si una sesión de 
 * administrador o de alumno a sido iniciado
 * a comparación con el archivo gad_verify.php, este no controla master por que 
 * no necesita saber si la sesión de master fue iniciada o se cerró
*/

$room_access = $_SESSION["room_access"];
$administrator_access = $_SESSION["administrator_access"];

$success = $room_access || $administrator_access;

echo json_encode($success);