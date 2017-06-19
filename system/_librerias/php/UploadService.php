<?php
session_start();
include_once "funciones.php";
include_once "Google/src/Google/autoload.php";

class UploadService_Config_Constraints{
    private $maxSize;
    private $extensions;
    
    function __construct() {
        $this->maxSize = 0.5;
    }
    
    function getMaxSize() {
        return $this->maxSize;
    }

    function getExtensions() {
        return $this->extensions;
    }

    function setMaxSize($maxSize) {
        $this->maxSize = $maxSize;
    }

    function setExtensions(array $extensions) {
        $this->extensions = $extensions;
    }
}

class UploadService_Config{
    private $path;
    private $toserver;
    private $videoPlatform;
    private $constraints;
    
    public function __construct() {
        $this->toserver = UploadService::TO_S3;
    }
    
    function getPath() {
        return $this->path;
    }

    function getToserver() {
        return $this->toserver;
    }

    function getVideoPlatform() {
        return $this->videoPlatform;
    }

    function getConstraints() {
        return $this->constraints;
    }

    function setPath($path) {
        $this->path = $path;
    }

    function setToserver($toserver) {
        $this->toserver = $toserver;
    }

    function setVideoPlatform($videoPlatform) {
        $this->videoPlatform = $videoPlatform;
    }

    function setConstraints(UploadService_Config_Constraints $constraints) {
        $this->constraints = $constraints;
    }
}

class UploadService{
    //Destiny
    const TO_S3 = 1;
    const TO_SERVER = 2;
    const TO_BOTH = 3;
    
    //Destiny Platform video
    const VIDEO_PLATFORM_OWL = 1;
    const VIDEO_PLATFORM_YOUTUBE = 2;
    
    //private vars
    private $config;
    
    //resources
    public $file;
    public $token;
            
    function __construct() {
        $this->file = new UploadService_File_Resource($this);
        $this->token = new Token_Resource();
    }
    
    function getConfig() {
        return $this->config;
    }

    function setConfig(UploadService_Config $config) {
        $this->config = $config;
    }
}

class UploadService_File{
    private $name;
    private $size;
    
    function getName() {
        return $this->name;
    }

    function getSize() {
        return $this->size;
    }
    
    function setName($name) {
        $this->name = $name;
    }

    function setSize($size) {
        $this->size = $size;
    }
}

class UploadService_File_Resource{
    private $uploadService;
    
    function __construct(UploadService $uploadService) {
        $this->uploadService = $uploadService;
    }
    
    public function prepare(UploadService_File $file){
        $config = $this->uploadService->getConfig();
        
//        $config = new UploadService_Config();
        
        $configConstraints = $config->getConstraints();
        
//        $configConstraints = new UploadService_Config_Constraints();
        
        $response = array(
            "success"   => false,
            "message"   => null
        );
        
        $file->setName(pathinfo($file->getName(), PATHINFO_FILENAME) . "." . strtolower(pathinfo($file->getName(), PATHINFO_EXTENSION)));
        
        $fileNameExtension      = pathinfo($file->getName(), PATHINFO_EXTENSION);
        $maxSizeMegaBytesValue  = $configConstraints->getMaxSize() * 1024 * 1024;
        
        if($file->getSize() < $maxSizeMegaBytesValue){
            if(
                // verify if file xtension to upload is in extensions
                in_array($fileNameExtension, $configConstraints->getExtensions()) ||
                // Is allowed for all extensions 
                in_array("*", $configConstraints->getExtensions())
            ){
                $response["success"] = true;
            }else{
                $extensionsString = implode(", ", $configConstraints->getExtensions());
            
                $response["message"] = "La extensión del archivo es {$fileNameExtension}, debe tener las siguientes extensiones {$extensionsString}";
            }
        }else{
            $response["message"] = "El archivo supera los {$configConstraints->getMaxSize()} Megabytes";
        }
        
        //Verify if filename extension is video
        if(file::isVideo($file->getName())){
            $response["isVideo"] = true;

            //Get token to video platform
            $postdata = http_build_query(
                array(
                    "name"      => "prueba",
                    "password"  => "12345"
                )
            );

            $options = array(
                "http" => array(
                    "method"  => "POST",
                    "header"  => "Content-type: application/x-www-form-urlencoded",
                    "content" => $postdata
                )
            );

            $context  = stream_context_create($options);

            $result = json_decode(file_get_contents('https://multimedia.owlgroup.org/login', false, $context));

            $response["token"] = $result->token;
        }
        
        return $response;
    }
    
    public function put(){
        $config = $this->uploadService->getConfig();
        
//        $config = new UploadService_Config();
        
        $configConstraints = $config->getConstraints();
        
//        $configConstraints = new UploadService_Config_Constraints();
        
        $response = array(
            "success"   => false,
            "message"   => null,
            "file"      => array(
                "name"          => null,
                "uri"           => null,
                "isVideo"       => false,
                "isImage"       => false,
                "physicsUri"    => null
            )
        );
        
        $path = file::cleanPath($config->getPath());
        
        //prepare file
        $file = $_FILES["file"];
        
        $fileError      = $file["error"];
        $fileName       = $file["name"];
        $fileSize       = $file["size"];
        $fileTempName   = $file["tmp_name"];
        $fileType       = $file["type"];
        
        $fileName = pathinfo($fileName, PATHINFO_FILENAME) . "." . strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileName = file::cleanName($fileName);
        
        //get numerator
        $numerator = (int) numerador('archivoTemporal', 0, '');
        $newFileName = "{$numerator}-{$fileName}";
        
        //destinies
        $basePath   = $_SERVER['DOCUMENT_ROOT'];
        $S3destiny  = "{$path}/{$newFileName}";
        $destiny    = "{$basePath}/{$S3destiny}";
        
        if($fileError === UPLOAD_ERR_OK){
            $videoControl = file::isVideo($fileName) && $config->getVideoPlatform();
            
            if($videoControl){
                //video control
                $response["success"] = move_uploaded_file($fileTempName, $destiny);
            }else{
                set_time_limit(600);
                
                //destiny file
                switch ($config->getToserver()){
                    case UploadService::TO_S3:
                        $S3 = new S3(AWS_ACCES_KEY, AWS_SECRET_KEY);
                        
                        $response["success"] = $S3->putObjectFile($fileTempName, "owlgroup", $S3destiny, S3::ACL_PUBLIC_READ);
                        break;
                    case UploadService::TO_SERVER:
                        $destinyFolder = str_replace("/{$newFileName}", '', $destiny);
                        !file_exists($destinyFolder) && !is_dir($destinyFolder) && creaCarpeta($destinyFolder);
                        
                        $response["success"] = move_uploaded_file($fileTempName, $destiny);
                        break;
                    case UploadService::TO_BOTH:
                        $S3 = new S3(AWS_ACCES_KEY, AWS_SECRET_KEY);
                        
                        $destinyFolder = str_replace("/{$newFileName}", '', $destiny);
                        !file_exists($destinyFolder) && !is_dir($destinyFolder) && creaCarpeta($destinyFolder);
                        
                        $response["success"] = $S3->putObjectFile($fileTempName, "owlgroup", $S3destiny, S3::ACL_PUBLIC_READ) && move_uploaded_file($fileTempName, $destiny);
                        break;
                }
            }
            
            if($response["success"]){
                $response["file"]["name"] = $newFileName;
                
                //Insert register to file temporal table
                $formId = post("form");
                $field  = post("field");
                $userId = $_SESSION["Persona"]["string"];
                $enterpriseUser = $_SESSION["enterprise_user"];
                
                addTempFile($numerator, $path, $newFileName, $fileName, $fileType, $formId, $userId, $enterpriseUser, $field);
                
                if($videoControl){
                    //video control
                    switch ($config->getVideoPlatform()){
                        case UploadService::VIDEO_PLATFORM_OWL:
                            //NOTHING HERE
                            break;
                        case UploadService::VIDEO_PLATFORM_YOUTUBE:
                            set_time_limit(18000);
                            
                            $systemGoogleClient = get_system_google_client();

                            if($systemGoogleClient){
                                $client = $systemGoogleClient->client;

                                //Define an object that will be used to make all API requests.
                                $youtube = new Google_Service_YouTube($client);
                                
                                try{
                                    $fileSize = filesize($destiny);

                                    $videoSnippet = new Google_Service_YouTube_VideoSnippet();
                                    $videoSnippet->setTitle($fileName);
                                    $videoSnippet->setDescription("This video was upload in " . date("Y-m-d H:i:s"));
                                    
                                    $videoStatus = new Google_Service_YouTube_VideoStatus();
                                    $videoStatus->setPrivacyStatus("unlisted"); //public | private | unlisted

                                    $video = new Google_Service_YouTube_Video();
                                    $video->setSnippet($videoSnippet);
                                    $video->setStatus($videoStatus);

                                    $chunkSizeBytes = 1 * 1024 * 1024; //1 megabyte

                                    $client->setDefer(true);

                                    //snippet,contentDetails,fileDetails,player,processingDetails,recordingDetails,statistics,status,suggestions,topicDetails
                                    $httpRequest = $youtube->videos->insert("status,snippet", $video);

                                    $mediaFileUpload = new Google_Http_MediaFileUpload(
                                        $client,
                                        $httpRequest,
                                        'video/*',
                                        null,
                                        true,
                                        $chunkSizeBytes
                                    );

                                    $mediaFileUpload->setFileSize($fileSize);

                                    $status = false;

                                    //open flow resource video file in binary mode
                                    $handle = fopen($destiny, "rb");

                                    while (!$status && !feof($handle)) {
                                      $chunk = fread($handle, $chunkSizeBytes);
                                      $status = $mediaFileUpload->nextChunk($chunk);
                                    }

                                    //close flow reource
                                    fclose($handle);

                                    $client->setDefer(false);

                                    $videoInserted = $status; //Google_Service_YouTube_Video class;

                                    $response["file"]["name"] = $videoInserted->getId();
                                }catch (Google_Service_Exception $e) {
                                    $response["success"] = false;
                                    $response["message"] = htmlspecialchars("A service error occurred: {$e->getMessage()}");
                                } catch (Google_Exception $e) {
                                    $response["success"] = false;
                                    $response["message"] = htmlspecialchars("An client error occurred: {$e->getMessage()}");
                                }
                            }else{
                                $response["success"] = false;
                                $response["message"] = "Ha ocurrido un error al establecer un medio para Google Services";
                            }
                            
                            //delete file because it's not necesary save in the server when the file is a video
                            unlink($destiny);
                            break;
                    }
                }else{
                    if(file::isImage($fileName)){
                        $response["file"]["isImage"] = true;
                        
                        switch($config->getToserver()){
                            case UploadService::TO_S3:
                            case UploadService::TO_BOTH:
                                $response["file"]["uri"] = CONS_IPArchivos . "/{$S3destiny}";
                                break;
                            case UploadService::TO_SERVER:
                                $response["file"]["uri"] = getDomain() . "/{$S3destiny}";
                                break;
                        }
                    }
                    
                    // Generate physics uri
                    switch($config->getToserver()){
                        case UploadService::TO_S3:
                        case UploadService::TO_BOTH:
                            $response["file"]["physicsUri"] = CONS_IPArchivos . "/{$S3destiny}";
                            break;
                        case UploadService::TO_SERVER:
                            $response["file"]["physicsUri"] = getDomain() . "/{$S3destiny}";
                            break;
                    }
                }
            }else{
                $response["message"] = "Destiny directory don't exists or isn't writable, type to server is {$config->getToserver()}";
            }
        }else{
            $response["message"] = "Happened a error whit code {$fileError}";
        }
        
        return $response;
    }
}

class Token_Resource{
    public function generate(){
        $length = 10;
        $tokenString = "";

        for($i = 0; $i < $length; $i++){
            $tokenString .= $this->generateChar();
        }
        
        return $tokenString;
    }
    
    private function generateChar(){
        if($this->randBool()){
            //range of ascii number code
            $asciiCode = rand(97,122);
        }else{
            if($this->randBool()){
                //range of ascii lower case
                $asciiCode = rand(48,57);
            }else{
                //range of ascii upper case
                $asciiCode = rand(65,90);
            }
        }
        
        return chr($asciiCode);
    }
    
    private function randBool(){
        return (bool) rand(0, 1);
    }
}