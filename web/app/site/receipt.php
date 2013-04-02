<?php
    require_once ('wb-app.inc');
    require_once (APP_WEB_DIR.'/inc/header.inc');
    
    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;
 
    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\wb\html\Application as AppHtml;
    use \com\indigloo\wb\Constants as AppConstants ;
    use \com\indigloo\Logger as Logger;

    if(!Login::hasSession()) {
        $www_host = Config::getInstance()->get_value("www.host.name") ;
        $fwd = "http://".$www_host. "/app/account/login.php" ;
        header('Location: '.$fwd);
        exit ;
    }

    $gWeb = \com\indigloo\core\Web::getInstance();

    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());
    $loginId = Login::getLoginIdInSession() ;

    $siteId = $gWeb->find(AppConstants::JUST_BORN_SITE_ID);

    if(empty($siteId)) {
        $message = "Error: no website_id in session " ;
        echo AppHtml::getBigError($message);
        exit ;
    }

    $siteDao = new \com\indigloo\wb\dao\Site();
    $siteDBRow = $siteDao->getOnId($siteId);

    if(empty($siteDBRow)) {
        $message = sprintf("Error: website with id %d does not exists",$siteId) ;
        echo AppHtml::getBigError($message);
        exit ;
    }

    $pageDao = new \com\indigloo\wb\dao\Page();
    // get Home page.
    // @todo - remove hard-coded string
    $pageDBRow = $pageDao->getOnSeoTitle($siteId,"home");

    if(empty($pageDBRow)) {
        $message = "Error: Home page does not exists" ;
        echo AppHtml::getBigError($message);
        exit ;
    }

    $pageId = $pageDBRow["id"] ;
    $receiptHtml = AppHtml::getSiteReceipt($siteDBRow,$pageId);

?>

<!DOCTYPE html>
<html>

    <head>
        <title> website details </title>
        <!-- meta tags -->
        <?php echo \com\indigloo\wb\util\Asset::version("/css/wb-bundle.css"); ?>

    </head>
    <?php include(APP_WEB_DIR."/app/inc/banner.inc"); ?>
        
        <div class="container">
        
            <div class="row">
                <div class="span8 offset1">
                    <?php echo $receiptHtml ; ?>
                </div>
            </div>
            

        </div>   <!-- container -->
        
    </body>
</html>