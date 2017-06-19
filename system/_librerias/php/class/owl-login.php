<?php
/**
 * Description of owl-login
 * This class reserved some sessions vars
 * - attempts
 * - captcha_id
 * 
 * @author Aaron Nuñez Quispe
 */
class Login {
    private $user = null;
    private $password = null;
    private $url_redirect = null;
	
		
	
    // private $response = array(
        // "success" => false
    // );
    
    // public function breaker($message){
        // $this->increaseAttempt();
        
        // if($message){
            // $this->response["message"] = $message;
        // }
        
        // $this->response["err"] = ($message)? $message : "Someone error succeded";
        
        // $this->write();
    // }
    
    public function execute(callable $func ) {

			
	    call_user_func($func);
		
        // if(!(session_status() === PHP_SESSION_ACTIVE)){
            // $this->response["err"] = "session_start() method don't inizialited";
            // $this->write();
        // }
        
        // $this->user = trim($_POST["user"]);
        // $this->password = trim($_POST["password"]);
        
        // if(!$this->user){
            // $this->response["message"] = "Ingrese su usuario";
            // $this->write();
        // }
        
        // if(!$this->password){
            // $this->response["message"] = "Ingrese su contraseña";
            // $this->write();
        // }
        // Filter user to email
        // if(!filter_var($this->user, FILTER_VALIDATE_EMAIL)){
            // $this->response["message"] = "El usuario debe ser de tipo email";
            // $this->write();
        // }
        
        // $attempts = (int) $_SESSION["attempts"];
        // if($attempts >= 3){
            // $captcha_id = $_SESSION["captcha_id"];
            // $captcha = $_POST["captcha"];
            
            // if(strcmp($captcha_id, $captcha)){
                // $this->response["message"] = "Ingrese el captcha correctamente";
                // $this->write();
            // }
        // }
        
        // $func($this->user, $this->password);
        
        // if don't establish success print success false
        // $this->response["err"] = "You don't liberate success process";
        // $this->write();
    }
    
    // public function setURLredirect($url){
        // $this->url_redirect = $url;
    // }
    
    // public function success(){
        // if($this->url_redirect){
            // $this->response["success"] = true;
            // $this->response["url_redirect"] = $this->url_redirect;
        // }else{
            // $this->response["err"] = "Url redirect isn't defined";
        // }
        
        // $this->write();
    // }
    
    // private function write(){
        // $attempts = (int) $_SESSION["attempts"];
        // if($attempts >= 3){
            // $this->response["captcha"] = true;
        // }
        
        // echo(json_encode($this->response));
        // exit();
    // }
    
    // private function increaseAttempt(){
        // $attempts = (int) $_SESSION["attempts"];
        // $attempts++;
        // $_SESSION["attempts"] = $attempts;
        
        // return $attempts;
    // }
}

