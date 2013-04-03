<?php
    // do we have login?
    use \com\indigloo\wb\auth\Login as Login;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util;

    $tmplUrl = '<li> <a href="{href}">&nbsp;{name} </a></li>' ;
    $params = array();
    
    $www_host = Config::getInstance()->get_value("www.host.name") ;
    $signout_url = "/app/account/logout.php" ;
     
    $sso_url = "http://".$www_host. "/app/account/sso.php" ;
    $current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
    
    $params = array(
        "q" =>  base64_encode($current_url), 
        "domain" => base64_encode($_SERVER["HTTP_HOST"]));
    $sso_url = Url::createUrl($sso_url,$params);

    if(Login::hasSession()) {
        $params = array("{href}" => $signout_url, "{name}" => "Logout") ;
    } else {
         $params = array("{href}" => $sso_url, "{name}" => "Sign in") ;
    }
   
    $loginUrl = str_replace(array_keys($params), array_values($params),$tmplUrl);
  
?>



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
                    <?php echo $loginUrl; ?>
                </ul>
                
            </div>

        </div>

    </div>
</div> <!-- site toolbar -->

<header role="banner">
    <hgroup>
        <h1> <a href="/"> <?php echo strtoupper($gSiteView->domain) ; ?></a> </h1>
    </hgroup>

</header>
 
<?php 

    if($gSiteView->isOwner) { 
        $editUrl = "" ;
        $newUrl = "" ;
        $qUrl = \com\indigloo\Url::current() ;

        if($G_FULL_ADMIN_TOOLBAR) {
            
            $params = array("q" => base64_encode($qUrl), "page_id" => $gPageId);
            $newHref = \com\indigloo\Url::createUrl("/app/post/new.php",$params) ;
            
            $newUrl = str_replace(
                array("{href}", "{name}"), 
                array($newHref,"<i class=\"icon icon-plus\"></i>&nbsp;New post"), 
                $tmplUrl);
            $editHref = ($gNumPosts > 0 ) ? \com\indigloo\Url::createUrl("/app/page/edit.php",$params) : '' ;
            
            if(!empty($editHref)) {
                $editUrl = str_replace(
                    array("{href}", "{name}"), 
                    array($editHref,"<i class=\"icon icon-edit\"></i>&nbsp;Edit"), 
                    $tmplUrl);
            }
        }

        $params = array("q" => base64_encode($qUrl));
        $allPageUrl = \com\indigloo\Url::createUrl("/app/page/all.php",$params)



    ?>

    <div id="admin-toolbar">
        <div>
            <ul class="tools unstyled">
                <?php echo $editUrl; ?>
                <?php echo $newUrl; ?>
                <li> <a href="<?php echo $allPageUrl; ?>"><i class="icon icon-list-alt"></i>&nbsp;Pages</a></li>
                

            </ul>
            <div class="clear"> </div>
        </div>
    </div>

<?php } ?>
