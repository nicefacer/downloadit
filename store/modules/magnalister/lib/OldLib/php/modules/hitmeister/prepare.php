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
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class HitmeisterPrepare extends MagnaCompatibleBase {

	protected $prepareSettings = array();

	public function __construct(&$params) {
		parent::__construct($params);

		$this->prepareSettings['selectionName'] = isset($_GET['view']) ? $_GET['view'] : 'apply';
		$this->resources['url']['mode'] = 'prepare';
		$this->resources['url']['view'] = $this->prepareSettings['selectionName'];
	}

	protected function saveMatching() {
		if (!array_key_exists('saveMatching', $_POST)) {
			return;
		}
		
		require_once(DIR_MAGNALISTER_MODULES . 'hitmeister/classes/HitmeisterProductSaver.php');
		
		$itemDetails = $_POST;
		$oProductSaver = new HitmeisterProductSaver($this->resources['session']);
		
		$aProductIDs = MagnaDB::gi()->fetchArray('
			SELECT pID FROM ' . TABLE_MAGNA_SELECTION . '
			 WHERE mpID="' . $this->mpID . '" AND
				   selectionname="' . $this->prepareSettings['selectionName'] . '" AND
				   session_id="' . session_id() . '"
		', true);

		if (1 == count($aProductIDs)) {
			$oProductSaver->saveSingleProductProperties($aProductIDs[0], $itemDetails, $this->prepareSettings['selectionName']);
		} else if (!empty($aProductIDs)) {
			$oProductSaver->saveMultipleProductProperties($aProductIDs, $itemDetails, $this->prepareSettings['selectionName']);
		}
		
		if (count($oProductSaver->aErrors) === 0) {
			$matchingNotFinished = isset($_POST['matching_nextpage']) && ctype_digit($_POST['matching_nextpage']);
			if ($matchingNotFinished === false) {
				MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
					'mpID' => $this->mpID,
					'selectionname' => $this->prepareSettings['selectionName'],
					'session_id' => session_id()
				));
			}
		} else {
			# stay on prepare product form
			$_POST['prepare'] = 'prepare';

			foreach ($oProductSaver->aErrors as $sError) {
				echo '<div class="errorBox">' . $sError . '</div>';
			}
		}	
	}

	protected function deleteMatching() {
		if (!(array_key_exists('unprepare', $_POST)) || empty($_POST['unprepare'])) {
			return;
		}
	 	$pIDs = MagnaDB::gi()->fetchArray('
			SELECT pID FROM '.TABLE_MAGNA_SELECTION.'
			 WHERE mpID=\''.$this->mpID.'\' AND
			       selectionname=\''.$this->prepareSettings['selectionName'].'\' AND
			       session_id=\''.session_id().'\'
		', true);

		if (empty($pIDs)) {
			return;
		}
		foreach ($pIDs as $pID) {
			$where = (getDBConfigValue('general.keytype', '0') == 'artNr')
				? array ('products_model' => MagnaDB::gi()->fetchOne('
							SELECT products_model
							  FROM '.TABLE_PRODUCTS.'
							 WHERE products_id='.$pID
						))
				: array ('products_id' => $pID);
			$where['mpID'] = $this->mpID;

			MagnaDB::gi()->delete(TABLE_MAGNA_HITMEISTER_PREPARE, $where);
			MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
				'pID' => $pID,
				'mpID' => $this->mpID,
				'selectionname' => $this->prepareSettings['selectionName'],
				'session_id' => session_id()
			));
		}
		unset($_POST['unprepare']);
	}

	protected function processMatching() {
		if ($this->prepareSettings['selectionName'] === 'match') {
			$className = 'MatchingPrepareView';
		} else {
			$className = 'ApplyPrepareView';
		}

		if (($class = $this->loadResource('prepare', $className)) === false) {
			if ($this->isAjax) {
				echo '{"error": "This is not supported"}';
			} else {
				echo 'This is not supported';
			}
			return;
		}

		$params = array();
		foreach (array('mpID', 'marketplace', 'marketplaceName', 'resources', 'prepareSettings') as $attr) {
			if (isset($this->$attr)) {
				$params[$attr] = &$this->$attr;
			}
		}

		$cMDiag = new $class($params);

		if ($this->isAjax) {
			echo $cMDiag->renderAjax();
		} else {
			$categories = MagnaDB::gi()->fetchArray('
				SELECT DISTINCT p2c.categories_id
				  FROM '.TABLE_MAGNA_SELECTION.' ms, '.TABLE_PRODUCTS_TO_CATEGORIES.' p2c
				 WHERE ms.mpID=\''.$this->mpID.'\' AND
				       ms.selectionname=\''.$this->prepareSettings['selectionName'].'\' AND
				       ms.session_id=\''.session_id().'\' AND
				       ms.pID=p2c.products_id
			', true);
			//echo print_m($categories, '$categories');
			$html = $cMDiag->process();
			echo $html;
		}
	}

	protected function processSelection() {
		if (($class = $this->loadResource('prepare', 'PrepareCategoryView')) === false) {
			if ($this->isAjax) {
				echo '{"error": "This is not supported"}';
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

	protected function processProductList() {
		if ($this->prepareSettings['selectionName'] === 'match') {
			$className = 'MatchingProductList';
		} else {
			$className = 'ApplyProductList';
		}

		if (($sClass = $this->loadResource('prepare', $className)) === false) {
			if ($this->isAjax) {
				echo '{"error": "This is not supported"}';
			} else {
				echo 'This is not supported';
			}
			return;
		}

		$o = new $sClass();
		echo $o;
	}

	public function process() {
		if (isset($_POST['request']) && $_POST['request'] === 'ItemSearchByTitle') {
			$product = array(
				'Id'		=> $_POST['productID'],
				'Results'	=> HitmeisterHelper::SearchOnHitmeister($_POST['search'], 'Title')
			);

			$sClass = $this->loadResource('prepare', 'MatchingPrepareView');

			$params = array();
			foreach (array('mpID', 'marketplace', 'marketplaceName', 'resources', 'prepareSettings') as $attr) {
				if (isset($this->$attr)) {
					$params[$attr] = &$this->$attr;
				}
			}

			$v = new $sClass($params);
			echo $v->getSearchResultsHtml($product);
			return;
		}

		if (isset($_POST['request']) && $_POST['request'] === 'ItemSearchByEAN') {
			$product = array(
				'Id'		=> $_POST['productID'],
				'Results'	=> HitmeisterHelper::SearchOnHitmeister($_POST['ean'], 'EAN')
			);

			$sClass = $this->loadResource('prepare', 'MatchingPrepareView');

			$params = array();
			foreach (array('mpID', 'marketplace', 'marketplaceName', 'resources', 'prepareSettings') as $attr) {
				if (isset($this->$attr)) {
					$params[$attr] = &$this->$attr;
				}
			}

			$v = new $sClass($params);
			echo $v->getSearchResultsHtml($product);
			return;
		}

		if (isset($_GET['automatching']) && $_GET['automatching'] === 'getProgress') {
			global $_MagnaSession;

			echo json_encode(array('x' => (int) MagnaDB::gi()->fetchOne('
				SELECT count(pID) FROM ' . TABLE_MAGNA_SELECTION . '
				 WHERE mpID=\'' . $_MagnaSession['mpID'] . '\'
				   AND selectionname=\'' . $this->prepareSettings['selectionName'] . '\'
				   AND session_id=\'' . session_id() . '\'
			  GROUP BY mpID
			')));
			return;
		} else if (isset($_GET['automatching']) && $_GET['automatching'] === 'start') {
			$autoMatchingStats = $this->insertAutoMatchProduct();
			$re = trim(sprintf(
				ML_HITMEISTER_TEXT_AUTOMATIC_MATCHING_SUMMARY,
				$autoMatchingStats['success'],
				$autoMatchingStats['nosuccess'],
				$autoMatchingStats['almost']
			));
			echo magnalisterIsUTF8($re) ? $re : utf8_encode($re);
		}

		$this->saveMatching();
		$this->deleteMatching();

		$hasNextPage = isset($_POST['matching_nextpage']) && ctype_digit($_POST['matching_nextpage']);

		if (
				(
					isset($_POST['prepare']) || 
					(isset($_GET['where']) && (($_GET['where'] == 'catMatchView') || ($_GET['where'] == 'catAttributes'))) || 
					$hasNextPage
				) &&
				($this->getSelectedProductsCount() > 0)
		) {
			$this->processMatching();
		} else {
			if (defined('MAGNA_DEV_PRODUCTLIST') && MAGNA_DEV_PRODUCTLIST === true ) {
				$this->processProductList();
			} else {
				$this->processSelection();
			}
		}
	}
	
	protected function getSelectedProductsCount() {
		$query = '
			SELECT COUNT(*)
			FROM ' . TABLE_MAGNA_SELECTION . ' s
			LEFT JOIN ' . TABLE_MAGNA_HITMEISTER_PREPARE . ' p on p.mpID = s.mpID and p.products_id = s.pID
			WHERE s.mpID = ' . $this->mpID . '
			    AND s.selectionname = "' . $this->prepareSettings['selectionName'] . '"
			    AND s.session_id = "' . session_id() . '"
		';

		if (isset($_POST['match']) && $_POST['match'] === 'notmatched') {
			$query .= ' AND coalesce(p.Verified, "") != "OK"';
		}

		return (int) MagnaDB::gi()->fetchOne($query);
	}

	private function insertAutoMatchProduct() {
		$autoMatchingStats = array (
			'success' => 0,
			'almost' => 0,
			'nosuccess' => 0,
			'_timer' => microtime(true)
		);

		$sClass = $this->loadResource('prepare', 'MatchingPrepareView');

		$params = array();
		foreach (array('mpID', 'marketplace', 'marketplaceName', 'resources', 'prepareSettings') as $attr) {
			if (isset($this->$attr)) {
				$params[$attr] = &$this->$attr;
			}
		}

		$v = new $sClass($params);

		$products = $v->getSelection(true);

		foreach ($products as $product) {
			$searchResults = HitmeisterHelper::SearchOnHitmeister($product['EAN'], 'EAN');

			if (count($searchResults) === 0) {
				$searchResults = HitmeisterHelper::SearchOnHitmeister($product['Title'], 'Title');
			}

			$matchedProductId = null;

			foreach ($searchResults as $searchResult) {
				if ($searchResult['ean_match'] === true) {
					$matchedProductId = $searchResult['id_item'];
					break;
				}
			}

			if (count($searchResults) > 1) {
				$autoMatchingStats['almost']++;
			}

			if ($matchedProductId === null) {
				$autoMatchingStats['nosuccess']++;
				MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
					'pID' => $product['Id'],
					'mpID' => $this->mpID,
					'selectionname' => 'Match',
					'session_id' => session_id()
				));
				continue;
			}

			$matchedProduct = array(
				'mpID'				=> $this->mpID,
				'products_id'		=> $product['Id'],
				'products_model'	=> $product['Model'],
				'Title'				=> $searchResult[$matchedProductId]['title'],
				'EAN'				=> reset($searchResult[$matchedProductId]['eans']),
				'ConditionType'		=> $product['Condition'],
				'ShippingTime'		=> $product['ShippingTime'],
				'Location'			=> $product['Country'],
				'Comment'			=> $product['Comment'],
				'PrepareType'		=> 'Match',
				'PreparedTS'		=> date('Y-m-d H:i:s'),
				'Verified'			=> 'OK'
			);

			MagnaDB::gi()->insert(TABLE_MAGNA_HITMEISTER_PREPARE, $matchedProduct, true);

			MagnaDB::gi()->delete(TABLE_MAGNA_SELECTION, array(
				'pID' => $product['Id'],
				'mpID' => $this->mpID,
				'selectionname' => 'Match',
				'session_id' => session_id()
			));

			$autoMatchingStats['success']++;
		}

		return $autoMatchingStats;
	}
	
}
