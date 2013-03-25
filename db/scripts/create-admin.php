<?php 
    include('wb-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Util as Util;
    
    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    error_reporting(-1);
    set_exception_handler('offline_exception_handler');

    // @WIP
    function create_admin($name, $email,$password) {

        $dbh = NULL ;
            
            try {

                $dbh =  PDOWrapper::getHandle();
                
                //Tx start
                $dbh->beginTransaction();
                
                // add entry to wb_login table
                // 2 HR expiry
                $expires = 2*3600 ;
                $access_token = "__MAGIC_TOKEN__" ;
                // local source
                $source = 1 ;
                $remoteIp =  \com\indigloo\Url::getRemoteIp();

                $sql1 = "insert into wb_login(name,source,access_token, " .
                    " created_on,expire_on,ip_address) ".
                    " values(:name,:source,:access_token, now(),%s, :ip_address) " ;
               

                $expiresOn = "(now() + interval ".$expires. " second)";
                $sql1 = sprintf($sql1,$expiresOn);

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":name", $name);
                $stmt1->bindParam(":source", $source);
                $stmt1->bindParam(":access_token", $access_token);
                $stmt1->bindParam(":ip_address", $remoteIp);

                $stmt1->execute();
                $stmt1 = NULL ;
                
                $loginId = $dbh->lastInsertId();
                settype($loginId, "integer");

                // create entry in wb_super_admin table

                $sql2 = " insert into wb_super_admin(login_id, name, email, password, salt) ".
                    " values(:login_id, :name, :email, :password, :salt) " ;

                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":login_id", $loginId);
                $stmt2->bindParam(":name", $name);
                $stmt2->bindParam(":email", $email);
                $stmt2->bindParam(":password", $password);
                $stmt2->bindParam(":login_id", $salt);

                
                $stmt2->execute();
                $stmt2 = NULL ;

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



?>
