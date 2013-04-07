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

  
    function add_widget_to_page($newOrgId,$widget) {

        // insert page content
        $row_number = $widget['ui_order'];
        $post_type = 1 ;
        
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
                $element->srcImage = $image ;
                $element->store = "external" ;
                $element->type = "image" ;
                $elements[] = $element ;
            }
            
            $has_media = (sizeof($elements) > 0 ) ? 1 : 0 ;
            $media_json = json_encode($elements);
            //remove escaping of solidus done by PHP 5.3 json_encode
            $media_json = str_replace("\/","/",$media_json);
        }

        //@todo : handle image content
        /* 
        <widget> 
            <type>IMAGE</type>
            <imageURI>http%3A%2F%2Fmedia1.indigloo.net.s3.amazonaws.com%2Fwww.mudroombenches.info%2F1ada9527263d066b_1294.jpg</imageURI>
            <thumbnailURI>http%3A%2F%2Fwww.mudroombenches.info%2Fdata%2Fimage%2Fthumbnail.php%3Fid%3D1807</thumbnailURI>
            <mime>image/jpeg</mime>
            <width>619</width>
            <height>199</height>
            <size>22714</size>
            <alignment>alignleft</alignment>
            <scale>full</scale>
            <customWidth>0</customWidth>
            <customHeight>0</customHeight>
        </widget>

        */

        if(empty($html) || empty($media_json)) {
            // nothing to add.
            return ;
        }

         
        $postDao = new \com\indigloo\wb\dao\Post();
        $pageId = NULL ;

        $raw_content = $html ;
        $html_content = nl2br($raw_content);
        $permalink = NULL;

        $postDao->add($newOrgId,
            $pageId,
            $widget["title"],
            $raw_content,
            $html_content,
            $media_json,
            $permalink);


    }

    function process_page($connx1,$oldOrgId,$newOrgId,$name,$key) {

        $name = $connx1->real_escape_string($name);
        $key = $connx1->real_escape_string($key);

        // $pageDao = new \com\indigloo\wb\dao\Page();
        // $newPageId = $pageDao->create($newOrgId,$name);

        $sql = " select * from gloo_block_data where org_id = %d and page_key = '%s'  ".
                " order by block_no, ui_order " ;

        $sql = sprintf($sql,$oldOrgId,$key);
        $widgets = MySQL\Helper::fetchRows($connx1, $sql); 
        foreach($widgets as $widget) {
            // push this widget into new page
            add_widget_to_page($newOrgId,$widget);
        }

    }


    // start:script
    $oldOrgIds = array(1231,1227, 1202,1229,1228,1200,1213,1193) ;
    $connx1 = from_db_connection();
    // create a new ORG before running this script
    $newOrgId = 2 ;

    // for each organization
    foreach($oldOrgIds as $oldOrgId) {
        // iterate over pages
        $sql1 = " select * from gloo_page where org_id = %d " ;
        $sql1 = sprintf($sql1,$oldOrgId);
        $pages =  MySQL\Helper::fetchRows($connx1, $sql1);

        foreach($pages as $page) {
            printf("org_id= %d, page= %s, key=%s \n ",$oldOrgId,$page['page_name'],$page['ident_key']);
            process_page($connx1,$oldOrgId,$newOrgId,$page['page_name'],$page['ident_key']);
        }
    }

?>
