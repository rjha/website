<?php
    
    include 'wb-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR.'/app/inc/admin.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger ;

    use \com\indigloo\Url as Url ;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\wb\Constants as AppConstants;

    
    $gWeb = \com\indigloo\core\Web::getInstance();
    $fvalues = array();
    $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

    try{
        
        $fhandler = new Form\Handler('form1', $_POST);
        $fhandler->addRule('title', 'Title', array('required' => 1));
        
        // get form values
        $fvalues = $fhandler->getValues();
        
        if ($fhandler->hasErrors()) {
            throw new UIException($fhandler->getErrors());
        }

        // find site_id injected in request
        $gSiteView = $gWeb->getRequestAttribute(AppConstants::SITE_SESSION_VIEW);
        $siteId = $gSiteView->id ;

        $pageDao = new \com\indigloo\wb\dao\Page();
        $pageId = $pageDao->create($siteId,$fvalues["title"]);

        //success
        $messages = array("page created successfully!");
        $gWeb->store(Constants::FORM_MESSAGES,$messages);
        $fwd = base64_decode($fUrl);
        header("Location: " . $fwd);
        

    } catch(UIException $ex) {
    	 
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());

        // decode furl  for use
        $fwd = base64_decode($fUrl);
        header("Location: " . $fwd);
        exit(1);
    }catch(\Exception $ex) {
    	 
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());

        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $message = " Error: something went wrong!" ;
        $gWeb->store(Constants::FORM_ERRORS,array($message));

        // decode fUrl  for use
        $fwd = base64_decode($fUrl);
        header("Location: " . $fwd);
        exit(1);
    }

    
?>