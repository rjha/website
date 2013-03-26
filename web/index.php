<?php

	require_once('wb-app.inc');
    require_once(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\wb\Constants as AppConstants ;

    $s_time = microtime(true);

    $gWeb = \com\indigloo\core\Web::getInstance();
    // site domain check
    $domain = $_SERVER["HTTP_HOST"];
    $orgDao = new \com\indigloo\wb\dao\Organization();
    $orgDBRow = $orgDao->getOnDomain($domain);

    if(empty($orgDBRow)) {
        $message = sprintf("<h2> Unknown domain :: %s </h2> ",$domain) ;
        echo $message ;
        exit(1);
    }

    $gOrgView = $orgDao->getSessionView($orgDBRow["org_id"]);
    $gWeb->setRequestAttribute(AppConstants::ORG_SESSION_VIEW,$gOrgView);


	$router = new com\indigloo\wb\router\MainRouter();
	//route to appropriate controller
	$router->route();
    $e_time = microtime(true);
    printf(" \n <!-- Request took %f milliseconds --> \n", ($e_time - $s_time)*1000);

?>