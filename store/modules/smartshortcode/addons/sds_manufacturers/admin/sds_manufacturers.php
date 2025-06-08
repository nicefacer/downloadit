<script type="text/javascript">
$(document).ready(function() {
$('#select_shortcode a').click(function() {

                                var val = $(this).attr('href');
                                switch(val){
                                    case 'sds_manufacturers':
                                        var scode = '[sds_manufacturers';
                                        var pval = prompt("Enter slider speed (in miliseconds)",'600');
                                        var sval = prompt("Enter number of slides",'6');
                                        if(pval == null){
                                            alert('Slide speed is required.');                                            
                                            return false;
                                        }
                                        scode += ' speed="'+pval+'"';
                                        scode += ' maxslide="'+sval+'"][/sds_manufacturers]';
                                        
                                        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
                                        parent.tinyMCE.activeEditor.windowManager.close();
                                    break;
                                    default:
                                    return callShortcodeMethod(val);
                                }
            });

});
</script>