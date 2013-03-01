<?php 
    include('wb-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Util as Util;
    
    error_reporting(-1);
    set_exception_handler('offline_exception_handler');

     
    function from_db_connection() {
        $connx = new \mysqli("127.0.0.1", "gloo", "osje8L", "websitedb1") ;
        return $connx ;
    }

    function move_widgets($connx1,$orgId,$name,$key) {
        $sql = " select title,widget_type, widget_xml, widget_code, widget_markdown, widget_html ".
                " from gloo_block_data where org_id = %d and page_key = '%s'  ".
                " order by block_no, ui_order " ;

        $sql = sprintf($sql,$orgId,$key);
        $widgets = MySQL\Helper::fetchRows($connx1, $sql); 
        foreach($widgets as $widget) {
            printf("\t type=%s, %s \n",$widget['widget_type'],$widget['title']);
        }

    }

    // start:script

    $orgId = 1193 ;
    $connx1 = from_db_connection();

    // iterate over pages
    $sql1 = " select * from gloo_page where org_id = %d " ;
    $sql1 = sprintf($sql1,$orgId);
    $pages =  MySQL\Helper::fetchRows($connx1, $sql1);

    foreach($pages as $page) {
        printf("Page= %s, key=%s \n ",$page['page_name'],$page['ident_key']);
        move_widgets($connx1,$orgId,$page['page_name'],$page['ident_key']);
    }

?>
