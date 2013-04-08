<?php
namespace com\indigloo\wb  {

    /*
     * 
     * functions copied from wordpress source code has a wp_ prefix.
     * Though we have made changes to simplify and make them 
     * self contained (no dependencies on other wordpress functions)
     * 
     * 
     */

    class Formatting {

        static function wp_html_excerpt( $str, $count ) {
            $str = self::wp_strip_all_tags( $str, true );
            $str = mb_substr( $str, 0, $count );
            // remove part of an entity at the end
            $str = preg_replace( '/&[^;\s]{0,6}$/', '', $str );
            return $str;
        }

        static function wp_strip_all_tags($string, $remove_breaks = false) {
        	$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
            $string = strip_tags($string);

            if ( $remove_breaks ) { $string = preg_replace('/[\r\n\t ]+/', ' ', $string); }

            return trim( $string );

        }

        static function strip_script_style_tags($string) {
            $string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
            return trim( $string );
        }

        static function wp_trim_words( $text, $num_words = 55, $more = null ) {
            if ( null === $more ) { $more =  '&hellip;'; }

            $original_text = $text;
            $text = self::wp_strip_all_tags( $text );
            $words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
            $sep = ' ';
            
            if ( count( $words_array ) > $num_words ) {
                array_pop( $words_array );
                $text = implode( $sep, $words_array );
                $text = $text . $more;
            } else {
                $text = implode( $sep, $words_array );
            }

            return $text; 
        }

    }

}

?>
