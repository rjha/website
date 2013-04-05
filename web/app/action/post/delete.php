<?php
    
    include 'wb-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/app/inc/admin.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger ;

    use \com\indigloo\Url as Url ;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\wb\Constants as AppConstants;
    
    $gWeb = \com\indigloo\core\Web::getInstance();  
    $qUrl = Url::tryBase64QueryParam("q", "/");

    try{
        
        // find site_id injected in request
        $gSiteView = $gWeb->getRequestAttribute(AppConstants::SITE_SESSION_VIEW);
        $siteId = $gSiteView->id ;
        $qparams = Url::getRequestQueryParams();

        if(!isset($qparams["post_id"]) || empty($qparams["post_id"])) {
            $message ="post delete action:: post id is missing" ;
            throw new UIException(array($message)) ;
        }

        $postId = $qparams["post_id"];
        $postDao = new \com\indigloo\wb\dao\Post();
        $postDao->delete($siteId,$postId) ;
        
        // success - post not there - go Home
        $fwd = Url::createUrl("/app/spinner.php", array("q" => base64_encode("/"))) ;
        $message = "success! post deleted" ;
        $gWeb->store("fs.router.message",$message);
        header("Location: ".$fwd);
        
    } catch(UIException $ex) {
    	 
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());

        // decode furl  for use
        $fwd = base64_decode($qUrl);
        header("Location: " . $fwd);
        exit(1);
    }catch(\Exception $ex) {
    	 
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());

        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $message = " Error: something went wrong!" ;
        $gWeb->store(Constants::FORM_ERRORS,array($message));

        // decode fUrl  for use
        $fwd = base64_decode($qUrl);
        header("Location: " . $fwd);
        exit(1);
    }

    
?>