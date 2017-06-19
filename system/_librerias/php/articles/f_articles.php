<?php
/**
 * #fyupanquia
 * SOLICITUD DE IMPORTACIÓN DE ARTICULOS
 */
header("Access-Control-Allow-Origin:*");
require_once('../funciones.php');
require_once('../conexiones.php');
require_once('FConnection.php');
require_once('FTransport.php');

if($_GET['action']=='send'){
	action('send');
}



function action($arg){
		$f_articles 		= new Import($arg);
		$variableMetodo 	= array($f_articles, $arg);

		if(is_callable($variableMetodo)){
			$f_articles->execute();
		}
	}


class Import{

	private $Connectionstring 	= null;
    private $tableTO 			= null;
    private $tableFROM 			= null;
    private $ColumnTO 			= null;
    private $ColumnFROM 		= null;
    private $method 			= null;
    private $arrIndices 		= ["server","name","user","password"];
    private $ColumnsTOArticle   = null;
    private $ColumnsTOAlmacen   = null;
    private $Where 				= "";

    public function Import($__method){

    	$this->method = $__method;
    	
    }

	public function execute(){
		$this->setAll();
		$this->connection();
	}

	public function send($json){
		WE($json);
	}

	private function setAll(){
		
		$this->Where     			= $_POST['WHERE'] ;

		if($_POST['tableTO'] != ""){
			$this->tableTO 				= $_POST['tableTO'];
		}else{
			$this->W(4,"tableTO undefined.","");
		}

		if($_POST['ColumnsFROM'] != ""){
			$this->ColumnsFROM 			= $_POST['ColumnsFROM'] ;
		}else{
			$this->W(4,"ColumnsFROM undefined.","");
		}

		if($_POST['Article'] != ""){
			$this->ColumnsTOArticle     = $_POST['Article'] ;
		}else{
			$this->W(4,"Article undefined.","");
		}

		if($_POST['Almacen'] != ""){
			$this->ColumnsTOAlmacen     = $_POST['Almacen'] ;
		}else{
			$this->W(4,"Almacen undefined.","");
		}

		if($_POST['Connectionstring'] != ""){
			$this->Connectionstring 	= decrypt( $_POST['Connectionstring'] , "e_commerce");
		}else{
			$this->W(4,"Connectionstring undefined.","");
		}

	}
	private function connection(){
		$arrConection = explode("__", $this->Connectionstring );
		$count = count($arrConection);


		if($count==4){
			
			$arrConection = $this->toObj($arrConection);  // convertir a índices asociativos

			$sttConnection = new FConnection($arrConection);
			$rsp 		   = $sttConnection->connect();
			if($rsp){

				
				$FTransport = new FTransport($rsp);
				$FTransport->setTableTo($this->tableTO);
				$FTransport->setColumnsFROM($this->ColumnsFROM );
				$FTransport->setColumnsTOArticle($this->ColumnsTOArticle);
				$FTransport->setColumnsTOAlmacen($this->ColumnsTOAlmacen);
				$FTransport->setWhere($this->Where);
				$FTransport->prepare()->execute();
				$status = $FTransport->getResponse();
				$this->finish($status);
			}else{
				$this->err(2);
			}

		}else{
			$this->err(3);
		}

		
	}
	private function err($b){
		$msg = "Error Desconocido";
		switch ($b) {
			case 'value':
				# code...
				break;
			case 2:
				$msg = "Error de Conexión.";
				break;
			case 3:
				$msg = "Insuficientes parámetros para realizar la conexión.";
				break;
									
			default:
				# code...
				break;
		}
		$this->W($b,$msg,"");
	}

	private function finish($status){
			$this->W($status["status"],$status["msg"],$status["data"]);
	}

	private function W($b,$msg,$data){
		$json = '{"status":"'.$b.'","msg":"'.$msg.'","data":"'.$data.'"}';
		$this->send($json);
	}

	private function toObj($arr){
		$obj = [];
		foreach ($arr as $index => $value) {
			$obj[$this->arrIndices[$index]]=$value;
		}
		return (object) $obj;
	}
}