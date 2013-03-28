<?php

    //app/action/upload/image.php
    include ('wb-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/app/inc/admin.inc');

    require_once(WEBGLOO_LIB_ROOT. '/ext/S3.php');

    set_exception_handler('webgloo_ajax_exception_handler');
    
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger ; 

    use \com\indigloo\wb\auth\Login as Login ; 
    use \com\indigloo\wb\Constants as AppConstants;

    function check_image_name($name) {

        $allowed = array("jpg","jpeg","png","gif");
        $extension = Util::getFileExtension($name);

        if(!empty($extension)) {
            $extension = strtolower($extension);
        }

        if(empty($extension) || !in_array($extension,$allowed)) {
            $message = sprintf("error uploading %s : only JPG, JPEG, GIF, or PNG allowed", $name);
            $data = array("code" => 500, "error" => $message);
            echo json_encode($data);
            exit ;
        }
       
    }

    if(!Login::hasSession()) {
        $message = array("code" => 401 , "message" => "Authentication failure: You need to login!");
        $json = json_encode($message); 
        echo $json;
        exit;
    } 
    
    $uploader =  NULL ; 
    $prefix = sprintf("%s/",date('Y/m/d')) ;
    
    // special prefix - DEV machines 
    $typeOfNode = Config::getInstance()->get_value("node.type");
    if(strcasecmp($typeOfNode, "development") == 0) {
        $prefix = 'test/'.$prefix ;
    }

    // valums file uploader sends a GET parameter - qqfile for normal
    // (multipart) file upload as well. Hence the check against FILE
    // should be the first one to decide the transport.
    // @see https://github.com/valums/file-uploader/issues/352
    
    if (!empty($_FILES) && isset($_FILES["qqfile"])) {
        $name = $_FILES["qqfile"]["name"] ;
        check_image_name($name);

        $pipe = new \com\indigloo\media\FormPipe();
        $uploader = new com\indigloo\media\ImageUpload($pipe);
        $uploader->process($prefix,"qqfile");
        
        
    } elseif (isset($_GET["qqfile"])) {
        $name = $_GET["qqfile"];
        check_image_name($name);

        $pipe = new \com\indigloo\media\XhrPipe();
        $uploader = new com\indigloo\media\ImageUpload($pipe);
        $uploader->process($prefix,$_GET["qqfile"]);
        
    } elseif(isset($_POST["qqUrl"])) {

        $pipe = new \com\indigloo\media\UrlPipe();
        $uploader = new com\indigloo\media\ImageUpload($pipe);
        $uploader->process($prefix,$_POST["qqUrl"]);
        
    } else {
        trigger_error("file upload is unable to determine pipe", E_USER_ERROR); 
    }

    //first - process the errors
    $errors = $uploader->getErrors() ;

    if (sizeof($errors) > 0 ) {
        $data = array("code" => 500, "error" => $errors[0]);
        echo json_encode($data);
    
    } else {
        
        $mediaVO = $uploader->getMediaData();
        $gWeb = \com\indigloo\core\Web::getInstance();
        // find site_id injected in request
        $gSiteView = $gWeb->getRequestAttribute(AppConstants::SITE_SESSION_VIEW);
        $siteId = $gSiteView->id ;

        $mediaDao = new com\indigloo\wb\dao\Media();
        $mediaId = $mediaDao->add($siteId,$mediaVO);
        $mediaVO->id  = $mediaId;
        
        $message = "file upload done!";
        $data = array("code" => 200, "mediaVO" => $mediaVO, "message" => $message,"success" => true);
        echo json_encode($data);
    
    }

?>
