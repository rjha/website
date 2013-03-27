<?php

namespace com\indigloo\wb\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\exception\DBException as DBException;
    use \com\indigloo\fs\Constants as AppConstants ;


    class Page {

        // @todo fix expensive-query
        // @see http://www.warpconduit.net/2011/03/23/selecting-a-random-record-using-mysql-benchmark-results/
        // @examined This query is used on thanks page after logout 
        // and Random posts controller.
        static function getRandom($orgId,$limit) {
            $mysqli = WbConnection::getInstance()->getHandle();

            //sanitize input
            settype($orgId,"integer");
            settype($limit,"integer");

            $sql = " SELECT p.*  FROM wb_page p  WHERE p.org_id = %d and p.has_media = 1 " ;
            $sql .=" and RAND()<(SELECT ((%d/COUNT(*))*4) FROM wb_page p2 where p2.org_id = %d ) ";
            $sql .= " ORDER BY RAND() LIMIT %d";
            $sql = sprintf($sql,$orgId,$limit,$orgId,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        /*
        static function get($limit) {
            $mysqli = WbConnection::getInstance()->getHandle();
            // latest first
            $sql = " select title,seo_title from wb_page order by id desc limit %d " ;
            $sql = sprintf($sql,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
            return $rows ;
        } */

		static function getLatest($orgId,$limit,$dbfilter) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
           	//input check
            settype($limit, "integer");
            settype($orgId, "integer");

            // latest first
            $sql = " select * from wb_page where org_id = %d " ;
            $sql = sprintf($sql,$orgId); 

            if(!empty($dbfilter) && isset($dbfilter["token"]) && !empty($dbfilter["token"])) {
                // use % to escape % in the sprintf
                $sql =  sprintf(" %s and title like '%s%%' ",$sql,$dbfilter["token"]);
            }
            
            $sql = sprintf(" %s  order by id desc limit %d ",$sql,$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getPaged($orgId,$start,$direction,$limit,$dbfilter) {

            $mysqli = WbConnection::getInstance()->getHandle();

            //sanitize input
            settype($limit, "integer");
            settype($start,"integer");
            settype($orgId, "integer");
            $direction = $mysqli->real_escape_string($direction);

            $sql = " select * from wb_page where org_id = %d " ;
            $sql = sprintf($sql,$orgId); 

            $q = new MySQL\Query($mysqli);
            $q->setPrefixAnd();

            if(!empty($dbfilter) && isset($dbfilter["token"]) && !empty($dbfilter["token"])) {
                // A percentage (%) is required to escape % inside sprintf
                // @imp - a string with percentage sign can be substituted as-it-is 
                // inside sprintf  using %s. 
                // However if you plan to use the part with % as a variable inside sprintf 
                // then you have to escape again (double escaping)

                $part =  " page.title like '%s%%' " ;
                $part = sprintf($part,$dbfilter["token"]) ;
                $q->addCondition($part) ;
                $sql .= $q->get();
            }

            $sql .= $q->getPagination($start,$direction,"page.id",$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            
            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }

        static function getOnSeoTitle($orgId,$hash) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
            //input check
            $hash = $mysqli->real_escape_string($hash);
            settype($orgId,"integer");
            
            $sql = " select * from wb_page where seo_title_hash = '%s' and org_id = %d " ;
            $sql = sprintf($sql,$hash,$orgId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getOnId($orgId,$pageId) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
            //input check
            settype($pageId,"integer");
            settype($orgId,"integer");

            $sql = " select * from wb_page where id = %d and org_id = %d" ;
            $sql = sprintf($sql,$pageId,$orgId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        /*
        static function getWidgetsOnHash($hash) {
            
            $mysqli = WbConnection::getInstance()->getHandle();

            $sql = " select wpc.* from wb_page_content wpc, wb_page wp" .
                " where  wpc.page_id = wp.id and wp.seo_title_hash = '%s' order by wpc.id desc " ;
            $sql = sprintf($sql,$hash);
            
            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
            return $rows ;

        } */

        static function getWidgetOnWidgetId($orgId,$pageId,$widgetId) {
            $mysqli = WbConnection::getInstance()->getHandle();
            //input
            settype($pageId,"integer") ;
            settype($widgetId,"integer") ;
            settype($orgId,"integer");

            $sql = " select * from wb_page_content where id = %d and page_id = %d and org_id = %d " ;
            $sql = sprintf($sql,$widgetId,$pageId,$orgId);

            $row = MySQL\Helper::fetchRow($mysqli,$sql);
            return $row ;

        }

        static function getLatestWidget($orgId,$pageId) {
            $mysqli = WbConnection::getInstance()->getHandle();
            
            //input
            settype($pageId,"integer") ;
            settype($orgId,"integer");

            $sql = " select * from wb_page_content where page_id = %d and org_id = %d ".
                " order by id desc limit 1" ;
            $sql = sprintf($sql,$pageId,$orgId);

            $row = MySQL\Helper::fetchRow($mysqli,$sql);
            return $row ;
        }

        static function getWidgetsOnId($orgId,$pageId) {
            $mysqli = WbConnection::getInstance()->getHandle();

            //sanitize input
            settype($pageId,"integer");
            settype($orgId,"integer");

            $sql = " select * from wb_page_content where page_id = %d " .
                " and org_id = %d order by id desc " ;
            $sql = sprintf($sql,$pageId,$orgId);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }   

        static function getWidgetsTitleOnId($orgId,$pageId) {

            $mysqli = WbConnection::getInstance()->getHandle();
            //sanitize input
            settype($pageId,"integer");
            settype($orgId,"integer");

            $sql = " select id,title from wb_page_content " .
                " where page_id = %d and org_id = %d order by id desc " ;

            $sql = sprintf($sql,$pageId,$orgId);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function updateWidget($orgId,$pageId,$widgetId,$title,$content,$mediaJson) {

            $dbh = NULL ;
            
            try {
                
                $dbh =  WbPdoWrapper::getHandle();
                
                //Tx start
                $dbh->beginTransaction();
                
                $sql1 = " update wb_page_content set title = :title, widget_html = :content, ".
                        " media_json = :media_json where id = :widget_id " .
                        " and page_id = :page_id and org_id = :org_id " ;
                
                $stmt1 = $dbh->prepare($sql1);

                $stmt1->bindParam(":widget_id", $widgetId);
                $stmt1->bindParam(":page_id", $pageId);
                $stmt1->bindParam(":org_id", $orgId);

                $stmt1->bindParam(":title", $title);
                $stmt1->bindParam(":content", $content);
                $stmt1->bindParam(":media_json", $mediaJson);

                $stmt1->execute();
                $stmt1 = NULL ;

                //Tx end
                $dbh->commit();
                $dbh = null;


            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                $message = $ex->getMessage();
                throw new DBException($message);
            }
        }

        static function create($orgId,$title) {
            
            $dbh = NULL ;
            $pageId = NULL ;

            try {

                $dbh =  WbPdoWrapper::getHandle();
                $dbh->beginTransaction();

                // new page
                $seo_title = \com\indigloo\util\StringUtil::convertNameToKey($title);
                $seo_title_hash = md5($seo_title);
                $random_key = Util::getRandomString(16);
                
                $sql = " insert into wb_page(org_id,title,seo_title,seo_title_hash,random_key, created_on ) ".
                    " values (:org_id,:title,:seo_title,:hash,:random_key,now()) " ;
                
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(":org_id",$orgId);
                $stmt->bindParam(":title", $title);
                $stmt->bindParam(":seo_title", $seo_title);
                $stmt->bindParam(":hash", $seo_title_hash);
                $stmt->bindParam(":random_key", $random_key);
                
                $stmt->execute();
                $stmt = NULL ;

                $pageId = $dbh->lastInsertId();

                //Tx end
                $dbh->commit();
                $dbh = null;
                
                return $pageId ;

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                $message = $ex->getMessage();
                throw new DBException($message);
            }

        }

        static function addWidget($orgId,$pageId,$title,$content,$mediaJson) {

            $dbh = NULL ;

            try {
                 
                $dbh =  WbPdoWrapper::getHandle();
                
                //Tx start
                $dbh->beginTransaction();
                $sql1 = " insert into wb_page_content(org_id,page_id,title,widget_html,media_json) ".
                        " values(:org_id,:page_id, :title, :content, :media_json) " ;
                
                $stmt1 = $dbh->prepare($sql1);

                $stmt1->bindParam(":org_id", $orgId);
                $stmt1->bindParam(":page_id", $pageId);
                $stmt1->bindParam(":title", $title);
                $stmt1->bindParam(":content", $content);
                $stmt1->bindParam(":media_json", $mediaJson);

                $stmt1->execute();
                $stmt1 = NULL ;

                //Tx end
                $dbh->commit();
                $dbh = null;

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                $message = $ex->getMessage();
                throw new DBException($message);
            }
        }
    }

}
?>