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
      
        function getOnId($siteId,$pageId) {
            $row = mysql\Page::getOnId($siteId,$pageId);
            return $row ;
        }

        function getOnSeoTitle($siteId,$seoTitle) {
            $hash = md5($seoTitle);
            $row = mysql\Page::getOnSeoTitle($siteId,$hash);
            return $row ;
        }

        function getRandomPosts($siteId,$limit) {
            $rows = mysql\Page::getRandomPosts($siteId,$limit);
            return $rows ;
        }

        function updatePost($siteId,$pageId,$postId,$title,$content,$mediaJson) {
            mysql\Page::updatePost($siteId,$pageId,$postId,$title,$content,$mediaJson) ;
        }

        function addPost($siteId,$pageId,$title,$content,$mediaJson) {
            mysql\Page::addPost($siteId,$pageId,$title,$content,$mediaJson) ;
        }

        function getPostsOnId($siteId,$pageId) {
            $rows = mysql\Page::getPostsOnId($siteId,$pageId);
            return $rows ;
        }

        function getPostsTitleOnId($siteId,$pageId) {
            $rows = mysql\Page::getPostsTitleOnId($siteId,$pageId);
            return $rows ;
        }

        function getLatestPost($siteId,$pageId) {
            $row = mysql\Page::getLatestPost($siteId,$pageId);
            return $row ;
        }

        function getPostOnPostId($siteId,$postId) {
            $row = mysql\Page::getPostOnPostId($siteId,$postId);
            return $row ;
        }

        function create($siteId,$title) {
            $pageId = mysql\Page::create($siteId,$title);
            return $pageId ;
        }
                             
    }
}

?>