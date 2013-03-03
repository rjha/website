<?php
namespace com\indigloo\wb\controller{
    
    use \com\indigloo\Url as Url ;

    class Page {

        
        function __construct() {
            
        }

        function process($params,$options) {
            
            $seo_title = $params["token"];
            $pageDao = new \com\indigloo\wb\dao\Page();
            $gMenulinks = $pageDao->getRandom(7);
            $widgetDBRows = $pageDao->getWidgetsOnSeoTitle($seo_title);

            $view = APP_WEB_DIR. "/themes/vanilla/page.tmpl" ;
            include ($view);
        }
        
    }
}
?>
