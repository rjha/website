<?php
    require_once ('wb-app.inc');
    require_once (APP_WEB_DIR.'/inc/header.inc');

    use com\indigloo\Util as Util;
    use com\indigloo\util\StringUtil as StringUtil;
    use com\indigloo\Url as Url;

    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\ui\form\Message as FormMessage;

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    
    // qUrl is where control will go after success
    // it is part of current URL params and base64 encoded
    // fUrl is current form URL where redirect happens on error
    // encode qUrl param is part of fURL 
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());


    $orgId = 1 ;

   

?>

<!DOCTYPE html>
<html>

    <head>
        <title> All pages </title>
        <!-- meta tags -->
        <?php echo \com\indigloo\wb\util\Asset::version("/css/wb-bundle.css"); ?>
         <style>
            
            body {
                font-size: 13px;
                font-family: "HelveticaNeue", "Helvetica Neue", Helvetica, Verdana, Arial, sans-serif ;
            }
            
            .page-header {
                padding-bottom: 9px;
            }

        </style>

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
                <div class="span3">
                     
                </div>
                <div class="span8">
                    <h3> Pages </h3>
                    <div id="user-menu">
                        <div class="item">
                            <a href="#" class="open-panel" rel="create-form">Create </a>
                        </div>
                        <div class="item">
                            <a href="#" class="open-panel" rel="search-form">Search </a>
                        </div>

                    </div> <!-- page:actions -->

                    <div id="page-message" class="hide-me"> </div>
                    <div id="create-form" class="panel panel-form">
                        <div class="form-message"> </div>
                        <form  id="form1"  name="form1" action="<?php echo Url::base() ?>/app/action/post/edit.php" enctype="multipart/form-data"  method="POST">  
                            <table class="form-table">
                                <tr>  
                                    <td> <input type="text" class="required" name="title" value="<?php echo $sticky->get('title'); ?>" /></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-actions2">
                                            <button class="btn btn-small" type="submit" name="save" value="Save">Create</button>
                                            <a class="btn btn-small close-panel" rel="create-form" href="#">Cancel </a>
                                        </div>

                                    </td>
                                </tr>
                                
                            </table>
                        </form>
                    </div> <!-- panel:1 -->

                     <div id="search-form" class="panel panel-form">
                        <div class="form-message"> </div>
                        <form  id="form2"  name="form2" action="<?php echo Url::base() ?>/app/action/post/edit.php" enctype="multipart/form-data"  method="POST">  
                            <table class="form-table">
                                <tr>  
                                    <td> <input type="text" class="required" name="token" value="<?php echo $sticky->get('token'); ?>" /></td>
                                </tr>
                                 <tr>
                                    <td>
                                        <div class="form-actions2">
                                            <button class="btn btn-small" type="submit" name="save" value="Save">Search</button>
                                            <a class="btn btn-small close-panel" rel="search-form" href="#">Cancel </a>
                                        </div>

                                    </td>
                                </tr>
                                
                            </table>
                        </form>
                    </div> <!-- panel:2 -->


                </div> <!-- column -->
                

            </div> <!-- row:content -->

        </div>   <!-- container -->
        <?php echo \com\indigloo\wb\util\Asset::version("/js/wb-bundle.js"); ?>
        
        <script type="text/javascript">

            $(document).ready(function(){

                $("#form1").validate({
                    errorLabelContainer: $("#form1 .form-message"),
                    onkeyup : false,
                    rules: {
                        title: {required: true }
                    },
                    messages: {
                        title: {required: "Title is required" }
                    }
                });

                $("a.open-panel").click(function(event) {

                    var divId = '#' + $(this).attr("rel");
                    //hide any open panels
                    $('.panel').hide();
                    //hide page message
                    $("#page-message").html('');
                    $("#page-message").hide();
                    // show target panel
                    $(divId).show("slow");
                });

                $("a.close-panel").click(function(event) {
                    event.preventDefault();
                    var divId = '#' + $(this).attr("rel");
                    $(divId).hide("slow");
                    //hide page message as well
                    $("#page-message").html('');
                    $("#page-message").hide();
                });
               
            });

        </script>
    </body>
</html>