<?php

    include ('wb-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Url ;

    $qparams = Url::getRequestQueryParams();
 
    if(!isset($qparams["q"]) || empty($qparams["q"])) {
        $qq = base64_encode("/");
    }else {
        $qq = $qparams["q"];
    }

    // do we have a session for www_host?
    if(!Login::hasSession()) {
        // redirect to login page
        $www_host = Config::getInstance()->get_value("www.host.name") ;
        $signin_url = "http://".$www_host."/app/account/login.php" ;
        $params = array("q" => $qq);
        $signin_url = Url::createUrl($signin_url,$params);

        header("Location: ".$signin_url);
        exit ;
    }

    if(!isset($qparams["domain"]) || empty($qparams["domain"])) {
        // error
        $message = "sso router: domain is missing" ;
        trigger_error($message,E_USER_ERROR) ;
    }

    $domain = base64_decode($qparams["domain"]);
    $session_id = session_id();

    $fwd = "http://".$domain."/app/account/site-session.php" ;
    $params = array("session_id" => $session_id, "q" => $qq);
    $fwd = Url::createUrl($fwd,$params);
    header("Location: ".$fwd);


?>