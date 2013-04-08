<?php
namespace com\indigloo\wb\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\exception\DBException as DBException;
    use \com\indigloo\wb\Constants as AppConstants ;

    class Site {

        static function getOnId($siteId) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
            //input check
            settype($siteId,"integer");
            
            $sql = " select * from wb_site where id = %d " ;
            $sql = sprintf($sql,$siteId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getOnLoginId($loginId) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
            // @input check
            settype($loginId,"integer");
            
            $sql = " select site.* from wb_site site, wb_site_admin admin ".
                " where site.id = admin.site_id and admin.login_id = %d " ;

            $sql = sprintf($sql,$loginId);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getOnDomain($domain) {
            
            $mysqli = WbConnection::getInstance()->getHandle();
            // @input check
            $domain = $mysqli->real_escape_string($domain);
            
            $sql = " select o.* from wb_site_domain d, wb_site o " .
                " where d.site_id = o.id and d.domain = '%s' " ;
                
            $sql = sprintf($sql,$domain);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getExtraDomains($siteId) {
            $mysqli = WbConnection::getInstance()->getHandle();
            // @input check
            settype($siteId,"integer");
            
            $sql = " select * from wb_site_domain where site_id = %d ".
                " and domain not in (select canonical_domain from wb_site where id = %d ) " ;
            $sql = sprintf($sql,$siteId,$siteId);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        /*
        static function getDomainCount($loginId,$domain) {
            $mysqli = WbConnection::getInstance()->getHandle();
           
            //input check
            settype($loginId,"integer");
            $domain = $mysqli->real_escape_string($domain);
            
            $sql = " select count(d.id) from wb_site_domain d,  wb_site o, wb_site_admin a ".
                " where d.domain = '%s' and d.site_id = o.id and o.id = a.site_id ".
                " and a.login_id = %d " ;

            $sql = sprintf($sql,$domain,$loginId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }*/

        static function getSessionView($siteId) {

            $mysqli = WbConnection::getInstance()->getHandle();
            settype($siteId,"integer");
            
            $sql = " select a.login_id from wb_site_admin a where site_id = %d  " ;
            $sql = sprintf($sql,$siteId);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }
        
        static function addDomain($siteId,$domain) {
            $dbh = NULL ;
            
            try {

                $dbh =  WbPdoWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();
                $sql1 = "insert into wb_site_domain(site_id,domain,created_on) ".
                    " values(:site_id, :domain, now())" ;

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":site_id",$siteId) ;
                $stmt1->bindParam(":domain",$domain) ;
                
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

        static function removeDomain($siteId,$domainId) {
            $dbh = NULL ;
            
            try {

                $dbh =  WbPdoWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();
                $sql1 = " delete from wb_site_domain where site_id = :site_id and id = :domain_id" ;

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":site_id",$siteId) ;
                $stmt1->bindParam(":domain_id",$domainId) ;
                
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

        static function updateTheme($siteId,$theme) {
            $dbh = NULL ;
            
            try {

                $dbh =  WbPdoWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();
                $sql1 = "update wb_site set theme_name = :theme where id = :site_id" ;

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":site_id",$siteId) ;
                $stmt1->bindParam(":theme",$theme) ;
                
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

        static function create($loginId, $name, $theme) {
            $dbh = NULL ;
            
            try {

                $dbh =  WbPdoWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $sql1 = 
                    " insert into wb_site(name,farm_domain,canonical_domain,theme_name,created_on) ".
                    " values(:name, :farm_domain, :canonical_domain, :theme, now()) " ;

                $farm_domain = Config::getInstance()->get_value("system.farm.domain", "indigloo.com") ;
                $canonical_domain = sprintf("%s.%s",$name,$farm_domain);
                 
                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":name",$name) ;
                $stmt1->bindParam(":farm_domain",$farm_domain) ;
                $stmt1->bindParam(":canonical_domain",$canonical_domain) ;
                $stmt1->bindParam(":theme",$theme) ;
                
                $stmt1->execute();
                $stmt1 = NULL ;

                $siteId = $dbh->lastInsertId();

                $sql2 = " insert into wb_site_domain(site_id,domain,created_on) ". 
                    " values (:site_id,:domain,now())" ;
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":site_id",$siteId) ;
                $stmt2->bindParam(":domain",$canonical_domain) ;
                $stmt2->execute();
                $stmt2 = NULL ;

                $sql3 = " insert into wb_site_admin(site_id,login_id,created_on) ".
                    " values (:site_id,:login_id,now())" ;
                $stmt3 = $dbh->prepare($sql3);
                $stmt3->bindParam(":site_id",$siteId) ;
                $stmt3->bindParam(":login_id",$loginId) ;
                $stmt3->execute();
                $stmt3 = NULL ;

                // insert home page
                $page_title = "Home" ;
                $seo_title = \com\indigloo\util\StringUtil::convertNameToKey($page_title);
                $seo_title_hash = md5($seo_title);
                $random_key = Util::getRandomString(16);
                
                $sql4 = " insert into wb_page(site_id,title,seo_title,seo_title_hash,random_key, created_on ) ".
                    " values (:site_id,:title,:seo_title,:hash,:random_key,now()) " ;
                
                $stmt4 = $dbh->prepare($sql4);
                $stmt4->bindParam(":site_id",$siteId);
                $stmt4->bindParam(":title", $page_title);
                $stmt4->bindParam(":seo_title", $seo_title);
                $stmt4->bindParam(":hash", $seo_title_hash);
                $stmt4->bindParam(":random_key", $random_key);
                
                $stmt4->execute();
                $stmt4 = NULL ;

                //Tx end
                $dbh->commit();
                $dbh = null;

                return $siteId ;

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($ex->getMessage(),$ex->getCode());
            }
        }

    }
}

?>
