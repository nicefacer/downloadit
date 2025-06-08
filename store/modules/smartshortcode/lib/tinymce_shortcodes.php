<?php
$ajaxurl = $admin_url;
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Smart Short Code</title>
 <style type="text/css">
body{
    padding:15px;
}
.mce-container-body{
    position: relative;
}
.shortcodes_window{
    width:100%;
    position: absolute;
}
.btn {
    -moz-user-select: none;
    background-image: none;
    border-radius: 3px;
    cursor: pointer;
    display: inline-block;
    font-size: 12px;
    font-weight: normal;
    line-height: 1.42857;
    margin-bottom: 0;
    padding: 4px 8px;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}
#select_shortcode{
    display: block;
    position: relative;
    left: 0;    
}
#select_shortcode a{
    width: 24%;
    display: inline-block;
    color:#444;
    margin-bottom: 5px;
    border:1px solid #999;
    padding: 10px 15px;
    transition: background ease-in 0.2s;
}
#select_shortcode a:hover{
    text-decoration: none;
    background: #78A300;
    color:#fff;
}
.tabfields{
    position: relative;
}
.sds_filemanager{
    position: absolute;
    right: 15px;
    top: 36px;
    cursor: pointer;
}
#select_shortcode a i:before{
    font-size: 18px;
    vertical-align: bottom;
}
input[type=radio], input[type=checkbox] {
    margin: 4px 5px 0 !important;
    margin-top: 1px \9;
    line-height: normal;
}

	 </style>
	<script type="text/javascript" src="<?php echo _PS_JS_DIR_ .  'jquery/jquery-1.11.0.min.js'?>"></script>
    <script type="text/javascript" src="<?php echo _PS_JS_DIR_ ; ?>tiny_mce/tiny_mce.js"></script>
    <link rel="stylesheet" href="<?php echo __PS_BASE_URI__.'modules/smartshortcode/css/bootstrap/css/bootstrap.min.css';?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo __PS_BASE_URI__.'modules/smartshortcode/plugins/fontawesome/css/font-awesome.min.css';?>" type="text/css" />
    <script type="text/javascript">
		$(document).ready(function() {
                        $('.shortcodes_window').css({left:-($('.shortcodes_window').width())+'px'});
                        $(document.body).on('click','.shortcodes_window a.backlink',function(){
                            //unbind event
                            $(document.body).off('click','.sds_filemanager');
                            $(document.body).off('click','#add_new');
                            $(document.body).off('click','span.awesome');
                            
                            
                            $('.shortcodes_window').animate({left:-($('.shortcodes_window').width())+'px',width:$('.shortcodes_window').width()+'px'},300,function(){
                                $(this).css({position:'absolute'});
                                $(this).html('');                            
                            });
                            $('#select_shortcode').animate({left:0,width:$('#select_shortcode').width()+'px'},300,function(){
                                $(this).css({position:'relative'});
                            });

                            
                            return false;
                        });

                        var callShortcodeMethod = function(val){
                            
                            $.ajax({                                
                                url: '<?php echo $ajaxurl?>',
                                type : 'POST',
                                data : {
                                    smartShortcodeAction : val,
                                },
                                dataType : 'html',
                                success : function(resp){
                                    $('#select_shortcode').css({position:'absolute'}).animate({left:-($('#select_shortcode').width()),width:$('#select_shortcode').width()+'px'},300,function(){
                                    });
                                    $('.shortcodes_window').html(resp).animate({left:'0px'},300,function(){
                                        $(this).css({position:'relative'});
                                    });
                                }
                            });
                            return false;
                        };

			
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
                                        scode += ' maxslide="'+sval+'"]';
                                        
                                        parent.tinyMCE.execCommand('mceInsertContent', false,scode);
                                        parent.tinyMCE.activeEditor.windowManager.close();
                                    break;
                                    default:
                                    return callShortcodeMethod(val);
                                }
			});

		});

		

		 
    </script>
</head>
<body>
<div id="main" class="mce-container-body">
<div id="select_shortcode">
<?php Hook::exec('sdsShortcodeAdminLists');?>
</div>
<div class="shortcodes_window"></div>
</div>
</body>
</html>