<?php
    require_once '_vistas/error.php';
 
    class Route{
 
    private $_uri = array();
    private $_method = array();
	  
	    public function add($uri,$method=null){
		     $this->_uri[] = '/'.trim($uri,'/');
			 
			 if($method != null ){
			     $this->_method[] = $method;
			 }
		
		}
		
	    public function submit(){
			  
            $uriParm = isset($_GET['uri']) ?  '/'.$_GET['uri'] : '/';

			$contSeccion = 0;
	
		    foreach($this->_uri as $key => $value){
			
				$secciones = explode('/', $uriParm);
				
				if(count($secciones) == 2 || count($secciones) == 3 ){
				     
						if($secciones[1]==""){
						     $uriParm = "/home";
						}
						
						if(preg_match("#^$value$#",$uriParm)){
						$contSeccion += 1;
						}	
					 
				}else{
					 if(preg_match("#^$value#",$uriParm)){
                         $contSeccion += 1;
					 }					
				}
            }
			
			
			
			if($contSeccion == 0){
				new Error();
				return;			    
			}else{
			
			}
			
		    foreach($this->_uri as $key => $value){

			    $secciones = explode('/', $uriParm);
				if(preg_match("#^$value#",$uriParm)){
		   
					 if(is_string($this->_method[$key])){
						$userMethod = $this->_method[$key];
						
						$parametrosGet = str_replace($value."/","", $uriParm);
			            $cadena_parametrosGet = explode('/', $parametrosGet);
						$contParametros = 0;
						$stringParametros = "";
						$stringParametroSegmento = "";

						for ($j = 0; $j < count($cadena_parametrosGet) +1 ; $j++) {
							$contParametros += 1;		
							$residuo = $contParametros % 2;
							if( $residuo == 0 ){
							     $delimitador = "<{defsei-cmd2}>"; 
						         $stringParametros .= $cadena_parametrosGet[$j].$delimitador  ;
							}else{
							     $stringParametros .= $cadena_parametrosGet[$j]."<{defsei-cmd1}>" ;
							}
						}
						
						$getParm = array();
			            $cadena_parametrosGetB = explode('<{defsei-cmd2}>', $stringParametros);		
						for ($k = 0; $k < count($cadena_parametrosGetB) - 1 ; $k ++) {
			                $cadena_parametrosGetB2 = explode('<{defsei-cmd1}>', $cadena_parametrosGetB[$k]);		
							$values = protect($cadena_parametrosGetB2[1]);
							$valuesB = $cadena_parametrosGetB2[1];
                            $getParm[$cadena_parametrosGetB2[0]] = $valuesB;
							 
						}

	                    if(count($secciones) > 2){
		                	new $userMethod($getParm);	
						    // echo "<br>::".$userMethod." :: <br>";
						}else{
						    // echo "<br>::".$userMethod." :: <br>";
						    new $userMethod($getParm);
						}						
						
						
					 }else{
					    if(preg_match("#^$value$#",$uriParm)){
						    call_user_func($this->_method[$key]);
						}
					 }
				}	
			
			
			}
		
		} 
 
    }