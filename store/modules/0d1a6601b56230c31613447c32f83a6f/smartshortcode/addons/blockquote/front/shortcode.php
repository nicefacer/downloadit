<?php

 function shortcode_blockquote($atts, $content = null, $tag, $hook_name) {

        return '<div class="block-quote">' . SmartShortCode::do_shortcode($content,$hook_name) . '</div>';
    }
    SmartShortCode::add_shortcode('block-quote', 'shortcode_blockquote');
     function shortcode_blockquote_left($atts, $content = null, $tag, $hook_name) {

        return '<div class="block-quote block-quote-left">' . SmartShortCode::do_shortcode($content,$hook_name). '</div>';
    }

    SmartShortCode::add_shortcode('block-quote-left', 'shortcode_blockquote_left');

    function shortcode_blockquote_right($atts, $content = null, $tag, $hook_name) {

        return '<div class="block-quote block-quote-right">' . SmartShortCode::do_shortcode($content,$hook_name) . '</div>';
    }

    SmartShortCode::add_shortcode('block-quote-right', 'shortcode_blockquote_right');
