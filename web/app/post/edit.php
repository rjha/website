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
    $qparams = Url::getRequestQueryParams();
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());

    $qPostId = Url::tryQueryParam("post_id");

    if(empty($qPostId)) {
        echo " Error :: post_id is missing from request " ;
        exit ;
    }

    $postDao = new \com\indigloo\wb\dao\Post();
    $postDBRow =  $postDao->getOnId($siteId,$qPostId) ;

    if(empty($postDBRow)) {
        echo " Error :: No post found for this page!" ;
        exit ;
    }

    $post_title = $postDBRow["title"];

    // @imp: why formSafeJson? we are enclosing the JSON string in single quotes
    // so the single quotes coming from DB should be escaped
    $strMediaJson = $sticky->get('media_json',$postDBRow['media_json']) ;
    $strMediaJson = Util::formSafeJson($strMediaJson);

    //post delete link
    $params = array("q" => $qUrl, "post_id" => $qPostId);
    $postDeleteHref = Url::createUrl("/app/action/post/delete.php",$params);

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Edit <?php echo $post_title ; ?> </title>
        <!-- meta tags -->
        <?php echo \com\indigloo\wb\util\Asset::version("/css/wb-bundle.css"); ?>
        <style>
            /* @hardcoded @inpage */
            .form-table { width:95%;}
            .form-table input {width: 90%;}
            .form-table textarea { width:90%; height: 320px;}
            .form-table .small-textarea { width:90%; height: 90px;}
            
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
                    <div class="mb20">
                       <a href="<?php echo base64_decode($qUrl) ?>"> &larr;&nbsp;Back</a>
                    </div>
                    <p>
                        Edit <?php echo $post_title ; ?>
                    </p>
                     
                </div>

                <div class="span8">

                    <div id="page-message" class="hide-me"> </div>
                    <?php FormMessage::render(); ?>

                    <div class="toolbar">
                        <ul class="tools unstyled">
                            <li> <a id="ful-open" href="#ful-container"><i class="icon icon-camera"></i>&nbsp;Add photo</a></li>
                            <li> <a id="confirm-delete" href="<?php echo $postDeleteHref; ?>"><i class="icon icon-remove"></i>&nbsp;Delete post</a></li>
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
                                    <textarea name="content" class="required" cols="50" rows="4" ><?php echo $sticky->get('content',$postDBRow['raw_content']); ?></textarea>
                                    
                                </td>
                            </tr>
                            
                            <tr>
                                <td> 
                                    <label>Excerpt*</label>
                                    <textarea name="excerpt" class="small-textarea required" cols="50" rows="2" ><?php echo $sticky->get('excerpt',$postDBRow['excerpt']); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td> 
                                    <label>Meta Description* (max 160 chars)</label>
                                    <textarea id="meta_description" maxlength="160" name="meta_description" class="required small-textarea" cols="10" rows="1" ><?php echo $sticky->get('meta_description',$postDBRow['meta_description']); ?></textarea>
                                    <br>
                                    <span id="meta_description_counter"></span>
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
                        <input type="hidden" name="page_id" value="<?php echo $postDBRow['page_id'] ?>" />
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
                $(".close-page-message").live("click",function(event) {
                    $("#page-message").html("");
                    $("#page-message").hide('slow');

                });

                $("#confirm-delete").click(function(event) {
                    event.preventDefault();
                    var data = {"deleteHref" : $(this).attr("href")} ;
                    var links = 
                        ' <div> <span> Really delete this post? </span> &nbsp;' +
                        ' <a href="{deleteHref}">Yes</a> &nbsp;|&nbsp; ' +
                        ' <a href="#" class="close-page-message">Cancel</a> ' +
                        ' <span> &nbsp; </span> </div>' ;
                    links = links.supplant(data) ;

                    $("#page-message").html(links);
                    $("#page-message").show('slow');
                });

                $("#form1").validate({
                    errorLabelContainer: $("#form-message"),
                    onkeyup : false,
                    rules: {
                        title: {required: true } ,
                        content: {required: true},
                        excerpt: {required: true},
                        meta_description: {required: true}
                        
                    },
                    messages: {
                        title: {required: "Title is required" },
                        content: {required: "Content is required"},
                        excerpt: {required: "Excerpt is required"},
                        meta_description: {required: "Meta Description is required"}
                    }
                });

                webgloo.util.addTextCounter("#meta_description", "#meta_description_counter");

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
