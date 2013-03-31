<?php

class Htmlutil_Core {

    public function __construct() {
        if (!function_exists('str_get_html')) {
            require Kohana::find_file('vendor', 'simple_html_dom');
        }
    }

    public function parse($text) {
        return str_get_html($text, true, true,DEFAULT_TARGET_CHARSET,FALSE);
    }

}