<?php


class SDS_UI_Tabs {

        static $tab_counter = 0, $tablink = '', $tabcontent = '';

        static function smart_tabgroup($atts, $contents = null, $tag='',$hook_name='') {
                extract(SmartShortCode::shortcode_atts(array(
                'class' => ''
                            ), $atts));
        
            self::$tab_counter = 1;

            SmartShortCode::do_shortcode($contents,$hook_name);

            return "<div class='".$class."'><ul class='nav-tab'>" . self::$tablink . "</ul><div class='tab-content'>" . SmartShortCode::do_shortcode(self::$tabcontent,$hook_name) . '</div></div><br>';
         }

        static function smart_tab($atts, $contents= null, $tag='',$hook_name='') {

            extract(SmartShortCode::shortcode_atts(array(
                'title' => '',
                'unique' => 'true'
                            ), $atts));

            $unique_tab = !empty($unique) && $unique != 'false' ? rand(0000, 9999) : self::$tab_counter;


            if (self::$tab_counter == 1):

                self::$tablink = "<li class='active'><a href='#tab-{$unique_tab}' data-toggle='tab'>$title</a></li>";

                self::$tabcontent = "<div class='tab-pane fade in active' id='tab-{$unique_tab}'><p>".SmartShortCode::do_shortcode($contents,$hook_name)."</p></div>";

            else:

                self::$tablink .= "<li class=''><a href='#tab-{$unique_tab}' data-toggle='tab'>$title</a></li>";

                self::$tabcontent .= "<div class='tab-pane fade' id='tab-{$unique_tab}'><p>".SmartShortCode::do_shortcode($contents,$hook_name)."</p></div>";

            endif;

            self::$tab_counter++;
        }

    }
    
    SmartShortCode::add_shortcode('tabgroup', array('SDS_UI_Tabs', 'smart_tabgroup'));
    SmartShortCode::add_shortcode('tab', array('SDS_UI_Tabs', 'smart_tab'));