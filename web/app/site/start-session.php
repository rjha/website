<?php

    include ('wb-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $qparams = Url::getRequestQueryParams();
    $state = $qparams["state"] ;
    

    // see key in session
    $random_key = $gWeb->find("auth2.random.key");
    

?>