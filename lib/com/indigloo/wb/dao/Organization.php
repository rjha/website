<?php

namespace com\indigloo\wb\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\wb\mysql as mysql;

    class Organization {

        function create($loginId,$name) {
            $orgId = mysql\Organization::create($loginId,$name);
            return $orgId ;
        }

        function getOnLoginId($loginId) {
        	$rows = mysql\Organization::getOnLoginId($loginId);
            return $rows ;
        }

        function getOnId($orgId) {
            $row = mysql\Organization::getOnId($orgId);
            return $row ;
        }

        function getOnDomain($domain) {
            $row = mysql\Organization::getOnDomain($domain);
            return $row ;
        }

        function getSessionView($orgId) {
            
            $rows = mysql\Organization::getSessionView($orgId);
            $admins = array();

            foreach($rows as $row) {
                $admins[] = $row["login_id"] ;
            }
            
            $view = new \com\indigloo\wb\view\Organization ;
            $view->admins = $admins ;

            return $view ;
        }
    }
}

?>
