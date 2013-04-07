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
        if(Router::$method == 'switch_form'){
            return;
        }

        $data = Event::$data;
        $htmlutil = new Htmlutil();
        $html = $htmlutil->parse($data, true, false);

        $title = $html->find('title ', 0)->innertext;
        $meta = array();

        if (Kohana::config('settings.site_banner_id') != NULL) {
            $banner = ORM::factory('media')->find(Kohana::config('settings.site_banner_id'));
            $logo = url::convert_uploaded_to_abs($banner->media_link);
            $meta['og:image'][] = $logo;
        }

        $meta['og:title'] = $title;


        if (Router::$controller == 'reports' && Router::$method == 'view') {

            $meta['og:description'] = @$html->find('.report-description-text', 0)->plaintext;

            $photos = @$html->find('.photothumb');

            foreach ($photos as $photo) {
                $meta['og:image'][] = $photo->href;
            }
        }


        if (!isset($meta['og:description'])) {
            $meta['og:description'] = Kohana::config('settings.site_tagline');
        }


        $meta_str = '';
        foreach ($meta as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $img) {
                    $meta_str .= '<meta property="' . $key . '" content="' . $img . '"/>';
                }
                continue;
            }
            $meta_str .= '<meta property = "' . $key . '" content = "' . $val . '"/>
                    ';
        }


        $html->find('head', 0)->innertext .= $meta_str;
        Event::$data = $html;
    }

}

new OpenGraph();