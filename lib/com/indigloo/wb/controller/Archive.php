<?php
namespace com\indigloo\wb\controller{
    
    use \com\indigloo\wb\Constants as AppConstants;
    
    class Archive {

        
        function __construct() {}

        function process($router_params,$router_options) {
        	
            $gWeb = \com\indigloo\core\Web::getInstance();
            $gSiteView = $gWeb->getRequestAttribute(AppConstants::SITE_SESSION_VIEW); 
            $siteId = $gSiteView->id ;

            $qparams = \com\indigloo\Url::getRequestQueryParams();
            $pageSize = 11;
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $paginator->setBaseConvert(false);
            
            $month = $router_params["month"] ;
            $year = $router_params["year"] ;

            settype($month, "integer");
            settype($year,"integer");

            $postDao = new \com\indigloo\wb\dao\Post();
            $dbfilter = array("year" => $year, "month" => $month);
            $postDBRows = $postDao->getPaged($siteId,$paginator,$dbfilter); 

            $view = APP_WEB_DIR. "/themes/common/archive.tmpl" ;
            include ($view);
        }

    }
}
?>
