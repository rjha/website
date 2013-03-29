<?php
    // do we have login?
    use \com\indigloo\wb\auth\Login as Login;

    $listUrl = '<li> <a href="{href}">&nbsp;{name} </a></li>' ;
    $params = array();
    
    if(Login::hasSession()) {
        $params = array("{href}" => "/app/logout.php", "{name}" => "Logout") ;
    } else {
         $params = array("{href}" => "/app/hack/login.php", "{name}" => "Sign in") ;
    }

    $loginUrl = str_replace(array_keys($params), array_values($params),$listUrl);
    
?>

<header role="banner">
    <hgroup>
        <h1> <a href="/"> <?php echo $gSiteView->name ; ?></a> </h1>
    </hgroup>

</header>

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
                    <li> <a href="/sitemap"><i class="icon icon-folder-open"></i>&nbsp;Sitemap</a></li>
                    <?php echo $loginUrl; ?>
                </ul>
                
            </div>

        </div>

    </div>
</div> <!-- site toolbar -->

<?php 

    if($gSiteView->isOwner) { 
        $editUrl = "" ;
        $newUrl = "" ;

        if($G_FULL_ADMIN_TOOLBAR) {
            $qUrl = \com\indigloo\Url::current() ;
            $params = array("q" => base64_encode($qUrl), "page_id" => $gPageId);
            $newHref = \com\indigloo\Url::createUrl("/app/post/new.php",$params) ;
            $newUrl = str_replace(
                array("{href}", "{name}"), 
                array($newHref,"<i class=\"icon icon-pencil\"></i>&nbsp;New post"), 
                $listUrl);
            $editHref = ($gNumPosts > 0 ) ? \com\indigloo\Url::createUrl("/app/post/edit.php",$params) : '' ;
            
            if(!empty($editHref)) {
                $editUrl = str_replace(
                    array("{href}", "{name}"), 
                    array($editHref,"<i class=\"icon icon-edit\"></i>&nbsp;Edit"), 
                    $listUrl);
            }
        }


    ?>

    <div id="admin-toolbar">
        <div>
            <ul class="tools unstyled">
                <?php echo $editUrl; ?>
                <?php echo $newUrl; ?>
                <li> <a href="/app/page/all.php"><i class="icon icon-list-alt"></i>&nbsp;Pages</a></li>
                <li> <a href="#"><i class="icon icon-wrench"></i>&nbsp;Settings</a></li>
                <li> <a href="#">&nbsp;Help</a></li>

            </ul>
            <div class="clear"> </div>
        </div>
    </div>

<?php } ?>
