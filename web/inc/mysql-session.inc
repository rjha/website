<?php

$sessionHandler = new \com\indigloo\wb\core\MySQLSession();
session_set_save_handler(array($sessionHandler,"open"),
                            array($sessionHandler,"close"),
                            array($sessionHandler,"read"),
                            array($sessionHandler,"write"),
                            array($sessionHandler,"destroy"),
                            array($sessionHandler,"gc"));


ini_set('session_use_cookies',1);
//Defaults to 1 (enabled) since PHP 5.3.0
//no passing of sessionID in URL
ini_set('session.use_only_cookies',1);

// the following prevents unexpected effects
// when using objects as save handlers
// @see http://php.net/manual/en/function.session-set-save-handler.php
register_shutdown_function('session_write_close');
session_start();
 

if (isset($_COOKIE[session_name()])) {
    //cookie lifetime in seconds
    $lifetime = \com\indigloo\Configuration::getInstance()->get_value("session.lifetime",3600);
    setcookie(session_name(), session_id(), time() + $lifetime , "/");
}


?>
