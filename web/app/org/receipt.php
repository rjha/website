<?php
    require_once ('wb-app.inc');
    require_once (APP_WEB_DIR.'/inc/header.inc');
    require_once (APP_WEB_DIR.'/app/inc/admin.inc');

    use com\indigloo\Util as Util;
    use com\indigloo\util\StringUtil as StringUtil;
    use com\indigloo\Url as Url;

    use com\indigloo\Constants as Constants;    
    use \com\indigloo\wb\auth\Login as Login ;

    $gWeb = \com\indigloo\core\Web::getInstance();

    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());
    $loginId = Login::getLoginIdInSession() ;

    $sOrgId = $gWeb->find("global.org.receipt.id");
    if(empty($sOrgId)) {
        echo "Error: no organization id in session " ;
        exit ;
    }

    // $orgDao = new \com\indigloo\wb\dao\organization();
    // $organization = $orgDao->getOnId($sOrgId);


    
    

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
                <div class="span9">
                    <div class="page-header">
                        <h2> Website details</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                 
                <div class="span8 offset1">
                   
                </div>

            </div> <!-- row:content -->

        </div>   <!-- container -->
        
    </body>
</html>