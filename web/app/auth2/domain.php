<?php

    include ('wb-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Url ;

    $qUrl = Url::tryBase64QueryParam('q', '/');
    $qUrl = base64_decode($qUrl);

    $gWeb = \com\indigloo\core\Web::getInstance();
    // do we have a session for www_host?
    if(!Login::hasSession()) {
        // redirect to login page
        $www_host = Config::getInstance()->get_value("www.host.name") ;
        $signin_url = "http://".$www_host."/app/hack/login.php" ;
        header("Location: ".$signin_url);
        exit ;
    }

    // get loginId from session
    $loginId = Login::getLoginIdInSession();
    $qparams = Url::getRequestQueryParams();
    $domain = base64_decode($qparams["domain"]);
    $state = $qparams["state"];

    // @todo same login_id from domain as well?
    // return success else return error
    $fwd = "http://".$domain."/app/site/start-session.php" ;
    $params = array("state" => $state);
    $fwd = Url::createUrl($fwd,$params);
    header("Location: ".$fwd);


?>