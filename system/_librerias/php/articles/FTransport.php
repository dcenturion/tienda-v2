<?php
/**
*  fyupanquia
*/
class FTransport 
{
	private $CN_TO ;
	private $tableTO;
	private $TableFROM;
	private $ColumnsTO;
	private $ColumnsFROM;
	private $Where 				= null ;
	private $rsp 				= [] ;
	private $allData;
	private $arrayColumns;
	private $cantColumnArticle;
	private $cantColumnAlmacen;
	private $ColumnsTOArticle 	= [];
	private $ColumnsTOAlmacen 	= [];
	private $status			  	= false;
	
	function __construct($cn)
	{
		$this->CN_TO = $cn;
	}

	public function setTableTo($val){
		$this->tableTO = $val;
	}

	public function setTableFROM($val){
		$this->TableFROM = $val;
	}

	public function setWhere($val){
		$this->Where = $val;
	}

	public function setColumnsTOArticle($val){
		$this->ColumnsTOArticle = explode("|", $val);
	}

	public function setColumnsTOAlmacen($val){
		$this->ColumnsTOAlmacen = explode("|", $val);
	}

	public function setColumnsFROM($val){

		 //$this->ColumnsFROM = str_replace(" ", "", $val);
		 $this->ColumnsFROM = str_replace("|", ",", $val);

	}

	public function prepare(){
			$WHERE = "";
		if($this->Where!="" && $this->Where!=null){
			$WHERE = "  AND _AL.AlmacenCod NOT IN(". $this->Where .") ";
		}

		$sqlGET = " SELECT 	".$this->ColumnsFROM."    
				    FROM almacen _AL
					INNER JOIN articulos AS _AR ON _AL.Producto = _AR.Producto
					INNER JOIN documento AS DOC ON _AR.ProductoFab = DOC.codigo
					WHERE DOC.entidad = 'owlgroup.org' AND 
					_AL.tipoproducto IN ('revista','libro') 
					$WHERE
					group by _AR.ProductoFab
					ORDER BY _AL.FechReg DESC ";

		$_allData = fetchAllArrayIndex($sqlGET);
		
		if(!empty($_allData)){
					
			$this->allData = $_allData;	
			$this->msg("",3,"");
		

		}else{
			$this->msg("No se encontraron resultados.",2,"");
			
		}

		return $this;
	}

	private function msg($msg,$b,$data){
		$this->rsp["msg"]    = $msg;
		$this->rsp["status"] = $b;
		$this->rsp["data"]   = $data;
	}
	private function  finish(){
		return  $this->rsp;
	}

	public function getResponse(){
		return $this->finish();
	}


	public function execute(){

		if($this->finish()["status"]==3){

			$count 		= 	0;
			$articulos 	=	"";
				foreach($this->allData as $index => $val){

						foreach ($val as $key => $value) {
							if($val[$key]==null )
								$val[$key]="";
						}
		            
					$table1 	=	[];	
					$table2 	=	[];

					$this->load($this->ColumnsTOArticle,$table1,$val);
					$this->load($this->ColumnsTOAlmacen,$table2,$val);

					$arrTables             = 	explode("|", $this->tableTO);
					
					$return 			= 	insertPDO($arrTables[0],$table1,$this->CN_TO);
					$codArticulo	    = 	$return['lastInsertId'];

					$articulos 		   .=   $codArticulo.",";

					$table2["Articulo"] =   $codArticulo;
					$return 			= 	insertPDO($arrTables[1],$table2,$this->CN_TO);
					//$codArticulo	    = 	$return['lastInsertId'];
					$count++;
				}
				if($count==0)
					$this->msg("[".$count."] Artículos al día.",5,"");
				else{
					$articulos  = substr($articulos, 0, strlen($articulos)-1);
					$this->msg("[".$count."] Articulos migrados correctamente.",1,$articulos);
				}
		}
		
	}

	public function load($Columns,&$table,$row){
	
		foreach ($Columns as $key => $value) {
				$table[$value] = $row[$value];
		}
	}



}
/*
if(!empty($allData)){
			foreach ($allData as $key => $value) {
							if($val[$key]==null )
								$val[$key]="";
				}
				var_dump(expression)
		}else{
			$this->msg("No se encontraron resultados.",2);
			$this->finish();
		}
		
*/