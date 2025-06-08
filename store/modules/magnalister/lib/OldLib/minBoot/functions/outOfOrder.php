<?php
function outOfOrder() {
	require(DIR_MAGNALISTER_INCLUDES.'admin_view_top.php');
	echo '<img style="display: block; margin: 0 auto 1em auto;" src="'.DIR_MAGNALISTER_IMAGES.'out_of_order.png" alt="Out of Order" />';
	require(DIR_MAGNALISTER_INCLUDES.'admin_view_bottom.php');
	require(DIR_WS_INCLUDES.'application_bottom.php');
	exit();
}