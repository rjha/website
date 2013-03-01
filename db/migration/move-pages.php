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

    function add_widget_to_page($connx2,$orgId,$newPageId,$widget) {

        // insert page content
        $row_number = $widget['ui_order'];
        // @todo : find widget_type from $widget['widget_type']
        // @todo : change image widget_xml to widget_media JSON
        $widget_type = 1 ;
        $media = "json" ;
        
        $sql = " insert into wb_page_content(org_id,page_id,row_number, title,".
                " widget_type, widget_html, widget_code, widget_markdown, widget_media,created_on) ".
                " values (?, ?, ?, ?, ?, ?, ?, ?,?, now()) " ;

        $stmt = $connx2->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iiisissss",
                    $orgId,
                    $newPageId,
                    $row_number,
                    $widget['title'],
                    $widget_type, 
                    $widget['widget_html'],
                    $widget['widget_code'],
                    $widget['widget_markdown'],
                    $media);


            $stmt->execute();
            $stmt->close();
        } else {
            trigger_error("problem creating statement",E_USER_ERROR);
        }

    }

    function process_page($connx1,$connx2,$orgId,$name,$key) {

        $name = $connx1->real_escape_string($name);
        $key = $connx1->real_escape_string($key);

        // create a page with this name
        $newPageId = create_page($connx2,$orgId,$name);

        $sql = " select * from gloo_block_data where org_id = %d and page_key = '%s'  ".
                " order by block_no, ui_order " ;

        $sql = sprintf($sql,$orgId,$key);
        $widgets = MySQL\Helper::fetchRows($connx1, $sql); 
        foreach($widgets as $widget) {
            // push this widget into new page
            add_widget_to_page($connx2,$orgId,$newPageId,$widget);
        }

    }


    // start:script
    $orgId = 1193 ;
    $connx1 = from_db_connection();
    $connx2 = to_db_connection();

    // iterate over pages
    $sql1 = " select * from gloo_page where org_id = %d " ;
    $sql1 = sprintf($sql1,$orgId);
    $pages =  MySQL\Helper::fetchRows($connx1, $sql1);

    foreach($pages as $page) {
        printf("Page= %s, key=%s \n ",$page['page_name'],$page['ident_key']);
        process_page($connx1,$connx2,$orgId,$page['page_name'],$page['ident_key']);
    }

?>
