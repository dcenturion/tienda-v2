<?php

header("Access-Control-Allow-Origin:*");
require_once('../funciones.php');

$action 	= $_GET["action"];

if($action=='authentication'){
	action('authentication');
}

	function action($arg){
		$f_login 		= new Login();
		$variableMetodo = array($f_login, $arg);
		if(is_callable($variableMetodo)){
			$f_login->execute($arg);
		}
	}

class Login {
    private $user 			= null;
    private $password 		= null;
    private $c_entity 		= null;
    private $url_redirect 	= null;
    private $response 		= [
        						"success" => false
    							];
    
    
    public function execute($func) {
    	if(is_callable(array($this, $func))){

        $this->user 	= $_GET["Usuario"];
        $this->password = $_GET["Contrasena"];
        $this->c_entity = $_GET["Entidad"];
        
        if(!$this->user){
            $this->response["message"] = "Ingrese su usuario";
            $this->write();
        }
        
        if(!$this->password){
            $this->response["message"] = "Ingrese su contraseña";
            $this->write();
        }
        //Filter user to email
        /*if(!filter_var($this->user, FILTER_VALIDATE_EMAIL)){
            $this->response["message"] = "El usuario debe ser de tipo email";
            $this->write();
        }*/

       	$this->$func($this->user, $this->password,$this->c_entity);

        
    	}
    }

    public function authentication($user,$pass,$c_entity){
    	require_once('../conexiones.php');

		$vConex = conexSys();
    	$pwd    = _crypt($pass);
    	$PDO 	= PDOConnection();
            $Q_U = "SELECT 
                    USU.Usuario,
                    USU.IdUsuario as user_id,
                    USU.ControlAcceso, 
                    USU.Estado, 
                    UE.Alumno, 
                    UE.Profesor,
                    UP.Codigo AS profile_id
                    FROM usuario_entidad AS UE 
                    INNER JOIN usuarios AS USU ON UE.Usuario 	= USU.Usuario
                    LEFT JOIN usuario_perfil AS UP ON UP.Codigo =  UE.Perfil
                    WHERE USU.CodigoParlante	=	'{$user}' 
                    AND USU.Contrasena 			= 	'{$pwd}' 
                    AND UE.EntidadCreadora 		= 	'{$c_entity}'";        
        	$statement 	= 	$PDO->query($Q_U);
        	$Usuario 	=	$statement->fetchObject();

            if ($Usuario) {
            	$this->response["success"] 	=	true ;
            	$this->response["obj"] 		= 	$Usuario;
            } else {
                $this->response["message"] = "Usuario y/o constraseña inválida";
            }
       $this->write();     
    }
    
    private function write(){
        echo(json_encode($this->response));
        exit();
    }
    

}

