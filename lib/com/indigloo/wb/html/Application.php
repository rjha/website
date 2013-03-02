<?php

namespace com\indigloo\wb\html {

    use \com\indigloo\Template ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util ;

    class Application {
    	
    	static function getPageTile($pageDBRow) {

            $html = NULL ;
            $template = 
            $view = new \stdClass;

            $view->title = $pageDBRow['title'] ;
            $view->id = $pageDBRow['id'] ;
            $view->link = $pageDBRow['seo_title'];
          	$view->hasImage = false ;

            $strMediaJson = $pageDBRow['media_json'];
            $mediaVO = json_decode($strMediaJson);
            if(sizeof($mediaVO) > 0 ) {
                $element = $mediaVO[0];
                $view->srcImage = $element->address; 
                $view->hasImage = true ;
            }

            $template = ($view->hasImage) ? "/fragments/tiles/image.tmpl" : "/fragments/tiles/text.tmpl";
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