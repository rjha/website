<?php

namespace com\indigloo\wb\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\Logger as Logger ;
    

    class Page {

		static function getLatest($limit) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
           
           	//input check
            settype($limit, "integer");

            $sql = " select * from wb_page order by id limit %d " ;
            $sql = sprintf($sql,$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getPaged($start,$direction,$limit) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit, "integer");
            settype($start,"integer");
            $direction = $mysqli->real_escape_string($direction);

            
            $sql =  " select * from wb_page page " ;
            $q = new MySQL\Query($mysqli);
            
            $sql .= $q->getPagination($start,$direction,"page.id",$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            
            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }

        static function getWidgetsOnHash($hash) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            $sql = " select wpc.* from wb_page_content wpc, wb_page wp" .
                " where  wpc.page_id = wp.id and wp.seo_title_hash = '%s' order by wpc.row_number " ;
            $sql = sprintf($sql,$hash);
            
            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
            return $rows ;

        }

        static function getLinks($limit) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            $sql = " select title,seo_title from wb_page order by id limit %d " ;
            $sql = sprintf($sql,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
            return $rows ;
        }
    }

}
?>