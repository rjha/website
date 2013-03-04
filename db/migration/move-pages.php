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
        $connx = new \mysqli("127.0.0.1", "gloo", "osje8L", "websitedb1") ;
        return $connx ;
    }

    function to_db_connection() {
        $connx = new \mysqli("127.0.0.1", "gloo", "osje8L", "wbdb1") ;
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

    function create_page($connx2,$name) {
        // @todo : fixed orgId
        // needs to change after we plug in domain routing
        $orgId = 1 ;
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

    function add_widget_to_page($connx2,$newPageId,$widget) {

        // @todo : the fixed orgId need to change
        $orgId = 1 ;

        // insert page content
        $row_number = $widget['ui_order'];
        // @imp fixed widget type for new schema
        // everything is a post in current code 
        // this may change in future
        $widget_type = 1 ;
        
        // find media json
        // get images out of this widget
        $html = "" ;
        $media_json = "" ;
        $has_media = 0 ;

        if(strcmp($widget["widget_type"],"EMBED_CODE") == 0 ) {
            $html = $widget["widget_code"] ;
        }

        if(strcmp($widget["widget_type"],"TEXT_ONLY") == 0 ) {
            $html = $widget["widget_html"] ;
            $images = get_images($widget['widget_html']) ;
            $elements = array();

            foreach($images as $image) {
                $element = new \stdClass ;
                $element->address = $image ;
                $element->source = "external" ;
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

        
        $sql = " insert into wb_page_content(org_id,page_id,row_number, title,".
                " widget_type, widget_html, created_on) ".
                " values (?,?,?,?,?,?, now()) " ;

        $stmt = $connx2->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iiisis",
                    $orgId,
                    $newPageId,
                    $row_number,
                    $widget['title'],
                    $widget_type, 
                    $widget['widget_html']);


            $stmt->execute();
            $stmt->close();
        } else {
            trigger_error("problem creating statement",E_USER_ERROR);
        }

         

        $sql = " update wb_page set media_json = ?, has_media = ? where id  = ? ";
        $stmt = $connx2->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sii",$media_json, $has_media, $newPageId);
            $stmt->execute();
            $stmt->close();
        } else {
            trigger_error("problem creating statement",E_USER_ERROR);
        }
         

    }

    function process_page($connx1,$connx2,$oldOrgId,$name,$key) {

        $name = $connx1->real_escape_string($name);
        $key = $connx1->real_escape_string($key);

        // create a page with this name
        $newPageId = create_page($connx2,$name);

        $sql = " select * from gloo_block_data where org_id = %d and page_key = '%s'  ".
                " order by block_no, ui_order " ;

        $sql = sprintf($sql,$oldOrgId,$key);
        $widgets = MySQL\Helper::fetchRows($connx1, $sql); 
        foreach($widgets as $widget) {
            // push this widget into new page
            add_widget_to_page($connx2,$newPageId,$widget);
        }

    }


    // start:script
    $oldOrgIds = array(1231,1227, 1202,1229,1228,1200,1213,1193) ;
    $connx1 = from_db_connection();
    $connx2 = to_db_connection();

    // for each organization
    foreach($oldOrgIds as $oldOrgId) {
        // iterate over pages
        $sql1 = " select * from gloo_page where org_id = %d " ;
        $sql1 = sprintf($sql1,$oldOrgId);
        $pages =  MySQL\Helper::fetchRows($connx1, $sql1);

        foreach($pages as $page) {
            printf("org_id= %d, page= %s, key=%s \n ",$oldOrgId,$page['page_name'],$page['ident_key']);
            process_page($connx1,$connx2,$oldOrgId,$page['page_name'],$page['ident_key']);
        }
    }

?>
