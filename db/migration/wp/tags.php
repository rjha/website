<?php 

    error_reporting(-1);
    libxml_use_internal_errors(true);

    function process_post($title,$category,$tags,$createdOn) {
        if(empty($content)) { return ; }
        // process post

    }


    // start:script 
    // wp.xml contains dump of wordpress posts

    if (file_exists('wp.xml')) {
        $doc = simplexml_load_file('wp.xml');

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
        // content and other elements can be  wrapped inside 
        // a separate namespace. To deal with such elements we 
        // use item->children on the namespace given in wp.xml 

        $ns_wp = $item->children("http://wordpress.org/export/1.1/");
        $attachment = $ns_wp->attachment_url ;

        if(empty($attachment)) {
            $ns_content = $item->children("http://purl.org/rss/1.0/modules/content/");
            $content =  (string) $ns_content->encoded;
            $link = $item->link ;

            $pubDate = $item->pubDate ;
            $createdOn = date("Y-m-d", strtotime($pubDate));

            $tags = "" ;
            $category = "" ;

            // tags and category
            // we can have multiple category elements inside an item

            foreach($item->category as $elemCategory) { 

                if(strcmp($elemCategory["domain"],"category") == 0 ) {
                    $category = $category." ".$elemCategory["nicename"] ;
                } 

                if(strcmp($elemCategory["domain"],"post_tag") == 0 ) {
                    $tags = $tags." ".$elemCategory["nicename"] ;
                } 
            }

            printf("title = %s, category = %s ,tags = %s , pub_date = %s  \n",$title,$category,$tags,$createdOn);
            process_post($title,$category,$tags,$createdOn);
        }

    }


?>



