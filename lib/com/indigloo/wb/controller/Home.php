<?php
namespace com\indigloo\wb\controller{
        
    class Home {

        
        function __construct() {
            
        }

        function process($params,$options) {
            $view = APP_WEB_DIR. '/themes/vanilla/home.tmpl' ;
            include ($view);
        }
        
    }
}
?>