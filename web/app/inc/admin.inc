<?php
    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\Url as Url ;
    use com\indigloo\wb\Constants as AppConstants ;
 
    $gWeb = \com\indigloo\core\Web::getInstance();

     // + site domain check
    $domain = $_SERVER["HTTP_HOST"];
    $orgDao = new \com\indigloo\wb\dao\Organization();
    $orgDBRow = $orgDao->getOnDomain($domain);

    if(empty($orgDBRow)) {
        $message = sprintf("<h2> Unknown domain :: %s </h2> ",$domain) ;
        echo $message ;
        exit(1);
    }

    $gOrgView = $orgDao->getSessionView($orgDBRow["org_id"]);
    $gWeb->setRequestAttribute(AppConstants::ORG_SESSION_VIEW,$gOrgView);
    // + site domain check over

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
