<?php

namespace com\indigloo\wb\dao {

    
    use com\indigloo\Util as Util ;
    use com\indigloo\wb\mysql as mysql;
    
    class Media {

        function add($siteId,$mediaVO) {
            $mediaId = mysql\Media::add($siteId,$mediaVO);
            if(empty($mediaId)) {
                trigger_error("No Media ID in DAO :: Error adding media", E_USER_ERROR);
            }
            
            return $mediaId ;
        }
        
    }

}

?>
