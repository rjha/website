<?php

namespace com\indigloo\wb\html {

    use \com\indigloo\Template ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util ;

    class Application {
    	
    	static function getPageTile($pageDBRow) {

            $html = NULL ;
            $template = "/fragments/page/tile.tmpl" ;
            $view = new \stdClass;

            $view->title = $pageDBRow['title'] ;
            $view->id = $pageDBRow['id'] ;
            $view->link = $pageDBRow['seo_title'];
          	
            $html = Template::render($template,$view);
            return $html ;

        }

        static function getWidget($widgetDBRow) {
            $html = NULL ;
            $template = "/fragments/widget/text.tmpl" ;
            $view = new \stdClass;
            
            $view->content = $widgetDBRow['widget_html'];
            $view->title = $widgetDBRow['title'];
           
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getLinks($links) {
            $html = NULL ;
            $template = "/fragments/links.tmpl" ;
            $view = new \stdClass;
            
            $view->links = $links ;
            $html = Template::render($template,$view);
            return $html ;
        }

    }

}

?>