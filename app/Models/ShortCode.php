<?php namespace App\Models;

class ShortCode
{

    //protected $table = 'swapspots';
    private static $short_code_tags = array(
        'fb_page_plugin' => 'self::insert_facebook_page_plugin',
        'fb_comments' => 'self::insert_facebook_comment_plugin',
        'amazon_related_product_widget' => 'self::insert_amazon_related_product_widget'
    );

    public static function replaceShortcode($content)
    {
        $shortcode_tags = self::$short_code_tags;

        if (false === strpos($content, '[')) {
            return $content;
        }

        if (empty($shortcode_tags || !is_array($shortcode_tags)))
            return $content;

        preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
        $tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );

        if (empty($tagnames))
            return $content;

        $content = self::replaceShortcodeInHtml($content, $tagnames);
        $pattern = self::getShortCodeRegex( $tagnames );
        $content = preg_replace_callback( "/$pattern/", 'self::do_shortcode_tag', $content );
        $content = self::unescapeInvalidShortcodes($content);
        return $content;
    }

    public static function replaceShortcodeInHtml($content, $tagnames)
    {
        // Normalize entities in unfiltered HTML before adding placeholders.
        $trans = array( '&#91;' => '&#091;', '&#93;' => '&#093;' );
        $content = strtr( $content, $trans );
        $trans = array( '[' => '&#91;', ']' => '&#93;' );

        $pattern = self::getShortCodeRegex( $tagnames );
        $textarr = self::HtmlSplit( $content );

        foreach ( $textarr as &$element ) {
            $element = trim($element);
            if ( '' == $element || '<' !== $element[0] ) {
                continue;
            }

            $noopen = false === strpos( $element, '[' );
            $noclose = false === strpos( $element, ']' );
            if ( $noopen || $noclose ) {
                // This element does not contain shortcodes.
                if ( $noopen xor $noclose ) {
                    // Need to encode stray [ or ] chars.
                    $element = strtr( $element, $trans );
                }
                continue;
            }

            if ( true || '<!--' === substr( $element, 0, 4 ) || '<![CDATA[' === substr( $element, 0, 9 ) ) {
                // Encode all [ and ] chars.
                $element = strtr( $element, $trans );
                continue;
            }

            $attributes = self::wp_kses_attr_parse( $element );
            if ( false === $attributes ) {
                // Some plugins are doing things like [name] <[email]>.
                if ( 1 === preg_match( '%^<\s*\[\[?[^\[\]]+\]%', $element ) ) {
                    \Log::info('do call back 1');
                    $element = preg_replace_callback( "/$pattern/", 'self::do_shortcode_tag', $element );
                }

                // Looks like we found some crazy unfiltered HTML.  Skipping it for sanity.
                $element = strtr( $element, $trans );
                continue;
            }

            // Get element name
            $front = array_shift( $attributes );
            $back = array_pop( $attributes );
            $matches = array();
            preg_match('%[a-zA-Z0-9]+%', $front, $matches);
            $elname = $matches[0];

            // Look for shortcodes in each attribute separately.
            foreach ( $attributes as &$attr ) {
                $open = strpos( $attr, '[' );
                $close = strpos( $attr, ']' );
                if ( false === $open || false === $close ) {
                    continue; // Go to next attribute.  Square braces will be escaped at end of loop.
                }
                $double = strpos( $attr, '"' );
                $single = strpos( $attr, "'" );
                if ( ( false === $single || $open < $single ) && ( false === $double || $open < $double ) ) {
                    // $attr like '[shortcode]' or 'name = [shortcode]' implies unfiltered_html.
                    // In this specific situation we assume KSES did not run because the input
                    // was written by an administrator, so we should avoid changing the output
                    // and we do not need to run KSES here.
                    $attr = preg_replace_callback( "/$pattern/", 'self::do_shortcode_tag', $attr );
                    \Log::info('do call back 2');
                } else {
                    \Log::info('do call back 3');
                    $attr = preg_replace_callback( "/$pattern/", 'self::do_shortcode_tag', $attr );
                }
            }
            $element = $front . implode( '', $attributes ) . $back;

            // Now encode any remaining [ or ] chars.
            $element = strtr( $element, $trans );
        }

        $content = implode( '', $textarr );

        return $content;
    }

    public static function wp_kses_attr_parse( $element ) {
        $valid = preg_match('%^(<\s*)(/\s*)?([a-zA-Z0-9]+\s*)([^>]*)(>?)$%', $element, $matches);
        if ( 1 !== $valid ) {
            return false;
        }

        $begin =  $matches[1];
        $slash =  $matches[2];
        $elname = $matches[3];
        $attr =   $matches[4];
        $end =    $matches[5];

        if ( '' !== $slash ) {
            // Closing elements do not get parsed.
            return false;
        }

        // Is there a closing XHTML slash at the end of the attributes?
        if ( 1 === preg_match( '%\s*/\s*$%', $attr, $matches ) ) {
            $xhtml_slash = $matches[0];
            $attr = substr( $attr, 0, -strlen( $xhtml_slash ) );
        } else {
            $xhtml_slash = '';
        }

        // Split it
        $attrarr = self::wp_kses_hair_parse( $attr );
        if ( false === $attrarr ) {
            return false;
        }

        // Make sure all input is returned by adding front and back matter.
        array_unshift( $attrarr, $begin . $slash . $elname );
        array_push( $attrarr, $xhtml_slash . $end );

        return $attrarr;
    }

    public static function wp_kses_hair_parse( $attr ) {
        if ( '' === $attr ) {
            return array();
        }

        $regex =
            '(?:'
            .     '[-a-zA-Z:]+'   // Attribute name.
            . '|'
            .     '\[\[?[^\[\]]+\]\]?' // Shortcode in the name position implies unfiltered_html.
            . ')'
            . '(?:'               // Attribute value.
            .     '\s*=\s*'       // All values begin with '='
            .     '(?:'
            .         '"[^"]*"'   // Double-quoted
            .     '|'
            .         "'[^']*'"   // Single-quoted
            .     '|'
            .         '[^\s"\']+' // Non-quoted
            .         '(?:\s|$)'  // Must have a space
            .     ')'
            . '|'
            .     '(?:\s|$)'      // If attribute has no value, space is required.
            . ')'
            . '\s*';              // Trailing space is optional except as mentioned above.

        // Although it is possible to reduce this procedure to a single regexp,
        // we must run that regexp twice to get exactly the expected result.

        $validation = "%^($regex)+$%";
        $extraction = "%$regex%";

        if ( 1 === preg_match( $validation, $attr ) ) {
            preg_match_all( $extraction, $attr, $attrarr );
            return $attrarr[0];
        } else {
            return false;
        }
    }

    public static function HtmlSplit( $input ) {
        return preg_split( self::getHtmlSplitRegex(), $input, -1, PREG_SPLIT_DELIM_CAPTURE );
    }

    public static function getShortCodeRegex()
    {
        $shortcode_tags = self::$short_code_tags;

        if ( empty( $tagnames ) ) {
            $tagnames = array_keys( $shortcode_tags );
        }
        $tagregexp = join( '|', array_map('preg_quote', $tagnames) );

        // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
        // Also, see shortcode_unautop() and shortcode.js.
        return
            '\\['                              // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . "($tagregexp)"                     // 2: Shortcode name
            . '(?![\\w-])'                       // Not followed by word character or hyphen
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            .     '(?:'
            .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
            .     ')*?'
            . ')'
            . '(?:'
            .     '(\\/)'                        // 4: Self closing tag ...
            .     '\\]'                          // ... and closing bracket
            . '|'
            .     '\\]'                          // Closing bracket
            .     '(?:'
            .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            .             '[^\\[]*+'             // Not an opening bracket
            .             '(?:'
            .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            .                 '[^\\[]*+'         // Not an opening bracket
            .             ')*+'
            .         ')'
            .         '\\[\\/\\2\\]'             // Closing shortcode tag
            .     ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
    }

    public static function getHtmlSplitRegex()
    {
        static $regex;

        if ( ! isset( $regex ) ) {
            $comments =
                '!'           // Start of comment, after the <.
                . '(?:'         // Unroll the loop: Consume everything until --> is found.
                .     '-(?!->)' // Dash not followed by end of comment.
                .     '[^\-]*+' // Consume non-dashes.
                . ')*+'         // Loop possessively.
                . '(?:-->)?';   // End of comment. If not found, match all input.

            $cdata =
                '!\[CDATA\['  // Start of comment, after the <.
                . '[^\]]*+'     // Consume non-].
                . '(?:'         // Unroll the loop: Consume everything until ]]> is found.
                .     '](?!]>)' // One ] not followed by end of comment.
                .     '[^\]]*+' // Consume non-].
                . ')*+'         // Loop possessively.
                . '(?:]]>)?';   // End of comment. If not found, match all input.

            $escaped =
                '(?='           // Is the element escaped?
                .    '!--'
                . '|'
                .    '!\[CDATA\['
                . ')'
                . '(?(?=!-)'      // If yes, which type?
                .     $comments
                . '|'
                .     $cdata
                . ')';

            $regex =
                '/('              // Capture the entire match.
                .     '<'           // Find start of element.
                .     '(?'          // Conditional expression follows.
                .         $escaped  // Find end of escaped element.
                .     '|'           // ... else ...
                .         '[^>]*>?' // Find end of normal element.
                .     ')'
                . ')/';
        }

        return $regex;
    }

    public static function unescapeInvalidShortcodes($content)
    {
        // Clean up entire string, avoids re-parsing HTML.
        $trans = array( '&#91;' => '[', '&#93;' => ']' );
        $content = strtr( $content, $trans );

        return $content;
    }

    public static function do_shortcode_tag($m)
    {
        $shortcode_tags = self::$short_code_tags;
        // allow [[foo]] syntax for escaping a tag
        if ( $m[1] == '[' && $m[6] == ']' ) {
            return substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $attr = self::shortcode_parse_atts( $m[3] );

        if ( ! is_callable( $shortcode_tags[ $tag ] ) ) {
            /* translators: %s: shortcode tag */
            \Log::info('Can\'t callback' . $tag);
            return $m[0];
        }

        if ( isset( $m[5] ) ) {
            // enclosing tag - extra parameter
            return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, $m[5], $tag ) . $m[6];
        } else {
            // self-closing tag
            return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, null,  $tag ) . $m[6];
        }
    }

    public static function shortcode_parse_atts($text)
    {
        $atts = array();
        $pattern = self::get_shortcode_atts_regex();
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
        if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
            foreach ($match as $m) {
                if (!empty($m[1]))
                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                elseif (!empty($m[3]))
                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                elseif (!empty($m[5]))
                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                elseif (isset($m[7]) && strlen($m[7]))
                    $atts[] = stripcslashes($m[7]);
                elseif (isset($m[8]))
                    $atts[] = stripcslashes($m[8]);
            }

            // Reject any unclosed HTML elements
            foreach( $atts as &$value ) {
                if ( false !== strpos( $value, '<' ) ) {
                    if ( 1 !== preg_match( '/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value ) ) {
                        $value = '';
                    }
                }
            }
        } else {
            $atts = ltrim($text);
        }
        return $atts;
    }

    public static function get_shortcode_atts_regex() {
	    return '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
    }

    public static function shortcode_atts($pairs, $atts, $shortcode = '')
    {
        $atts = (array)$atts;
        $out = array();
        foreach ($pairs as $name => $default) {
            if (array_key_exists($name, $atts))
            {
                $out[$name] = $atts[$name];
            } else {
                $out[$name] = $default;
            }
        }
        return $out;
    }

    public static function insert_facebook_page_plugin($atts) {
        $atts = self::shortcode_atts(
            array(
                'small-header' => 'false',
                'adapt-container-width' => 'true',
                'hide-cover' => 'false',
                'show-facepile' => 'true',
                'page-url' => '',
                'width' => '',
                'height' => ''
            ), $atts, 'fb_page_plugin'
        );
        if ($atts['width'] != '')
            $width = ' data-width="' . $atts['width'] . '" ';
        else
            $width = '';

        if ($atts['height'] != '')
            $height = ' data-height="' . $atts['height'] . '" ';
        else
            $height = '';

        $out = '<div id="fb-root"></div><script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';
        $out .= '<div class="fb-page" data-href="' . $atts['page-url'] . '" data-tabs="timeline" data-small-header="' . $atts['small-header'] . '" data-adapt-container-width="' . $atts['adapt-container-width'] . '" data-hide-cover="' . $atts['hide-cover'] .  '" data-show-facepile="' . $atts['show-facepile'] . '"' . $width . $height . '></div>';
        return $out;
    }

    public static function insert_facebook_comment_plugin($atts) {
        $atts = self::shortcode_atts(
            array(
                'num-posts' => '5',
                'order-by' => 'social',
                'page-url' => '',
                'width' => '',

            ), $atts, 'fb_comments'
        );

        if ($atts['width'] != '')
            $width = ' data-width="' . $atts['width'] . '" ';
        else
            $width = '';

        $out = '<div id="fb-root"></div><script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';
        $out .= '<div class="fb-comments" data-href="' . $atts['page-url'] . '" data-order-by="' . $atts['order-by'] . '" data-numposts="' . $atts['num-posts'] . '"' . $width . '></div>';
        return $out;
    }

    public static function insert_amazon_related_product_widget($atts) {

        $atts = self::shortcode_atts(
            array(
                'assoc-placement' => 'adunit0',
                'tracking-id' => '',
                'link-id' => '',
                'emphasize-categories' => '',
                'default-category' => '',
                'fallback-mode-value' => '',

            ), $atts, 'amazon_related_product_widget'
        );

        return '<script type="text/javascript">
                amzn_assoc_placement = "' . $atts['assoc-placement'] . '";
                amzn_assoc_enable_interest_ads = "true";
                amzn_assoc_tracking_id = "' . $atts['tracking-id'] . '";
                amzn_assoc_ad_mode = "auto";
                amzn_assoc_ad_type = "smart";
                amzn_assoc_marketplace = "amazon";
                amzn_assoc_region = "US";
                amzn_assoc_linkid = "' . $atts['link-id'] . '";
                amzn_assoc_emphasize_categories = "' . $atts['emphasize-categories'] . '";
                amzn_assoc_default_category = "' . $atts['default-category'] . '";
                amzn_assoc_fallback_mode = {"type":"search","value":"' . $atts['fallback-mode-value'] .  '"};
                </script>
                <script src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US" type="text/javascript"></script>
                <div id="amzn_assoc_ad_div_' . $atts['assoc-placement'] . '_0"></div>';
    }
}