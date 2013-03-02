<?php 
    include('wb-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Util as Util;
    
    use \com\indigloo\wb\dao as Dao ;

    error_reporting(-1);
    set_exception_handler('offline_exception_handler');

     
    function from_db_connection() {
        $connx = new \mysqli("127.0.0.1", "gloo", "osje8L", "wbdb1") ;
        return $connx ;
    }

    

    function create_page($connx2,$orgId,$name) {
        $title = $name ;
        $seo_title = \com\indigloo\util\StringUtil::convertNameToKey($name);

        $seo_title_hash = md5($seo_title);
        $random_key = Util::getRandomString(16);
        
        // insert into DB 
        $sql = " insert into wb_page(org_id,title,seo_title,seo_title_hash,".
                " random_key, created_on ) values (?,?,?,?,?,now()) " ;

        $stmt = $connx2->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("issss",
                    $orgId,
                    $title,
                    $seo_title,
                    $seo_title_hash,
                    $random_key);


            $stmt->execute();
            $stmt->close();
        } else {
            trigger_error("problem creating statement",E_USER_ERROR);
        }

        $sql = " select last_insert_id() as page_id " ;
        $row = MySQL\Helper::fetchRow($connx2,$sql);

        $page_id = $row['page_id'];
        return $page_id ;

    }

    function update_page_media($connx1,$pageId,$images) {

        $elements = array();

        foreach($images as $image) {
            $element = new \stdClass ;
            $element->address = $image ;
            $element->source = "external" ;
            $element->type = "image" ;
            $elements[] = $element ;
        }
         
        $mediaJson = json_encode($elements);
        //remove escaping of solidus done by PHP 5.3 json_encode
        $mediaJson = str_replace("\/","/",$mediaJson);
        
        $sql = " update wb_page set media_json = ? where id  = ? ";
        $stmt = $connx1->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("si",$mediaJson,$pageId);
            $stmt->execute();
            $stmt->close();
        } else {
            trigger_error("problem creating statement",E_USER_ERROR);
        }

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

    // start:script
    $orgId = 1193 ;
    $connx1 = from_db_connection();
    

    // iterate over content
    $sql1 = " select * from wb_page_content where org_id = %d " ;
    $sql1 = sprintf($sql1,$orgId);
    $widgets =  MySQL\Helper::fetchRows($connx1, $sql1);

    foreach($widgets as $widget) {
        
        // get images out of this widget
        $images = get_images($widget['widget_html']) ;
        // save into page
        $pageId = $widget['page_id'];
        update_page_media($connx1,$pageId,$images);
    
    }

?>
