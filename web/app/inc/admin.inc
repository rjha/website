<?php
    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\Configuration as Config ;
    use com\indigloo\wb\Constants as AppConstants ;
 
    // do we have a login?
    $loginId = Login::tryLoginIdInSession();
    if(empty($loginId)) {
        // see if we have a sso login?
        $www_host = Config::getInstance()->get_value("www.host.name") ;
        $sso_url = "http://".$www_host. "/app/account/sso.php" ;

        $current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $params = array(
            "q" => base64_encode($current_url), 
            "domain" => base64_encode($_SERVER["HTTP_HOST"]));
        $sso_url = Url::createUrl($sso_url,$params);
        header("Location: ".$sso_url);
        exit ;

    }

    $gWeb = \com\indigloo\core\Web::getInstance();
    $rp = new com\indigloo\wb\core\RequestProcessor();
    $rp->process();

    $gSiteView = $gWeb->getRequestAttribute(AppConstants::SITE_SESSION_VIEW); 
    if(!$gSiteView->isOwner) {
        
        $fwd = AppConstants::WWW_LOGIN_URL ;
        header('Location: '.$fwd);
        exit ;
    }
    
?>
