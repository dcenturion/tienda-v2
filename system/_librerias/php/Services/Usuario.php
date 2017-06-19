<?php 

header("Access-Control-Allow-Origin:*");
require_once('../funciones.php');
require_once('../conexiones.php');
require_once('../global_usuarios_comercializadora.php');

$action	  		   = $_POST["action"];
$params["email"]   = $_POST["email"];
$params["entidad"] = $_POST["entidad"];


$g = new Usuario($params);
WE( $g->$action() );


/**
*   
*/
class Usuario 
{	
	private $PDO     = null;
	private $user    = "";
	private $entidad = "";

	function __construct($params)
	{
		$this->PDO 	     = PDOConnection();
		$this->user      = $params["email"];
		$this->entidad   = $params["entidad"];
	}

	function existsUser(){


		$qUser = "  SELECT   
                    DISTINCT(tab2.Usuario) as Usuario,tab1.EntidadCreadora,tab2.Contrasena,tab2.CodigoParlante,tab2.Apellidos,tab2.Nombres 
                    FROM usuario_entidad AS tab1 
                    LEFT JOIN usuarios AS tab2
                    ON tab1.Usuario = tab2.Usuario 
                    WHERE tab1.EntidadCreadora IN ('".$this->entidad."')
					AND tab2.CodigoParlante = '".$this->user."'
                    ORDER BY IdCodCorrelativo DESC
                    LIMIT 1 "; 

		$stmt = $this->PDO;
		$stmt = $stmt->prepare($qUser);
		$stmt->execute();
		$user = $stmt->fetchObject();

		if($user)
			WE(json_encode(array('status' =>true)));
		else
			WE(json_encode(array('status' =>false)));
	}


	function getdataforUser(){
                    $qUser = "  SELECT   
                                DISTINCT(tab2.Usuario) as Usuario,tab1.EntidadCreadora,tab2.Contrasena,tab2.CodigoParlante,tab2.Apellidos,tab2.Nombres 
                                FROM usuario_entidad AS tab1 
                                LEFT JOIN usuarios AS tab2
                                ON tab1.Usuario = tab2.Usuario 
                                WHERE tab1.EntidadCreadora IN ('$this->entidad')
								AND tab2.CodigoParlante = '$this->user'
                                ORDER BY IdCodCorrelativo DESC
                                LIMIT 1 "; 

                        $stmt = $this->PDO;
						$stmt = $stmt->prepare($qUser);
						$stmt->execute();
						$user = $stmt->fetchObject();

						if($user)
							WE(json_encode(array('status' =>true,'data'=>$user)));
						else
							WE(json_encode(array('status' =>false)));
	}
	function save(){
		$rsp = false;
		if(isset($_REQUEST["user"]) && isset($_REQUEST["keysuscription"]) ){
			        $rg = fetch("SELECT tab2.IdUsuario ,tab2.Usuario
					                FROM usuario_entidad AS tab1 
									LEFT JOIN usuarios AS tab2 ON tab1.Usuario = tab2.Usuario
									INNER JOIN empresa AS emp ON  tab1.EntidadCreadora = emp.PaginaWeb 
					                WHERE   tab2.CodigoParlante ='".$_REQUEST["user"]."' AND emp.KeySuscripcion = '".$_REQUEST["keysuscription"]."' 
					                GROUP BY tab2.Usuario");

			        if($rg){//update
			        		$this->updateUserBoth();
			        }else{//create

			        	$rgentity = fetch("SELECT PaginaWeb FROM empresa WHERE KeySuscripcion='".$_REQUEST["keysuscription"]."' ORDER BY CodEmpresa	LIMIT 1");
			        	if($rgentity){
							$vConex 	= conexSys();
				        	$statecreateuser = CrearUser_Compra2($_REQUEST["website"],$_REQUEST["user"],$_REQUEST["name"],$_REQUEST["lastname"],$_REQUEST["password"],3,$rgentity["PaginaWeb"],$vConex,false);
				        	if($statecreateuser)
				        		$rsp = true;
			        	}
			        				        	
			        }
		}
		return json_encode(["success"=>$rsp]);
	}
	function insertUser(){
		$vConex 	= conexSys();
		$pagweb 	= $_POST["pagweb"];
		$email      = $_POST["email"];
		$nombres    = $_POST["Nombres"];
		$apellidos  = $_POST["Apellidos"];
		$clavedefault = $_POST["Contrasena"];
		$entidadCreadora = $_POST["entidadCreadora"];
		//$crypt       = $_POST["crypt"];
		//$crypt      = ($crypt==1)?true:false;

		$rsp = CrearUser_Compra2($pagweb,$email,$nombres,$apellidos,$clavedefault,3,$entidadCreadora,$vConex,true);

		if($rsp)
			WE(json_encode(array('status' =>true)));
		else
			WE(json_encode(array('status' =>false)));
	}
	function updateUser(){
		
	}
	function updateUserBoth(){

		$uUser   = "UPDATE usuarios SET ";
		//$uStudent= "UPDATE alumnos SET ";
		//$uTeacher = "UPDATE profesores SET ";

        if($_POST["name"]!=""){
        	$name = true;
        	$fieldsuser  .= "Nombres=:name,";
        	//$fieldsuser  .= "Nombres=:name,";
        	//$fieldsuser  .= "Nombres=:name,";
        }
        if($_POST["lastname"]!=""){
        	$lastname = true;
        	$fieldsuser     .= "Apellidos=:lastname,";
        	//$fieldsstudent  .= "ApellidosPat=:lastname,";
        	//$fieldsteacher  .= "ApellidosPat=:lastname,";
        }
        if($_POST["password"]!=""){
        	$pass = true;
        	$fieldsuser     .= "Contrasena=:pass,";
        	//$fieldsstudent  .= "Contrasena=:pass,";
        	//$fieldsteacher  .= "Contrasena=:pass,";
        }

        if($fieldsuser!=""){
        	$fieldsuser = rtrim($fieldsuser,",");
        	$uUser .= $fieldsuser;
        	/*$fieldsstudent = rtrim($fieldsstudent,",");
        	$uStudent .= $fieldsstudent;
        	$fieldsteacher = rtrim($fieldsteacher,",");
        	$uTeacher .= $fieldsteacher;*/


        	$uUser    .= " WHERE Usuario=:user ";
        	//$uStudent .= " WHERE Usuario=:user ";
        	//$uTeacher .= " WHERE Usuario=:user ";
        	
        	$stmt = $this->PDO;  
        	$stmtuser    = $stmt->prepare($uUser);
        	//$stmtstudent = $stmt->prepare($uUser);
        	//$stmtteacher = $stmt->prepare($uUser);

        	if($name){
	        	$stmtuser->bindParam(':name',$_POST["name"]        , PDO::PARAM_STR);
	        	//$stmtstudent->bindParam(':name',$_POST["name"]        , PDO::PARAM_STR);
	        	//$stmtteacher->bindParam(':name',$_POST["name"]        , PDO::PARAM_STR);
        	}

	        if($lastname){
	        	$stmtuser->bindParam(':lastname',$_POST["lastname"]  , PDO::PARAM_STR);  
	        	//$stmtstudent->bindParam(':lastname',$_POST["lastname"]  , PDO::PARAM_STR);  
	        	//$stmtteacher->bindParam(':lastname',$_POST["lastname"]  , PDO::PARAM_STR);  
	        }	        
			    
			if($pass){
				$stmtuser->bindParam(':pass',$_POST["password"]     , PDO::PARAM_STR);  
				//$stmtstudent->bindParam(':pass',$_POST["password"]     , PDO::PARAM_STR);  
				//$stmtteacher->bindParam(':pass',$_POST["password"]     , PDO::PARAM_STR);  
			}

			$stmtuser->bindParam(':user',$_POST["user"] , PDO::PARAM_STR);  
			//$stmtstudent->bindParam(':user',$_POST["user"]."Alumno" , PDO::PARAM_STR);  
			//$stmtteacher->bindParam(':user',$_POST["user"]."Profesor" , PDO::PARAM_STR);  

			$rspuser    = $stmtuser->execute();
			//$rspstudent = $stmtstudent->execute();
			//$rspteacher = $stmtteacher->execute();

			$rsp = $rspuser;

        }else{
        	$rsp = false;
        }
        
		if($rsp)
			WE(json_encode(array('success' =>true)));
		else
			WE(json_encode(array('success' =>false)));
	}
	function getregisteredbyebook(){
		$rsp = ["success"=>false,"data"=>null];
		if(isset($_REQUEST["idwarehouse"])){

			$idwarehouse = $_REQUEST["idwarehouse"];

	        $AuxEM = fetchAll(" SELECT IdMatricula id,Cliente client,FechaInscripcion dateregister,Estado state 
			                    FROM matriculas 
			                    WHERE Producto='{$idwarehouse}' ");
	        if($AuxEM) 
	        	$rsp = ["success"=>true,"data"=>$AuxEM];
		}
		return json_encode($rsp);
	}
}