<?php

    include('wb-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');
    include (APP_WEB_DIR.'/app/account/login-error.inc');

    // @imp special error handler for login page
    set_error_handler('login_error_handler');

    use \com\indigloo\Util;
    use \com\indigloo\Url ;
    use \com\indigloo\Constants as Constants;

    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Logger as Logger;
    
    use \com\indigloo\wb\auth\Login as Login;
    use \com\indigloo\wb\mysql as mysql ;

    function raiseUIError() {
        $uimessage = "something went wrong with the signin process. please try again" ;
        trigger_error($uimessage,E_USER_ERROR);
    }

    $fbAppId = Config::getInstance()->get_value("facebook.app.id");
    $fbAppSecret = Config::getInstance()->get_value("facebook.app.secret");

    $www_host = Config::getInstance()->get_value("www.host.name") ;
    $fbCallback = "http://".$www_host. "/app/account/fb/login-router.php";

    $code = NULL;
    $error = NULL ;

    if(array_key_exists("code",$_REQUEST)) {
        $code = $_REQUEST["code"];
    }

    if(array_key_exists("error",$_REQUEST)) {
        $error = $_REQUEST["error"] ;
        $description = $_REQUEST["error_description"] ;

        $message = sprintf(" Facebook returned error :: %s :: %s ",$error,$description);
        Logger::getInstance()->error($message);
        raiseUIError();
    }

    if(empty($code) && empty($error)) {
        //new state token
        $stoken = Util::getMD5GUID();
        $gWeb = \com\indigloo\core\Web::getInstance();
        $gWeb->store("fb_state_token",$stoken);

        $fbDialogUrl = "http://www.facebook.com/dialog/oauth?client_id=" .$fbAppId;
        $fbDialogUrl .= "&redirect_uri=" . urlencode($fbCallback) ."&scope=email&state=".$stoken;
        echo("<script> window.top.location ='" . $fbDialogUrl . "'</script>");
        exit ;
    }

    //last state token
    $stoken = $gWeb->find("fb_state_token",true);

    if(!empty($code) && (strcmp($_REQUEST["state"],$stoken) == 0)) {

        //request to get access token
        $fbTokenUrl = "https://graph.facebook.com/oauth/access_token?client_id=".$fbAppId ;
        $fbTokenUrl .= "&redirect_uri=" . urlencode($fbCallback). "&client_secret=" . $fbAppSecret ;
        $fbTokenUrl .= "&code=" . $code;

        $response = file_get_contents($fbTokenUrl);
        $params = null;
        parse_str($response, $params);
        
        if(!is_array($params) && !array_key_exists("access_token",$params)) {
            $message = "Could not retrieve access_token from Facebook";
            Logger::getInstance()->error($message);
            raiseUIError();
        }

        $expires = isset($params["expires"]) ? $params["expires"] : 3600 ;
        process_user($params["access_token"],$expires);

    }
    else {

        $message = "Error: Facebook state token is different from application state token";
        Logger::getInstance()->error($message);
        raiseUIError();
      
    }

    /**
     * 
     * @param access_token - access token returned by facebook for offline use
     * @param expires - time in seconds till the access_token expiry  
     * 
     * 
     */

    function process_user($access_token,$expires) {
        
        $graph_url = "https://graph.facebook.com/me?access_token=".$access_token;
        $user = json_decode(file_get_contents($graph_url));

        if(!property_exists($user,'id')) {
            $message = "No facebook_id in graph API response" ;
            Logger::getInstance()->error($message);
            raiseUIError();
        }

        
        $facebookId = $user->id;
        // these properties can be missing
        $email = property_exists($user,'email') ? $user->email : '';
        $name = property_exists($user,'name') ? $user->name : '';
        $firstName = property_exists($user,'first_name') ? $user->first_name : '';
        $lastName = property_exists($user,'last_name') ? $user->last_name : '';
        
        $firstName = empty($firstName) ? "Anonymous" : $firstName ;
        $name = empty($name) ? $firstName : $name ;

        $message = sprintf("indigloo login :: fb_id %d ,email %s ",$facebookId,$email);
        Logger::getInstance()->info($message);

        $facebookDao = new \com\indigloo\wb\dao\Facebook();
        $data = $facebookDao->getOrCreate($facebookId,
            $name,
            $firstName,
            $lastName,
            $email,
            $access_token,
            $expires);

        $loginId = $data["loginId"];
        
        if(empty($loginId)) {
            $message = "Fatal error : Not able to create login" ;
            Logger::getInstance()->error($message);
            raiseUIError();
        }
        
        // success - update login record
        // start a session
        // put loginId in session
        $remoteIp = \com\indigloo\Url::getRemoteIp();
        mysql\Login::updateTokenIp(session_id(),$loginId,$access_token,$expires,$remoteIp);
        Login::startOAuth2Session($loginId,$name);
        
        $siteDao = new \com\indigloo\wb\dao\Site();
        $siteDBRows = $siteDao->getOnLoginId($loginId);
        $num_sites = sizeof($siteDBRows);

        switch($num_sites) {
            case 0 :
                $fwd = "/app/site/create.php" ;
                header("Location: $fwd") ;
                break ;
            case 1 :
                $domain = $siteDBRows[0]["canonical_domain"] ;
                $host = "http://".$domain ;
                $params = array("session_id" => session_id(),"q" => base64_encode($host));
                $fwd = "http://".$domain."/app/account/site-session.php" ;
                $fwd = Url::createUrl($fwd,$params);
                
                header("Location: $fwd") ;
                break ;
            default :
                $fwd = "/app/site/list.php" ;
                header("Location: $fwd") ;
                break ;
            }
       
    }


 ?>
