<?php
// Implementamos la librería de Culqi
require_once dirname(__FILE__) . '/../../../lib/culqi.php';
// Implementamos la librería de validación de Culqi
require 'culqiValidar.php';


try {
    // Se recibe la respuesta (información de la venta) cifrada a través de una petición POST
    $llaveCifrada = $_POST['informacionDeVentaCifrada'];
    // Se descifra la llave
    $datosDeVentaRealizada = Culqi::descifrar($llaveCifrada, true);
    // Se convierte en array los datos de la venta
    $datosDeVentaRealizada = json_decode($datosDeVentaRealizada);
    // Se determina la fecha y hora de la venta
    $fecha = date('d-m-Y, g:i:s a');
    // Se muestra la vista de venta realizada
    require '../views/ventaRealizada.php';

} catch (InvalidParamsException $e) {
    // En caso de error, se muestra el mensaje
    echo $e->getMessage()."\n";
}
