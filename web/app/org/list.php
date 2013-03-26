<?php
    require_once ('wb-app.inc');
    require_once (APP_WEB_DIR.'/inc/header.inc');   
    require_once (APP_WEB_DIR.'/app/inc/admin.inc');
   
    use \com\indigloo\Util as Util;
    use \com\indigloo\util\StringUtil as StringUtil;
    use \com\indigloo\Url as Url;

    use \com\indigloo\Constants as Constants;
    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\wb\html\Application as AppHtml ;

    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());

    $loginId = Login::getLoginIdInSession();
 
    $orgDao = new \com\indigloo\wb\dao\Organization();
    $orgDBRows = $orgDao->getOnLoginId($loginId);
    $orgTableHtml = AppHtml::getOrgTable($orgDBRows);
    
       

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Select a website </title>
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
                    <div class="page--header">
                        <h2> Select a website </h2>
                    </div>
                    <?php echo $orgTableHtml ; ?>
                 </div>
            </div> <!-- row:1 -->

        </div>   <!-- container -->
       
    </body>
</html>