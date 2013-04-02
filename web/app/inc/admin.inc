<?php
    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\wb\Constants as AppConstants ;
    use \com\indigloo\Configuration as Config ;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $rp = new com\indigloo\wb\core\RequestProcessor();
    $rp->process();
    $gSiteView = $gWeb->getRequestAttribute(AppConstants::SITE_SESSION_VIEW); 
    
    if(!$gSiteView->isOwner) {
        $www_host = Config::getInstance()->get_value("www.host.name") ;
        $fwd = "http://".$www_host. "/app/account/login.php" ;
        header('Location: '.$fwd);
        exit ;
    }
    
?>
