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
            $gOrgView = $gWeb->getRequestAttribute(AppConstants::ORG_SESSION_VIEW);
            $orgId = $gOrgView->id ;

            if($gpage == "1") {
                $this->loadHomePage($orgId,$gpage);
            } else {
                $this->loadNextPage($orgId,$gpage);
            }
        }

        function loadHomePage($orgId,$gpage) {
        	
    		$pageDao = new \com\indigloo\wb\dao\Page();
            $pageSize = 20;


            $pageDBRows = $pageDao->getLatest($orgId,$pageSize);

            $qparams = Url::getRequestQueryParams();
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $paginator->setBaseConvert(false);

         	//data for paginator
         	$startId = NULL ;
         	$endId = NULL ;
         	$pageBaseUrl = "/" ;
         	$gNumRecords = sizeof($pageDBRows);

         	if($gNumRecords > 0 ) {
         		$startId = $pageDBRows[0]["id"] ;
                $endId =   $pageDBRows[$gNumRecords-1]["id"] ;
         	}

            $title = sprintf("page %d of theconverseshoes.info",$gpage);
            $gMetaDescription = $title;
            $gPageTitle = $title ;
            $gMetaKeywords = "shoes converse shoes vans shoes knee high reef sandal";

            $view = APP_WEB_DIR. "/themes/vanilla/tiles.tmpl" ;
            include ($view);
        }

        function loadNextPage($orgId,$gpage) {
			 
    		$pageDao = new \com\indigloo\wb\dao\Page();
     		$qparams = Url::getRequestQueryParams();

            $pageSize = 20;
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $paginator->setBaseConvert(false);

            $pageDBRows = $pageDao->getPaged($orgId,$paginator);

            //data for paginator
         	$startId = NULL ;
         	$endId = NULL ;
         	$pageBaseUrl = "/" ;
         	$gNumRecords = sizeof($pageDBRows);

         	if($gNumRecords > 0 ) {
         		$startId = $pageDBRows[0]["id"] ;
                $endId =   $pageDBRows[$gNumRecords-1]["id"] ;
         	}

            $title = sprintf("page %d of theconverseshoes.info",$gpage);
            $gMetaDescription = $title;
            $gPageTitle = $title ;
            $gMetaKeywords = "shoes converse shoes vans shoes knee high reef sandal";

            $view = APP_WEB_DIR. "/themes/vanilla/tiles.tmpl" ;
            include ($view);
        }
        
    }
}
?>
