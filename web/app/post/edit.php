<?php
    require_once ('wb-app.inc');
    require_once (APP_WEB_DIR.'/inc/header.inc');

    use com\indigloo\Util as Util;
    use com\indigloo\util\StringUtil as StringUtil;
    use com\indigloo\Url as Url;

    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\ui\form\Message as FormMessage;

    use \com\indigloo\wb\html\Application as AppHtml ;

    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    
    // qUrl is where control will go after success
    // it is part of current URL params and base64 encoded
    // fUrl is current form URL where redirect happens on error
    // encode qUrl param is part of fURL 

    $qparams = Url::getRequestQueryParams();
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());

    $qPageId = Url::tryQueryParam("page_id");
    $qWidgetId = Url::tryQueryParam("tab_id");

    if(empty($qPageId)) {
        echo " Error :: page_id is missing from request " ;
        exit ;
    }

    $orgId = 1 ;
    $pageDao = new \com\indigloo\wb\dao\Page();

    $widgetRow = empty($qWidgetId) ? 
        $pageDao->getLatestWidget($qPageId) :$pageDao->getWidgetOnWidgetId($qPageId,$qWidgetId) ;

    if(empty($widgetRow)) {
        echo " Error :: No post found for this page!" ;
        exit ;
    }

    $pageDBRow = $pageDao->getOnId($qPageId);
    $post_title = $widgetRow["title"];

    // @imp: why formSafeJson? we are enclosing the JSON string in single quotes
    // so the single quotes coming from DB should be escaped
    $strMediaJson = $sticky->get('media_json',$widgetRow['media_json']) ;
    $strMediaJson = Util::formSafeJson($strMediaJson);

    // widgets list
    $widgetTabRows = $pageDao->getWidgetsTitleOnId($qPageId);

    $tabParams = $qparams ;
    unset($tabParams["tab_id"]);
    
    $baseURI = Url::base()."/app/post/edit.php" ;
    $widgetTabsHtml = AppHtml::getWidgetTabs($baseURI,$tabParams,$widgetRow["id"],$widgetTabRows);

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Edit <?php echo $pageDBRow["title"]; ?>  </title>
        <!-- meta tags -->
        <?php echo \com\indigloo\wb\util\Asset::version("/css/wb-bundle.css"); ?>
        <style>
            /* @hardcoded @inpage */
            .form-table { width:95%;}
            .form-table input {width: 90%;}
            .form-table textarea { width:90%; height: 320px;}
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
                    <div class="span11 offset1">
                        <?php echo $widgetTabsHtml ; ?>
                    </div>
                </div> <!-- row:header -->

                <div class="row">
                    
                    <div class="span9 offset1">
                    <?php FormMessage::render(); ?>
                    <div class="toolbar">
                        <ul class="tools unstyled">
                            <li> <a id="ful-open" href="#ful-container"><i class="icon icon-camera"></i>&nbsp;Add photo</a></li>
                        </ul>
                        <div class="clear"> </div>
                    </div> <!-- toolbar -->

                    <div id="form-message"> </div>

                    <form  id="form1"  name="form1" action="<?php echo Url::base() ?>/app/action/post/edit.php" enctype="multipart/form-data"  method="POST">  
                        <table class="form-table">
                            
                            <tr>  
                                <td>
                                    <div id="ful-container"> </div> 
                                    <div class="section1">
                                        <div id="image-preview"> </div>
                                    </div>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Title*</label>
                                    <input type="text" class="required" name="title" value="<?php echo $sticky->get('title',$widgetRow['title']); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Content*</label>
                                    <textarea name="content" class="required" cols="50" rows="4" ><?php echo $sticky->get('content',$widgetRow['widget_html']); ?></textarea>
                                    
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

                        <input type="hidden" name="widget_id" value="<?php echo $widgetRow['id']; ?>" />
                        <input type="hidden" name="page_id" value="<?php echo $qPageId ?>" />
                        <input type="hidden" name="media_json" value='<?php echo $strMediaJson ; ?>' />
                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />


                    </form>

                </div> <!-- col:1 -->

            </div> <!-- row:content -->

        </div>   <!-- container -->
        <?php echo \com\indigloo\wb\util\Asset::version("/js/wb-bundle.js"); ?>
        
        <script type="text/javascript">

            $(document).ready(function(){

                $("#form1").validate({
                    errorLabelContainer: $("#form-message"),
                    onkeyup : false,
                    rules: {
                        title: {required: true } ,
                        content: {required: true} 
                        
                    },
                    messages: {
                        title: {required: "Title is required" },
                        content: {required: "Content is required"}
                    }
                });

                // use all default options
                var media_options = {} ;
                webgloo.media.init(media_options);
                webgloo.media.attachEvents();
                
                var uploader = new qq.FileUploader({
                    element: document.getElementById('ful-container'),
                    button : document.getElementById('ful-open'),
                    action: '/app/action/upload/image.php',
                    allowedExtensions: ['png','gif','jpg','jpeg'],
                    debug: false,
                    uploadButtonText : 'Add photo', 
                    
                    onComplete: function(id, fileName, responseJSON) {
                        webgloo.media.addImage(responseJSON.mediaVO);
                    },  

                    showMessage: function(message){ 
                        var tmpl = '<li class="qq-uplad-fail"> <span class="error"> {message}</span></li> ';
                        var errorMessage = tmpl.supplant({"message" : message}) ;
                        $(".qq-upload-list").append(errorMessage);
                        
                    }
                });
            });

        </script>
    </body>
</html>
