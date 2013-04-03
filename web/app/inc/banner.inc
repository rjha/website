<?php
    // do we have login?
    use \com\indigloo\wb\auth\Login as Login;
    use \com\indigloo\wb\Constants as AppConstants;

    $listUrl = '<li> <a href="{href}">&nbsp;{name} </a></li>' ;
    $params = array();
    
    if(Login::hasSession()) {
        $params = array("{href}" => "/app/account/logout.php", "{name}" => "Logout") ;
    } else {
         $params = array("{href}" => "/app/account/login.php", "{name}" => "Sign in") ;
    }

    $loginUrl = str_replace(array_keys($params), array_values($params),$listUrl);
    $gHostDomain = $_SERVER["HTTP_HOST"]; 
  
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
        <h1> <a href="/"> <?php echo $gHostDomain; ?></a> </h1>
    </hgroup>

</header>

