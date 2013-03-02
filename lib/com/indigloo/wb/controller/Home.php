<?php
namespace com\indigloo\wb\controller{
    
    use \com\indigloo\Url as Url ;

    class Home {

        
        function __construct() {
            
        }

        function process($params,$options) {
            $seo_title = "home" ;
            $pageDao = new \com\indigloo\wb\dao\Page();
            $gMenulinks = $pageDao->getLinks(10);

            $widgetDBRows = $pageDao->getWidgetsOnSeoTile($seo_title);
            $view = APP_WEB_DIR. "/themes/vanilla/page.tmpl" ;
            include ($view);
 
        }
        
    }
}
?>
