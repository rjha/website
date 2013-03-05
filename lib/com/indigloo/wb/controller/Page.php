<?php
namespace com\indigloo\wb\controller{
    
    use \com\indigloo\Url as Url ;
    use \com\indigloo\util\StringUtil as StringUtil ;

    class Page {

        
        function __construct() {
            
        }

        function process($params,$options) {
            
            $seo_title = $params["token"];
            $pageDao = new \com\indigloo\wb\dao\Page();
            $gMenulinks = $pageDao->getRandom(7);
            $widgetDBRows = $pageDao->getWidgetsOnSeoTitle($seo_title);

            $gPageTitle = StringUtil::convertKeyToName($seo_title);
            $gMetaKeywords = "shoes converse shoes vans shoes knee high reef sandal";
            $gMetaDescription = StringUtil::convertKeyToName($seo_title);
            $gSelfUrl = Url::base().$options["path"];
            
            $view = APP_WEB_DIR. "/themes/vanilla/page.tmpl" ;
            include ($view);
        }
        
    }
}
?>
