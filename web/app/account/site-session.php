<?php

    include ('wb-app.inc');

    // make this site use the session_id of 
    // our www.host.name. The new session identifier shoud be 
    // set before calling session_start() 
    
    if(isset($_REQUEST["session_id"]) && !empty($_REQUEST["session_id"])) {
        session_id($_REQUEST["session_id"]);
    }

    include(APP_WEB_DIR . '/inc/header.inc');
    $qUrl = \com\indigloo\Url::tryBase64QueryParam("q", "/");
    $qUrl = base64_decode($qUrl);

    // go back to caller
    header("Location: ".$qUrl);

?>