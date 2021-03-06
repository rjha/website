<?php

namespace com\indigloo\wb\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\wb\mysql as mysql;

    class Page {

        function getPaged($siteId,$paginator,$dbfilter=array()) {
            $limit = $paginator->getPageSize();
            
            if($paginator->isHome()){
                return $this->getLatest($siteId,$limit,$dbfilter);
            } else {

                $params = $paginator->getDBParams();
                $start = $params['start'];
                $direction = $params['direction'];
                $rows = mysql\Page::getPaged($siteId,$start,$direction,$limit,$dbfilter);
                return $rows ;
            }
        }

        function getLatest($siteId,$limit,$dbfilter=array()) {
            $rows = mysql\Page::getLatest($siteId,$limit,$dbfilter);
            return $rows ;
        }
      
        function getLatestTitles($siteId,$limit) {
            $rows = mysql\Page::getLatestTitles($siteId,$limit);
            return $rows ;
        }

        function getOnId($siteId,$pageId) {
            $row = mysql\Page::getOnId($siteId,$pageId);
            return $row ;
        }

        function getOnSeoTitle($siteId,$seoTitle) {
            $hash = md5($seoTitle);
            $row = mysql\Page::getOnSeoTitle($siteId,$hash);
            return $row ;
        }

        function create($siteId,$title) {
            $pageId = mysql\Page::create($siteId,$title);
            return $pageId ;
        }
                             
    }
}

?>