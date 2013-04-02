<?php

    include ('wb-app.inc');
    include(APP_CLASS_LOADER);
    
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\wb\Constants as AppConstants;
   
    // DB connx is needed for mysql session.
    $domain = $_SERVER["HTTP_HOST"];
    $pos1 = strpos($domain,".");
    $top_domain = substr($domain,$pos1 + 1);
    
    $gWeb = \com\indigloo\core\Web::getInstance();
    $gWeb->start();
    $gWeb->setRequestAttribute(AppConstants::SITE_TOP_DOMAIN,$top_domain);

    // session_start() should be called in logout script
    $session_backend = Config::getInstance()->get_value("session.backend");
    $session_backend = empty($session_backend) ? "default" :  strtolower($session_backend);

    switch($session_backend) {
        case "mysql" :
            include(APP_WEB_DIR . '/inc/mysql-session.inc');
            break ;
        default:
            include(APP_WEB_DIR . '/inc/session.inc');
            break ;
    }
    
    include(APP_WEB_DIR . '/inc/global-error.inc');
    

    // destroy session
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );

        setcookie (session_id(), "", time() - 3600);
    }

    session_destroy();
    // redirect to a _session free page
    // redirecting to a page that includes our standard
    // header will start a new session
    header('Location: /');

?>
