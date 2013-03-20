<?php
    
    include 'wb-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger ;

    use \com\indigloo\Url as Url ;
    use \com\indigloo\exception\UIException as UIException;
    
    $gWeb = \com\indigloo\core\Web::getInstance(); 
    $fvalues = array();
    $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

    try{
        
        $fhandler = new Form\Handler('form1', $_POST);
        $fhandler->addRule('title', 'Title', array('required' => 1));
        // _do_not_ escape the content
        $fhandler->addRule('content', 'Content', array('required' => 1, "rawData" => 1));
        $fhandler->addRule('page_id', 'PageId', array('required' => 1));
       	$fhandler->addRule('media_json', 'media json', array('rawData' => 1));

        // get form values
        $fvalues = $fhandler->getValues();
        
        if ($fhandler->hasErrors()) {
            throw new UIException($fhandler->getErrors());
        }

        $pageDao = new \com\indigloo\wb\dao\Page();
        $pageDao->addWidget($fvalues["page_id"],
                            $fvalues["title"],
                            $fvalues["content"],
                            $fvalues["media_json"]);

         
        //success - go to page
        $fwd = base64_decode($fvalues["qUrl"]);
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