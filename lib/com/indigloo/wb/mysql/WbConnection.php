<?php



namespace com\indigloo\wb\mysql {

    use \com\indigloo\Logger as Logger;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\wb\Constants as AppConstants;

    class WbConnection {

        static private $instance = NULL;
        private $mysqli ;
        
        private function __construct() {
            $this->mysqli = NULL ;
        }

        function close() {
            if (!is_null($this->mysqli)) {
                $this->mysqli->close();
            }

            self::$instance == NULL;
        }

        static function getInstance() {
            if (is_null(self::$instance)) {
                self::$instance = new \com\indigloo\wb\mysql\WbConnection();
            }
            
            return self::$instance;
        }

        public function getHandle() {
             
            if(is_null($this->mysqli)) {
                
                // read map of domain vs. database from config file
                $gWeb = \com\indigloo\core\Web::getInstance();
                $top_domain = $gWeb->getRequestAttribute(AppConstants::ORG_TOP_DOMAIN);

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
                $this->mysqli = new \mysqli($pieces[0],$pieces[1],$pieces[2],$pieces[3]);

                if (mysqli_connect_errno ()) {
                    trigger_error(mysqli_connect_error(), E_USER_ERROR);
                    exit(1);
                }

            }

            return $this->mysqli;
        }

        public function getLastInsertId() {
            return $this->mysqli->insert_id ;
        }

    }

}
?>
