<?php
namespace com\indigloo\wb\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    class Organization {

        static function getOnId($orgId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
           
            //input check
            settype($orgId,"integer");
            
            $sql = " select * from wb_org where id = %d " ;
            $sql = sprintf($sql,$orgId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }


        static function getOnLoginId($loginId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
           
            //input check
            settype($loginId,"integer");
            
            $sql = " select org.* from wb_org org, wb_org_admin admin ".
                " where org.id = admin.org_id and admin.login_id = %d " ;

            $sql = sprintf($sql,$loginId);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getDomainCount($loginId,$domain) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
           
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

        static function create($loginId, $name) {
            $dbh = NULL ;
            
            try {

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                // first set everyone to 0 
                $sql1 = " insert into wb_org(name,domain,created_on) values(:name, :domain,now()) " ;
                $top_domain = Config::getInstance()->get_value("system.farm.domain", "indigloo.com") ;
                $domain = sprintf("%s.%s",$name,$top_domain);
                
                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":name",$name) ;
                $stmt1->bindParam(":domain",$domain) ;
                $stmt1->execute();
                $stmt1 = NULL ;

                $orgId = $dbh->lastInsertId();

                $sql2 = " insert into wb_org_domain(org_id,domain,created_on) ". 
                    " values (:org_id,:domain,now())" ;
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":org_id",$orgId) ;
                $stmt2->bindParam(":domain",$domain) ;
                $stmt2->execute();
                $stmt2 = NULL ;

                $sql3 = " insert into wb_org_admin(org_id,login_id,created_on) ".
                    " values (:org_id,:login_id,now())" ;
                $stmt3 = $dbh->prepare($sql3);
                $stmt3->bindParam(":org_id",$orgId) ;
                $stmt3->bindParam(":login_id",$loginId) ;
                $stmt3->execute();
                $stmt3 = NULL ;

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
