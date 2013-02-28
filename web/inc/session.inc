<?php

//default session management
ini_set('session_use_cookies',1);
//Defaults to 1 (enabled) since PHP 5.3.0
//no passing of sessionID in URL
ini_set('session.use_only_cookies',1);
session_start();

if (isset($_COOKIE[session_name()])) {
    //cookie lifetime in seconds
    $lifetime = \com\indigloo\Configuration::getInstance()->get_value("session.lifetime",3600);
    setcookie(session_name(), session_id(), time() + $lifetime , "/");
}

?>
