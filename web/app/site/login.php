<?php

    include ('wb-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util;

    $qUrl = \com\indigloo\Url::tryBase64QueryParam('q', '/');
    $qUrl = base64_decode($qUrl);

    $gWeb = \com\indigloo\core\Web::getInstance();
    
    $www_host = Config::getInstance()->get_value("www.host.name") ;
    $signin_url = "http://".$www_host."/app/auth2/domain.php" ;
    $random_key = Util::getMD5GUID();

    // store in current session
    $gWeb->store("auth2.random.key",$random_key);
    
    $params = array(
        "domain" => base64_encode($_SERVER["HTTP_HOST"]),
        "state" => $random_key);

    $signin_url = Url::createUrl($signin_url,$params);
    header("Location: ".$signin_url);

?>