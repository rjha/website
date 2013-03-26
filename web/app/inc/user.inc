<?php
    use \com\indigloo\wb\auth\Login as Login ;
    use com\indigloo\wb\Constants as AppConstants ;
 
    $loginId = Login::tryLoginIdInSession();
   
    if(empty($loginId)) {
        $fwd = AppConstants::WWW_LOGIN_URL ;
        header('Location: '.$fwd);
        exit ;
    }

?>
