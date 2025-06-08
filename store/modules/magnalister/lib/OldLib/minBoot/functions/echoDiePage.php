<?php
function echoDiePage($title, $content, $style = '', $showbacklink = true) {
	echo '<!doctype html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>magnalister :: '.$title.'</title>
    <style>
		body { max-width: 600px; padding: 20px; font: 12px sans-serif; line-height: 16px; color: #333334;}
		h1{ font-size: 130%; letter-spacing: -0.5px; }
		a { color: #E31A1C; text-decoration: none; }
		a:hover { text-decoration: underline; }
    	'.$style.'
    </style>
  </head>
  <body>
    <h1>'.$title.'</h1>
    <p>'.$content.'</p>
	'.(($showbacklink && isset($_SERVER['HTTP_REFERER']))
		? (($_SESSION['language'] == 'german') 
			? '<a href="'.$_SERVER['HTTP_REFERER'].'" title="Zur&uuml;ck">Zur&uuml;ck</a>'
			: '<a href="'.$_SERVER['HTTP_REFERER'].'" title="Back">Back</a>'
    	)
    	: ''
    ).'
  </body>
</html>';
	include_once(DIR_WS_INCLUDES . 'application_bottom.php');
	exit();	
}