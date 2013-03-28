<?php
namespace com\indigloo\wb\controller{
    
    class Post {
        
        function __construct() { }

        function process($router_params,$router_options) {
            
            $view = APP_WEB_DIR. "/themes/vanilla/post.tmpl" ;
            include ($view);
        }
        
    }
}
?>
