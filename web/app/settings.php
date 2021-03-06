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
    
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());

    // fetch extra domains 
    // do not bring in the canonical domain
    $siteDao = new \com\indigloo\wb\dao\Site();
    $extraDomains = $siteDao->getExtraDomains($siteId);
    $siteDBRow = $siteDao->getOnId($siteId);

    $domainsTableHtml = AppHtml::getDomainsTable($siteId,$extraDomains);

    
?>

<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo $gSiteView->name ;?> settings </title>
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

            .form-table .w600 { width : 600px;}

        </style>

        <?php include(APP_WEB_DIR."/app/inc/banner.inc"); ?>
        
        <div class="container">
        
            <div class="row">
                <div class="span3">
                    <div class="mb20">
                       <a href="<?php echo base64_decode($qUrl) ?>"> &larr;&nbsp;Back</a>
                    </div>
                    <p> settings </p>
                </div>
                <div class="span8">
                    <?php FormMessage::render(); ?>
                     <div class="section">
                       
                        <p class="muted">
                            Select default for a small website. Masonry is good for image heavy websites
                            <br>use Blog for magazines and personal blogs. 
                        </p>
                        <form  id="form2"  name="form2" action="<?php echo Url::base() ?>/app/action/site/update-theme.php"  method="POST">  
                            <table class="form-table">
                                <tr>  
                                    <td> 
                                        <label>
                                        Home page style (current :<b> <?php echo $siteDBRow["theme_name"]; ?> </b>)</label>
                                       <select name="theme">
                                        <option value="default">Default</option>
                                        <option value="masonry">Masonry</option>
                                        <option value="blog">Blog</option>
                                       </select>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-actions2">
                                            <button class="btn btn-small" type="submit" name="save" value="Save">Save</button>
                                        </div>

                                    </td>
                                </tr>
                                
                            </table>

                            <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                        </form>
                    </div>
                    <div class="section">
                        <p class="muted">
                            Need to access this site from your own domain?  
                            Add it here. <br> You also have to point your DNS
                            records to our IP.

                        </p>
                        <form  id="form1"  name="form1" action="<?php echo Url::base() ?>/app/action/site/add-domain.php"  method="POST">  
                            <table class="form-table">
                                <tr>  
                                    <td> <label>Your domain*</label>
                                        <input type="text" class="required" name="domain" value="<?php echo $sticky->get('domain'); ?>" /></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-actions2">
                                            <button class="btn btn-small" type="submit" name="save" value="Save">Save</button>
                                        </div>

                                    </td>
                                </tr>
                                
                            </table>

                            <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                        </form>
                    </div>
                    <div class="section1">
                        <?php echo $domainsTableHtml ; ?>
                    </div>
                   
                    <div class="section">
                        <p class="muted"> 
                            site header and footer will be included on every page.
                        </p>
                        <form  id="form3"  name="form3" action="<?php echo Url::base() ?>/app/action/site/hf.php"  method="POST">  
                            <table class="form-table">
                                <tr>  
                                    <td> <label>Site Header</label>
                                        <textarea name="page_header" class="w600" ><?php echo $sticky->get('page_header',$siteDBRow["page_header"]); ?></textarea>
                                    </td>
                                </tr>
                                <tr>  
                                    <td> <label>Footer</label>
                                        <textarea name="page_footer" class="w600"><?php echo $sticky->get('page_footer',$siteDBRow["page_footer"]); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-actions2">
                                            <button class="btn btn-small" type="submit" name="save" value="Save">Save</button>
                                        </div>

                                    </td>
                                </tr>
                                
                            </table>

                            <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />
                        </form>
                    </div>

                </div>
                     
                
            </div> <!-- row:1 -->

        </div>   <!-- container -->
       
    </body>
</html>