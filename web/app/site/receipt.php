<?php
    require_once ('wb-app.inc');
    require_once (APP_WEB_DIR.'/inc/header.inc');
    require_once (APP_WEB_DIR.'/app/inc/user.inc');

    use com\indigloo\Util as Util;
    use com\indigloo\util\StringUtil as StringUtil;
    use com\indigloo\Url as Url;

    use com\indigloo\Constants as Constants;    
    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\wb\html\Application as AppHtml;
    use \com\indigloo\wb\Constants as AppConstants ;

    $gWeb = \com\indigloo\core\Web::getInstance();

    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());
    $loginId = Login::getLoginIdInSession() ;

    $siteId = $gWeb->find(AppConstants::JUST_BORN_SITE_ID);

    if(empty($siteId)) {
        echo "Error: no website_id in session " ;
        exit ;
    }

    $siteDao = new \com\indigloo\wb\dao\Site();
    $siteDBRow = $siteDao->getOnId($siteId);

    if(empty($siteDBRow)) {
        $message = sprintf("Error: website with id %d does not exists",$siteId) ;
        echo $message ;
        exit ;
    }

    $pageDao = new \com\indigloo\wb\dao\Page();
    // get Home page.
    // @todo - remove hard-coded string
    $pageDBRow = $pageDao->getOnSeoTitle($siteId,"home");

    if(empty($pageDBRow)) {
        $message = "Error: Home page does not exists" ;
        echo $message ;
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

     <body>
         <header role="banner">
            <hgroup>
                <h1> <a href="/">website builder app</a> </h1>
            </hgroup>

        </header>

        <div class="navbar">
            <div class="navbar-inner">
                <div class="container">

                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a> <!-- 3bars for smaller screens -->

                    <div class="nav-collapse">
                        <ul class="nav">
                            <li> <a href="/"><i class="icon icon-home"></i>&nbsp;Home</a></li>
                        </ul>
                        
                    </div>

                </div>

            </div>
        </div> <!-- toolbar -->
        
        <div class="container">
        
            <div class="row">
                <div class="span8 offset1">
                    <?php echo $receiptHtml ; ?>
                </div>
            </div>
            

        </div>   <!-- container -->
        
    </body>
</html>