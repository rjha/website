<?php
    require_once ('wb-app.inc');
    require_once (APP_WEB_DIR.'/inc/header.inc');
    require_once (APP_WEB_DIR.'/app/inc/admin.inc');

    use com\indigloo\Util as Util;
    use com\indigloo\util\StringUtil as StringUtil;
    use com\indigloo\Url as Url;

    use com\indigloo\Constants as Constants;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\ui\form\Message as FormMessage;

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

    $qparams = Url::getRequestQueryParams();
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());

    $qPageId = Url::tryQueryParam("page_id");
    $qPostId = Url::tryQueryParam("tab_id");

    if(empty($qPageId)) {
        echo " Error :: page_id is missing from request " ;
        exit ;
    }

    $pageDao = new \com\indigloo\wb\dao\Page();
    $postDBRow = empty($qPostId) ? 
        $pageDao->getLatestPost($siteId,$qPageId) :$pageDao->getPostOnPostId($siteId,$qPostId) ;

    if(empty($postDBRow)) {
        echo " Error :: No post found for this page!" ;
        exit ;
    }

    $pageDBRow = $pageDao->getOnId($siteId,$qPageId);
    $post_title = $postDBRow["title"];

    // @imp: why formSafeJson? we are enclosing the JSON string in single quotes
    // so the single quotes coming from DB should be escaped
    $strMediaJson = $sticky->get('media_json',$postDBRow['media_json']) ;
    $strMediaJson = Util::formSafeJson($strMediaJson);

    $postTabRows = $pageDao->getPostsTitleOnId($siteId,$qPageId);

    $tabParams = $qparams ;
    unset($tabParams["tab_id"]);
    
    $baseURI = Url::base()."/app/post/edit.php" ;
    $postTabsHtml = AppHtml::getPostTabs($baseURI,$tabParams,$postDBRow["id"],$postTabRows);

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
            
            .page-header {
                padding-bottom: 9px;
            }

        </style>

    </head>

     <body>
        <?php include(APP_WEB_DIR."/app/inc/banner.inc"); ?>
        <div class="container">
             
            <div class="row">
                <div class="span3">
                <p>
                    <?php echo $pageDBRow["title"]; ?> 
                    <br>
                    &nbsp;Edit post
                </p>
                    <?php echo $postTabsHtml ; ?>
                </div>

                <div class="span8">
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
                                    <input type="text" class="required" name="title" value="<?php echo $sticky->get('title',$postDBRow['title']); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Content*</label>
                                    <textarea name="content" class="required" cols="50" rows="4" ><?php echo $sticky->get('content',$postDBRow['post_html']); ?></textarea>
                                    
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

                        <input type="hidden" name="post_id" value="<?php echo $postDBRow['id']; ?>" />
                        <input type="hidden" name="page_id" value="<?php echo $qPageId ?>" />
                        <input type="hidden" name="media_json" value='<?php echo $strMediaJson ; ?>' />
                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />


                    </form>

                </div> <!-- col:2 -->

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
