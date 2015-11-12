<?php
namespace Libs;

use \Libs\simplehtmldom\simple_html_dom;
use \Libs\Net;
/**
*
*/
class Web
{
    // helper functions
    // -----------------------------------------------------------------------------
    // get html dom form file
    public static function file_get_html() {
        $dom = new simple_html_dom;
        $args = func_get_args();
        $dom->load(call_user_func_array('\\Libs\\Net::curl', $args), true);
        return $dom;
    }

    // get html dom form string
    public static function str_get_html($str, $lowercase=true) {
        $dom = new simple_html_dom;
        $dom->load($str, $lowercase);
        return $dom;
    }

    // dump html dom tree
    public static function dump_html_tree($node, $show_attr=true, $deep=0) {
        $lead = str_repeat('    ', $deep);
        echo $lead.$node->tag;
        if ($show_attr && count($node->attr)>0) {
            echo '(';
            foreach($node->attr as $k=>$v)
                echo "[$k]=>\"".$node->$k.'", ';
            echo ')';
        }
        echo "\n";

        foreach($node->nodes as $c)
            dump_html_tree($c, $show_attr, $deep+1);
    }

    // get dom form file (deprecated)
    public static function file_get_dom() {
        $dom = new simple_html_dom;
        $args = func_get_args();
        $dom->load(call_user_func_array('\\Libs\\Net::curl', $args), true);
        return $dom;
    }

    // get dom form string (deprecated)
    public static function str_get_dom($str, $lowercase=true) {
        $dom = new simple_html_dom;
        $dom->load($str, $lowercase);
        return $dom;
    }
}
