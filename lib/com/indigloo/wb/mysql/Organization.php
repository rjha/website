<?php
namespace com\indigloo\wb\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\exception\DBException as DBException;

    class Organization {

        static function getOnId($orgId) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
            //input check
            settype($orgId,"integer");
            
            $sql = " select * from wb_org where id = %d " ;
            $sql = sprintf($sql,$orgId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getOnLoginId($loginId) {

            $mysqli = WbConnection::getInstance()->getHandle();
           
            // @input check
            settype($loginId,"integer");
            
            $sql = " select org.* from wb_org org, wb_org_admin admin ".
                " where org.id = admin.org_id and admin.login_id = %d " ;

            $sql = sprintf($sql,$loginId);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getOnDomain($domain) {
            
            $mysqli = WbConnection::getInstance()->getHandle();
            // @input check
            $domain = $mysqli->real_escape_string($domain);
            
            $sql = " select org_id from wb_org_domain where domain = '%s' " ;
            $sql = sprintf($sql,$domain);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            
            return $row;
        }

        static function getDomainCount($loginId,$domain) {
            $mysqli = WbConnection::getInstance()->getHandle();
           
            //input check
            settype($loginId,"integer");
            $domain = $mysqli->real_escape_string($domain);
            
            $sql = " select count(d.id) from wb_org_domain d,  wb_org o, wb_org_admin a ".
                " where d.domain = '%s' and d.org_id = o.id and o.id = a.org_id ".
                " and a.login_id = %d " ;

            $sql = sprintf($sql,$domain,$loginId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getSessionView($orgId) {

            $mysqli = WbConnection::getInstance()->getHandle();
            settype($orgId,"integer");
            
            $sql = " select a.login_id from wb_org_admin a where org_id = %d  " ;
            $sql = sprintf($sql,$orgId);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function create($loginId, $name) {
            $dbh = NULL ;
            
            try {

                $dbh =  WbPdoWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $sql1 = " insert into wb_org(name,farm_domain,canonical_domain,created_on) ".
                    " values(:name, :farm_domain, :canonical_domain, now()) " ;
                $farm_domain = Config::getInstance()->get_value("system.farm.domain", "indigloo.com") ;
                $canonical_domain = sprintf("%s.%s",$name,$farm_domain);
                
                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":name",$name) ;
                $stmt1->bindParam(":farm_domain",$farm_domain) ;
                $stmt1->bindParam(":canonical_domain",$canonical_domain) ;
                
                $stmt1->execute();
                $stmt1 = NULL ;

                $orgId = $dbh->lastInsertId();

                $sql2 = " insert into wb_org_domain(org_id,domain,created_on) ". 
                    " values (:org_id,:domain,now())" ;
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":org_id",$orgId) ;
                $stmt2->bindParam(":domain",$canonical_domain) ;
                $stmt2->execute();
                $stmt2 = NULL ;

                $sql3 = " insert into wb_org_admin(org_id,login_id,created_on) ".
                    " values (:org_id,:login_id,now())" ;
                $stmt3 = $dbh->prepare($sql3);
                $stmt3->bindParam(":org_id",$orgId) ;
                $stmt3->bindParam(":login_id",$loginId) ;
                $stmt3->execute();
                $stmt3 = NULL ;

                // insert home page
                $page_title = "Home" ;
                $seo_title = \com\indigloo\util\StringUtil::convertNameToKey($page_title);
                $seo_title_hash = md5($seo_title);
                $random_key = Util::getRandomString(16);
                
                $sql4 = " insert into wb_page(org_id,title,seo_title,seo_title_hash,random_key, created_on ) ".
                    " values (:org_id,:title,:seo_title,:hash,:random_key,now()) " ;
                
                $stmt4 = $dbh->prepare($sql4);
                $stmt4->bindParam(":org_id",$orgId);
                $stmt4->bindParam(":title", $page_title);
                $stmt4->bindParam(":seo_title", $seo_title);
                $stmt4->bindParam(":hash", $seo_title_hash);
                $stmt4->bindParam(":random_key", $random_key);
                
                $stmt4->execute();
                $stmt4 = NULL ;

                //Tx end
                $dbh->commit();
                $dbh = null;

                return $orgId ;

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($ex->getMessage(),$ex->getCode());
            }
        }

    }
}

?>
