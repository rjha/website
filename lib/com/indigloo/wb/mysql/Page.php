<?php

namespace com\indigloo\wb\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\exception\DBException as DBException;
    use \com\indigloo\fs\Constants as AppConstants ;


    class Page {

		static function getLatest($siteId,$limit,$dbfilter) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
           	//input check
            settype($limit, "integer");
            settype($siteId, "integer");

            // latest first
            $sql = " select * from wb_page where site_id = %d " ;
            $sql = sprintf($sql,$siteId); 

            if(!empty($dbfilter) && isset($dbfilter["token"]) && !empty($dbfilter["token"])) {
                // use % to escape % in the sprintf
                $sql =  sprintf(" %s and title like '%s%%' ",$sql,$dbfilter["token"]);
            }
            
            $sql = sprintf(" %s  order by id desc limit %d ",$sql,$limit);
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

            $sql = " select * from wb_page where site_id = %d " ;
            $sql = sprintf($sql,$siteId); 

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

        static function getLatestTitles($siteId,$limit) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
            //input check
            settype($limit, "integer");
            settype($siteId, "integer");

            // latest first
            $sql = " select id,title,seo_title,random_key from wb_page ".
                " where site_id = %d order by title  limit %d" ;
            $sql = sprintf($sql,$siteId,$limit); 
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getOnSeoTitle($siteId,$hash) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
            //input check
            $hash = $mysqli->real_escape_string($hash);
            settype($siteId,"integer");
            
            $sql = " select * from wb_page where seo_title_hash = '%s' and site_id = %d " ;
            $sql = sprintf($sql,$hash,$siteId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getOnId($siteId,$pageId) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
            //input check
            settype($pageId,"integer");
            settype($siteId,"integer");

            $sql = " select * from wb_page where id = %d and site_id = %d" ;
            $sql = sprintf($sql,$pageId,$siteId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function create($siteId,$title) {
            
            $dbh = NULL ;
            $pageId = NULL ;

            try {

                $dbh =  WbPdoWrapper::getHandle();
                $dbh->beginTransaction();

                // new page
                $seo_title = \com\indigloo\util\StringUtil::convertNameToKey($title);
                $seo_title_hash = md5($seo_title);
                $random_key = Util::getRandomString(16);
                
                $sql = " insert into wb_page(site_id,title,seo_title,seo_title_hash,random_key, created_on ) ".
                    " values (:site_id,:title,:seo_title,:hash,:random_key,now()) " ;
                
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(":site_id",$siteId);
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
    }

}
?>