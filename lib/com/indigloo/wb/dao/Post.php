<?php

namespace com\indigloo\wb\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\wb\mysql as mysql;
    use \com\indigloo\wb\Formatting as Formatting ;

    class Post {

        function getPaged($siteId,$paginator,$dbfilter=array()) {
            $limit = $paginator->getPageSize();
            
            if($paginator->isHome()){
                return $this->getLatest($siteId,$limit,$dbfilter);
            } else {

                $params = $paginator->getDBParams();
                $start = $params['start'];
                $direction = $params['direction'];
                $rows = mysql\Post::getPaged($siteId,$start,$direction,$limit,$dbfilter);
                return $rows ;
            }
        }

        function getLatest($siteId,$limit,$dbfilter=array()) {
            $rows = mysql\Post::getLatest($siteId,$limit,$dbfilter);
            return $rows ;
        }
        
        function getRandom($siteId,$limit) {
            $rows = mysql\Post::getRandom($siteId,$limit);
            return $rows ;
        }

        function update($siteId,
            $postId,
            $title,
            $raw_content,
            $mediaJson,
            $brify=true) {

            $html_content = ($brify === true) ? nl2br($raw_content) : $raw_content;
            $excerpt = Formatting::wp_trim_words($html_content);

            $seo_title = \com\indigloo\util\StringUtil::convertNameToKey($title);
            $has_media = (strcmp($mediaJson,'[]') == 0 ) ? 0 : 1 ;
            
            mysql\Post::update($siteId,
                $postId,
                $title,
                $seo_title,
                $raw_content,
                $html_content,
                $excerpt,
                $mediaJson,
                $has_media) ;
        }

        function add($siteId,
            $pageId,
            $title,
            $raw_content,
            $mediaJson,
            $permalink=NULL,
            $brify=true) {

            $html_content = ($brify === true) ? nl2br($raw_content) : $raw_content;
            // 55 words excerpts for posts
            $excerpt = Formatting::wp_trim_words($html_content);
            
            $seo_title = \com\indigloo\util\StringUtil::convertNameToKey($title);
            $has_media = (strcmp($mediaJson,'[]') == 0 ) ? 0 : 1 ;
            
            mysql\Post::add($siteId,
                $pageId,
                $title,
                $seo_title,
                $raw_content,
                $html_content,
                $excerpt,
                $mediaJson,
                $has_media,
                $permalink) ;
        }

        function delete($siteId,$postId) {
            mysql\Post::delete($siteId,$postId);
        }

        function getOnPageId($siteId,$pageId) {
            $rows = mysql\Post::getOnPageId($siteId,$pageId);
            return $rows ;
        }

        function getTitlesOnPageId($siteId,$pageId) {
            $rows = mysql\Post::getTitlesOnPageId($siteId,$pageId);
            return $rows ;
        }

        function getTheLatestOnPageId($siteId,$pageId) {
            $row = mysql\Post::getTheLatestOnPageId($siteId,$pageId);
            return $row ;
        }
         
        function getOnId($siteId,$postId) {
            $row = mysql\Post::getOnId($siteId,$postId);
            return $row ;
        }
                       
    }
}

?>