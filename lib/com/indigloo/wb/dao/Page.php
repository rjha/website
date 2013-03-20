<?php

namespace com\indigloo\wb\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\wb\mysql as mysql;

    class Page {

        function getPaged($paginator) {
            $limit = $paginator->getPageSize();
            
            if($paginator->isHome()){
                return $this->getLatest($limit);
            } else {

                $params = $paginator->getDBParams();
                $start = $params['start'];
                $direction = $params['direction'];
                $rows = mysql\Page::getPaged($start,$direction,$limit);
                return $rows ;
            }
        }

        function getLatest($limit) {
            $rows = mysql\Page::getLatest($limit);
            return $rows ;
        }

      
        function getOnId($pageId) {
            $row = mysql\Page::getOnId($pageId);
            return $row ;
        }

        function getOnSeoTitle($seoTitle) {
            $hash = md5($seoTitle);
            $row = mysql\Page::getOnSeoTitle($hash);
            return $row ;
        }

        function getIdOnSeoTitle($seoTitle) {
            $row = $this->getOnSeoTitle($seoTitle);
            $pageId = empty($row) ? NULL : $row['id'] ;
            return $pageId ;
        }

        function getRandom($limit) {
            $rows = mysql\Page::getRandom($limit);
            return $rows ;
        }

        function updateWidget($pageId,$widgetId,$title,$content,$mediaJson) {
            mysql\Page::updateWidget($pageId,$widgetId,$title,$content,$mediaJson) ;
        }

        function addWidget($pageId,$title,$content,$mediaJson) {
            mysql\Page::addWidget($pageId,$title,$content,$mediaJson) ;
        }

        function getWidgetsOnSeoTitle($seoTitle) {
            $hash = md5($seoTitle);
            $rows = mysql\Page::getWidgetsOnHash($hash);
            return $rows ;
        }

        function getWidgetsOnId($pageId) {
            $rows = mysql\Page::getWidgetsOnId($pageId);
            return $rows ;
        }

        function getWidgetsTitleOnId($pageId) {
            $rows = mysql\Page::getWidgetsTitleOnId($pageId);
            return $rows ;
        }

        function getLatestWidget($pageId) {
            $row = mysql\Page::getLatestWidget($pageId);
            return $row ;
        }

        function getWidgetOnWidgetId($pageId,$widgetId) {
            $row = mysql\Page::getWidgetOnWidgetId($pageId,$widgetId);
            return $row ;
        }

                             
    }
}

?>