<?php
    require_once ('wb-app.inc');
    require_once (APP_WEB_DIR.'/inc/header.inc');   
    
    use \com\indigloo\Util as Util;
    use \com\indigloo\util\StringUtil as StringUtil;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\Constants as Constants;
    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\wb\html\Application as AppHtml ;

    if(!Login::hasSession()) {
        $www_host = Config::getInstance()->get_value("www.host.name") ;
        $fwd = "http://".$www_host. "/app/account/login.php" ;
        header('Location: '.$fwd);
        exit ;
    }

    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());

    $loginId = Login::getLoginIdInSession();
 
    $siteDao = new \com\indigloo\wb\dao\Site();
    $siteDBRows = $siteDao->getOnLoginId($loginId);
    $siteTableHtml = AppHtml::getSiteTable($siteDBRows);
    
       

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Select a website </title>
        <!-- meta tags -->
        <?php echo \com\indigloo\wb\util\Asset::version("/css/wb-bundle.css"); ?>

    </head>

     <body>
     <?php include(APP_WEB_DIR."/app/inc/banner.inc"); ?>
        
        <div class="container">
        
            <div class="row">
                <div class="span8 offset1">
                    <div class="page--header">
                        <h2> Select a website </h2>
                    </div>
                    <?php echo $siteTableHtml ; ?>
                 </div>
            </div> <!-- row:1 -->

        </div>   <!-- container -->
       
    </body>
</html>