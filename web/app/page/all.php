<?php
    require_once ('wb-app.inc');
    require_once (APP_WEB_DIR.'/inc/header.inc');
    require_once (APP_WEB_DIR.'/app/inc/admin.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\util\StringUtil as StringUtil;
    use \com\indigloo\Url as Url;

    use \com\indigloo\Constants as Constants;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\ui\form\Message as FormMessage;

    use \com\indigloo\wb\html\Application as AppHtml ;
    use \com\indigloo\wb\Constants as AppConstants;

    // get site_id injected in request
    $gWeb = \com\indigloo\core\Web::getInstance();
    $gSiteView = $gWeb->getRequestAttribute(AppConstants::SITE_SESSION_VIEW);
    $siteId = $gSiteView->id ;
    

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    
    // qUrl is where control will go after success
    // it is part of current URL params and base64 encoded
    // fUrl is current form URL where redirect happens on error
    // encode qUrl param is part of fURL 
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());

    // fetch pages
    $pageDao = new \com\indigloo\wb\dao\Page();
    $qparams = Url::getRequestQueryParams();
    $pageSize = 10;
    $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
    $paginator->setBaseConvert(false);

    $dbfilter = array();
    if(isset($qparams["token"]) && !empty($qparams["token"])) {
        // Rule: encode parameters before passing them to createUrl() method
        // What we get back from getRequestQueryParams is always urldecoded
        $dbfilter["token"] = $qparams["token"] ;
    }

    $pageDBRows = $pageDao->getPaged($siteId,$paginator,$dbfilter);
    //data for paginator
    $startId = NULL ;
    $endId = NULL ;
    $pageBaseUrl = "/app/page/all.php" ;
    $gNumRecords = sizeof($pageDBRows);

    if($gNumRecords > 0 ) {
        $startId = $pageDBRows[0]["id"] ;
        $endId =   $pageDBRows[$gNumRecords-1]["id"] ;
    }

    $pageTable = AppHtml::getPageTable($pageDBRows);
    
    //@todo : open create form when error.   

?>

<!DOCTYPE html>
<html>

    <head>
        <title> All pages </title>
        <!-- meta tags -->
        <?php echo \com\indigloo\wb\util\Asset::version("/css/wb-bundle.css"); ?>

    </head>

     <body>
        <style>
            /* @hardcoded @inpage */
            .table-nav {
                margin-bottom: 1px;
                margin-left: 0;
                list-style: none ;
            }

        </style>

        <?php include(APP_WEB_DIR."/app/inc/banner.inc"); ?>
        
        <div class="container">
        
            <div class="row">
                <div class="span3">
                    <div class="mb20">
                       <a href="<?php echo base64_decode($qUrl) ?>"> &larr;&nbsp;Back</a>
                    </div>
                    <p> All pages </p>
                </div>
                <div class="span8">
                    <div class="toolbar">
                        <ul class="tools unstyled">
                            <li> <a href="#" class="btn open-panel" rel="create-form"><i class="icon icon-plus"></i>&nbsp;New page </a></li>
                            <li>
                                <div>
                                    <form  name="form2" action="/app/action/page/search.php"   method="POST">
                                      
                                        <input type="text" class="required" name="token" value="<?php echo $_REQUEST['token'] ?>" />
                                        <button class="btn btn-small" type="submit"><i class="icon icon-search"></i></button>
                                        &nbsp;
                                        <a href="/app/page/all.php">clear?</a>
                                      </form>
                              </div>
                            </li>
                        </ul>
                        <div class="clear"> </div>
                    </div> <!-- toolbar -->

                    <?php FormMessage::render(); ?>

                    <div id="page-message" class="hide-me"> </div>
                    <div id="create-form" class="panel panel-form">
                        <div class="form-message"> </div>
                        <form  id="form1"  name="form1" action="<?php echo Url::base() ?>/app/action/page/create.php" enctype="multipart/form-data"  method="POST">  
                            <table class="form-table">
                                <tr>  
                                    <td> <label> Page name*</label>
                                        <input type="text" class="required" name="title" value="<?php echo $sticky->get('title'); ?>" /></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-actions2">
                                            <button class="btn btn-small" type="submit" name="save" value="Save">create</button>
                                            <a class="btn btn-small close-panel" rel="create-form" href="#">close</a>
                                        </div>

                                    </td>
                                </tr>
                                
                            </table>
                            <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                        </form>
                    </div> <!-- panel:1 -->

                     
                    <div style="padding-top:20px;">&nbsp; </div>
                    <?php echo $pageTable ; ?>
                </div>
                
            </div> <!-- row:1 -->

            <div class="pt10">
                <?php $paginator->render($pageBaseUrl,$startId,$endId,$gNumRecords);  ?>
            </div>

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