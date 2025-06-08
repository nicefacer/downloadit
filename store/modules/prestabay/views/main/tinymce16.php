<?php

/**
 * File tinymce16.phtml
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

<script>
var ad = '/';
var iso = 'en';
</script>

<?php
    $widthDefined = isset($width)?$width:900;
    $heightDefined = isset($height)?$height:500;
?>

<script type="text/javascript">

    var prestaBayTinySettings = {
            mode : "exact",
            external_plugins: {},
            external_filemanager_path: '',
            editor_selector :"autoload_tinymce",
            editor_deselector : "noEditor",
            entity_encoding: "raw",
            convert_urls : false,
            forced_root_block : false,
            force_br_newlines : true,
            force_p_newlines : false,
            remove_linebreaks : false,
            remove_trailing_nbsp : false,
            verify_html : false,
            apply_source_formatting: false,
            cleanup_on_startup : false,
            cleanup : false,
            valid_elements: '*[*]',
            extended_valid_elements: '*[*]',
            valid_children : "+body[style]",
            width: "<?php echo $widthDefined; ?>",
            height: "<?php echo $heightDefined; ?>",
            setup : function(ed) {
                ed.on('keydown', function(ed, e) {
                    tinyMCE.triggerSave();
                }
            );
        }
    };

    function tinymce_remove(editorId) {
        editorId = "<?php echo $element; ?>";
        tinymce.remove('#'+ editorId);
    }

    function tinymce_add(editorId) {
        editorId = "<?php echo $element; ?>";
        tinySetup(prestaBayTinySettings);
    }

    function insertHTML(id, html) {
        tinymce.get(id).insertContent(html);
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

</script>