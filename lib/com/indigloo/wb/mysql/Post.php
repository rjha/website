<?php

namespace com\indigloo\wb\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\exception\DBException as DBException;
    use \com\indigloo\fs\Constants as AppConstants ;


    class Post {


		static function getLatest($siteId,$limit) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
           	//input check
            settype($limit, "integer");
            settype($siteId, "integer");

            // latest first
            $sql = " select * from wb_post where site_id = %d  order by id desc limit %d " ;
            $sql = sprintf($sql,$siteId,$limit); 
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getPaged($siteId,$start,$direction,$limit,$dbfilter) {

            $mysqli = WbConnection::getInstance()->getHandle();

            //sanitize input
            settype($limit, "integer");
            settype($start,"integer");
            settype($siteId, "integer");
            $direction = $mysqli->real_escape_string($direction);

            $sql = " select * from wb_post where site_id = %d " ;
            $sql = sprintf($sql,$siteId); 

            $q = new MySQL\Query($mysqli);
            $sql .= $q->getPagination($start,$direction,"id",$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            
            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }

    }

}
?>