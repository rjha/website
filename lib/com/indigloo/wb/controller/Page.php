<?php
namespace com\indigloo\wb\controller{
    
    class Page {
        
        function __construct() { }

        function process($router_params,$router_options) {
            
            $view = APP_WEB_DIR. "/themes/vanilla/page.tmpl" ;
            include ($view);
        }
        
    }
}
?>
