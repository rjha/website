<?php

namespace com\indigloo\wb\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\wb\mysql as mysql;

    class Site {

        function create($loginId,$name) {
            // avoid case confusion.
            $name = strtolower($name);
            $siteId = mysql\Site::create($loginId,$name);
            return $siteId ;
        }

        function getOnLoginId($loginId) {
        	$rows = mysql\Site::getOnLoginId($loginId);
            return $rows ;
        }

        function getOnId($siteId) {
            $row = mysql\Site::getOnId($siteId);
            return $row ;
        }

        function getOnDomain($domain) {
            $domain = strtolower($domain);
            $row = mysql\Site::getOnDomain($domain);
            return $row ;
        }

        function getSessionView($siteId) {
            
            $rows = mysql\Site::getSessionView($siteId);
            $admins = array();

            foreach($rows as $row) {
                $admins[] = $row["login_id"] ;
            }
            
            $view = new \com\indigloo\wb\view\Site ;
            $view->admins = $admins ;

            return $view ;
        }

        function getExtraDomains($siteId) {
            $rows = mysql\Site::getExtraDomains($siteId);
            return $rows ;
        }

        function addDomain($siteId,$domain) {
            $domain = strtolower($domain);
            mysql\Site::addDomain($siteId,$domain);
        }

        function removeDomain($siteId,$domainId) {
            mysql\Site::removeDomain($siteId,$domainId);
        }

        function updateTheme($siteId,$theme) {
            mysql\Site::updateTheme($siteId,$theme);
        }

    }
}

?>
