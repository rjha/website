<?php

namespace com\indigloo\wb\html {

    use \com\indigloo\Template ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util ;

    class Application {

        static function getOrgReceipt($row) {
            $html = NULL ;
            $template = "/fragments/org/receipt.tmpl" ;

            $view = new \stdClass ;
            $view->href = "http://".$row["domain"];
            $view->name = $row["name"] ;
            
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getOrgTable($rows) {
            if(empty($rows)) {
                return "<div class=\"noresults\"> No organization found </div>" ;
            }

            $html = NULL ;
            $template = "/fragments/org/table.tmpl" ;

            $view = new \stdClass ;
            $view->rows = array();

            foreach($rows as $row) {
                $row["href"] = "http://".$row["domain"];
                $view->rows[] = $row ;
            }

            $html = Template::render($template,$view);
            return $html ;
        }

        static function getPageTable($pageDBRows) {
            if(empty($pageDBRows)) {
                return "<div class=\"noresults\"> No pages found </div>" ;
            }

            $html = NULL ;
            $template = "/fragments/page/table.tmpl" ;

            $view = new \stdClass ;
            $view->rows = array();

            foreach($pageDBRows as $row) {
                // page URL would be different for different organizations
                $row["href"] = Url::base()."/".$row["seo_title"];
                $view->rows[] = $row ;
            }

            $html = Template::render($template,$view);
            return $html ;

        }

    	static function getWidgetTabs($baseURI,$tabParams,$tabId,$tabRows) {
            if(sizeof($tabRows) <= 1 ) {
                return "" ;
            }
            
            $html = NULL ;
            $template = "/fragments/generic/tabs.tmpl" ;
            
            $view = new \stdClass ;
            $view->tabs = array();

            foreach($tabRows as $row) {
                $tab = array();
                $tab["class"] = ($tabId == $row["id"]) ? "active" : "" ;
                $tab["name"] = $row["title"] ;
                $tabParams["tab_id"] = $row["id"];
                $tab["href"] = Url::createUrl($baseURI,$tabParams) ;
                array_push($view->tabs,$tab);
            }

            $html = Template::render($template,$view);
            return $html ;
        }

    	static function getPageTile($pageDBRow) {

            $html = NULL ;
            $view = new \stdClass;

            $view->title = $pageDBRow['title'] ;
            $view->id = $pageDBRow['id'] ;
            $view->link = Url::base()."/".$pageDBRow['seo_title'];
            
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
            $template = "/fragments/widget/post.tmpl" ;
            $view = new \stdClass;
            
            $view->hasImage = false ;
            $view->images = array();
            // get media
            $imagesJson = $widgetDBRow["media_json"];
            $images = json_decode($imagesJson);

            if(!empty($images) && (sizeof($images) > 0)) {
                $view->hasImage = true ;
                

                foreach($images as $image) {
                    $imgv = self::convertImageJsonObj($image);
                    array_push($view->images,$imgv);
                }
            } 

            $view->content = $widgetDBRow['widget_html'];
            $view->title = $widgetDBRow['title'];
           
            $html = Template::render($template,$view);
            return $html ;
        }

        static function convertImageJsonObj($jsonObj) {
            $imgv = array();

            $imgv["name"] = $jsonObj->originalName ;
            $prefix = ($jsonObj->store == 's3') ? 'http://' : Url::base().'/' ;
            $tfile = NULL ;

            //@imp: thumbnail not available? use original image
            if(property_exists($jsonObj,"thumbnailName")) {
                $imgv["tname"] = $jsonObj->thumbnailName ;
                $tfile = $jsonObj->thumbnail ;
            } else {
                $imgv["tname"] = $jsonObj->originalName ;
                $tfile = $jsonObj->storeName ;
            }

            $imgv["source"] = $prefix.$jsonObj->bucket.'/'.$jsonObj->storeName;
            $imgv["tsource"] = $prefix.$jsonObj->bucket.'/'.$tfile ;
            $imgv["width"] = $jsonObj->width ;
            $imgv["height"] = $jsonObj->height;
            //@todo add thumbnail width and height?
            return $imgv ;
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