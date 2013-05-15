<?php

namespace com\indigloo\wb\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\exception\DBException as DBException;
    use \com\indigloo\fs\Constants as AppConstants ;


    class Post {

        // @todo fix expensive-query
        // @see http://www.warpconduit.net/2011/03/23/selecting-a-random-record-using-mysql-benchmark-results/
        // get N random posts

        static function getRandom($siteId,$limit) {
            $mysqli = WbConnection::getInstance()->getHandle();

            //sanitize input
            settype($siteId,"integer");
            settype($limit,"integer");

            $sql = " SELECT p.*  FROM wb_post p  WHERE p.site_id = %d and p.has_media = 1 " ;
            $sql .=" and RAND()<(SELECT ((%d/COUNT(*))*4) FROM wb_post p2 where p2.site_id = %d ) ";
            $sql .= " ORDER BY RAND() LIMIT %d";
            $sql = sprintf($sql,$siteId,$limit,$siteId,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function getOnPageId($siteId,$pageId) {
            $mysqli = WbConnection::getInstance()->getHandle();

            //sanitize input
            settype($pageId,"integer");
            settype($siteId,"integer");

            $sql = " select * from wb_post where page_id = %d " .
                " and site_id = %d order by id desc " ;
            $sql = sprintf($sql,$pageId,$siteId);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }   

        static function getTheLatestOnPageId($siteId,$pageId) {
            $mysqli = WbConnection::getInstance()->getHandle();
            
            //input
            settype($pageId,"integer") ;
            settype($siteId,"integer");

            $sql = " select * from wb_post where page_id = %d and site_id = %d ".
                " order by id desc limit 1" ;
            $sql = sprintf($sql,$pageId,$siteId);

            $row = MySQL\Helper::fetchRow($mysqli,$sql);
            return $row ;
        }

        static function getTitlesOnPageId($siteId,$pageId) {

            $mysqli = WbConnection::getInstance()->getHandle();
            //sanitize input
            settype($pageId,"integer");
            settype($siteId,"integer");

            $sql = " select id,title from wb_post " .
                " where page_id = %d and site_id = %d order by id desc " ;

            $sql = sprintf($sql,$pageId,$siteId);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function getOnId($siteId,$postId) {
            $mysqli = WbConnection::getInstance()->getHandle();
            //input
            settype($postId,"integer") ;
            settype($siteId,"integer");

            $sql = " select * from wb_post where id = %d  and site_id = %d " ;
            $sql = sprintf($sql,$postId,$siteId);

            $row = MySQL\Helper::fetchRow($mysqli,$sql);
            return $row ;

        }

        static function getFilter($dbfilter) {
            if(empty($dbfilter)) { return "" ;}
            
            $predicate = "" ;

            if(isset($dbfilter["year"]) && !empty($dbfilter["year"])) {
                $predicate = " and year(created_on) = ".$dbfilter["year"] ;
            }

            if(isset($dbfilter["month"]) && !empty($dbfilter["month"])) {
                $predicate = " and month(created_on) = ".$dbfilter["month"] ;
            }

            if(isset($dbfilter["tag"]) && !empty($dbfilter["tag"])) {
                $predicate = " and tags like '%%".$dbfilter["tag"]."%%' " ;
            }

            if(isset($dbfilter["category"]) && !empty($dbfilter["category"])) {
                $predicate = " and groups like '%%".$dbfilter["category"]."%%' " ;
            }

            return $predicate ;

        }

		static function getLatest($siteId,$limit,$dbfilter) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
           	//input check
            settype($limit, "integer");
            settype($siteId, "integer");

            // latest first
            $sql = " select * from wb_post where site_id = %d  " ;
            $sql .= self::getFilter($dbfilter) ;
            $sql .= " order by id desc limit %d " ;
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
            $sql .= self::getFilter($dbfilter) ;
            $sql = sprintf($sql,$siteId); 

            $q = new MySQL\Query($mysqli);
            $q->setPrefixAnd();
            
            $sql .= $q->getPagination($start,$direction,"id",$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            
            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }

        static function update($siteId,
            $postId,
            $title,
            $seo_title,
            $raw_content,
            $html_content,
            $excerpt,
            $mediaJson,
            $has_media,
            $meta_description) {

            $dbh = NULL ;
            
            try {
                
                $dbh =  WbPdoWrapper::getHandle();
                
                //Tx start
                $dbh->beginTransaction();
                
                $sql1 = 
                    " update wb_post set title = :title, seo_title = :seo_title, ".
                    " raw_content = :raw_content, html_content = :html_content, excerpt = :excerpt, ".
                    " has_media = :has_media, media_json = :media_json, meta_description = :meta_description, " .
                    " updated_on = now()where id = :post_id and site_id = :site_id " ;
                
                $stmt1 = $dbh->prepare($sql1);

                $stmt1->bindParam(":post_id", $postId);
                $stmt1->bindParam(":site_id", $siteId);
                $stmt1->bindParam(":title", $title);
                $stmt1->bindParam(":seo_title", $seo_title);

                $stmt1->bindParam(":raw_content", $raw_content);
                $stmt1->bindParam(":html_content", $html_content);
                $stmt1->bindParam(":excerpt", $excerpt);
                $stmt1->bindParam(":meta_description", $meta_description);

                $stmt1->bindParam(":has_media", $has_media);
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

        static function add($siteId,
            $pageId,
            $title,
            $seo_title,
            $raw_content,
            $html_content,
            $excerpt,
            $mediaJson,
            $has_media,
            $permalink,
            $meta_description) {

            $dbh = NULL ;

            try {
                 
                $dbh =  WbPdoWrapper::getHandle();
                
                //Tx start
                $dbh->beginTransaction();
                $sql1 = 
                    " insert into wb_post(site_id,page_id,title, seo_title,raw_content, ".
                    " html_content,excerpt,has_media,media_json, permalink,meta_description,created_on) ".
                    " values(:site_id,:page_id, :title, :seo_title,:raw_content, ".
                    " :html_content, :excerpt, :has_media, :media_json, :permalink,:meta_description, now()) " ;
                
                $stmt1 = $dbh->prepare($sql1);

                $stmt1->bindParam(":site_id", $siteId);
                $stmt1->bindParam(":page_id", $pageId);

                $stmt1->bindParam(":title", $title);
                $stmt1->bindParam(":seo_title", $seo_title);

                $stmt1->bindParam(":raw_content", $raw_content);
                $stmt1->bindParam(":html_content", $html_content);
                $stmt1->bindParam(":excerpt", $excerpt);
                $stmt1->bindParam(":meta_description", $meta_description);

                $stmt1->bindParam(":has_media", $has_media);
                $stmt1->bindParam(":media_json", $mediaJson);
                $stmt1->bindParam(":permalink", $permalink);
                
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

        static function delete($siteId,$postId) {
            $dbh = NULL ;
            
            try {

                $dbh =  WbPdoWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();
                $sql1 = " delete from wb_post where site_id = :site_id and id = :post_id" ;

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":site_id",$siteId) ;
                $stmt1->bindParam(":post_id",$postId) ;
                
                $stmt1->execute();
                $stmt1 = NULL ;

                //Tx end
                $dbh->commit();
                $dbh = null;

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($ex->getMessage(),$ex->getCode());
            }
        }

    }

}
?>