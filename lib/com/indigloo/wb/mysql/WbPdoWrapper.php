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

            
            $dbstring = Config::getInstance()->get_value("system.dbstring");

            if(empty($dbstring)) {
                $message = "system.dbstring is missing in config";
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
