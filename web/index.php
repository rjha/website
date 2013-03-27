<?php

	require_once('wb-app.inc');
    require_once(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\wb\Constants as AppConstants ;

    $s_time = microtime(true);
    $rp = new \com\indigloo\wb\core\RequestProcessor();
    $rp->process();
     
	$router = new com\indigloo\wb\router\MainRouter();
	//route to appropriate controller
	$router->route();
    $e_time = microtime(true);
    printf(" \n <!-- Request took %f milliseconds --> \n", ($e_time - $s_time)*1000);

?>