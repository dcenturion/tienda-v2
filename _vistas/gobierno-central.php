<?php
require_once './_vistas/layout.php';

class gobiernoCentral{

    private $_parm;
    public  function __construct($_parm=null)
    {
        $id = $_parm["id"];
        $site = "Eventos";
        $layout  = new Layout();

        echo $layout->main($this->viewHome($id),$id,$site);

    }



    public function viewHome($id) {

        $cnOwlPDO = PDOConnection();
        $FechaHoraSrv = FechaHoraSrv();
        $empresa = $_SESSION['empresa'];
        $user = $_SESSION['user'];

        $layout  = new Layout();

        $datos = array();
        $datos['numeros'] = "996 614 532 / 999 777 768 ";

        $datos['direccion'] = "Lima - Perú ";

        $datos['email'] = "informes@episodiosplanning.com";


        $layout->formContacto = $layout->render("./_vistas/form_contactos.phtml",$datos);

        return $layout->render('./_vistas/gobierno-central.phtml',$id);



    }



    public function formContacto($arg) {



        return $arg;

    }



}