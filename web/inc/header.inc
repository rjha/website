<?php
    // application header
    // classes should be loaded first or we get incomplete class object errors
    // Also, do not output anything in class_loader (apart from errors ) - otherwise you will
    // get session header related warnings. Please keep this order.


    include(APP_CLASS_LOADER);

    use \com\indigloo\core\Web  ;
    use \com\indigloo\Configuration as Config  ;
    use \com\indigloo\wb\Constants as AppConstants;

    // site domain check
    // DB connx depends on top domain
    // DB connx is needed for mysql session.
    $domain = $_SERVER["HTTP_HOST"];
    // @assumption
    // crude hack
    // a general scheme to get TLD from a host is surprisingly hard
    // (e.g. res.in / co.uk schemes)
    // here we assume that all our domains are of form x.y.z

    $pos1 = strpos($domain,".");
    $top_domain = substr($domain,$pos1 + 1);
    
    $gWeb = \com\indigloo\core\Web::getInstance();
    $gWeb->start();
    $gWeb->setRequestAttribute(AppConstants::SITE_TOP_DOMAIN,$top_domain);


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
    
    
    
?>
