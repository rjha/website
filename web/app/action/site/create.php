<?php
    
    include 'wb-app.inc';
    include(APP_WEB_DIR . '/inc/header.inc');
    include(APP_WEB_DIR . '/app/inc/user.inc');

    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger ;

    use \com\indigloo\Url as Url ;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\exception\DBException as DBException;

    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\wb\Constants as AppConstants ;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $loginId = Login::getLoginIdInSession(); 
    $fvalues = array();
    $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

    try{
        
        $fhandler = new Form\Handler('form1', $_POST);
        $fhandler->addRule('name', 'Name', array('required' => 1));
        
        // get form values
        $fvalues = $fhandler->getValues();
        
        if ($fhandler->hasErrors()) {
            throw new UIException($fhandler->getErrors());
        }

        // name is alphanumeric
        if(!ctype_alnum($fvalues["name"])) {    
            $message = "Error: Only letters and numbers are allowed in a name!" ;
            throw new UIException(array($message));
        }

        $siteDao = new \com\indigloo\wb\dao\Site();
        $siteId = $siteDao->create($loginId,$fvalues["name"]) ;

        // success
        $gWeb->store(AppConstants::JUST_BORN_SITE_ID,$siteId);
        $fwd = "/app/site/receipt.php" ;
        header("Location: " .$fwd);
        

    } catch(UIException $ex) {
    	 
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());

        // decode furl  for use
        $fwd = base64_decode($fUrl);
        header("Location: " . $fwd);
        exit(1);

    } catch(DBException $ex) {
       
        $errors = array();
        $code = $ex->getCode();
        $message = $ex->getMessage();

        if( ($code == 23000)
            && Util::icontains($message,"duplicate")
            && Util::icontains($message,"uniq_name")) {
                $errors = array("This name already exists!");
        } else {
            $errors = array(" Error: doing database operation!") ;
        }

        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());

        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_ERRORS,$errors);

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