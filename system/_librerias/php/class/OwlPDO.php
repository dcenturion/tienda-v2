<?php
/**
 * This class Requires Owl connection (PDOConnection)
 * Yupanqui Allcca Frank
 * 27.02.17
 */
require_once($_SERVER['DOCUMENT_ROOT'].'/system/_librerias/php/conexiones.php');

class OwlPDO
{

    function __construct()
    {

    }

    static function isPDO($arg = null){
        return ($arg instanceof PDO);
    }

    static function basicEval($query,$fields,$pdo){

        return ( is_array($fields) || is_null($fields) ) && self::isPDO($pdo);
    }

    static function _fetch($Query,$fields = null ,$cn=null){
        $stmt       = false;
        $pdo 		= ($cn) ?  $cn : PDOConnection();

        if( self::basicEval($Query,$fields,$pdo)   ){

            try {
                $stmt = $pdo->prepare($Query);
                $stmt->execute($fields);
            } catch (PDOException $e) {
                die($e->getMessage());
            }

        }

        return $stmt;
    }

    /**
     * Anti Injection Method
     *
     * @return array query result
     * @param Query string database query
     * @param fields array params
     * @param cn object PDO connection
     **/
    static function fetchArr($Query,$fields = null ,$cn=null) {
        $rsp  = false;
        $stmt = self::_fetch($Query,$fields,$cn);

        if($stmt){
            $rsp = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $rsp;
    }

    /**
     * Anti Injection Method
     *
     * @return object query result
     * @param Query string database query
     * @param fields array params
     * @param cn object PDO connection
     **/
    static function fetchObj($Query,$fields = null ,$cn=null) {
        $rsp  = false;
        $stmt = self::_fetch($Query,$fields,$cn);
        // vd($stmt);
        if($stmt){
            $rsp = $stmt->fetchObject();
        }
        return $rsp;
    }

    /**
     * Anti Injection Method
     *
     * @return Array Arrays query result
     * @param Query string database query
     * @param fields array params
     * @param cn object PDO connection
     **/
    static function fetchAllArr($Query,$fields = null ,$cn=null) {
        $rsp  = false;
        $stmt = self::_fetch($Query,$fields,$cn);
        if($stmt){
            $rsp = [];
            while ($arr = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                $rsp[] = $arr;
            }
        }
        return $rsp;
    }
    /**
     * Anti Injection Method
     *
     * @return Array Objects query result
     * @param Query string database query
     * @param fields array params
     * @param cn object PDO connection
     **/
    static function fetchAllObj($Query,$fields = null ,$cn=null) {
        $rsp  = false;
        $stmt = self::_fetch($Query,$fields,$cn);
        if($stmt){
            $rsp = [];
            while ($obj = $stmt->fetchObject() ) {
                $rsp[] = $obj;
            }
        }
        return $rsp;
    }

    /**
     * Anti Injection Method UPDATE
     * @param $tabla string: Nombre de tabla
     * @param $data array: Columnas y valores a actualizar
     * @param $where array: Columnas y valores de filtro
     * @param cn object PDO connection
     **/

    static function update($tabla, array $data, array $where, $cn = null) {

        $whereArray = $setArray = array();
        $whereString = $setString = '';

        $tabla = (string) $tabla;
        $where = (array) $where;
        $return = false;
        $rsp  = false;
        if (!empty($tabla) && !empty($data) && !empty($where)) {

            $setArray = parseDataFilter($data, $cn);
            $whereArray = parseDataFilter($where, $cn);

            $setString = implode(', ', $setArray);
            $whereString = implode(' AND ', $whereArray);

            $sql = "UPDATE $tabla SET $setString WHERE $whereString ";
            $query = $cn->prepare($sql);

            try {

                foreach ($data as $name => &$value) {
                    $query->bindParam( ":".$name, $value);
                }
                foreach ($where as $name => &$value) {
                    $query->bindParam( ":".$name, $value);
                }
                $query->execute();

                $cn = null;

            } catch (PDOException $e) {
                die($e->getMessage());
            }

        }
        return $rsp;
    }

    /**
     * Anti Injection Method INSERT
     * @param $data array: Columnas y valores a guardar en la tabla
     * @param cn object PDO connection
     **/

    static function insert($tabla, array $data, $cn = null) {

        $names = $values = array();
        $tabla = (string) $tabla;
        $data = (array) $data;
        $return = array('success' => false, 'lastInsertId' => 0);

        if (!empty($tabla) && !empty($data)) {

            foreach ($data as $key => $value) {
                $names[] = (string) $key;

                $valor = $cn->quote($value);
                $values[] = is_int($valor) ? $valor : ":$key";
            }
            $namesString = implode(', ', $names);
            $valuesString = implode(', ', $values);


            $sql = "INSERT INTO $tabla ( $namesString ) VALUES( $valuesString )";
            $query = $cn->prepare($sql);

            try {

                foreach ($data as $name => &$value) {
                    $query->bindParam( ":".$name, $value);
                }

                $query->execute();
                $return['success'] = $query;
                $return['lastInsertId'] = $cn->lastInsertId();
                $cn = null;

            } catch (PDOException $e) {
                die($e->getMessage());
                print "Error!: " . $e->getMessage() . "</br>";
            }
        }

        return $return;
    }

    static function delet($tabla, array $data, $cn = null) {

        $names = $values = array();
        $tabla = (string) $tabla;
        $data = (array) $data;
        $return = array('success' => false, 'lastInsertId' => 0);

        if (!empty($tabla) && !empty($data)) {

            foreach ($data as $key => $value) {
                $names[] = (string) $key;

                $valor = $cn->quote($value);
                $values[] = is_int($valor) ? $valor : "$key = :$key";
            }
            $namesString = implode(', ', $names);
            $whereString = implode(' AND ', $values);


            $sql = "DELETE FROM $tabla WHERE $whereString ";
            $query = $cn->prepare($sql);

            try {

                foreach ($data as $name => &$value) {
                    $query->bindParam( ":".$name, $value);
                }

                $query->execute();
                $return['success'] = $query;
                $cn = null;

            } catch (PDOException $e) {
                die($e->getMessage());
                print "Error!: " . $e->getMessage() . "</br>";
            }
        }

        return $return;
    }


    static function countrows($query, array $fields, $cn = null) {

        $rsp  = false;
        $stmt = self::_fetch($query,$fields,$cn);
        try {

            $rows  = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = count( $rows );
            $cn = null;

        } catch (PDOException $e) {
            die($e->getMessage());
            print "Error!: " . $e->getMessage() . "</br>";
        }


        return $count;
    }

    static function countcolumn($query, array $fields, $cn = null) {

        $stmt = self::_fetch($query,$fields,$cn);
        try {

            $count = $stmt->columnCount();;
            $cn = null;

        } catch (PDOException $e) {
            die($e->getMessage());
            print "Error!: " . $e->getMessage() . "</br>";
        }

        $return[] = $count;
        $return[] = $stmt;
        return $return;
    }

    static function drop($tabla, $cn = null) {

        $sql = "DROP TABLE IF EXISTS $tabla  ";
        $query = $cn->prepare($sql);

        try {

            $query->execute();
            $cn = null;

        } catch (PDOException $e) {
            die($e->getMessage());
            print "Error!: " . $e->getMessage() . "</br>";
        }

        $return[] = $count;
        $return[] = $stmt;
        return $return;
    }

    static function ex($sql, $cn = null) {

        $query = $cn->prepare($sql);

        try {

            $query->execute();
            $cn = null;

        } catch (PDOException $e) {
            die($e->getMessage());
            print "Error!: " . $e->getMessage() . "</br>";
        }

        $return[] = $count;
        $return[] = $stmt;
        return $return;
    }
}
