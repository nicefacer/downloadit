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
 * $Id:$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

require_once(DIR_MAGNALISTER_MODULES . 'magnacompatible/classes/MLProductListMagnaCompatibleAbstract.php');

class HitmeisterCheckinProductList extends MLProductListMagnaCompatibleAbstract{
	
	protected function getSelectionName() {
		return 'checkin';
	}
	public function __construct() {
		$this->aListConfig[] = array(
			'head' => array(
				'attributes' => 'class="lowestprice"',
				'content' => 'ML_MAGNACOMPAT_LABEL_CATEGORY',
			),
			'field' => array('magnacompatmpcategory'),
		);
		parent::__construct();
		$this
			->addDependency('MLProductListDependencyCheckinToSummaryAction')
			->addDependency('MLProductListDependencyTemplateSelectionAction')
			->addDependency('MLProductListDependencyLastPreparedFilter', array(
				'propertiestablename' => TABLE_MAGNA_HITMEISTER_PREPARE, 
				'propertiestablealias' => 'hp', 
				'preparedtimestampfield' => 'PreparedTS',
			))
		;
	}
	
	/**
	 * adding propertiestable for filter
	 */
	protected function buildQuery(){
		parent::buildQuery()->oQuery
			->join(
				array(
					TABLE_MAGNA_HITMEISTER_PREPARE,
						'hp',
						(
							(getDBConfigValue('general.keytype', '0') == 'artNr')
								? 'p.products_model = hp.products_model'
								: 'p.products_id = hp.products_id'
						).
						" AND hp.mpID = '".$this->aMagnaSession['mpID']."'"
				),
				ML_Database_Model_Query_Select::JOIN_TYPE_INNER
			)
//			->where("p.products_ean IS NOT NULL AND p.products_ean <> ''")
		;
		return $this;
	}
	
	protected function getPrepareData($aRow, $sFieldName = null) {
		if (!isset($this->aPrepareData[$aRow['products_id']])) {
			$this->aPrepareData[$aRow['products_id']] = MagnaDB::gi()->fetchRow("
				SELECT * 
				FROM ".TABLE_MAGNA_HITMEISTER_PREPARE." 
				WHERE 
					".(
						(getDBConfigValue('general.keytype', '0') == 'artNr')
							? 'products_model=\''.MagnaDB::gi()->escape($aRow['products_model']).'\''
							: 'products_id=\''.$aRow['products_id'].'\''
					)."
					AND mpID = '".$this->aMagnaSession['mpID']."'
			");
		}
		if($sFieldName === null){
			return $this->aPrepareData[$aRow['products_id']];
		}else{
			return isset($this->aPrepareData[$aRow['products_id']][$sFieldName]) ? $this->aPrepareData[$aRow['products_id']][$sFieldName] : null;
		}
	}
	
	protected function getMarketPlaceCategory($aRow) {
		$aData = $this->getPrepareData($aRow);
		if ($aData !== false) {
			return '<table class="nostyle"><tbody>
				<tr><td>
					<table class="nostyle"><tbody>
						<tr>
							<td class="label">'.'Kategorie'.':&nbsp;</td>
							<td>'.(empty($aData['MarketplaceCategories']) ? '&mdash;' : $aData['MarketplaceCategories']).(empty($aData['MarketplaceCategoriesName']) ? '' : ' '.$aData['MarketplaceCategoriesName']).'</td>
						<tr>
					</tbody></table>
				</td><tr>
			</tbody></table>';
		}
		return '&mdash;';
	}
}
