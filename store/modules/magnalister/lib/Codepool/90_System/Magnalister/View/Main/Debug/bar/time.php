<?php 
class_exists('ML',false) or die(); 
global  $_updaterTime, $_executionTime;
$_executionTime = microtime(true) -  $_executionTime;
$memory = memory_usage();
?>
    Entire page served in <b><?php echo microtime2human($_executionTime);?></b><br/><hr/>
	Updater Time: <?php echo microtime2human($_updaterTime); ?>. <br/>
	API-Request Time: <?php echo microtime2human(MagnaConnector::gi()->getRequestTime())?>. <br/>
	Processing Time: <?php echo microtime2human($_executionTime - $_updaterTime - MagnaConnector::gi()->getRequestTime());?>. <br/><hr/>
	<?php echo (($memory !== false) ? 'Max. Memory used: <b>'.$memory.'</b>.<br/><hr/>' : ''); ?>
	DB-Stats: <br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Queries used: <b><?php echo MLDatabase::getDbInstance()->getQueryCount();?></b><br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Query time: <?php echo microtime2human(MLDatabase::getDbInstance()->getRealQueryTime())?>
<?php
