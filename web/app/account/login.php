<?php
    require_once ('wb-app.inc');
    require_once (APP_WEB_DIR.'/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
     
    use com\indigloo\ui\form\Message as FormMessage;

    $www_host = Config::getInstance()->get_value("www.host.name") ;

    // login only works from www.host.name domain
    if(strcmp($www_host,strtolower($_SERVER["HTTP_HOST"])) != 0 ) {
        $fwd = "http://".$www_host. "/app/account/login.php" ;
        header('Location: '.$fwd);
        exit ;
    }

    $gWeb = \com\indigloo\core\Web::getInstance();
    $stoken = Util::getMD5GUID();
    $gWeb->store("fb_state_token",$stoken);

    $fbAppId = Config::getInstance()->get_value("facebook.app.id");
    // facebook login callback
    $fbCallback = "http://".$www_host."/app/account/fb/login-router.php" ;

    $fbDialogUrl = "https://www.facebook.com/dialog/oauth?client_id=".$fbAppId ;
    $fbDialogUrl .= "&redirect_uri=".urlencode($fbCallback)."&scope=email&state=".$stoken ;

    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo G_APP_NAME ; ?> - Sign In</title>
        <?php echo \com\indigloo\wb\util\Asset::version("/css/wb-bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/banner.inc'); ?>
        
        <div class="container mh600">
            
            <div class="row">
                <div class="span8 offset1">
                    <?php FormMessage::render() ?>
                    <div class="p20">

                       <h3> Sign in </h3>
                        <p class="text-info">
                           You need to sign in to create a new website or manage your
                           existing website.
                        </p>
                        <div class="mt20">
                            <a target="_top" href="<?php echo $fbDialogUrl; ?>" class="btn btn-large btn-primary">Sign in with Facebook &nbsp; &raquo;</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html>

