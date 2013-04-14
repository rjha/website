<?php 

    include('wb-app.inc');
    include (APP_CLASS_LOADER);

    use \com\indigloo\util\StringUtil ;

    error_reporting(-1);
    libxml_use_internal_errors(true);

    // Cache-Control : public, max-age=31536000
    // Expires Thu, 10 Apr 2014 05:03:44 GMT

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

    function create_post($title,$permalink,$content) {
        global $siteId ;
        if(empty($content)) { return ; }

        // create a page (used for permalink)
        $pageDao = new \com\indigloo\wb\dao\Page();
        $postDao = new \com\indigloo\wb\dao\Post();
        // get page_seo_key
        $pos1 = strpos($permalink,"/",8);
        if($pos1 === false) {
            $message = sprintf("No slash in permalink %s ",$permalink);
            trigger_error($message, E_USER_ERROR);
        }

        $permalink = substr($permalink,$pos1+1);
        $page_seo_key = rtrim($permalink,"/");

        $page_name = StringUtil::convertKeyToName($page_seo_key);
        if(strpos($page_name,"?") !== false) { return ; }

        $pageId = $pageDao->create($siteId,$page_name);

        $images = get_images($content) ;
        $elements = array();

        foreach($images as $image) {
            $element = new \stdClass ;
            $element->srcImage = $image ;
            $element->store = "external" ;
            $element->display = "inline" ;
            $element->type = "image" ;
            $elements[] = $element ;
        }
            
        $media_json = json_encode($elements);
        //remove escaping of solidus done by PHP 5.3 json_encode
        $media_json = str_replace("\/","/",$media_json);
        
        $postDao->add($siteId,
            $pageId,
            $title,
            $content,
            $media_json,
            $permalink,
            false);

    }


    // start:script 
    // delete from wb_post where site_id = 1;
    // delete from wb_page where site_id = 1 and seo_title <> 'home' ;
    
    $doc  = NULL ;
    $siteId = 2 ;
    $title = NULL ;
    $mediaLink = NULL ;
    $count = 1 ;

    if (file_exists('wp.xml')) {
        $doc = simplexml_load_file('wp.xml');
        //$doc = simplexml_load_file('wp.xml',null, LIBXML_NOCDATA);

        if($doc === false) {
            echo "Failed loading XML\n";
            foreach(libxml_get_errors() as $error) {
                echo "\t", $error->message;
            }
        }

    } else {
        echo('Failed to open wp.xml.');
        exit ;
    }


    foreach($doc->channel->item as $item) {

        $title = $item->title ;
        
        $ns_wp = $item->children("http://wordpress.org/export/1.1/");
        $attachment = $ns_wp->attachment_url ;

        if(empty($attachment)) {
            $ns_content = $item->children("http://purl.org/rss/1.0/modules/content/");
            $content =  (string) $ns_content->encoded;
            $link = $item->link ;
            // post title, permalink, content
            create_post($title,$link,$content);
            $count++ ;
        }

    }


?>



