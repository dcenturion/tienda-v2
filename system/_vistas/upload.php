<?php
session_start();

require_once('../_librerias/php/funciones.php');
require_once('../_librerias/php/UploadService.php');


$token = post("token");
$prepare = post("PREPARE_UPLOAD");

$configSession = $_SESSION[$token];

//create a Upload Service
//create constraints
$constraints = new UploadService_Config_Constraints();
$constraints->setMaxSize($configSession["constraints"]["maxSize"]);
$constraints->setExtensions($configSession["constraints"]["extensions"]);

//create configs
$config = new UploadService_Config();
$config->setToserver($configSession["toserver"]);
$config->setPath($configSession["path"]);
$config->setVideoPlatform($configSession["videoPlatform"]);
$config->setConstraints($constraints);

$upload = new UploadService();
$upload->setConfig($config);

$fileResource = $upload->file;
//$fileResource = new UploadService_File_Resource();

$response = null;

if($prepare){
    $file = new UploadService_File();
    $file->setName(post("name"));
    $file->setSize(post("size"));
    
    $response = $fileResource->prepare($file);
}else{
    $response = $fileResource->put();
}

echo json_encode($response);