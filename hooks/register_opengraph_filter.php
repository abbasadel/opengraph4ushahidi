<?php

defined('SYSPATH') or die('No direct script access.');

class OpenGraph {

    public function __construct() {
        Event::add('system.pre_controller', array($this, 'post'));
    }

    public function post() {
        Event::add('system.display', array($this, 'add'));
    }

    public function add() {
        if (Router::$controller != 'reports') {
            return;
        }
        $data = Event::$data;
        $htmlutil = new Htmlutil();
        $html = $htmlutil->parse($data, true, false);

        $meta = array();

        $title = $html->find('title ', 0)->innertext;
        $desc = @$html->find('.report-description-text', 0)->plaintext;
        $photos = @$html->find('.photothumb');
        $logo = @$html->find('#page img',1)->src;



        $meta['og:title'] = $title;
        $meta['og:description'] = $desc;
        

        foreach ($photos as $photo) {
            $meta['og:image'][] = $photo->href;
        }
        
        $meta['og:image'][]=$logo;


        $meta_str = '';
        foreach ($meta as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $img) {
                    $meta_str .= '<meta property="' . $key . '" content="' . $img . '"/>';
                }
                continue;
            }
            $meta_str .= '<meta property = "' . $key . '" content = "' . $val . '"/>
                    ' ;
        }

        $html->find('head', 0)->innertext .= $meta_str;



        Event::$data = $html;
       
    }

}

new OpenGraph();