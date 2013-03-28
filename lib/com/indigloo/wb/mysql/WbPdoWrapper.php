<?php
namespace com\indigloo\wb\mysql {

    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\wb\Constants as AppConstants;
    
    /*
     * to close the connx to database, set $dbh = NULL inside your application code
     * 
     * excerpt from the PHP manual 
     * ------------------------------------------
     * 
     * Upon successful connection to the database, an instance of the PDO class
     * is returned to your script. The connection remains active for the lifetime
     * of that PDO object. To close the connection, you need to destroy the object 
     * by ensuring that all remaining references to it are deleted--you do this by 
     * assigning NULL to the variable that holds the object. If you don't do this 
     * explicitly, PHP will automatically close the connection when your script ends.
     * 
     * ---------------------------------------------
     *
     */

    class WbPdoWrapper {

        static function getHandle() {

            $gWeb = \com\indigloo\core\Web::getInstance();
            // Top Level domain
            $top_domain = $gWeb->getRequestAttribute(AppConstants::SITE_TOP_DOMAIN);
            $key = sprintf("%s.%s",$top_domain,"dbstring");
            $dbstring = Config::getInstance()->get_value($key);

            if(empty($dbstring)) {
                $key = "system.dbstring" ;
                $dbstring = Config::getInstance()->get_value($key);
            }

            if(empty($dbstring)) {
                $message = sprintf("domain %s :: system.dbstring is missing in config",$top_domain);
                trigger_error($message,E_USER_ERROR);
            }

            $pieces = explode(":",$dbstring);

            $host = $pieces[0] ;
            $dbname = $pieces[3] ;
            $dsn = sprintf("mysql:host=%s;dbname=%s",$host,$dbname);

            $user = $pieces[1] ;
            $password = $pieces[2];
            $dbh = new \PDO($dsn, $user, $password);

            //throw exceptions
            $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $dbh ;
        }
    }


}
?>
