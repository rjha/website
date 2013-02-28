<?php

	require_once('website-app.inc');
    require_once(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\website\Constants as AppConstants ;

    $s_time = microtime(true);
    
    // find domain 
    // find organization data for this domain
    // inject domain name and organization data into request scope
    $host = $_SERVER["HTTP_HOST"];
    // @todo - no Host : find NoHostController
    $organizationDao = new \com\indigloo\website\dao\Organization();
    $gPubOrgData = $organizationDao->getPublicData($host);

    // @todo - process org Data

    $gWeb = \com\indigloo\core\Web::getInstance();
    $gWeb->setRequestAttribute(AppConstants::PUB_ORG_DATA,$gPubOrgData);
	$gWeb->setRequestAttribute(AppConstants::PUB_DOMAIN_NAME,$host);

	$router = new com\indigloo\website\router\MainRouter();
	//route to appropriate controller
	$router->route();
    $e_time = microtime(true);
    printf(" \n <!-- Request took %f milliseconds --> \n", ($e_time - $s_time)*1000);

?>