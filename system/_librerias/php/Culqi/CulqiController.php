<?php

// Implementamos la librería de Culqi
require_once 'lib/culqi.php';
// Implementamos la librería de validación de Culqi
require 'CulqiValidar.php';
require '../funciones.php';

require_once('../Entitys/Coin.php');
require_once('../Entitys/Proform.php');

$ConexionPDO =  conexOwlPDO();
$conexEmp     = conexEmp();
$FechaHora = FechaHoraSrv();
$MoneySystem = Coin::monetarysystem();


switch ($_POST["method"]) {
  case "processrequest":
          processrequest();
    break;
  case "processresponse":
          processresponse();
    break;
  default:
    # code...
    break;
}

function processrequest(){
  global $MoneySystem,$FechaHora;
      //Se recupera los datos del cliente desde el formulario
    $rsp = ["success"=>false,"data"=>null,"id_error"=>null];

    $sell  = rGTPDO(null,"SELECT Codigo FROM culqi_sells WHERE numero_pedido='".$_POST["id_proform"]."'");

    if(!$sell){

        if( trim($_POST["email"])!=""  && trim($_POST["name"])!=""      &&  trim($_POST["address"])!=""  &&
        trim($_POST["phone"])!=""  && trim($_POST["id_client"])!="" &&  trim($_POST["total"])!=""    && trim($_POST["id_proform"])!="" ){

        if (filter_var($_POST["name"], FILTER_VALIDATE_EMAIL)){
          $_POST["name"] = explode("@", $_POST["name"])[0];
        }

         if ($_POST["lastname"]==""){
          $arrlastname = explode(" ", $_POST["name"]);
          if(count($arrlastname)>1){
            $_POST["name"] = array_shift($arrlastname);
            $_POST["lastname"] = implode(" ", $arrlastname );
          }else{
            $_POST["name"]     = array_shift($arrlastname);
            $_POST["lastname"] = "Unknow";
          }

        }

        $lenname = strlen($_POST["name"]);
        $lenlastname = strlen($_POST["lastname"]);
        $lenphone = strlen($_POST["phone"]);

        if($lenname<2){
          $_POST["name"] = $_POST["name"]." xx";
        }else if($lenname>15){
          $_POST["name"] = substr($_POST["name"], 0, 14);
        }
        if($lenlastname<2){
          $_POST["lastname"] = $_POST["lastname"]." xx";
        }else if($lenlastname>15){
          $_POST["lastname"] = substr($_POST["lastname"], 0, 14);
        }
         if($lenphone<5){
          $_POST["phone"] = $_POST["phone"]."00000";
        }else if($lenphone>15){
          $_POST["phone"] = substr($_POST["phone"], 0, 14);
        }

          $datosDeCliente["ciudad"]             = "Lima";
          $datosDeCliente["cod_pais"]           = "PE";
          $datosDeCliente["apellidos"]          = $_POST["lastname"];
          $datosDeCliente["correo_electronico"] = $_POST["email"];
          $datosDeCliente["direccion"]          = $_POST["address"];
          $datosDeCliente["nombres"]            = $_POST["name"];
          $datosDeCliente["num_tel"]            = $_POST["phone"];


            // Se guarda la información de la venta para enviarla a Culqi
          $datosDeVenta = [];
          /*
          array(
              //'moneda' => 'USD', //Moneda de la venta ("PEN" O "USD")
              //Monto de la venta (ejem: 10.25, no se incluye el punto decimal)
              //Número de pedido de la venta, y debe ser único (de no ser así, recibirá como respuesta un error)
              //'numero_pedido' => CulqiValidar::codigoAleatorio()
          );*/

          if( $MoneySystem->Codigo ==1 )      $datosDeVenta["moneda"] = "PEN";
          else if( $MoneySystem->Codigo ==2 ) $datosDeVenta["moneda"] = "USD";
          else if( $MoneySystem->Codigo ==3 ) $datosDeVenta["moneda"] = "EUR";


          $datosDeVenta["id_usuario_comercio"] = $_POST["id_client"];
          $datosDeVenta["monto"]               = str_replace(".", "", $_POST["total"]);
          //$datosDeVenta["numero_pedido"]       = CulqiValidar::codigoAleatorio();#integración
          $datosDeVenta["numero_pedido"]     = $_POST["id_proform"]; #produccion
          $datosDeVenta["descripcion"]         = 'Compra de Ebook ('.$_POST["id_proform"].')';
          
          // Validamos si los datos del cliente y de la venta cumplen con las restricciones
          //CulqiValidar::validarDatosDeCliente($datosDeCliente);
          //CulqiValidar::validarDatosDeVenta($datosDeVenta);

          // Enviamos los datos de la venta al Servidor de Culqi
          $data = CulqiValidar::crearVenta($datosDeCliente, $datosDeVenta);

          $fields = [
                      "mensaje_respuesta_usuario"=>$data["mensaje_respuesta_usuario"],
                      "monto"=>$data["monto"],
                      "mensaje_respuesta"=>$data["mensaje_respuesta"],
                      "ticket"=>$data["ticket"],
                      "codigo_respuesta"=>$data["codigo_respuesta"],
                      "numero_pedido"=>$data["numero_pedido"],
                      "codigo_comercio"=>$data["codigo_comercio"],
                      "informacion_venta"=>$data["informacion_venta"],
                      "FHCreacion"=>$FechaHora,
                      "FHActualizacion"=>$FechaHora,
                      "CtaSuscripcion"=>$_POST["ctasuscripcion"]
                    ];
          insert('culqi_sells',$fields);

          $rsp = ["success"=>true,"data"=> $data ];
      }

    }else{
      $rsp["id_error"] = 3;
      $_POST["numero_pedido"] = $_POST["id_proform"];
      $rsp["new_proform"]     =  error_process("CANCELADO");
    }
  echo json_encode($rsp);
  exit();
}

function processresponse(){
  global $FechaHora;

  $rsp = ["success"=>false,"message"=>null];

  try {
      // Se recibe la respuesta (información de la venta) cifrada a través de una petición POST
      $llaveCifrada = $_POST['data'];
      // Se descifra la llave
      $data = Culqi::descifrar($llaveCifrada, true);
      $data = json_decode($data);


      if($data==null){
        $case = "CANCELADO";
        $rsp["new_proform"]  =  error_process("CANCELADO");
        $rsp["message"]      = ["mensaje_respuesta_usuario"=>"La transacción ha sido cancelada."];
      }else{

        if($data->codigo_respuesta=="venta_exitosa"){
          $case = "CORRECTO";
          closesell($data); // matricular
          $rsp["success"] = true;
          $rsp["id_proform"] = $data->numero_pedido;
        }
        else{
          $case = "DENEGADO";
          $rsp["new_proform"]  =  error_process($case);
        }
        $rsp["message"]  = ["mensaje_respuesta_usuario"=>$data->mensaje_respuesta_usuario,"mensaje_respuesta"=>$data->mensaje_respuesta];
          try {
                $fields = [ 
                    "id_transaccion"=>"".$data->id_transaccion,
                    "ticket"=>"".$data->ticket,
                    "correo_electronico"=>"".$data->correo_electronico,
                    "numero_tarjeta"=>"".$data->numero_tarjeta,
                    "numero_pedido"=>"".$data->numero_pedido,
                    "pais_emisor"=>"".$data->pais_emisor,
                    "codigo_autorizacion"=>"".$data->codigo_autorizacion,
                    "nombre_emisor"=>"".$data->nombre_emisor,
                    "marca"=>"".$data->marca,
                    "mensaje_respuesta_usuario"=>"".$data->mensaje_respuesta_usuario,
                    "mensaje_respuesta"=>"".$data->mensaje_respuesta,
                    "codigo_respuesta"=>"".$data->codigo_respuesta,
                    "codigo_comercio"=>"".$data->codigo_comercio,
                    "referencia_transaccion"=>"".$data->referencia_transaccion,
                    "apellido_tarjeta_habiente"=>"".$data->apellido_tarjeta_habiente,
                    "nombre_tarjeta_habiente"=>"".$data->nombre_tarjeta_habiente,
                    "FHCreacion"=>$FechaHora,
                    "FHActualizacion"=>$FechaHora
                  ];

            $insert = insert('culqi',$fields);
          } catch (Exception $e) {
            
          }
      }

  } catch (InvalidParamsException $e) {
    $rsp["new_proform"]  =  error_process("CANCELADO");
    $rsp["message"]  = ["mensaje_respuesta_usuario"=>"La transacción ha sido cancelada."];
  }

  echo json_encode($rsp);
  exit();

}


function error_process($state=null){
   global $FechaHora;

        $data = [];
        $codCliente       = $_POST["id_client"];
        $CodProforma      = $_POST["numero_pedido"];
        //$costo_producto   = $_POST["total"];
        //$nombre_comprador = $_POST["fullname"];
        

        if($state!=null)
        xSQLPDO("UPDATE ct_proformas SET Estado='$state',Cliente='$codCliente' WHERE Codigo='$CodProforma'");

        
        /** double proforma **/
        try {

          $arrproformdets          = rGMXPDO("SELECT pd.Articulo,pd.Precio,pd.Cantidad,pd.Total,pd.TipoFormato,pd.Peso,
                                              pd.TotalDesc,pd.subtotal,pd.excedentePeso,pd.excedenteUbicacion,pd.PrecioPeso FROM ct_proformasdet pd
                                              INNER JOIN ct_proformas p ON pd.Proforma = p.Codigo 
                                              WHERE p.Codigo ='$CodProforma'");

          $arrproforms    = rGTPDO($ConexionPDO,"SELECT * FROM ct_proformas WHERE Codigo='$CodProforma' ORDER BY Codigo DESC LIMIT 1");
          if(arrproformdets){

             $newproform =  insert("ct_proformas",[
                        "CtaSuscripcion"=>$arrproforms["CtaSuscripcion"],
                        "FHCreacion"=>$FechaHora,
                        "FHActualizacion"=>$FechaHora,
                        "Moneda"=>$arrproforms["Moneda"],
                        "Estado"=>"Pendiente",
                        "SessionId"=>$arrproforms["SessionId"],
                        "TotalPrecio"=>$arrproforms["TotalPrecio"],
                        "SubTotal"=>$arrproforms["SubTotal"],
                        "TotalDescuento"=>$arrproforms["TotalDescuento"],
                        "tipoEntrega"=>$arrproforms["tipoEntrega"],
                        "UsuarioSolicitante"=>$arrproforms["UsuarioSolicitante"]
                        ]);
            if($newproform){

               $rgKeySuscripcion   =   rGTPDO($ConexionPDO,"SELECT p.Codigo,p.CtaSuscripcion,s.KeySuscripcion,s.proveedor,cet.paginaweb,p.tipoEntrega 
                                                     FROM ct_proformas p 
                                                     INNER JOIN ct_empresa s  ON p.CtaSuscripcion=s.Codigo
                                                     INNER JOIN ct_entidad cet  ON s.Codigo = cet.CtaSuscripcion
                                                     WHERE p.Codigo='$CodProforma'");

              $data["id"]             = $newproform["lastInsertId"];
              $data["session"]        = $arrproforms["SessionId"];
              $data["entity"]         = $rgKeySuscripcion["CtaSuscripcion"];

               foreach ($arrproformdets as $key => $proformdet) {
                insert("ct_proformasdet",[
                              "CtaSuscripcion"=>$arrproforms["CtaSuscripcion"],
                              "FHCreacion"=>$FechaHora,
                              "FHActualizacion"=>$FechaHora,
                              "Proforma"=>$newproform["lastInsertId"],
                              "Articulo"=>$proformdet["Articulo"],
                              "Precio"=>$proformdet["Precio"],
                              "Cantidad"=>$proformdet["Cantidad"],
                              "Total"=>$proformdet["Total"],
                              "TipoFormato"=>$proformdet["TipoFormato"],
                              "Peso"=>$proformdet["Peso"],
                              "TotalDesc"=>$proformdet["TotalDesc"],
                              "subtotal"=>$proformdet["subtotal"],
                              "excedentePeso"=>$proformdet["excedentePeso"],
                              "excedenteUbicacion"=>$proformdet["excedenteUbicacion"],
                              "PrecioPeso"=>$proformdet["PrecioPeso"]
                              ]);
               }
            }
          }

        } catch (Exception $e) {
          
        } 

        return $data;
}


/*
Información de la venta: JL3g1eNslO70wd2DQEdk2_qwYQABPQwr9wWIDEFIiQz7KHXzXkBhafJbUl0s7jPIUAC2scJ0DtPW5TffEaCnilbQaV1VxFA1fz-oUxzaGB9LxOR9jzuntDuHU9zrr2jno5RVHuZrckbAdIgtYyWOIA==
Código de comercio: Lci681TSDAjh
Número de pedido: XMkf70OcLhvld6n
Código de respuesta: venta_registrada
Mensaje de respuesta: Venta creada exitosamente.
Ticket de la venta: Ldlf0JutwhTCMW3HdjoXvG0yyAEAXj4n8HU
*/



function closesell($obj){
    global $ConexionPDO,$FechaHora;
        $codCliente       = $_POST["id_client"];
        $CodProforma      = $obj->numero_pedido; // integration
        //$CodProforma  = 4165; //localhost assign manually proform id
        if( is_numeric($CodProforma) ){
            $costo_producto   = $_POST["total"];
            $nombre_comprador = $_POST["fullname"];

            xSQLPDO("UPDATE ct_proformas SET Estado='Pagado',Cliente='$codCliente' WHERE Codigo='".$CodProforma."'");

            $order = new Proform($ConexionPDO);
            $order->sendEmailCloseProform($CodProforma);

            RegistraPedido($codCliente, $CodProforma, $costo_producto);
            RegistraVenta($CodProforma,$costo_producto,$nombre_comprador);

            $rgKeySuscripcion   =   rGTPDO($ConexionPDO,"SELECT p.Codigo,p.CtaSuscripcion,s.KeySuscripcion,fed.EntidadCreadora proveedor,fed.color,fed.logo,fed.url,p.tipoEntrega 
                                                         FROM ct_proformas p 
                                                         INNER JOIN ct_empresa s  ON p.CtaSuscripcion=s.Codigo
                                                         INNER JOIN front_end_data fed ON s.Codigo=fed.CtaSuscripcion 
                                                         WHERE p.Codigo='$CodProforma'");

            $Codigo_empresa   = $rgKeySuscripcion['proveedor'];
            //$WebSolicitante   = $rgKeySuscripcion['paginaweb'];
            $WebSolicitante   = EGURUCLUBS;
            $tipoEntrega      = $rgKeySuscripcion['tipoEntrega'];


            $fed = json_decode( ["logo"=>EGURUCLUBS."_files/empresa/$CodProforma/logos/".$rgKeySuscripcion['logo'],"url"=>EGURUCLUBS.$rgKeySuscripcion['url']."/library","color"=>$rgKeySuscripcion['color']] );
 
                
            if($tipoEntrega!="Fisico"){
                  //# Captura de clave de usuario , para registro en P.E.
                  //# La clave será la misma en casos [MISMO DESTINO/DISTINTO DESTINO]        
                  $rgpass       =    rGTPDO($ConexionPDO, "SELECT clave FROM ct_cliente  WHERE Codigo='$codCliente'" );
                  $pass         =    $rgpass['clave'];

                  $Matris_Product   =   ProcesoVenta($CodProforma); //Lista de contactos por proforma

                  foreach ($Matris_Product as $key => $Product) {

                      $fields = array(
                        'IdEcomerce'    => urlencode($Codigo_empresa),
                        'PagWeb'        => urlencode($WebSolicitante),
                        'Nombres_clie'    => urlencode( $Product['Nombres'] ),
                        'Apellidos_clie'  => urlencode($Product['Apellidos']),
                        'Email_clie'    => urlencode($Product['Email']),
                        'Productos'     => urlencode($Product['AlmacenDestino']),
                        'tipoarticulo'    => urlencode($Product['tipoarticulo']),
                        'passw'       => urlencode($pass),
                        '_crypt'      => 0,
                        'fed'   =>  urlencode($fed)
                      );

                      insert("fyupanquia",[
                                            "empresa"=>$Codigo_empresa,
                                            "nombres"=>$Product['Nombres'],
                                            "apellidos"=>$Product['Apellidos'],
                                            "email"=>$Product['Email'],
                                            "productos"=>$Product['AlmacenDestino'],
                                            "ta"=>$Product['tipoarticulo'],
                                            "pass"=>$pass,
                                            "web"=>$WebSolicitante,
                                            "date"=>$FechaHora,
                                            
                                          ]);
                      
                      $xx = fast(OWLPLATINUM.'owlgroup/procesa_pedidos.php', $fields,false,"POST");   //# Registro en la Plataforma
                      
                  }
            }
        }
        
}


function fast($url,array $fields,$json = false ,$method="GET"){
    $html_params = http_build_query($fields,'&');
    $url = ($method=="GET")?$url."?".$html_params:$url;
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST,$method);
      curl_setopt($ch,CURLOPT_POSTFIELDS, $html_params );
      $output = curl_exec($ch);
      curl_close($ch);
      if($json===true) return json_decode($output);  
      else return $output;
  }



      function RegistraPedido($idCliente, $item_number, $costo_producto) {
        global $FechaHora,$MoneySystem;
        
        $rgPedidos      = rGMXPDO("select CtaSuscripcion,Articulo,Precio,Cantidad,Total from ct_proformasdet where Proforma=$item_number");
        $CtaSuscripcion = $rgPedidos[0]['CtaSuscripcion'];

        $insertorder = insert("ct_pedidos",[
                              "CtaSuscripcion"=>$CtaSuscripcion,
                              "FHCreacion"=>$FechaHora,
                              "Cliente"=>$idCliente,
                              "Proformas"=>$item_number,
                              "TipoPago"=>12,
                              "Moneda"=>$MoneySystem->Codigo,
                              "FechaEmision"=>$FechaHora,
                              "TotalPrecio"=>$costo_producto,
                              "Estado"=>"Pagado",
                            ]);

        $idPedido = $insertorder["lastInsertId"];


        foreach ($rgPedidos as $key => $rgPedido) {
          insert("ct_pedidosdet",[
                                  "CtaSuscripcion"=>$CtaSuscripcion,
                                  "FHCreacion"=>$FechaHora,
                                  "Pedido"=>$idPedido,
                                  "Articulo"=>$rgPedido['Articulo'],
                                  "Precio"=>$rgPedido['Precio'],
                                  "Cantidad"=>$rgPedido['Cantidad'],
                                  "Total"=>$rgPedido['Total']
                                 ]);
        }
      }

      function RegistraVenta($item_number, $costo_producto, $nombre_comprador) {
          global $FechaHora,$MoneySystem;

          $rgPedido     = rGTPDO(null,"SELECT CtaSuscripcion,Articulo,Precio,Cantidad,Total FROM ct_proformasdet WHERE Proforma=$item_number");
          $CtaSuscripcion = $rgPedido['CtaSuscripcion'];
          
          insert("ct_registroventas",[
                                      "CtaSuscripcion"=>$CtaSuscripcion,
                                      "FHCreacion"=>$FechaHora,
                                      "DocSerie"=>$item_number,
                                      "BaseImp"=>$costo_producto,
                                      "Total"=>$costo_producto,
                                      "Moneda"=>$MoneySystem->Codigo,
                                      "RazonSocial"=>$nombre_comprador,
                                      "CodTipoDoc"=>1
                                      ]);
      }

      function ProcesoVenta($item_number) {
          xSQLPDO("UPDATE ct_contacto_destino SET estado='Pagado' WHERE CodProforma=$item_number");

        $rg       = rGMXPDO(" SELECT vnt_al.CodPlataforma AlmacenDestino,cd.Nombres,cd.Apellidos,cd.Email ,cta.TipoProducto as tipoarticulo
                              FROM ct_proformasdet pd 
                              INNER JOIN vent_articulos cta on pd.Articulo=cta.Codigo
                              INNER JOIN ct_contacto_destino cd ON pd.Codigo = cd.CodProformaDet 
                              INNER JOIN vent_almacen vnt_al ON vnt_al.Articulo=cta.Codigo
                              AND pd.Proforma = cd.CodProforma 
                              WHERE pd.Proforma=$item_number and pd.TipoFormato='Virtual' ");
        /*
        foreach ($rg as $key => $value) {
          $data[] = $value;
        }*/
        return $rg;
      }
      function RedirectServer($Url, $fields) {
        //set POST variables
        //open connection
        $ch = curl_init();
        $fields_string = '';
        foreach ($fields as $key => $value) {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        //set the url, number of POST vars, POST data
        /*
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        */
        


        $ch = curl_init($Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string );
        $output = curl_exec($ch);
        curl_close($ch);
        WE($output);
    } 