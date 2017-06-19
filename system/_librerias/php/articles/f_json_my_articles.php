<?php
/**
 * #fyupanquia
 * SOLICITUD DE CONSULTA DE ARTICULOS DE UN DETERMINADO UsuarioENTIDAD
 */
header("Access-Control-Allow-Origin:*");
require_once('../funciones.php');
require_once('../conexiones.php');


action($_GET['action']);



function action($arg){
        $f_articles         = new MyArticles($arg);
        $variableMetodo     = array($f_articles, $arg);

        if(is_callable($variableMetodo)){
            $f_articles->execute();
        }
}

class MyArticles{
    private $method                 = null;
    private $types                  = [];
    private $usuarioEntidad         = null;
    private $CodAlmacen             = null;


    public function MyArticles($__method){

        $this->method = $__method;

    }

    public function execute(){
        $this->setAll();
        $metodo = $this->method;
        $this->$metodo();
    }

    private function setAll(){
        if($_POST['types'] != ""){
            $this->types                = explode("|", $_POST['types']);
        }else{
            $this->WE(["status"=>false,"msg"=>"types undefined"]);
        }

        if($_POST['usuarioEntidad'] != ""){
            $this->usuarioEntidad               = $_POST['usuarioEntidad'];
        }else{
            //$this->WE(["status"=>false,"msg"=>"usuarioEntidad undefined"]);
        }
        if($_POST['CodAlmacen'] != ""){
            $this->CodAlmacen               = $_POST['CodAlmacen'];
        }else{
            //$this->WE(["status"=>false,"msg"=>"codCatalogo undefined"]);
        }

    }

    public function getProductosAlmacen(){
        $qCods = "  SELECT
                    T1.ProductoFab As Producto,
                    AL.AlmacenCod AS CodAlmacen 
                    FROM almacen AL
                    INNER JOIN articulos AS T1 ON AL.Producto = T1.Producto
                    INNER JOIN documento AS doc ON T1.ProductoFab = doc.codigo
                    WHERE doc.entidad = 'owlgroup.org' AND 
                    AL.tipoproducto IN ('revista','libro')
                    group by T1.ProductoFab
                    ORDER BY AL.FechReg DESC";
        $MxEPUB = fetchAll($qCods);
        $MxEPUB["success"] = true;
        echo json_encode($MxEPUB);
        exit();
    }

    public function getMyArticles(){
        //$vConex = conexSys();

        $tipos = $this->prepareTypes($this->types);

        $Q_EPUB = "SELECT AR.Titulo as Titulo,
            AR.Descripcion as Descripcion,
            CA.Descripcion as categorias,
            AL.TipoProducto as TipProducto,
            AR.ProductoFab As Producto,
            AL.AlmacenCod As CodAlmacen,
            CA.CategoriCod as categoryId,
            CA.Imagen,
            CA.Descripcion as CategoryTitle,
            TP.Descripcion as DescripTipoProd,
            MA.IdFacturasCab,
            AR.CodArticuloVenta,
            CA.Color
            FROM matriculas MA 
            INNER JOIN almacen AL ON MA.Producto = AL.AlmacenCod 
            INNER JOIN articulos AR ON AR.Producto = AL.Producto 
            INNER JOIN categorias CA on AR.Categoria = CA.CategoriCod
            INNER JOIN tipoproducto TP on AL.TipoProducto = TP.TipoProductoId 
             ";

        if($this->usuarioEntidad!=null && $this->usuarioEntidad!=''){
            $Q_EPUB .=  " WHERE MA.Estado = 'Matriculado' AND MA.Cliente = '".$this->usuarioEntidad."Alumno' ";
        }else if($this->CodAlmacen!=null && $this->CodAlmacen!=''){
            $Q_EPUB .=  " WHERE AL.AlmacenCod = '".$this->CodAlmacen."' ";
        }

        $Q_EPUB .= " AND (".$tipos.") ";

        $MxEPUB = fetchAll($Q_EPUB);

        //Creando un array para almacenar los cursos del programa
        $jsonData = $this->getDefault($this->types);
        foreach ($MxEPUB as $EPUB) {
            foreach ($this->types as $key => $value) {

                if($EPUB->TipProducto==$value){

                    $articlesType = $jsonData[$value]["articles"];
                    array_push($articlesType, $EPUB);
                    $jsonData[$value]["articles"] = $articlesType ;
                }
            }
        }
        $jsonData["success"] = true;
        WE(json_encode($jsonData));
    }

    private function getDefault($types){
        $arr = [];
        foreach ($types as $key => $value) {
            $arr[$value]["articles"] = [];
        }
        return $arr;
    }

    private function prepareTypes($arrTypes){
        $rsp = "";
        foreach ($arrTypes as $key => $value) {
            $rsp .=" AL.TipoProducto = '".$value."' OR";
        }
        $rsp = substr($rsp, 0,strlen($rsp)-3);
        return $rsp;
    }

    private function WE($data){
        $pre = json_encode($data);

        $a = array('[{','[]',']','},{');
        $b = array('{"articles":{','{}','}','},"articles":{');
        $pre = str_replace($a, $b, $pre);
        echo $pre;
        exit;
    }
}