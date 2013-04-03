<?php

namespace com\indigloo\wb\html {

    use \com\indigloo\Template ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util ;

    class Application {

     static function getHelp($key) {

            $pos = \strpos($key,"/");
            //bad key
            if($pos !== false) {
                $message = \sprintf("wrong format for help file key: {%s} ",$key) ;
                throw new \Exception($message);
            }

            $name = \str_replace(".","/",$key);
            $path = \sprintf("%s/fragments/help/%s.html",APP_WEB_DIR,$name) ;

            if(!\file_exists($path)) {
                $message = sprintf("unable to locate help file {%s}",$path);
                throw new \Exception($message);
            }

            //get buffered output

            \ob_start();
            include ($path);
            $buffer = \ob_get_contents();
            \ob_end_clean();

            return $buffer;
        }
        
        static function getBigError($message) {
            $html = NULL ;
            $template = "/fragments/generic/big-error.tmpl" ;

            $view = new \stdClass;
            $view->href = Url::base();
            $view->message = $message ;

            $html = Template::render($template,$view); 
            return $html ;
        }

        static function getDefaultMenu($menuRows) {
            $html = NULL ;
            $template = "/fragments/generic/menu.tmpl" ;
            
            $view = new \stdClass ;
            $view->rows = array();

            foreach($menuRows as $menuRow) {
                $row = array();
                $row["name"] = $menuRow["title"];
                $row["href"] = Url::base()."/".$menuRow["seo_title"];
                $view->rows[] = $row;
            }

            $html = Template::render($template,$view); 
            return $html ;
        }

        static function getSiteReceipt($row,$pageId) {
            $html = NULL ;
            $template = "/fragments/site/receipt.tmpl" ;

            $view = new \stdClass ;
            $view->host = "http://".$row["canonical_domain"];
            $view->name = $row["name"] ;

            // post URL
            $postUrl = $view->host."/app/post/new.php" ;
            $params = array("page_id" => $pageId, "q" => base64_encode($view->host));
            $postUrl = Url::createUrl($postUrl,$params);

            // session Url
            $fwd = $view->host."/app/account/site-session.php" ; 
            $sessionId = session_id();
            $params = array("session_id" => $sessionId, "q" => base64_encode($postUrl));
            $view->fwdUrl = Url::createUrl($fwd,$params);

            $html = Template::render($template,$view); 
            return $html ;
        }

        static function getSiteTable($rows) {
            if(empty($rows)) {
                return "<div class=\"noresults\"> No sites found </div>" ;
            }

            $html = NULL ;
            $template = "/fragments/site/table.tmpl" ;

            $view = new \stdClass ;
            $view->rows = array();

            foreach($rows as $row) {
                
                $host =  "http://".$row["canonical_domain"] ;
                $params = array("session_id" => session_id(),"q" => base64_encode($host));
                $fwd = $host."/app/account/site-session.php" ;
                $fwd = Url::createUrl($fwd,$params);
                $row["href"] = $fwd;
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
                $row["href"] = Url::base()."/".$row["seo_title"];
                // edit url and new post url should set q param
                $qUrl = Url::current();
                $params = array("q" => base64_encode($row["href"]), "page_id" => $row["id"]) ;
                
                $row["newHref"] = Url::createUrl("/app/post/new.php",$params);
                $row["editHref"] =  Url::createUrl("/app/page/edit.php",$params) ;
                $row["hasEdit"] = ($row["num_post"] > 0 ) ? true : false ;
                $view->rows[] = $row ;
            }

            $html = Template::render($template,$view);
            return $html ;

        }

    	static function getPostTabs($baseURI,$tabParams,$tabId,$tabRows) {
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

    	static function getPostTile($postDBRow) {

            $html = NULL ;
            $view = new \stdClass;

            $view->title = $postDBRow['title'] ;
            $view->id = $postDBRow['id'] ;
             
          	$view->hasImage = false ;
            $view->href= sprintf("%s/post/%d/%s",Url::base(),$postDBRow["id"],$postDBRow["seo_title"]);

            $strMediaJson = $postDBRow['media_json'];
            $mediaVO = json_decode($strMediaJson);

            if(sizeof($mediaVO) > 0 ) {
                $image = $mediaVO[0];
                $imgv = self::convertImageJsonObj($image);
                
                $view->srcImage = $imgv["tsource"]; 
                $view->hasImage = true ;
            }

            $template = ($view->hasImage) ? "/fragments/tiles/image.tmpl" : "/fragments/tiles/text.tmpl";
            $html = Template::render($template,$view);
            return $html ;

        }

        static function getPost($postDBRow) {
            $html = NULL ;
            $template = "/fragments/post/post.tmpl" ;
            $view = new \stdClass;
            
            $view->hasImage = false ;
            $view->images = array();
            // get media
            $imagesJson = $postDBRow["media_json"];
            $images = json_decode($imagesJson);

            if(!empty($images) && (sizeof($images) > 0)) {
                $view->hasImage = true ;
                
                foreach($images as $image) {
                    $imgv = self::convertImageJsonObj($image);
                    array_push($view->images,$imgv);
                }
            } 

            $view->content = $postDBRow['html_content'];
            $view->title = $postDBRow['title'];
           
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