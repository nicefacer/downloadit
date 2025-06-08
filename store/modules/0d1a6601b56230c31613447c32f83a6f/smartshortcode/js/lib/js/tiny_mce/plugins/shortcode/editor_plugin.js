/* Adapted from http://brettterpstra.com/adding-a-tinymce-button/ */

(function() {
    tinyMCE.create('tinymce.plugins.Wptuts', {
        init : function(ed, url) {
            
            ed.addButton('shortcode', {
                title : 'Short Code',
              
                image : url + '/dropcap.jpg',
                
                onclick : function() {

					ed.windowManager.open({

						file : url + '/tinymce_shortcodes.php',

						width : 330,

						height : 120,

						inline : 1

					});
	}
            });
 

        },
        // ... Hidden code
    });
    // Register plugin
    tinyMCE.PluginManager.add( 'wptuts', tinyMCE.plugins.Wptuts );
})();