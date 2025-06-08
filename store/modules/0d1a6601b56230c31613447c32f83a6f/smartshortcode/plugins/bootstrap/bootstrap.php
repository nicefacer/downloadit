
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Smart Short Code</title>
	 <style type="text/css">
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
	
	 </style>
	 <?php
require_once(dirname(__FILE__).'/../../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../../init.php');

if (!defined('_PS_ADMIN_DIR_'))
	define('_PS_ADMIN_DIR_', getcwd());

$token = Tools::getToken();
$ajaxurl = Context::getContext()->link->getAdminLink('AdminStats',false)."&token=".$token;

?>
	<script type="text/javascript" src="<?php echo _PS_JS_DIR_ .  'jquery/jquery-1.11.0.min.js'?>"></script>
    <script type="text/javascript" src="<?php echo _PS_JS_DIR_ ; ?>tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript">
		jQuery(document).ready(function() {

			jQuery('#insert').attr("disabled", true);

			jQuery('#insert').addClass("disabled");

			jQuery('#select_shortcode').change(function() {

				if( jQuery(this).val() == '' ) {

					jQuery('#insert').attr("disabled", true);

					jQuery('#insert').addClass("disabled");

				} else {

					jQuery('#insert').removeAttr("disabled");

					jQuery('#insert').removeClass("disabled");

				}

			});

		});

		

		 function returnShortcodeValue() {

			var out;

			

			switch(jQuery('#select_shortcode').val())
			{
         
				case "one-col": 

					out = "[smart_row][smart_col class='col-xs-12 col-md-12 col-sm-12 col-lg-12']Your content here[/smart_col][/smart_row]";

					break;
                                        
				case "two-col": 

					out = "[smart_row][smart_col class='col-xs-6 col-md-6 col-sm-6 col-lg-6']Your content here[/smart_col][smart_col class='col-xs-6 col-md-6 col-sm-6 col-lg-6']Your content here[/smart_col][/smart_row]";

					break;

				case "three-col": 

					out = "[smart_row][smart_col class='col-xs-4 col-md-4 col-sm-4 col-lg-4']Your content here[/smart_col][smart_col class='col-xs-4 col-md-4 col-sm-4 col-lg-4']Your content here[/smart_col][smart_col class='col-xs-4 col-md-4 col-sm-4 col-lg-4']Your content here[/smart_col][/smart_row]";

					break;
                                        
				case "four-col": 

					out = "[smart_row][smart_col class='col-xs-3 col-md-3 col-sm-3 col-lg-3']Your content here[/smart_col][smart_col class='col-xs-3 col-md-3 col-sm-3 col-lg-3']Your content here[/smart_col][smart_col class='col-xs-3 col-md-3 col-sm-3 col-lg-3']Your content here[/smart_col][smart_col class='col-xs-3 col-md-3 col-sm-3 col-lg-3']Your content here[/smart_col][/smart_row]";

					break;

				case "six-col": 

					out = "[smart_row][smart_col class='col-xs-2 col-md-2 col-sm-2 col-lg-2']Your content here[/smart_col][smart_col class='col-xs-2 col-md-2 col-sm-2 col-lg-2']Your content here[/smart_col][smart_col class='col-xs-2 col-md-2 col-sm-2 col-lg-2']Your content here[/smart_col][smart_col class='col-xs-2 col-md-2 col-sm-2 col-lg-2']Your content here[/smart_col][smart_col class='col-xs-2 col-md-2 col-sm-2 col-lg-2']Your content here[/smart_col][smart_col class='col-xs-2 col-md-2 col-sm-2 col-lg-2']Your content here[/smart_col][/smart_row]";

					break;
                                        
				case "twelve-col": 

					out = "[smart_row][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][smart_col class='col-xs-1 col-md-1 col-sm-1 col-lg-1']Your content here[/smart_col][/smart_row]";

					break;
				default: 
					out = '';
			}
parent.tinyMCE.execCommand('mceInsertContent', false,out);
parent.tinyMCE.activeEditor.windowManager.close();
		}
    </script>
</head>
<body>
	<fieldset>
    <legend>Select a Shortcode</legend>
	 <div id="main" class="mce-container-body">
        <select id="select_shortcode">
			<option value="">Select</option>
                <optgroup label="Columns">
					<option value="one-col">One Column</option>
					<option value="two-col">Two Column</option>
					<option value="three-col">Three Column</option>
					<option value="four-col">Four Column</option>
					<option value="six-col">Six Column</option>
					<option value="twelve-col">Twelve Column</option>
				</optgroup>
        </select>
		</div>
		</fieldset>
       <button class="btn submit" type="submit"  onclick="returnShortcodeValue()" id="insert" />Add</button>
       <button type="button" class="btn"  onclick="parent.tinyMCE.activeEditor.windowManager.close()" id="cancel" />Close</button>
</div>
</body>
</html>