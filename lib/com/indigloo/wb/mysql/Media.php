<?php

namespace com\indigloo\wb\mysql {

    use com\indigloo\mysql as MySQL;

    class Media {

        static function add($mediaVO) {

            $mysqli = WbConnection::getInstance()->getHandle();
            $mediaId = NULL ;

            $sql = " insert into wb_media(bucket,original_name, stored_name, " ;
            $sql .= " size,mime, original_height, original_width,created_on,store,thumbnail,thumbnail_name) ";
            $sql .= " values(?,?,?,?,?,?,?,now(),?,?,?) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sssisiisss",
                        $mediaVO->bucket,
                        $mediaVO->originalName,
                        $mediaVO->storeName,
                        $mediaVO->size,
                        $mediaVO->mime,
                        $mediaVO->height,
                        $mediaVO->width,
                        $mediaVO->store,
                        $mediaVO->thumbnail,
                        $mediaVO->thumbnailName);


                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

            $mediaId = WbConnection::getInstance()->getLastInsertId();
            return $mediaId;
        }

    }

}
?>
