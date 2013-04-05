<?php

    include ('wb-app.inc');
    include(APP_WEB_DIR . '/inc/header.inc');

    $qUrl = \com\indigloo\Url::tryBase64QueryParam('q', '/');
    $qUrl = base64_decode($qUrl);

    $gWeb = \com\indigloo\core\Web::getInstance();
    $message = $gWeb->find("fs.router.message",true);
    $message = empty($message) ? "" : $message ;
?>

<!DOCTYPE html>
<html>

    <head>
        <title> Redirect page</title>
        <?php echo \com\indigloo\wb\util\Asset::version("/css/wb-bundle.css"); ?>
        
    </head>

     <body>
       <?php include(APP_WEB_DIR."/app/inc/banner.inc"); ?>
        
        <div class="container mh600">
           
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h2>Redirecting. Please wait...</h2>
                         <div> 
                            <img src="/css/asset/fs/fb_loader.gif" alt="ajax loader" />
                        </div>
                    </div>
                    <p class="text-error"> <h3> <?php echo $message; ?> </h3> </p>
                    
                </div>
            </div>
        </div> <!-- container -->

        <script>
            window.setTimeout(function() {window.location.href = '<?php echo $qUrl; ?>'; }, 8000);
        </script>

    </body>
</html>
