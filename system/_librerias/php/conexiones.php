<?php
//Define MYSQL vars asdasdasdasd
const DB_SERVER ="localhost";
// const DB_SERVER ="owl-plataforma-educativa.cnid4nk1yxvr.us-west-2.rds.amazonaws.com";
const DB_NAME = "eguru2";
const DB_USER = "root";
const DB_PASSWORD = "";

function PDOConnection($server = null, $dbname = null, $user = null, $password = null) {
    $SERVER = ($server) ? $server : DB_SERVER;
    $DBNAME = ($dbname) ? $dbname : DB_NAME;
    $USER = ($user) ? $user : DB_USER;
    $PASSWORD = ($password) ? $password : DB_PASSWORD;

    $pdo = null;

    try {
        $pdo = new PDO("mysql:host={$SERVER};dbname={$DBNAME}", $USER, $PASSWORD);
    } catch (PDOException $e) {
        die($e->getMessage());
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
}


function conexSys() {
    $cnx = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
    mysql_select_db(DB_NAME, $cnx);

    return $cnx;
}

function conexSis_Emp($db_name) {
    $cnx = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
    mysql_select_db($db_name, $cnx);

    return $cnx;
}

##Acceso Para Programadores
const CONS_IPArchivos = "https://d2mv2wiw5k8g3l.cloudfront.net";
const AWS_ACCES_KEY = "AKIAIY2TIOYPZTKWHSNQ";
const AWS_SECRET_KEY = "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";
const AWS_PATH = "owlgroup";
