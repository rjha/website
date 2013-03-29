<?php
    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\Url as Url ;
    use com\indigloo\wb\Constants as AppConstants ;
 
    $gWeb = \com\indigloo\core\Web::getInstance();
    $rp = new com\indigloo\wb\core\RequestProcessor();
    $rp->process();
    $gSiteView = $gWeb->getRequestAttribute(AppConstants::SITE_SESSION_VIEW); 
    
    if(!$gSiteView->isOwner) {
        
        $fwd = AppConstants::WWW_LOGIN_URL ;
        header('Location: '.$fwd);
        exit ;
    }
    
?>
