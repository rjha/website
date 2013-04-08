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
    
    function remove_script_tags($html) { 
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $nodes = $doc->getElementsByTagName("script");

        foreach($nodes  as $node) {
            $node->parentNode->removeChild($node);
        }
        
        $newHtml = $doc->saveXML();
        /*
        $newHtml = '';
        foreach ($doc->getElementsByTagName('body')->item(0)->childNodes as $child) {
            $newHtml .= $doc->saveXML($child);
        } */

        return $newHtml;
    }

    $siteId = 2 ;
    $postId = 928 ;

    $postDao = new \com\indigloo\wb\dao\Post();
    $postDBRow = $postDao->getOnId($siteId,$postId);
    $html = $postDBRow["html_content"] ;
    echo $html ;
    echo "\n --------------- \n" ;
    $html = remove_script_tags($html) ;
    echo $html ;


   

?>
