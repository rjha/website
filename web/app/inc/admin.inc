<?php
    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\Url as Url ;
    use com\indigloo\wb\Constants as AppConstants ;
 
    $gWeb = \com\indigloo\core\Web::getInstance();
    
    $rp = new com\indigloo\wb\core\RequestProcessor();
    $rp->process();
    
    $gOrgView = $gWeb->getRequestAttribute(AppConstants::ORG_SESSION_VIEW);
    $loginId = Login::tryLoginIdInSession();
    $admins = $gOrgView->admins;

    if(empty($gOrgView) 
        || empty($loginId) 
        || !in_array($loginId,$admins)) {
         
        $fwd = AppConstants::WWW_LOGIN_URL ;
        header('Location: '.$fwd);
        exit ;
    }

?>
