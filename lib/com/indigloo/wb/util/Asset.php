<?php
namespace com\indigloo\wb\util{

    use \com\indigloo\Configuration as Config ;

    /* 
     * @see also
     * http://php.net/manual/en/language.namespaces.fallback.php
     * when PHP encounters a non-qualified class name - it assumes current namespace
     * when PHP encounters a non-qualified function name - it will fallback to global 
     * definition. 
     * Good practice is to qualify all class/function names.
     * 
     */
    class Asset {

        private static function getMinified($path) {
            $parts = pathinfo($path);
            //dir/file.min.extension 
            $template = "%s/%s.min.%s" ;
            $fname = sprintf($template,$parts["dirname"],$parts["filename"],$parts["extension"]);
            return $fname ;
        }

         private static function getTimeStampName($path) {
            
            $fname = $path ;
            $parts = \pathinfo($path);
            //supplied path is relative to APP_WEB_DIR
            $fullpath = APP_WEB_DIR.$path ;
            $ts = 1 ;
            
            if(\file_exists($fullpath)) {
                $ts = \filemtime($fullpath);
                $ts = "t".$ts ;
                \settype($ts,"string");
                // 10 digit unix timestamp cover from
                // 09 sept. 2001 - 20 Nov. 2286
                // $perl -MPOSIX -le 'print ctime(1000000000)' 

                $length = \strlen($ts);
                if($length != 11 ) {
                    $message = "Asset versioning timestamp is out of range" ;
                    throw new \Exception($message);
                }

                // dir/file.ts.min.extension
                // fs-bundle.css becomes fs-bundle.min.<ts>.css

                $template = "%s/%s.min.%s.%s" ;
                $fname = sprintf($template,$parts["dirname"],$parts["filename"],$ts,$parts["extension"]);
                
            }

            return $fname ;
        }

        static function version($path) {
            $link = '' ;
            $fname = self::getTimeStampName($path) ;
            
            $parts = \pathinfo($path);


            if(\strcasecmp($parts["extension"],"css") == 0 ) {
                $tmpl = '<link rel="stylesheet" type="text/css" href="{fname}" >' ;
                $link = \str_replace("{fname}",$fname,$tmpl);
            }

            if(\strcasecmp($parts["extension"],"js") == 0 ) {
                $tmpl = '<script type="text/javascript" src="{fname}"></script>' ;
                $link = \str_replace("{fname}",$fname,$tmpl);
            }

            
            return $link ;
        }

    }

}
?>
