<?php


namespace com\indigloo\wb\core {

	use \com\indigloo\wb\Constants as AppConstants;
	use \com\indigloo\wb\auth\Login as Login ;

    class RequestProcessor {

    	function __construct() { }

    	function process() {
			 
    		$gWeb = \com\indigloo\core\Web::getInstance();
		    
		    $domain = $_SERVER["HTTP_HOST"];
 			// Load site data from DB
		    $siteDao = new \com\indigloo\wb\dao\Site();
		    $siteDBRow = $siteDao->getOnDomain($domain);
			
		    if(empty($siteDBRow)) {
		        $message = sprintf("<h2> Unknown domain :: %s </h2> ",$domain) ;
		        echo $message ;
		        exit(1);
		    }

		    // load site admins data from DB
		    $gSiteView = $siteDao->getSessionView($siteDBRow["id"]);
		    $gSiteView->domain = $domain ;
		    $gSiteView->name = $siteDBRow["name"];
		    $gSiteView->id = $siteDBRow["id"] ;

		    $theme_name = empty($siteDBRow["theme_name"]) ? 
		    	AppConstants::DEFAULT_THEME_NAME : $siteDBRow["theme_name"] ;
		    
		    // does this theme exists?
		    $theme_dir = APP_WEB_DIR."/themes/".$theme_name ;

		    if(!file_exists($theme_dir)) {
		    	//issue warning
		    	// switch to default
		    	$theme_name = AppConstants::DEFAULT_THEME_NAME ;
		    }

		    $gSiteView->theme = $theme_name ;
			
			// Login check
			$loginId = Login::tryLoginIdInSession();
    		$admins = $gSiteView->admins;

    		if(!empty($loginId) && (in_array($loginId,$admins))) {
    			$gSiteView->isOwner = true ;
    		} else {
    			$gSiteView->isOwner = false ;
    		}

			// set in request
		    $gWeb->setRequestAttribute(AppConstants::SITE_SESSION_VIEW,$gSiteView);
			
		}

    }

}
?>