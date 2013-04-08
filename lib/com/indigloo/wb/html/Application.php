<?php

namespace com\indigloo\wb\html {

    use \com\indigloo\Template ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util ;

    use \com\indigloo\Configuration as Config;
    use \com\indigloo\wb\auth\Login as Login ;
    use \com\indigloo\wb\Constants as AppConstants;

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

        static function getPageMenu($menuRows) {
             
            if(sizeof($menuRows) <= 1 ) {  return "" ;}
            
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

            // postUrl - a new post
            // homeUrl - home page of your site
            // host - for display

            $postUrl = $view->host."/app/post/new.php" ;
            $params = array("page_id" => $pageId, "q" => base64_encode($view->host));
            $postUrl = Url::createUrl($postUrl,$params);

            // post button Url
            $fwd = $view->host."/app/account/site-session.php" ; 
            $sessionId = session_id();
            $params = array("session_id" => $sessionId, "q" => base64_encode($postUrl));
            $view->postUrl = Url::createUrl($fwd,$params);
            
            // home button url
            $params = array("session_id" => $sessionId, "q" => base64_encode($view->host));
            $view->homeUrl = Url::createUrl($fwd,$params);

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

        static function getDomainsTable($siteId,$rows) {
            if(empty($rows)) { return "" ; }

            $html = NULL ;
            $template = "/fragments/site/domains.tmpl" ;

            $view = new \stdClass;
            $view->rows = array();

            foreach($rows as $row) {
                $link = "/app/action/site/remove-domain.php" ;
                $params = array(
                    "q" => base64_encode(Url::current()), 
                    "id" => $row["id"], 
                    "site_id" =>$siteId ) ;
                $row["href"] = Url::createUrl($link,$params);
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
            
            if(!empty($postDBRow["permalink"])) {
                $view->href = Url::base()."/".$postDBRow["permalink"] ; 
            } else {
                $view->href = Url::base()."/post/".$postDBRow["id"]."/".$postDBRow["seo_title"];
            }
            
            $strMediaJson = $postDBRow['media_json'];
            $imageObjs = json_decode($strMediaJson);

            if(sizeof($imageObjs) > 0 ) {
                $imgv = self::convertImageJsonObj($imageObjs[0]);
                $view->srcImage = $imgv["tsource"]; 
                $view->hasImage = true ;
            }

            $template = ($view->hasImage) ? "/fragments/post/image-tile.tmpl" : "/fragments/post/text-tile.tmpl";
            $html = Template::render($template,$view);
            return $html ;

        }

        static function getPostWidget($postDBRow) {

            $html = NULL ;
            $view = new \stdClass;

            $view->title = $postDBRow['title'] ;
            $view->id = $postDBRow['id'] ;
            $view->content = $postDBRow["excerpt"];
            $view->hasImage = false ;
            
            if(!empty($postDBRow["permalink"])) {
                $view->href = Url::base()."/".$postDBRow["permalink"] ; 
            } else {
                $view->href = Url::base()."/post/".$postDBRow["id"]."/".$postDBRow["seo_title"];
            }
            
            $strMediaJson = $postDBRow['media_json'];
            $imageObjs = json_decode($strMediaJson);

            if(sizeof($imageObjs) > 0 ) {
                $imgv = self::convertImageJsonObj($imageObjs[0]);
                $view->srcImage = $imgv["tsource"]; 
                $view->hasImage = true ;
            }

            $template =  "/fragments/post/widget.tmpl" ;
            $html = Template::render($template,$view);
            return $html ;

        }

        static function getPost($postDBRow) {
            $html = NULL ;
            $template = "/fragments/post/full.tmpl" ;
            $view = new \stdClass;
            
            $view->hasImage = false ;
            $view->images = array();
            // get media
            $imagesJson = $postDBRow["media_json"];
            $images = json_decode($imagesJson);

            if(!empty($images) && (sizeof($images) > 0)) {
               
                foreach($images as $image) {
                    /* do not process inline images for post */
                    if(strcmp($image->store,'inline') != 0 ) {
                        $imgv = self::convertImageJsonObj($image);
                        array_push($view->images,$imgv);
                    }
                }
            } 
            
            $view->hasImage = sizeof($view->images > 0 ) ? true : false ;
            $view->content = $postDBRow['html_content'];
            $view->title = $postDBRow['title'];
           
            $html = Template::render($template,$view);
            return $html ;
        }

        static function convertImageJsonObj($jsonObj) {
            $imgv = array();
            /* inline images are part of post content */
            if($jsonObj->store == 'inline') {
                $imgvname["name"] = $jsonObj->srcImage ;
                $imgv["tname"] = $jsonObj->srcImage ;
                $imgv["source"] = $jsonObj->srcImage ;
                $imgv["tsource"] = $jsonObj->srcImage ;

                return $imgv ;
            }

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

        static function getAppToolbar($gSiteView,$buttons=0,$options=array()) {
            if(!$gSiteView->isOwner) { return "" ;}

            $html = NULL ;
            $template = "/fragments/app/toolbar.tmpl" ;
            $view = new \stdClass;
            $tmplUrl = '<li> <a href="{href}">&nbsp;{name} </a></li>' ;

            $view->editPageUrl = "" ;
            $view->editPostUrl = "" ;
            $view->newPostUrl = "" ;
            $view->allPageUrl = "" ;
            $view->settingsUrl = "" ;

            $qUrl = Url::current() ;
            $params = array("q" => base64_encode($qUrl));

            if($buttons & AppConstants::TOOLBAR_NEW_POST) {
                if(isset($options["page_id"]) && !empty($options["page_id"])) {
                    $params["page_id"] = $options["page_id"] ;
                }

                $href = Url::createUrl("/app/post/new.php",$params) ;
                $view->newPostUrl = str_replace(
                    array("{href}", "{name}"), 
                    array($href,"<i class=\"icon icon-plus\"></i>&nbsp;new post"), 
                    $tmplUrl);
            }

            if($buttons & AppConstants::TOOLBAR_EDIT_POST) {
                if(!isset($options["post_id"]) || empty($options["post_id"])) {
                    $message = "post_id is required for edit post toolbar button" ;
                    trigger_error($message,E_USER_ERROR);
                }

                $params["post_id"] = $options["post_id"];
                $href = Url::createUrl("/app/post/edit.php",$params) ;
                $view->editPostUrl = str_replace(
                    array("{href}", "{name}"), 
                    array($href,"<i class=\"icon icon-edit\"></i>&nbsp;edit post"), 
                    $tmplUrl);
            }

            if($buttons & AppConstants::TOOLBAR_EDIT_PAGE) {
                if(!isset($options["page_id"]) || empty($options["page_id"])) {
                    $message = "page_id is required for edit page toolbar button" ;
                    trigger_error($message,E_USER_ERROR);
                }

                $params["page_id"] = $options["page_id"] ;
                $href = Url::createUrl("/app/page/edit.php",$params) ;
                $view->editPageUrl = str_replace(
                    array("{href}", "{name}"), 
                    array($href,"<i class=\"icon icon-edit\"></i>&nbsp;edit page"), 
                    $tmplUrl);
            }

            if($buttons & AppConstants::TOOLBAR_SETTINGS) {
                $href = "http://".$gSiteView->canonical_domain ."/app/settings.php";
                $href = Url::createUrl($href,$params);
                $view->settingsUrl = str_replace(
                    array("{href}", "{name}"), 
                    array($href,"<i class=\"icon icon-cog\"></i>&nbsp;settings"), 
                    $tmplUrl);
            }

            if($buttons & AppConstants::TOOLBAR_ALL_PAGES) {
                $params = array("q" => base64_encode($qUrl));
                $href = Url::createUrl("/app/page/all.php",$params);
                $view->allPageUrl = str_replace(
                    array("{href}", "{name}"), 
                    array($href,"<i class=\"icon icon-list-alt\"></i>&nbsp;pages"), 
                    $tmplUrl);
            }

            $html = Template::render($template,$view);
            return $html ;

        }

        static function getAppSiteToolbar() {
            $html = NULL ;
            $template = "/fragments/app/site-toolbar.tmpl" ;
            $view = new \stdClass;
            
            $tmplUrl = '<li> <a href="{href}">&nbsp;{name} </a></li>' ;
            $params = array();
            
            $www_host = Config::getInstance()->get_value("www.host.name") ;
            $signout_url = "/app/account/logout.php" ;
             
            $sso_url = "http://".$www_host. "/app/account/sso.php" ;
            $current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
            
            $params = array(
                "q" =>  base64_encode($current_url), 
                "domain" => base64_encode($_SERVER["HTTP_HOST"]));
            $sso_url = Url::createUrl($sso_url,$params);

            if(Login::hasSession()) {
                $params = array("{href}" => $signout_url, "{name}" => "Logout") ;
            } else {
                 $params = array("{href}" => $sso_url, "{name}" => "Sign in") ;
            }
           
            $loginUrl = str_replace(array_keys($params), array_values($params),$tmplUrl);

            $view->loginUrl = $loginUrl;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getAppBanner($gSiteView) {
            $html = NULL ;
            $template = "/fragments/app/banner.tmpl" ;
            $html = Template::render($template,$gSiteView);
            return $html ;
        }

    }

}

?>