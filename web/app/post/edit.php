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

    $q_title = Url::getQueryParam("title");
    $seo_title = base64_decode($q_title);

    $orgId = 1 ;
    $pageDao = new \com\indigloo\wb\dao\Page();
    $pageId = $pageDao->getIdOnSeoTitle($seo_title);
    $widgetRows = $pageDao->getWidgetsOnId($pageId);
    $widgetRow = $widgetRows[0] ;
    // if more than one widget? show in sidebar


?>

<!DOCTYPE html>
<html>

    <head>
        <title> Post edit page </title>
        <!-- meta tags -->
        <?php echo \com\indigloo\wb\util\Asset::version("/css/wb-bundle.css"); ?>
        <style>
            /* @hardcoded */
            .form-table { width:95%;}
            .form-table input {width: 90%;}
            .form-table textarea { width:90%; height: 320px;}

        </style>

    </head>

     <body>
         <header role="banner">
            <hgroup>
                <h1> <a href="/">App header</a> </h1>
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
                <div class="span12">
                    <div class="page-header">
                        <h2> Edit page</h2>
                    </div>

                </div>
            </div> <!-- page:header -->

            <div class="row">
                
                <div class="span8 offset1">
                    <div>
                        <?php FormMessage::render(); ?>
                    </div>
                    <form  id="form1"  name="form1" action="<?php echo Url::base() ?>/app/action/post/edit.php" enctype="multipart/form-data"  method="POST">  
                        <table class="form-table">
                            <tr>
                                <td>
                                    <label>Post*</label>
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
                                        <a href="/"> <button class="btn" type="button" name="cancel"><span>Cancel</span></button> </a>
                                    </div>

                                </td>
                            </tr>

                        </table>

                        <input type="hidden" name="widget_id" value="<?php echo $widgetRow['id']; ?>" />
                        <input type="hidden" name="page_id" value="<?php echo $pageId ?>" />
                        <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                        <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />


                    </form>

                </div> <!-- col:1 -->

                
                <div class="span3">
                    <!-- multiple widgets? -->
                    
                </div> <!-- col:2 -->

            </div> <!-- row:content -->

        </div>   <!-- container -->

    </body>
</html>
