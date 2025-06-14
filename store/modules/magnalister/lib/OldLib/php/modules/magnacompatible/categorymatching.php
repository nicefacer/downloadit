<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id: categorymatching.php 1206 2011-08-23 16:54:32Z MaW $
 *
 * (c) 2011 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class MagnaCompatCatMatch extends MagnaCompatibleBase {	
	protected $prepareSettings = array();

	public function __construct(&$params) {
		parent::__construct($params);

		$this->prepareSettings['selectionName'] = 'catmatch';
		$this->resources['url']['mode'] = $this->prepareSettings['selectionName'];
	}
	
	protected function saveMatching() {
		if (!array_key_exists('saveMatching', $_POST)) {
			return;
		}
		MLDatabase::getDbInstance()->query(eecho('
			REPLACE INTO '.TABLE_MAGNA_COMPAT_CATEGORYMATCHING.'
				SELECT DISTINCT ms.mpID, p.products_id, p.products_model, 
				       \''.MLDatabase::getDbInstance()->escape($_POST['mpCategory']).'\' AS mp_category_id,
				       \''.MLDatabase::getDbInstance()->escape($_POST['storeCategory']).'\' AS store_category_id
				  FROM '.TABLE_MAGNA_SELECTION.' ms, '.TABLE_PRODUCTS.' p
				 WHERE ms.mpID=\''.$this->mpID.'\' AND
				       ms.selectionname=\''.$this->prepareSettings['selectionName'].'\' AND
				       ms.session_id=\''.MLShop::gi()->getSessionId().'\' AND
				       ms.pID=p.products_id
		', false));
		MLDatabase::getDbInstance()->delete(TABLE_MAGNA_SELECTION, array(
			'mpID' => $this->mpID,
			'selectionname' => $this->prepareSettings['selectionName'],
			'session_id' => MLShop::gi()->getSessionId()
		));
	}
	
	protected function deleteMatching() {
		if (!(array_key_exists('unprepare', $_POST)) || empty($_POST['unprepare'])) {
			return;
		}
	 	$pIDs = MLDatabase::getDbInstance()->fetchArray('
			SELECT pID FROM '.TABLE_MAGNA_SELECTION.'
			 WHERE mpID=\''.$this->mpID.'\' AND
			       selectionname=\''.$this->prepareSettings['selectionName'].'\' AND
			       session_id=\''.MLShop::gi()->getSessionId().'\'
		', true);

		if (empty($pIDs)) {
			return;
		}
		foreach ($pIDs as $pID) {
			$where = (getDBConfigValue('general.keytype', '0') == 'artNr')
				? array ('products_model' => MLDatabase::getDbInstance()->fetchOne('
							SELECT products_model
							  FROM '.TABLE_PRODUCTS.'
							 WHERE products_id='.$pID
						))
				: array ('products_id' => $pID);
			$where['mpID'] = $this->mpID;

			MLDatabase::getDbInstance()->delete(TABLE_MAGNA_COMPAT_CATEGORYMATCHING, $where);
			MLDatabase::getDbInstance()->delete(TABLE_MAGNA_SELECTION, array(
				'pID' => $pID,
				'mpID' => $this->mpID,
			    'selectionname' => $this->prepareSettings['selectionName'],
			    'session_id' => MLShop::gi()->getSessionId()
			));
		}
		unset($_POST['unprepare']);
	}
	
	protected function processMatching() {
		if (($class = $this->loadResource('catmatch', 'CategoryMatching')) === false) {
			if ($this->isAjax) {
				echo '{"error": "This is not supported"}';
			} else {
				echo 'This is not supported';
			}
			return;
		}
	
		$params = array();
		foreach (array('mpID', 'marketplace', 'marketplaceName') as $attr) {
			if (isset($this->$attr)) {
				$params[$attr] = &$this->$attr;
			}
		}

		$cMDiag = new $class($params);

		if ($this->isAjax) {
			echo $cMDiag->renderAjax();
		} else {
			$categories = MLDatabase::getDbInstance()->fetchArray('
				SELECT DISTINCT p2c.categories_id
				  FROM '.TABLE_MAGNA_SELECTION.' ms, '.TABLE_PRODUCTS_TO_CATEGORIES.' p2c
				 WHERE ms.mpID=\''.$this->mpID.'\' AND
				       ms.selectionname=\''.$this->prepareSettings['selectionName'].'\' AND
				       ms.session_id=\''.MLShop::gi()->getSessionId().'\' AND
				       ms.pID=p2c.products_id
			', true);
			//echo print_m($categories, '$categories');
			$html = $cMDiag->renderView() . '
				<table class="datagrid autoOddEven hover">
					<thead>
						<tr><td>'.ML_LABEL_SELECTED_CATEGORIES.'</td></tr>
					</thead>
					<tbody>';
			foreach ($categories as $cID) {
				$html .= '
						<tr><td>
							<ul><li>'.str_replace('<br />', '</li><li>', renderCategoryPath($cID)).'</li></ul>
						</td></tr>';
			}
			$html .= '
					</tbody>
				</table>';
			echo $html;
		}
	}
	
	protected function processSelection() {
		if (($class = $this->loadResource('catmatch', 'PrepareCategoryView')) === false) {
			if ($this->isAjax) {
				echo '{error: \'This is not supported\'}';
			} else {
				echo 'This is not supported';
			}
			return;
		}
		$pV = new $class(null, $this->prepareSettings);
		if ($this->isAjax) {
			echo $pV->renderAjaxReply();
		} else {
			echo $pV->printForm();
		}
	}

	public function process() {
		$this->saveMatching();
		$this->deleteMatching();
		if (isset($_POST['prepare']) || (isset($_GET['where']) && ($_GET['where'] == 'catMatchView'))) {
			$this->processMatching();
		} else {
			$this->processSelection();
		}
	}
	
}
