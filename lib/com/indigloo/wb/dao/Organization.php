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
    }
}

?>
