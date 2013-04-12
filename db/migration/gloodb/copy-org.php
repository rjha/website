<?php 
    include('wb-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Util as Util;
    use \com\indigloo\wb\Formatting ;

    use \com\indigloo\wb\dao as Dao ;
    use \com\indigloo\util\StringUtil ;

    error_reporting(-1);
    set_exception_handler('offline_exception_handler');
    
    function from_db_connection() {
        $connx = new \mysqli("127.0.0.1", "gloo", "osje8L", "gloodb") ;
        return $connx ;
    }

    function get_images($html) {
         
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $nodes = $doc->getElementsByTagName("img");

        $length = $nodes->length ;
        $count = 0 ;
        $images = array();

        for($i = 0 ; $i < $length; $i++) {

            $node = $nodes->item($i);
            // all srcImages are external images
            $srcImage = $node->getAttribute("src");     
            array_push($images,$srcImage);
        }

       
        return $images ;
    }
  
    function add_widget_to_page($newOrgId,$pageId,$widget,$permalink) {

        // insert page content
        $row_number = $widget['ui_order'];
        $post_type = 1 ;
        
        // find media json
        // get images out of this widget
        $html = "" ;
        $media_json = "" ;
        $has_media = 0 ;

        if(strcmp($widget["widget_type"],"IMAGE") == 0 ) {
            
            $elements = array();
            $element = new \stdClass ;

            $xmlDoc = new SimpleXMLElement($widget['widget_xml']);

            $imageUrl = urldecode($xmlDoc->imageURI);
            // s3 URL are of form http://xyz/storeName
            //  find first slash after http:// in string
            $pos1 = strpos($imageUrl,"/",8);
            
            $element->store = "s3" ;
            $element->bucket = "media1.indigloo.net" ;
            $element->type = "image" ;
            $element->storeName = substr($imageUrl,$pos1+1);
            $element->originalName = md5($element->storeName);

            $element->width = sprintf("%s",$xmlDoc->width) ;
            $element->height = sprintf("%s",$xmlDoc->height) ;
            $element->size = sprintf("%s",$xmlDoc->size) ;
            $element->mime = sprintf("%s",$xmlDoc->mime) ;

            $elements[] = $element ;
            
            $media_json = json_encode($elements);
            //remove escaping of solidus done by PHP 5.3 json_encode
            $media_json = str_replace("\/","/",$media_json);
            $html = $widget["widget_html"] ;

        }

        if(strcmp($widget["widget_type"],"EMBED_CODE") == 0 ) {
            $html = $widget["widget_code"] ;
        }

        if(strcmp($widget["widget_type"],"TEXT_ONLY") == 0 ) {
            $html = $widget["widget_html"] ;
            $images = get_images($widget['widget_html']) ;
            $elements = array();

            foreach($images as $image) {
                $element = new \stdClass ;
                $element->srcImage = $image ;
                $element->store = "inline" ;
                $element->type = "image" ;
                $elements[] = $element ;
            }
            
            $has_media = (sizeof($elements) > 0 ) ? 1 : 0 ;
            $media_json = json_encode($elements);
            //remove escaping of solidus done by PHP 5.3 json_encode
            $media_json = str_replace("\/","/",$media_json);
        }
    
        if(empty($html) || empty($media_json)) {
            // nothing to add.
            return ;
        }

        $postDao = new \com\indigloo\wb\dao\Post();
        
        // remove script and style tags
        // most likely script tags have been included
        // for google adsense.
        $html = Formatting::strip_script_style_tags($html) ;

        // rawData = true 
        $postDao->add($newOrgId,
            $pageId,
            $widget["title"],
            $html,
            $media_json,
            $permalink,
            true);

    }

    function process_page($connx1,$oldOrgId,$newOrgId,$key,$permalink) {

        $key = $connx1->real_escape_string($key);

        $sql = " select * from gloo_block_data where org_id = %d and page_key = '%s'  ".
                " order by block_no, ui_order " ;

        $sql = sprintf($sql,$oldOrgId,$key);
        $widgets = MySQL\Helper::fetchRows($connx1, $sql); 
        
        // create a new page
        $pageDao = new \com\indigloo\wb\dao\Page();
        $pageId = NULL ;

        if(!empty($permalink)) {
            $page_title = StringUtil::convertKeyToName($permalink);
            printf("create page with title = %s",$page_title);
            flush();
            $pageId = $pageDao->create($newOrgId,$page_title);
        }

        foreach($widgets as $widget) {
            // push this widget into new page
            add_widget_to_page($newOrgId,$pageId,$widget,$permalink);
            flush();
        }

    }

    // prereq:
    // 28863
    // 28872
    // 28858
    // 28860
    //
    // start:script
    $oldOrgIds = array(1231,1227, 1202,1229,1228,1200,1213,1193) ;
    // $oldOrgIds = array(1193) ;
    $connx1 = from_db_connection();
    // create a new ORG before running this script
    $newOrgId = 1 ;

    // for each organization
    foreach($oldOrgIds as $oldOrgId) {
        // iterate over pages
        $sql1 = " select * from gloo_page where org_id = %d " ;
        $sql1 = sprintf($sql1,$oldOrgId);
        $pages =  MySQL\Helper::fetchRows($connx1, $sql1);

        foreach($pages as $page) {
            printf("org_id= %d, page= %s, page_key=%s \n ",$oldOrgId,$page['page_name'],$page['ident_key']);
            $permalink = NULL ;
            $page_seo_key = $page["seo_key"] ;
            if(!empty($page_seo_key) && (strcmp($page_seo_key,"home") != 0)) {
                $permalink = $page_seo_key ;
                printf("permalink =%s \n ",$permalink);
            }

            process_page($connx1,$oldOrgId,$newOrgId,$page['ident_key'],$permalink);
        }
    }

?>
