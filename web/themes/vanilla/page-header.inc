<?php

    $qUrl = \com\indigloo\Url::current() ;
    $params = array("q" => base64_encode($qUrl), "page_id" => $gPageId);
    $editUrl = ($gNumWidgets > 0 ) ? \com\indigloo\Url::createUrl("/app/post/edit.php",$params) : '' ;
    $newUrl = \com\indigloo\Url::createUrl("/app/post/new.php",$params) ;


?>

<header role="banner">
    <hgroup>
        <h1> <a href="/">The converse shoes</a> </h1>
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
                    <li> <a href="/sitemap"><i class="icon icon-folder-open"></i>&nbsp;All posts</a></li>
                    
                </ul>
                
            </div>

        </div>

    </div>
</div> <!-- site toolbar -->

<div id="admin-toolbar">
    <div>
        <ul class="tools unstyled">
            <li> <a href="<?php echo $editUrl; ?>"><i class="icon icon-edit"></i>&nbsp;Edit</a></li>
            <li> <a href="<?php echo $newUrl; ?>"><i class="icon icon-pencil"></i>&nbsp;Post</a></li>
            <li> <a id="page-popup" href="/app/page/all.php"><i class="icon icon-list-alt"></i>&nbsp;Pages</a></li>
            <li> <a href="#"><i class="icon icon-wrench"></i>&nbsp;Settings</a></li>
            <li> <a href="#">&nbsp;Help</a></li>

        </ul>
        <div class="clear"> </div>
    </div>
</div>

