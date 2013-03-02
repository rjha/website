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

        function getWidgetsOnSeoTile($seoTitle) {
            $hash = md5($seoTitle);
            $rows = mysql\Page::getWidgetsOnHash($hash);
            return $rows ;
        }

        function getLinks($limit) {
            $rows = mysql\Page::getLinks($limit);
            return $rows ;
        }
    }
}

?>