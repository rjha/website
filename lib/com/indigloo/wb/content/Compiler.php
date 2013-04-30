<?php
namespace com\indigloo\wb\content{
    
    use \com\indigloo\wb\Constants as AppConstants;
    use \com\indigloo\wb\html\Application as AppHtml;
    use \com\indigloo\Util ;

    class Compiler {

        
        private $mediaJson ;
        private $text ;
        private $imagesObj ;

        function __construct($text,$mediaJson) {
            $this->text = $text ;
            $this->mediaJson = $mediaJson;
            $this->imagesObj = json_decode($mediaJson);

            foreach($this->imagesObj as $imageObj) {
                $imageObj->display = "external" ;
            }
        }

        function getText() { return $this->text ; }
        function getMediaJson() { return $this->mediaJson ; }

        /**
         *
         *
         * 
         * @return $output - output of  text content + content commands 
         * The content command are of form {[command_name|arg1=val1,arg2=val2]}
         *
         */
        function compile() {
            // change the greediness of + matches
            // http://www.perl.com/doc/manual/html/pod/perlre.html
            
            $pattern = '/{\[.+?\]}/' ;
            $callback = array($this,'process');
            $this->text = preg_replace_callback($pattern, $callback, $this->text);
            $this->mediaJson = json_encode($this->imagesObj) ; 
            $this->mediaJson = Util::formSafeJson($this->mediaJson);
            
        }

        /**
         *
         * @param  $matches - what is matched by preg_callback in compile() method
         * @return string - containing output
         * 
         */
        function process($matches) {
            // @debug
            print_r($matches) ;

            // Match is {[COMMAND_TOKEN]}
            $token = $matches[0];
            $len = strlen($token);
            // substring from position 2(0-indexed) and read length-4
            // COMMAND_TOKEN
            $commandData = substr($token,2,$len-4);
            $commandData = trim($commandData);
            //trimmed length
            $len = strlen($commandData);
            //COMMAND_DATA is COMMAND|ATTRIBS
            //haystack,needle
            $pos1 = strpos($commandData,"|");

            $command = NULL ;
            $attribData = NULL ;

            if ($pos1 !== false) {
                //attribs found
                $command = substr($commandData,0,$pos1);
                $attribData = substr($commandData,$pos1+1,$len-$pos1-1 );
            } else {
                // No attribs case
                $command = $commandData;
                
            }
            
            if(is_null($command)) {
                $message = sprintf("Unknown command %s",$command);
                trigger_error($message,E_USER_ERROR);
            }
            
            
            $attribs = array();
            if(!is_null($attribData)) {
                $pairs = explode(",",$attribData);
                
                foreach ($pairs as $pair){
                    $elements = explode("=",$pair);
                    if(sizeof($elements) == 2 ) {
                        $attribs[$elements[0]] = $elements[1];
                    }
                }
            }
            
            $output = self::process_command($command,$attribs)  ;
            return $output ;
            
        }

        function process_command($command,$attribs) {

            $text = "" ;

            switch($command) {
                case "image" :
                    
                    if(!empty($this->imagesObj) && (sizeof($this->imagesObj) > 0)) {
                        $index = (isset($attribs["index"])) ? $attribs["index"] : 0  ;
                        settype($index, "integer");
                        $index-- ;

                        if($index < (sizeof($this->imagesObj))) {
                            
                            $imageObj = $this->imagesObj[$index] ;
                            // prepare substitution text
                            $imgv = AppHtml::convertImageJsonObj($imageObj);
                            $text = ' <div class="photo"> <img src="'.$imgv["source"].'" /> </div> ' ;
                            $imageObj->display = "inline" ;
                            $this->imagesObj[$index] = $imageObj;
                        }

                    }

                    break ;

                default :
                    break ;
            }

            return $text ;
        }

    }
}
?>
