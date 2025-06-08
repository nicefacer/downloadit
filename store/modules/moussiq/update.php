<?php
/**
 * Moussiq PRO
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2014 silbersaiten
 * @version   2.2.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */

require(dirname(__FILE__).'/../../config/config.inc.php');
// require_once(_PS_ROOT_DIR_.'/init.php');

$sql = '
SELECT `id_moussiq_service`,
	   `name`,
       `cron_schedule`,
       `last_upd`, `export_engine`
FROM `'._DB_PREFIX_.'moussiq_service`

WHERE `status`        = 1
AND   `cron_schedule` IS NOT NULL';

if ($result = Db::getInstance()->ExecuteS($sql))
{
	require_once(dirname(__FILE__).'/moussiq.php');
	require_once(dirname(__FILE__).'/classes/CronParser.php');
	require_once(dirname(__FILE__).'/classes/Export.php');
	require_once(dirname(__FILE__).'/classes/ExportTools.php');
	require_once(dirname(__FILE__).'/classes/MoussiqService.php');

	$parser = new CronParser();
	foreach ($result as $service)
		if ($parser->calcLastRan($service['cron_schedule']))
			if ($parser->getLastRanUnix() > $service['last_upd'])
			{
				$exportEngine = Export::setExportEngine($service['export_engine'], (int)($service['id_moussiq_service']));
				if (is_object($exportEngine))
					if ($exportEngine->startImport((int)($service['id_moussiq_service'])))
						Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'moussiq_service` SET `last_upd` = "'.time().'" WHERE `id_moussiq_service` = '.(int)($service['id_moussiq_service']));
			}
}
?>
