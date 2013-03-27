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
		    $gWeb->setRequestAttribute(AppConstants::ORG_TOP_DOMAIN,$top_domain);
 			$gWeb->setRequestAttribute(AppConstants::ORG_HOST_DOMAIN,$domain);

 			// Load data from DB
		    $orgDao = new \com\indigloo\wb\dao\Organization();
		    $orgDBRow = $orgDao->getOnDomain($domain);
			
		    if(empty($orgDBRow)) {
		        $message = sprintf("<h2> Unknown domain :: %s </h2> ",$domain) ;
		        echo $message ;
		        exit(1);
		    }

		    $gOrgView = $orgDao->getSessionView($orgDBRow["org_id"]);
		    // set in request
		    $gWeb->setRequestAttribute(AppConstants::ORG_SESSION_VIEW,$gOrgView);
			
		}

    }

}
?>