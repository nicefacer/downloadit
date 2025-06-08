<?php

/**
 * File tinymce.phtml
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
?>

<?php if (CoreHelper::isPS16()) { ?>
    <script type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>

    <?php $tmcefile = "/js/tinymce.inc.js"; ?>
    <?php
        if (!file_exists(_PS_ROOT_DIR_ . $tmcefile)) {
            $tmcefile = '/js/admin/tinymce.inc.js';
        }
    ?>
    <script type="text/javascript" src="..<?php echo $tmcefile; ?>"></script>

    <?php RenderHelper::view("main/tinymce16.php", array('element' => $element)); ?>
    <?php return; ?>
<?php } ?>
<?php
    $widthDefined = isset($width)?$width:900;
    $heightDefined = isset($height)?$height:500;
    $isjQueryTinyMce = false;

    $skin = false;
    if (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/themes/advanced/skins/cirkuit/ui.css')) {
      $skin  = 'skin:"cirkuit",';
    }

    // In PrestaShop 1.4.2 path to tinymce was changed
    if (file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/tiny_mce.js')) {
?>
    <script type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>

<?php } else if (file_exists(_PS_ROOT_DIR_ . '/js/tinymce/jscripts/tiny_mce/jquery.tinymce.js')) {
    $isjQueryTinyMce = true;
?>
    <script type="text/javascript" src="../js/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
<?php } else if (file_exists(_PS_ROOT_DIR_ . '/js/tinymce/jscripts/tiny_mce/tiny_mce.js')) { ?>
    <script type="text/javascript" src="../js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<?php } else { ?>
    <script type="text/javascript">alert("TinyMCE not found. Please contact to support");</script>
<?php } ?>

<script type="text/javascript">
    function tinymce_remove(editorId) {
        editorId = "<?php echo $element; ?>";
        tinyMCE.execCommand('mceRemoveControl', false, editorId);
    }

    function tinymce_add(editorId) {
        editorId = "<?php echo $element; ?>";
        tinyMCE.execCommand('mceAddControl', false, editorId);
    }

    jQuery.fn.extend({
        insertAtCaret: function(myValue){
            return this.each(function(i) {
                if (document.selection) {
                    this.focus();
                    sel = document.selection.createRange();
                    sel.text = myValue;
                    this.focus();
                }
                else if (this.selectionStart || this.selectionStart == '0') {
                    var startPos = this.selectionStart;
                    var endPos = this.selectionEnd;
                    var scrollTop = this.scrollTop;
                    this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                    this.focus();
                    this.selectionStart = startPos + myValue.length;
                    this.selectionEnd = startPos + myValue.length;
                    this.scrollTop = scrollTop;
                } else {
                    this.value += myValue;
                    this.focus();
                }
            })
        }
    });

          jQuery(document).ready(function() {


<?php if ($isjQueryTinyMce) { ?>
          jQuery("#<?php echo $element; ?>").tinymce({
	      // Location of TinyMCE script
	      script_url : '<?php echo __PS_BASE_URI__; ?>js/tinymce/jscripts/tiny_mce/tiny_mce.js',
 <?php  } else {
 	  echo 'tinyMCE.init({';
} ?>
            mode : "exact",
            theme : "advanced",
            <?php echo $skin?$skin:''; ?>
            editor_selector : "rte",
            editor_deselector : "noEditor",
            plugins : "safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen,preview",
            // Theme options
            theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
            theme_advanced_buttons4 : "styleprops,|,cite,abbr,acronym,del,ins,attribs,pagebreak",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : false,
            width: "<?php echo $widthDefined; ?>",
            height: "<?php echo $heightDefined; ?>",
            font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
            entity_encoding: "raw",
            convert_urls : false,
            language : "en",
            forced_root_block : false,
            force_br_newlines : true,
            force_p_newlines : false,
            remove_linebreaks : false,
            remove_trailing_nbsp : false,
            verify_html : false,
            extended_valid_elements : "div*",
            apply_source_formatting: false,
            cleanup_on_startup : false,
            cleanup : false,
            valid_elements: '*[*]',
            extended_valid_elements: '*[*]',
            valid_children : "+body[style]"

        });
    });
</script>