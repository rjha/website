<?php
namespace com\indigloo\wb\controller{
    
    use \com\indigloo\wb\Constants as AppConstants;
    
    class Home {

        
        function __construct() {}

        function process($router_params,$router_options) {
        	
            $gWeb = \com\indigloo\core\Web::getInstance();
            $gSiteView = $gWeb->getRequestAttribute(AppConstants::SITE_SESSION_VIEW); 
            $view = APP_WEB_DIR. "/themes/".$gSiteView->theme."/home.tmpl" ;
            include ($view);
        }

    }
}
?>
