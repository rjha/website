<?php

namespace com\indigloo\wb\html {

    use \com\indigloo\Template ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util ;

    class Page {
    	
    	static function getTile($pageDBRow) {

            $html = NULL ;
            $template = "/fragments/page/tile.tmpl" ;
            $view = new \stdClass;

            $view->title = $pageDBRow['title'] ;
            $view->id = $pageDBRow['id'] ;
            $view->link = $pageDBRow['seo_title'];
          	
            $html = Template::render($template,$view);
            return $html ;

        }

    }

}

?>