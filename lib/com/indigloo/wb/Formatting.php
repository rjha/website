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

        static function autop($text) {
            $text = self::wp_autop($text) ;
            $text = self::wp_make_clickable($text);

            return $text ;
        }
        /*  
         * 
         * Replaces double line-breaks with paragraph elements. 
         * @param string $pee The text which has to be formatted.
         * @param bool $br Optional. If set, this will convert all remaining line-breaks after paragraphing. Default true.
         * @return string Text which has been converted into correct paragraph tags.
         *
        */
        static function wp_autop($pee, $br = true) {
            $pre_tags = array();

            if ( trim($pee) === '' )
                return '';

            $pee = $pee . "\n"; // just to make things a little easier, pad the end

            if ( strpos($pee, '<pre') !== false ) {
                $pee_parts = explode( '</pre>', $pee );
                $last_pee = array_pop($pee_parts);
                $pee = '';
                $i = 0;

                foreach ( $pee_parts as $pee_part ) {
                    $start = strpos($pee_part, '<pre');

                    // Malformed html?
                    if ( $start === false ) {
                        $pee .= $pee_part;
                        continue;
                    }

                    $name = "<pre wp-pre-tag-$i></pre>";
                    $pre_tags[$name] = substr( $pee_part, $start ) . '</pre>';

                    $pee .= substr( $pee_part, 0, $start ) . $name;
                    $i++;
                }

                $pee .= $last_pee;
            }

            $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
            // Space things out a little
            $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|noscript|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
            $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
            $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
            $pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
            if ( strpos($pee, '<object') !== false ) {
                $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
                $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
            }
            $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
            // make paragraphs, including one at the end
            $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
            $pee = '';
            foreach ( $pees as $tinkle )
                $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
            $pee = preg_replace('|<p>\s*</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
            $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);
            $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
            $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
            $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
            $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
            $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
            $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
            if ( $br ) {
                $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', array(self,'wp_autop_newline_preservation_helper'), $pee);
                $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
                $pee = str_replace('<WPPreserveNewline />', "\n", $pee);
            }
            $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
            $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
            $pee = preg_replace( "|\n</p>$|", '</p>', $pee );

            if ( !empty($pre_tags) )
                $pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);

            return $pee;
        }

        static function wp_autop_newline_preservation_helper( $matches ) {
            return str_replace("\n", "<WPPreserveNewline />", $matches[0]);
        }

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

        /**
         * Convert plaintext URI to HTML links.
         *
         * Converts URI, www and ftp, and email addresses. Finishes by fixing links
         * within links.
         *
         *
         * @param string $text Content to convert URIs.
         * @return string Content with converted URIs.
         */
        static function wp_make_clickable( $text ) {
            $r = '';
            $textarr = preg_split( '/(<[^<>]+>)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // split out HTML tags
            foreach ( $textarr as $piece ) {
                if ( empty( $piece ) || ( $piece[0] == '<' && ! preg_match('|^<\s*[\w]{1,20}+://|', $piece) ) ) {
                    $r .= $piece;
                    continue;
                }

                // Long strings might contain expensive edge cases ...
                if ( 10000 < strlen( $piece ) ) {
                    // ... break it up
                    foreach (self::wp_split_str_by_whitespace( $piece, 2100 ) as $chunk ) { // 2100: Extra room for scheme and leading and trailing paretheses
                        if ( 2101 < strlen( $chunk ) ) {
                            $r .= $chunk; // Too big, no whitespace: bail.
                        } else {
                            $r .= self::wp_make_clickable( $chunk );
                        }
                    }
                } else {
                    $ret = " $piece "; // Pad with whitespace to simplify the regexes

                    $url_clickable = '~
                        ([\\s(<.,;:!?])                                    # 1: Leading whitespace, or punctuation
                        (                                                  # 2: URL
                            [\\w]{1,20}+://                                # Scheme and hier-part prefix
                            (?=\S{1,2000}\s)                               # Limit to URLs less than about 2000 characters long
                            [\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]*+         # Non-punctuation URL character
                            (?:                                            # Unroll the Loop: Only allow puctuation URL character if followed by a non-punctuation URL character
                                [\'.,;:!?)]                                # Punctuation URL character
                                [\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]++     # Non-punctuation URL character
                            )*
                        )
                        (\)?)                                                  # 3: Trailing closing parenthesis (for parethesis balancing post processing)
                    ~xS'; 
                    // The regex is a non-anchored pattern and does not have a single fixed starting character.
                    // Tell PCRE to spend more time optimizing since, when used on a page load, it will probably be used several times.

                    $ret = preg_replace_callback($url_clickable, array(self,'wp_make_url_clickable_cb'), $ret );
                    $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', array(self,'wp_make_email_clickable_cb'), $ret );

                    $ret = substr( $ret, 1, -1 ); // Remove our whitespace padding.
                    $r .= $ret;
                }
            }

            // Cleanup of accidental links within links
            $r = preg_replace( '#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i', "$1$3</a>", $r );
            return $r;
        }

        /**
         * Breaks a string into chunks by splitting at whitespace characters.
         * The length of each returned chunk is as close to the specified length goal as possible,
         * with the caveat that each chunk includes its trailing delimiter.
         * Chunks longer than the goal are guaranteed to not have any inner whitespace.
         *
         * Joining the returned chunks with empty delimiters reconstructs the input string losslessly.
         *
         * Input string must have no null characters 
         * (or eventual transformations on output chunks must not care about null characters)
         *
         * <code>
         * _split_str_by_whitespace( "1234 67890 1234 67890a cd 1234   890 123456789 1234567890a    
         * 45678   1 3 5 7 90 ", 10 ) ==
         * array (
         *   0 => '1234 67890 ',  // 11 characters: Perfect split
         *   1 => '1234 ',        //  5 characters: '1234 67890a' was too long
         *   2 => '67890a cd ',   // 10 characters: '67890a cd 1234' was too long
         *   3 => '1234   890 ',  // 11 characters: Perfect split
         *   4 => '123456789 ',   // 10 characters: '123456789 1234567890a' was too long
         *   5 => '1234567890a ', // 12 characters: Too long, but no inner whitespace on which to split
         *   6 => '   45678   ',  // 11 characters: Perfect split
         *   7 => '1 3 5 7 9',    //  9 characters: End of $string
         * );
         * </code>
         *
         *
         *
         * @param string $string The string to split.
         * @param int $goal The desired chunk length.
         * @return array Numeric array of chunks.
         */
        static function wp_split_str_by_whitespace( $string, $goal ) {
            $chunks = array();

            $string_nullspace = strtr( $string, "\r\n\t\v\f ", "\000\000\000\000\000\000" );

            while ( $goal < strlen( $string_nullspace ) ) {
                $pos = strrpos( substr( $string_nullspace, 0, $goal + 1 ), "\000" );

                if ( false === $pos ) {
                    $pos = strpos( $string_nullspace, "\000", $goal + 1 );
                    if ( false === $pos ) {
                        break;
                    }
                }

                $chunks[] = substr( $string, 0, $pos + 1 );
                $string = substr( $string, $pos + 1 );
                $string_nullspace = substr( $string_nullspace, $pos + 1 );
            }

            if ( $string ) {
                $chunks[] = $string;
            }

            return $chunks;
        }

        static function wp_make_email_clickable_cb($matches) {
            $email = $matches[2] . '@' . $matches[3];
            return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
        }

        static function wp_make_url_clickable_cb($matches) {
            $url = $matches[2];

            if ( ')' == $matches[3] && strpos( $url, '(' ) ) {
                // If the trailing character is a closing parethesis, 
                // and the URL has an opening parenthesis in it, 
                // add the closing parenthesis to the URL.
                // Then we can let the parenthesis balancer do its thing below.
                $url .= $matches[3];
                $suffix = '';
            } else {
                $suffix = $matches[3];
            }

            // Include parentheses in the URL only if paired
            while ( substr_count( $url, '(' ) < substr_count( $url, ')' ) ) {
                $suffix = strrchr( $url, ')' ) . $suffix;
                $url = substr( $url, 0, strrpos( $url, ')' ) );
            }

            if ( empty($url) )
                return $matches[0];

            return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $suffix;
        }

    }

}

?>
