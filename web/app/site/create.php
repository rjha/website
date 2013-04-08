<?php
    require_once ('wb-app.inc');
    require_once (APP_WEB_DIR.'/inc/header.inc');
     
    use \com\indigloo\Util as Util;
    use \com\indigloo\util\StringUtil as StringUtil;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Configuration as Config;

    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\wb\auth\Login as Login ;

    
    $www_host = Config::getInstance()->get_value("www.host.name") ;

    // site create only works from www.host.name domain
    if(strcmp($www_host,strtolower($_SERVER["HTTP_HOST"])) != 0 ) {
        $fwd = "http://".$www_host. "/app/site/create.php" ;
        header('Location: '.$fwd);
        exit ;
    }

    if(!Login::hasSession()) {
        $fwd = "http://".$www_host. "/app/account/login.php" ;
        header('Location: '.$fwd);
        exit ;
    }

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    
    // qUrl is where control will go after success
    // it is part of current URL params and base64 encoded
    // fUrl is current form URL where redirect happens on error
    // encode qUrl param is part of fURL 
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());
    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Create website </title>
        <!-- meta tags -->
        <?php echo \com\indigloo\wb\util\Asset::version("/css/wb-bundle.css"); ?>

    </head>

     <body>
        <?php include(APP_WEB_DIR."/app/inc/banner.inc"); ?>
        
        <div class="container">
        
            <div class="row">
                <div class="span9">
                    <div class="page-header">
                        <h2> Create website </h2>
                    </div>
                </div>
            </div>
            <div class="row">
                 
                <div class="span8 offset1">
                    <?php FormMessage::render(); ?>

                    <div id="form-message"> </div>

                    <form  id="form1"  name="form1" action="<?php echo Url::base() ?>/app/action/site/create.php" enctype="multipart/form-data"  method="POST">  
                        <table class="form-table">
                            
                            <tr>
                                <td>
                                   <p class="muted">

                                    Only letters and numbers are allowed.
                                    This name should not be in use.
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label>Name*</label>
                                    <input type="text" class="required" name="name" value="<?php echo $sticky->get('name'); ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td>
                                     <p class="muted">
                                        Select default for a small website. Masonry is good for
                                        image heavy websites.
                                    </p>
                                    <label> Home page style </label>
                                    
                                    <select name="theme">
                                        <option value="default">Default</option>
                                        <option value="masonry">Masonry</option>
                                        <option value="blog">Blog</option>
                                    </select>
                                </td>
                            </tr>


                            <tr>
                                <td>
                                    <div class="form-actions">
                                        <button class="btn btn-primary" type="submit" name="save" value="Save"><span>Save</span></button>
                                         <a href="<?php echo base64_decode($qUrl); ?>"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
                                    </div>

                                </td>
                            </tr>

                        </table>

                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />


                    </form>

                </div>

            </div> <!-- row:content -->

        </div>   <!-- container -->
        <?php echo \com\indigloo\wb\util\Asset::version("/js/wb-bundle.js"); ?>
        
        <script type="text/javascript">

            $(document).ready(function(){

                $("#form1").validate({
                    errorLabelContainer: $("#form-message"),
                    onkeyup : false,
                    rules: { name: {required: true }},
                    messages: {name: {required: "Name is required" }}
                });

            });

        </script>
    </body>
</html>