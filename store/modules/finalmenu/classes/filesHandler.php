<?php

class filesHandler
{

	private static $was_written_CSS = FALSE;
	private static $was_written_JS = FALSE;
	// Menu basic css
	private static $tabs_specific_css = "";
	// Blocks specific CSS
	private static $blocks_specific_css = "";
	// Blocks specific JS
	private static $blocks_specific_js = "";

	public static function addTabsCSS($css)
	{
		filesHandler::$tabs_specific_css .= $css;
	}

	public static function addBlocksCSS($css)
	{
		filesHandler::$blocks_specific_css .= $css;
	}

	public static function addBlocksJS($js)
	{
		filesHandler::$tabs_specific_js .= $js;
	}

	public static function addTabCSS($tab_index, $tab_float, $tab, $settings, $menu_prefix, $menu_type)
	{
		// if check is for backup compatibility -> older versions does not have pdng property
		$tab_bg_img_pdng_left = (isset($settings['tab_bg_img_pdng_left'])) ? $settings['tab_bg_img_pdng_left'] : 0;
		$tab_bg_img_pdng_right = (isset($settings['tab_bg_img_pdng_right'])) ? $settings['tab_bg_img_pdng_right'] : 0;
		$tab_bg_img_pdng_bottom = (isset($settings['tab_bg_img_pdng_bottom'])) ? $settings['tab_bg_img_pdng_bottom'] : 0;
		$tab_bg_img_pdng_top = (isset($settings['tab_bg_img_pdng_top'])) ? $settings['tab_bg_img_pdng_top'] : 0;

		$css = "{$menu_prefix} #FINALmenu-tab-{$menu_type}-{$tab_index} {
				{$tab_float}
			}
			{$menu_prefix} #FINALmenu-{$menu_type}-{$tab_index}-tab-wrapper {
				color: {$tab['other_text_color']};
				padding-left: {$tab_bg_img_pdng_left}px;
				padding-right: {$tab_bg_img_pdng_right}px;
				padding-bottom: {$tab_bg_img_pdng_bottom}px;
				padding-top: {$tab_bg_img_pdng_top}px;
			}
			{$menu_prefix} #FINALmenu-{$menu_type}-{$tab_index}-tab-wrapper a {
				color: {$tab['links_color']};
			}
			{$menu_prefix} #FINALmenu-{$menu_type}-{$tab_index}-tab-wrapper a:hover {
				color: {$tab['links_hover_color']};
			}
			{$menu_prefix} #FINALmenu-{$menu_type}-{$tab_index}-tab-wrapper .tab-block {
				border-color: {$settings['tab_blocks_border_color']} !important;
			}";

		filesHandler::addTabsCSS($css);
	}

	public static function addAdvanceMenuTabCSS($tab_index, $settings, $tab_position, $id_lang, $menu_prefix, $menu_type)
	{
		if(empty($settings['tab_background_link'][$id_lang])) {
			$bg = 'background-color: ' . $settings['tab_wrapper_bg_color'] . ';';
		} else {

			// if check is for backup compatibility -> older versions does not have position property
			$position = ((isset($settings['tab_bg_img_position'])) ? str_replace('-', ' ', $settings['tab_bg_img_position']) : "top left");
			$bg = "background-image: url({$settings['tab_background_link'][$id_lang]});
				   background-position: {$position};
				   background-origin: content-box;";
		}

		$css = "{$menu_prefix} #FINALmenu-{$menu_type}-{$tab_index}-tab-wrapper {
				{$tab_position}
				{$bg}
				background-repeat: {$settings['tab_bg_img_repeat']};
			}

			{$menu_prefix} #FINALmenu-{$menu_type}-{$tab_index}-tab-wrapper .hidden-categories {
				background-color: {$settings['tab_wrapper_bg_color']};
			}

			{$menu_prefix} #FINALmenu-{$menu_type}-{$tab_index}-tab-wrapper .related-posts-title {
				border-color: {$settings['tab_blocks_border_color']} !important;
			}";

		filesHandler::addTabsCSS($css);
	}

	public static function addGripItemsCSS($values, $path, $tab_index, $block_index, $menu_prefix, $menu_type)
	{
		$column_width = floor($values['nmb_of_columns']/$values['item_number_of_columns']);
		$column_width = ($column_width == 0) ? 100 : (100/$column_width);

		$column_width_SMALL = floor($values['nmb_of_columns']/($values['item_number_of_columns'] +1));
		$column_width_SMALL = ($column_width_SMALL == 0) ? 100 : (100/$column_width_SMALL);

		$css = "@media (min-width: 768px) {
				{$menu_prefix} #FINALmenu-{$menu_type}_{$values['name']}_{$tab_index}_{$block_index} .category-grid-view {
					width: {$column_width_SMALL}%;
				}
			}
			@media (min-width: 992px) {
			   {$menu_prefix} #FINALmenu-{$menu_type}_{$values['name']}_{$tab_index}_{$block_index} .category-grid-view {
					width: {$column_width}%;
				}
			}";

		filesHandler::addBlocksCSS($css);
	}

	public static function addAdvanceMenuBlockCSS($values, $tab_index, $block_index, $menu_prefix, $menu_type)
	{
		$padding_top ='padding-top: '.$values['padding_top'].'px;';
		$padding_bottom = 'padding-bottom: '.$values['padding_bottom'].'px;';
		$padding_left = 'padding-left: '.$values['padding_left'].'px;';
		$padding_right = 'padding-right: '.$values['padding_right'].'px;';
		$float = 'float: '.$values['float'].';';

		$css = "{$menu_prefix} #FINALmenu-{$menu_type}_{$values['name']}_{$tab_index}_{$block_index} {
				{$padding_top}
				{$padding_bottom}
				{$padding_left}
				{$padding_right}
				{$float}
			}";

		filesHandler::addBlocksCSS($css);
	}

	public static function generateCSS($desktop_menu_settings, $mobile_menu_settings, $path)
	{
		$head = '
			/**
			 * Finalmenu
			 * @author     Matej Berka
			 * @copyright  2014 Matej
			 */
		';
		$menu_top_links_font_size = ($desktop_menu_settings["menu_top_links_font_size"]-3);
		$imports = (empty($desktop_menu_settings['menu_top_links_font_url'])) ? '' : '@import url('.$desktop_menu_settings['menu_top_links_font_url'].');';
		$imports .= (empty($mobile_menu_settings['FINALm_links_font_url'])) ? '' : '@import url('.$mobile_menu_settings['FINALm_links_font_url'].');';
		$basic_style = "
			{$imports}
			.row > #FINALmenu {
				margin: 20px 15px 0px 15px;
			}

			#FINALmenu {
				clear: both;
				z-index: 9999;
				background-color: {$desktop_menu_settings['menu_background_color']};
			}

			#FINALmenu li,
			#FINALmenu-vertical li {
				list-style-type: none;
			}

			#FINALmenu-desktop-nav > li.left-tabs {
				border-right: 1px solid;
				border-color: {$desktop_menu_settings['links_separator_color']};
			}

			#FINALmenu-desktop-nav > li.right-tabs {
				border-left: 1px solid;
				border-color: {$desktop_menu_settings['links_separator_color']};
			}

			#FINALmenu .container {
				padding-left: 0px;
				padding-right: 0px;
			}

			#FINALmenu.sticky_menu {
				position: fixed;
				width: 100%;
				top: -100px;
				left: 0px;
				margin: 0px !important;
				opacity: {$desktop_menu_settings['sticky_menu_transparency']};
			}

			#FINALmenu.sticky_menu:hover {
				opacity: 1
			}

			#FINALmenu.sticky_menu .tab-note {
				display: none;
			}

			#FINALmenu .show-items-icon {
				margin-left: 10px;
				cursor: pointer;
				float: right;
				font-size: {$menu_top_links_font_size}px;
				line-height: {$desktop_menu_settings['menu_top_links_line_height']}px;
			}

			.FINALmenu-simple-tab ul {
				 padding: 10px;
				 background: {$desktop_menu_settings['menu_background_color']};
			}

			#FINALmenu #FINALmenu-desktop-nav {
				position: relative;
				font-family: {$desktop_menu_settings['menu_top_links_font']};
				display: table;
				margin-bottom: 0px;
				width: 100%;
			}

			#FINALmenu #FINALmenu-desktop-nav,
			#FINALmenu-vertical #FINALmenu-vertical-nav,
			#FINALmenu.sticky_menu,
			#FINALmenu-mobile-nav li,
			#FINALmenu-vertical #FINALmenu-vertical-nav > li,
			#FINALmenu #FINALmenu-desktop-nav > li {
				-o-transition: color .3s ease-out, all .3s ease-in;
				-ms-transition: color .3s ease-out, all .3s ease-in;
				-moz-transition: color .3s ease-out, all .3s ease-in;
				-webkit-transition: color .3s ease-out, all .3s ease-in;
				transition: color .3s ease-out, all .3s ease-in;
			}

			#FINALmenu-vertical .FINALmenu-tab-content .show-items-icon,
			#FINALmenu .FINALmenu-tab-content .show-items-icon {
				line-height: 25px;
				margin-left: 0px;
				font-size: 15px !important;
			}

			#FINALmenu-vertical .FINALmenu-tab-content .second-level-item .show-items-icon,
			#FINALmenu .FINALmenu-tab-content .second-level-item .show-items-icon {
				margin-right: 15px;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav > li,
			#FINALmenu #FINALmenu-desktop-nav > li {
				vertical-align: middle;
				text-align: left;
			}

			#FINALmenu #FINALmenu-desktop-nav > li:hover {
				background: {$desktop_menu_settings['tab_hover_background_color']};
			}

			.top-link-wrapper {
				position: relative;
				padding: 15px;
			}

			#FINALmenu .top-link-wrapper i {
				color: {$desktop_menu_settings['icons_color']};
			}

			#FINALmenu .top-link-wrapper a,
			#FINALmenu .top-link-wrapper span {
				color: {$desktop_menu_settings['text_color']};
			}

			#FINALmenu-vertical .bx-controls-direction,
			#FINALmenu .bx-controls-direction {
				display: block;
				width: 43px;
			}

			body #FINALmenu #FINALmenu-desktop-nav > li:hover .top-link-wrapper .tab-note,
			.tab-note {
				color: {$desktop_menu_settings["text_color"]} !important;
			}

			#FINALmenu #FINALmenu-desktop-nav > li:hover .top-link-wrapper i,
			#FINALmenu #FINALmenu-desktop-nav > li:hover .top-link-wrapper a,
			#FINALmenu #FINALmenu-desktop-nav > li:hover .top-link-wrapper span {
				color: {$desktop_menu_settings['foreground_hover_color']} !important;
			}

			#FINALmenu #FINALmenu-desktop-nav > li .top-link-wrapper a,
			#FINALmenu #FINALmenu-desktop-nav > li .top-link-wrapper span {
				float: left;
				font-size: {$desktop_menu_settings['menu_top_links_font_size']}px;
				line-height: {$desktop_menu_settings['menu_top_links_line_height']}px;
			}


			.FINALmenu-tab-content {
				position: absolute;
				display: none;
				z-index: 99;
				padding: 0px;
				margin: 0px;
			}

			#FINALmenu .FINALmenu-simple-tab .FINALmenu-tab-content {
				left: 0px;
			}

			/* categories specific */
			#FINALmenu-vertical #FINALmenu-vertical-nav .hidden-categories,
			#FINALmenu #FINALmenu-desktop-nav .hidden-categories {
				display: none;
				position: absolute;
				left: 69%;
				padding: 15px;
				z-index: 99;
				line-height: 18px;
				width: 185px;
				-webkit-box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
				-moz-box-shadow: 0 2px 10px rgba(0,0,0,0.15);
				box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .first-level-item ul,
			#FINALmenu #FINALmenu-desktop-nav .first-level-item ul {
				clear: both;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .first-level-item > span,
			#FINALmenu-vertical #FINALmenu-vertical-nav .first-level-item > a,
			#FINALmenu #FINALmenu-desktop-nav .first-level-item > span,
			#FINALmenu #FINALmenu-desktop-nav .first-level-item > a {
				font-size: 16px;
				font-weight: bold;
			}

			#FINALmenu-vertical .FINALmenu-simple-tab,
			#FINALmenu .FINALmenu-simple-tab {
				position: relative;
			}

			#FINALmenu-vertical .FINALmenu-simple-tab ul,
			#FINALmenu .FINALmenu-simple-tab ul {
				width: 200px;
				max-width: 100%;
				box-sizing: border-box;
				-moz-box-sizing: border-box;
			}

			#FINALmenu-vertical .FINALmenu-simple-tab a,
			#FINALmenu .FINALmenu-simple-tab a {
				line-height: 27px;
				font-size: 14px;
				text-align: left;
				max-width: 80%;
			}

			.top-level-link {
				max-width: 100% !important;
			}

			#FINALmenu-vertical .FINALmenu-simple-tab .hidden-categories,
			#FINALmenu .FINALmenu-simple-tab .hidden-categories {
				margin-top: -30px;
			}

			#FINALmenu-vertical .cms-pages a, #FINALmenu-vertical .categories a,
			#FINALmenu-vertical .suppliers a, #FINALmenu-vertical .manufacturers a,
			#FINALmenu-vertical .suppliers span, #FINALmenu-vertical .manufacturers span,
			#FINALmenu .cms-pages a, #FINALmenu .categories a,
			#FINALmenu .suppliers a, #FINALmenu .manufacturers a,
			#FINALmenu .suppliers span, #FINALmenu .manufacturers span {
				float: left;
				line-height: 30px;
				font-size: 14px;
				text-align: left;
				max-width: 80%;
			}

			#FINALmenu-vertical .categories li, #FINALmenu-vertical .cms-pages li,
			#FINALmenu-vertical .suppliers li, #FINALmenu .manufacturers li,
			#FINALmenu .categories li, #FINALmenu .cms-pages li,
			#FINALmenu .suppliers li, #FINALmenu .manufacturers li {
				position: relative;
				float: left;
			}
			#FINALmenu-vertical .categories li.sub-items, #FINALmenu-vertical .cms-pages li.sub-items,
			#FINALmenu-vertical .suppliers li.sub-items, #FINALmenu-vertical .manufacturers li.sub-items,
			#FINALmenu .categories li.sub-items, #FINALmenu .cms-pages li.sub-items,
			#FINALmenu .suppliers li.sub-items, #FINALmenu .manufacturers li.sub-items {
				width: 100%;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .related-posts-title,
			#FINALmenu #FINALmenu-desktop-nav .related-posts-title {
				line-height: 25px;
				margin: 10px 0px;
				font-weight: bold;
				clear: both;
				border-bottom: 1px dashed;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .related-posts li a,
			#FINALmenu #FINALmenu-desktop-nav .related-posts li a {
				font-size: 12px;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .first-level-item, #FINALmenu-vertical #FINALmenu-vertical-nav .sub-item,
			#FINALmenu #FINALmenu-desktop-nav .first-level-item, #FINALmenu #FINALmenu-desktop-nav .sub-item {
				width: 100%;
			}

			.tab-note {
				position: absolute;
				border-radius: 2px;
				top: -13px;
				left: 4px;
				font-size: 14px !important;
				line-height: 16px !important;
				padding: 3px 7px;
			}

			span.separator {
				clear: both;
				display: block;
				width: 100%;
			}

			/*Tabs layouts*/
			#FINALmenu .layout-1 i,
			#FINALmenu-vertical .layout-1 i,
			#FINALmenu-vertical .layout-1 img,
			#FINALmenu .layout-1 img {
				margin-right: 10px;
			}
			#FINALmenu-vertical .layout-2 i,
			#FINALmenu .layout-2 i {
				width: 100%;
				padding-bottom: 5px;
				display: block;
				text-align: center;
			}

			#FINALmenu-vertical .layout-2 img,
			#FINALmenu .layout-2 img {
				margin: 0 auto;
				padding-bottom: 5px;
				display: block;
			}

			#FINALmenu-vertical .image-wrapper a,
			#FINALmenu .image-wrapper a {
				max-width: 100% !important;
			 }

			/*product block specific*/
			#FINALmenu-vertical #FINALmenu-vertical-nav .image-wrapper,
			#FINALmenu #FINALmenu-desktop-nav .image-wrapper {
				float: left;
				padding: 10px;
				padding-bottom: 0px;
				overflow: hidden;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .image-wrapper p,
			#FINALmenu #FINALmenu-desktop-nav .image-wrapper p {
				padding: 15px;
				text-align: center;
				clear: both;
				font-weight: bold;
				margin: 0px;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .image-wrapper a,
			#FINALmenu-vertical #FINALmenu-vertical-nav .image-wrapper img,
			#FINALmenu #FINALmenu-desktop-nav .image-wrapper a,
			#FINALmenu #FINALmenu-desktop-nav .image-wrapper img {
				width: 100%;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .image-view,
			#FINALmenu #FINALmenu-desktop-nav .image-view {
				overflow: hidden;
				position: relative;
				clear: both;
				width: 100%;
				float: left;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .image-view .second-image,
			#FINALmenu #FINALmenu-desktop-nav .image-view .second-image {
				position: absolute;
				left: 0px;
				top: 0px;
				opacity: 0;
				max-width: 100%;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .bx-controls,
			#FINALmenu #FINALmenu-desktop-nav .bx-controls {
				font-family: \"FontAwesome\";
				margin-right: 10px;
				float: right;
				margin-bottom: 10px;
				margin-top: 0px;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .bx-controls a,
			#FINALmenu #FINALmenu-desktop-nav .bx-controls a {
				width: auto;
				font-size: 16px;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .bx-prev:before,
			#FINALmenu #FINALmenu-desktop-nav .bx-prev:before {
				padding: 5px;
				content: \"\\f053\";
				font-family: \"FontAwesome\";
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .bx-next:before,
			#FINALmenu #FINALmenu-desktop-nav .bx-next:before {
				padding: 5px;
				content: \"\\f054\";
				font-family: \"FontAwesome\";
			}

			#FINALmenu-vertical .first-level-item {
				float: none !important;
			}

			/*custom image*/
			#FINALmenu-vertical .custom-image img,
			#FINALmenu .custom-image img {
				width: 100%;
			}

			/* search field specific */
			#searchbox p {
				margin-bottom: 0px;
			}

			#FINALmenu-vertical .image-view a,
			#FINALmenu .image-view a {
				width: 100% !important;
			}

			/*SEPARATOR*/
			#FINALmenu-vertical .separator-left,
			#FINALmenu .separator-left {
				border-left: 1px solid;
			}

			#FINALmenu-vertical .separator-right,
			#FINALmenu .separator-right {
				border-right: 1px solid;
			}

			#FINALmenu-vertical .separator-top,
			#FINALmenu .separator-top {
				border-top: 1px solid;
			}

			#FINALmenu-vertical .separator-bottom,
			#FINALmenu .separator-bottom {
				border-bottom: 1px solid;
			}

			#FINALmenu-vertical .separator-left-right,
			#FINALmenu .separator-left-right {
				border-left: 1px solid;
				border-right: 1px solid;
			}

			#FINALmenu-vertical .separator-top-bottom,
			#FINALmenu .separator-top-bottom {
				border-top: 1px solid;
				border-bottom: 1px solid;
			}

			#FINALmenu-vertical .separator-complet,
			#FINALmenu .separator-complet {
				border: 1px solid;
			}

			.search-wrapper {
				background-color: white;
				padding: 12px;
				border-radius: 20px;
				position: relative;
				border: 1px solid #CCC;
			}

			.search-wrapper .search_query_menu {
				border: 0px;
				line-height: 35px;
				margin-right: 15px;
				outline: none;
			}

			.final_no_padding {
				padding: 0px !important;
			}

			.search-wrapper .button-search {
				background: none;
				border: none;
				position: absolute;
				top: 15px;
				right: 10px;
				outline: none;
			}

			.search-wrapper .button-search span {
				display: none;
			}

			.search-wrapper .button-search:before {
				content: \"\\f002\";
				display: block;
				font-family: \"FontAwesome\";
				font-size: 14px;
				color: #666;
				width: 100%;
				text-align: center;
			}

			.category-grid-view {
				overflow: hidden;
				padding: 5px;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav .categories .category-grid-view a,
			#FINALmenu #FINALmenu-desktop-nav .categories .category-grid-view a {
				max-width: 100%;
				float: none;
			}

			.category-grid-view img {
				margin: 0px 10px 0px 0px;
				float: left;
			}
			.category-grid-view div {
				padding-bottom:  5px;
				overflow: hidden;
				display: block;
			}

			.category-grid-view .product-category-name {
				font-weight: bold;
			}

			/*MOBILE MENU*/
			.mobile_menu_wrapper {
				color: {$mobile_menu_settings["FINALm_text_color"]};
			}

			.menu-place-holder {
				line-height: 35px;
				padding: 10px 20px;
				font-size: 25px;
				cursor: pointer;
				text-align: left;
			}

			#FINALmenu-mobile-nav {
				margin: 0px;
				display: none;
				font-family: {$mobile_menu_settings['FINALm_links_font']};
				font-size: {$mobile_menu_settings['FINALm_links_font_size']}px;
				line-height: {$mobile_menu_settings['FINALm_links_line_height']}px;
				background-color: {$mobile_menu_settings['FINALm_bg_color']};
			}

			#FINALmenu-mobile-nav a {
				width: 100%;
				margin-left: 10px;
				color: {$mobile_menu_settings['FINALm_text_color']};
			}

			#FINALmenu-mobile-nav i {
				color: {$mobile_menu_settings['FINALm_icon_color']};
			}

			#FINALmenu-mobile-nav i,
			#FINALmenu-mobile-nav a,
			#FINALmenu-mobile-nav li {
				font-size: {$mobile_menu_settings['FINALm_links_font_size']}px;
				line-height: {$mobile_menu_settings['FINALm_links_line_height']}px;
			}

			#FINALmenu-mobile-nav li:hover {
				color: {$mobile_menu_settings['FINALm_text_hover_color']};
				background-color: {$mobile_menu_settings['FINALm_background_hover_color']};
			}

			#FINALmenu-mobile-nav ul {
				display: none;
				background-color: {$mobile_menu_settings['FINALm_submenu_bg_color']};
			}

			#FINALmenu-mobile-nav .related-posts-title {
				padding-left: 10px;
				color: {$mobile_menu_settings['FINALm_text_color']};
				border-bottom: 1px dashed {$mobile_menu_settings['FINALm_bg_color']};
			}

			#FINALmenu-mobile-nav i {
				float: right;
				font-size: 19px;
				margin-right: 15px;
				line-height: {$mobile_menu_settings['FINALm_links_line_height']}px;
			}

			#FINALmenu-mobile-nav li a {
				padding-left: 10px;
			}

			/* MEDIA */
			@media (max-width: 768px) {
				#FINALmenu-vertical-nav,
				#FINALmenu-desktop-nav {
					display: none !important;
				}

				.mobile_menu_wrapper {
					display: block !important;
				}
			}

			@media (min-width: 768px) {
				#FINALmenu-vertical-nav,
				#FINALmenu-desktop-nav {
					display: table !important;
				}

				.mobile_menu_wrapper {
					display: none !important;
				}
			}";

		$output = $head.$basic_style.filesHandler::$blocks_specific_css.filesHandler::$tabs_specific_css.html_entity_decode($desktop_menu_settings['custom_css'], ENT_QUOTES);

		if(filesHandler::$was_written_CSS) {
			$fh = fopen($path, 'a') or die ("Can not open FINALmenu.css");
		} else {
			filesHandler::$was_written_CSS = TRUE;
			$fh = fopen($path, 'w') or die ("Can not open FINALmenu.css");
		}

		fwrite($fh, $output);
		fclose($fh);
	}

	public static function appendCSS($settings, $path)
	{
		$imports = (empty($settings['menu_top_links_font_url'])) ? '' : '@import url('.$settings['menu_top_links_font_url'].');';
		$menu_top_links_font_size = ($settings["menu_top_links_font_size"]-3);
		$output = "
			{$imports}
			#FINALmenu-vertical {
				background-color: {$settings['menu_background_color']};
			}

			#FINALmenu-vertical-nav > li {
				width: 100%;
				border-bottom: 1px solid;
				border-color: {$settings['links_separator_color']};
			}

			#FINALmenu-vertical-nav > li:last-child {
				border-bottom: 0px solid;
			}

			#FINALmenu-vertical .FINALmenu-simple-tab .FINALmenu-tab-content {
				left: 100%;
				top: 0px;
			}

			#FINALmenu-vertical .show-items-icon {
				margin-left: 10px;
				cursor: pointer;
				float: right;
				font-size: {$menu_top_links_font_size}px;
				line-height: {$settings['menu_top_links_line_height']}px;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav {
				position: relative;
				font-family: {$settings['menu_top_links_font']};
				display: table;
				margin-bottom: 0px;
				width: 100%;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav > li:hover {
				background: {$settings['tab_hover_background_color']};
			}

			#FINALmenu-vertical .top-link-wrapper i {
				color: {$settings['icons_color']};
			}

			#FINALmenu-vertical .top-link-wrapper a,
			#FINALmenu-vertical .top-link-wrapper span {
				color: {$settings['text_color']};
			}

			body #FINALmenu-vertical #FINALmenu-vertical-nav > li:hover .top-link-wrapper .tab-note {
				color: {$settings['text_color']} !important;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav > li:hover .top-link-wrapper i,
			#FINALmenu-vertical #FINALmenu-vertical-nav > li:hover .top-link-wrapper a,
			#FINALmenu-vertical #FINALmenu-vertical-nav > li:hover .top-link-wrapper span {
				color: {$settings['foreground_hover_color']} !important;
			}

			#FINALmenu-vertical #FINALmenu-vertical-nav > li .top-link-wrapper a,
			#FINALmenu-vertical #FINALmenu-vertical-nav > li .top-link-wrapper span {
				float: left;
				font-size: {$settings['menu_top_links_font_size']}px;
				line-height: {$settings['menu_top_links_line_height']}px;
			}

			#FINALmenu-vertical .FINALmenu-tab-content > div {
				padding: 0px;
			}

			/* MEDIA */
			@media (min-width: 768px) {
				.tw-w-768 {
					position: absolute;
					margin-left: 100% !important;
					top: 0px;
					width: {$settings['tab_wrapper_w_768']}px;
				}
				.separator-bg-screens {
					display: none;
				}
				.separator-sm-screens {
					display: block;
				}
			}

			@media (min-width: 992px) {
				.separator-bg-screens {
					display: block;
				}
				.separator-sm-screens {
					display: none;
				}
				.tw-w-992 {
					width: {$settings['tab_wrapper_w_992']}px;
				}
			}

			@media (min-width: 1200px) {
				.tw-w-1200 {
					width: {$settings['tab_wrapper_w_1200']}px;
				}
			}";

		$output .= filesHandler::$tabs_specific_css . filesHandler::$blocks_specific_css;

		if(filesHandler::$was_written_CSS) {
			$fh = fopen($path, 'a') or die ("Can not open FINALmenu.css");
		} else {
			filesHandler::$was_written_CSS = TRUE;
            if(file_exists($path)) {
                $fh = fopen($path, 'a') or die ("Can not open FINALmenu.css");
            } else {
                $fh = fopen($path, 'w') or die ("Can not open FINALmenu.css");
            }
		}

		fwrite($fh, $output);
		fclose($fh);
	}

	public static function generateCustomJS($menu_settings, $mobileDevice, $path)
	{
		$output ='$(document).ready(function () { // <![CDATA[';
		// basic JS
		$output .= '
				window.menuSliders = new Array();
				window.menuAnimateInHorizontal = "'.$menu_settings['menuAnimateIn'].'";
				window.menuAnimateOutHorizontal = "'.$menu_settings['menuAnimateOut'].'";
				var value = $("input[name=search_query]").val();
				$("input[name=search_query]").focusin(function () {
					$(this).val("");
				});
				$("input[name=search_query]").change(function () {
					value = $("input[name=search_query]").val();
				});
				$("input[name=search_query]").focusout(function () {
					$(this).val(value);
				});
		';

		if(!$mobileDevice && $menu_settings['sticky_menu'] == 1)
			$output .= '
				var menu = $("#FINALmenu");
				var menu_position = menu.offset();
				var sticky = false;

				$(window).scroll(function () {
					if ($( window ).width() > 768) {
						var window_position = $(window).scrollTop();
						if (window_position >= menu_position.top) {
							if (!sticky) {
								menu.addClass("sticky_menu");
								  $("#FINALmenu.sticky_menu").animate({
									"top": "0px",
								  }, 300);
								sticky = true;
								// $(".shopping_cart").clone().appendTo(menu);
							}
						} else {
							if (sticky) {
								// $("#FINALmenu.sticky_menu .shopping_cart").remove();
								menu.removeClass("sticky_menu").removeAttr("style");
								sticky = false;
							}
						}
					}
				});';

		// custom and blocks specific JS
		$output .= html_entity_decode($menu_settings['custom_js'], ENT_QUOTES).filesHandler::$blocks_specific_js;
		$output .= '// ]]>
				});';

		if(filesHandler::$was_written_JS) {
			$fh = fopen($path, 'a') or die ("Can not open FINALmenu_blocks_specific.js");
		} else {
			filesHandler::$was_written_JS = TRUE;
			$fh = fopen($path, 'w') or die ("Can not open FINALmenu_blocks_specific.js");
		}

		fwrite($fh, $output);
		fclose($fh);
	}

	public static function appendJS($settings, $path)
	{
		$output = 'window.menuAnimateInVertical = "'.$settings['menuAnimateIn'].'";
			window.menuAnimateOutVertical = "'.$settings['menuAnimateOut'].'";
		';

		if(filesHandler::$was_written_JS) {
			$fh = fopen($path, 'a') or die ("Can not open FINALmenu_blocks_specific.js");
		} else {
			filesHandler::$was_written_JS = TRUE;
            if(file_exists($path)) {
                $fh = fopen($path, 'a') or die ("Can not open FINALmenu_blocks_specific.js");
            } else {
                $fh = fopen($path, 'w') or die ("Can not open FINALmenu_blocks_specific.js");   
            }
		}

		fwrite($fh, $output);
		fclose($fh);
	}
}
