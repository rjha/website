<?php


namespace com\indigloo\wb\core {

	use \com\indigloo\wb\Constants as AppConstants;

    class RequestProcessor {

    	function __construct() { }

    	function process() {
			 
    		$gWeb = \com\indigloo\core\Web::getInstance();
		    // site domain check
		    $domain = $_SERVER["HTTP_HOST"];
		    // @assumption
		    // crude hack
		    // a general scheme to get TLD from a host is surprisingly hard
		    // (e.g. res.in / co.uk schemes)
		    // here we assume that all our domains are of form x.y.z

		    $pos1 = strpos($domain,".");
		    $top_domain = substr($domain,$pos1 + 1);
		    
			// set in request
		    $gWeb->setRequestAttribute(AppConstants::SITE_TOP_DOMAIN,$top_domain);
 			$gWeb->setRequestAttribute(AppConstants::SITE_HOST_DOMAIN,$domain);

 			// Load data from DB
		    $siteDao = new \com\indigloo\wb\dao\Site();
		    $siteDBRow = $siteDao->getOnDomain($domain);
			
		    if(empty($siteDBRow)) {
		        $message = sprintf("<h2> Unknown domain :: %s </h2> ",$domain) ;
		        echo $message ;
		        exit(1);
		    }

		    $gSiteView = $siteDao->getSessionView($siteDBRow["id"]);
		    $gSiteView->domain = $domain ;
		    $gSiteView->name = $siteDBRow["name"];
		    $gSiteView->id = $siteDBRow["id"] ;
			
			// set in request
		    $gWeb->setRequestAttribute(AppConstants::SITE_SESSION_VIEW,$gSiteView);
			
		}

    }

}
?>