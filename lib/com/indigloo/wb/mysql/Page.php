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
        // get N random posts

        static function getRandomPosts($siteId,$limit) {
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

        static function getPostOnPostId($siteId,$postId) {
            $mysqli = WbConnection::getInstance()->getHandle();
            //input
            settype($postId,"integer") ;
            settype($siteId,"integer");

            $sql = " select * from wb_post where id = %d  and site_id = %d " ;
            $sql = sprintf($sql,$postId,$siteId);

            $row = MySQL\Helper::fetchRow($mysqli,$sql);
            return $row ;

        }

        static function getLatestPost($siteId,$pageId) {
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

        static function getPostsOnId($siteId,$pageId) {
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

        static function getPostsTitleOnId($siteId,$pageId) {

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

        static function updatePost($siteId,$pageId,$postId,$title,$content,$mediaJson) {

            $dbh = NULL ;
            
            try {
                
                $dbh =  WbPdoWrapper::getHandle();
                
                //Tx start
                $dbh->beginTransaction();
                
                $sql1 = " update wb_post set title = :title, post_html = :content, ".
                        " has_media = :has_media, media_json = :media_json " .
                        " where id = :post_id and page_id = :page_id and site_id = :site_id " ;
                
                $stmt1 = $dbh->prepare($sql1);

                $stmt1->bindParam(":post_id", $postId);
                $stmt1->bindParam(":page_id", $pageId);
                $stmt1->bindParam(":site_id", $siteId);

                $stmt1->bindParam(":title", $title);
                $stmt1->bindParam(":content", $content);

                $has_media = (strcmp($mediaJson,'[]') == 0 ) ? 0 : 1 ;
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

        static function addPost($siteId,$pageId,$title,$content,$mediaJson) {

            $dbh = NULL ;

            try {
                 
                $dbh =  WbPdoWrapper::getHandle();
                
                
                //Tx start
                $dbh->beginTransaction();
                $sql1 = 
                    " insert into wb_post(site_id,page_id,title, seo_title,post_html,has_media,media_json) ".
                    " values(:site_id,:page_id, :title, :seo_title,:content,:has_media, :media_json) " ;
                
                $stmt1 = $dbh->prepare($sql1);

                $stmt1->bindParam(":site_id", $siteId);
                $stmt1->bindParam(":page_id", $pageId);

                $seo_title = \com\indigloo\util\StringUtil::convertNameToKey($title);
                $stmt1->bindParam(":title", $title);
                $stmt1->bindParam(":seo_title", $seo_title);
                $stmt1->bindParam(":content", $content);

                $has_media = (strcmp($mediaJson,'[]') == 0 ) ? 0 : 1 ;
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
    }

}
?>