<?php
namespace com\indigloo\wb\controller{
    
    use \com\indigloo\Url as Url ;

    class Tile {

        
        function __construct() {
            
        }

        function process($params,$options) {

        	$gpage = Url::tryQueryParam("gpage");
            $gpage = empty($gpage) ? "1" : $gpage ;
            if($gpage == "1") {
                $this->loadHomePage($gpage);
            } else {
                $this->loadNextPage($gpage);
            }
        }

        function loadHomePage($gpage) {
        	
    		$pageDao = new \com\indigloo\wb\dao\Page();
            $pageSize = 20;
            $pageDBRows = $pageDao->getLatest($pageSize);

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

        function loadNextPage($gpage) {
			 
    		$pageDao = new \com\indigloo\wb\dao\Page();
     		$qparams = Url::getRequestQueryParams();

            $pageSize = 20;
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $paginator->setBaseConvert(false);

            $pageDBRows = $pageDao->getPaged($paginator);

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
