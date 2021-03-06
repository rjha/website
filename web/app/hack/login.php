<?php
    include ('wb-app.inc');
    include (APP_WEB_DIR.'/inc/header.inc');

    use com\indigloo\Constants as Constants;
    use \com\indigloo\Logger as Logger;

    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\wb\mysql as mysql ;
    
    try{

        $access_token = NULL ;

        if(array_key_exists("token", $_POST)) {
            $access_token = $_POST["token"] ;
        }

        if(!empty($access_token)) {
            //copy from graph API explorer
            // make sure you have manage_pages/publish_stream/email permissions
            $message = "" ;
            $facebookId = "100000110234029" ;
            $name = "Rajeev Jha" ;
            $firstName = "Rajeev" ;
            $lastName = "Jha" ;
            $email ="jha.rajeev@gmail.com" ;
            
            // we need to ensure that expiry > what is in valid token check
            // otherwise our code goes nuts!
            // @see http://developers.facebook.com/docs/howtos/login/extending-tokens/
            // facebook short lived token should be valid for 1-2 HR
            // our code validation is for 30 minutes 
            // so lets put the expire_on after 1 HR
            $expires = 1*3600 ;
            
            $facebookDao = new \com\indigloo\wb\dao\Facebook();
            $data = $facebookDao->getOrCreate($facebookId,
                $name,
                $firstName,
                $lastName,
                $email,
                $access_token,
                $expires);

            $loginId = $data["loginId"];
            
            // success - update login record
            // start a session
            // put loginId in session
            $remoteIp = \com\indigloo\Url::getRemoteIp();
            mysql\Login::updateTokenIp(session_id(),$loginId,$access_token,$expires,$remoteIp);
            Login::startOAuth2Session($loginId,$name);
            
            $siteDao = new \com\indigloo\wb\dao\Site();
            $siteDBRows = $siteDao->getOnLoginId($loginId);
            $num_sites = sizeof($siteDBRows);

            if($num_sites == 0 ) {
                $fwd = "/app/site/create.php" ;
                header("Location: $fwd") ;
            } else if($num_sites == 1 ) {
                $domain = $siteDBRows[0]["canonical_domain"] ;
                $fwd = "http://".$domain ;
                header("Location: $fwd") ;

            } else {
                $fwd = "/app/site/list.php" ;
                header("Location: $fwd") ;
            }

        } else {
            $message = "No token : enter a token please!" ;
        }

        
    } catch(\Exception $ex) {
        
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());
        $message = $ex->getMessage();
        
    }
    
?>

<!DOCTYPE html>
<html>

    <head>
        <title> Login Hack</title>
        <?php echo \com\indigloo\wb\util\Asset::version("/css/wb-bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR."/app/inc/banner.inc"); ?>
        
        <div class="container">
            
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h2>Login Hack</h2>
                    </div>
                    <p class="comment-text"> <?php echo $message; ?> </p>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        Token : <input type="text" name ="token" value="" style="width:600px;"/>
                        <button class="btn" type="submit" name="submit" value="submit"> Submit</button>
                    </form>
                    
                </div>
            </div>
        </div>
         

    </body>
</html>