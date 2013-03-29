<?php
namespace com\indigloo\wb\controller{
    
    class Home {

        
        function __construct() {}

        function process($router_params,$router_options) {
            $view = APP_WEB_DIR. "/themes/masonry/home.tmpl" ;
            include ($view);
        }

    }
}
?>
