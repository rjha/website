<?php

namespace com\indigloo\wb\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\wb\mysql as mysql;

    class Facebook {

        function getOrCreate($facebookId,
            $name,
            $firstName,
            $lastName,
            $email,
            $access_token,
            $expires) {

            // input check
            if(empty($facebookId)) {
                $message = "Bad input : No facebook id for user!" ;
                trigger_error($message,E_USER_ERROR);
            }

            if(empty($access_token)) {
                $message = "Bad input : access token is missing!" ;
                trigger_error($message,E_USER_ERROR);
            }

            $data = array();
            $loginId = NULL ;

            //is existing record?
            $facebookId = trim($facebookId);
            $remoteIp =  \com\indigloo\Url::getRemoteIp();
            $row = $this->getOnFacebookId($facebookId);

            if(empty($row)){
                $message = sprintf("Login::Facebook::create id %s, email %s ",$facebookId,$email);
                Logger::getInstance()->info($message);
                $source = 1 ;

                $loginId = mysql\Facebook::create(
                    $facebookId,
                    $name,
                    $firstName, 
                    $lastName,
                    $email,
                    $source,
                    $access_token,
                    $expires,
                    $remoteIp);

                $data["loginId"] = $loginId;

            } else {
                
                // existing  facebook user
                $loginId = $row["login_id"];
                $data["loginId"] = $loginId;
                
            }

            return $data ;

        }

        function getOnFacebookId($facebookId) {
            $row = mysql\Facebook::getOnFacebookId($facebookId);
            return $row ;
        }

        function getOnLoginId($loginId) {
            $row = mysql\Facebook::getOnLoginId($loginId);
            return $row ;
        }
        
    }
}

?>
