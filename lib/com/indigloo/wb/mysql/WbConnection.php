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
                
                
                $dbstring = Config::getInstance()->get_value("system.dbstring");
                if(empty($dbstring)) {
                    $message = "system.dbstring is missing in config";
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
