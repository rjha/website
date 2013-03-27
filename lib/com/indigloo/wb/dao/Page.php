<?php

namespace com\indigloo\wb\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\wb\mysql as mysql;

    class Page {

        function getPaged($orgId,$paginator,$dbfilter=array()) {
            $limit = $paginator->getPageSize();
            
            if($paginator->isHome()){
                return $this->getLatest($orgId,$limit,$dbfilter);
            } else {

                $params = $paginator->getDBParams();
                $start = $params['start'];
                $direction = $params['direction'];
                $rows = mysql\Page::getPaged($orgId,$start,$direction,$limit,$dbfilter);
                return $rows ;
            }
        }

        function getLatest($orgId,$limit,$dbfilter=array()) {
            $rows = mysql\Page::getLatest($orgId,$limit,$dbfilter);
            return $rows ;
        }
      
        function getOnId($orgId,$pageId) {
            $row = mysql\Page::getOnId($orgId,$pageId);
            return $row ;
        }

        function getOnSeoTitle($orgId,$seoTitle) {
            $hash = md5($seoTitle);
            $row = mysql\Page::getOnSeoTitle($orgId,$hash);
            return $row ;
        }

        /*
        function getIdOnSeoTitle($seoTitle) {
            $row = $this->getOnSeoTitle($seoTitle);
            $pageId = empty($row) ? NULL : $row['id'] ;
            return $pageId ;
        }*/

        function getRandom($orgId,$limit) {
            $rows = mysql\Page::getRandom($orgId,$limit);
            return $rows ;
        }

        function updateWidget($orgId,$pageId,$widgetId,$title,$content,$mediaJson) {
            mysql\Page::updateWidget($orgId,$pageId,$widgetId,$title,$content,$mediaJson) ;
        }

        function addWidget($orgId,$pageId,$title,$content,$mediaJson) {
            mysql\Page::addWidget($orgId,$pageId,$title,$content,$mediaJson) ;
        }

        /*
        function getWidgetsOnSeoTitle($seoTitle) {
            $hash = md5($seoTitle);
            $rows = mysql\Page::getWidgetsOnHash($hash);
            return $rows ;
        }*/

        function getWidgetsOnId($orgId,$pageId) {
            $rows = mysql\Page::getWidgetsOnId($orgId,$pageId);
            return $rows ;
        }

        function getWidgetsTitleOnId($orgId,$pageId) {
            $rows = mysql\Page::getWidgetsTitleOnId($orgId,$pageId);
            return $rows ;
        }

        function getLatestWidget($orgId,$pageId) {
            $row = mysql\Page::getLatestWidget($orgId,$pageId);
            return $row ;
        }

        function getWidgetOnWidgetId($orgId,$pageId,$widgetId) {
            $row = mysql\Page::getWidgetOnWidgetId($orgId,$pageId,$widgetId);
            return $row ;
        }

        function create($orgId,$title) {
            $pageId = mysql\Page::create($orgId,$title);
            return $pageId ;
        }
                             
    }
}

?>