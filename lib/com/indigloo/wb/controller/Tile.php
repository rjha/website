<?php
namespace com\indigloo\wb\controller{
    
    use \com\indigloo\Url as Url ;
    use \com\indigloo\wb\Constants as AppConstants;

    class Tile {

        
        function __construct() {}

        function process($params,$options) {

        	$gpage = Url::tryQueryParam("gpage");
            $gpage = empty($gpage) ? "1" : $gpage ;


            $gWeb = \com\indigloo\core\Web::getInstance();
            $gSiteView = $gWeb->getRequestAttribute(AppConstants::SITE_SESSION_VIEW);
            $siteId = $gSiteView->id ;

            if($gpage == "1") {
                $this->loadHomePage($siteId,$gpage);
            } else {
                $this->loadNextPage($siteId,$gpage);
            }
        }

        function loadHomePage($siteId,$gpage) {
        	
    		$postDao = new \com\indigloo\wb\dao\Post();
            $pageSize = 20;


            $postDBRows = $postDao->getLatest($siteId,$pageSize);

            $qparams = Url::getRequestQueryParams();
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $paginator->setBaseConvert(false);

         	//data for paginator
         	$startId = NULL ;
         	$endId = NULL ;
         	$pageBaseUrl = "/" ;
         	$gNumRecords = sizeof($postDBRows);

         	if($gNumRecords > 0 ) {
         		$startId = $postDBRows[0]["id"] ;
                $endId =   $postDBRows[$gNumRecords-1]["id"] ;
         	}

            $view = APP_WEB_DIR. "/themes/vanilla/tiles.tmpl" ;
            include ($view);
        }

        function loadNextPage($siteId,$gpage) {
			 
    		$postDao = new \com\indigloo\wb\dao\Post();
     		$qparams = Url::getRequestQueryParams();

            $pageSize = 20;
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $paginator->setBaseConvert(false);

            $postDBRows = $postDao->getPaged($siteId,$paginator);

            //data for paginator
         	$startId = NULL ;
         	$endId = NULL ;
         	$pageBaseUrl = "/" ;
         	$gNumRecords = sizeof($postDBRows);

         	if($gNumRecords > 0 ) {
         		$startId = $postDBRows[0]["id"] ;
                $endId =   $postDBRows[$gNumRecords-1]["id"] ;
         	}

            $view = APP_WEB_DIR. "/themes/vanilla/tiles.tmpl" ;
            include ($view);
        }
        
    }
}
?>
