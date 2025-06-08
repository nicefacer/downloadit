<?php
require_once MLFilesystem::getOldLibPath('php'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TopTen.php');
class EbayTopTen extends TopTen{
	/**
	 *
	 * @param string $sType  topPrimaryCategory || topSecondaryCategory || topStoreCategory || topStoreCategory2
	 * @return array (key=>value)
	 * @throws Exception 
	 */
	public function getTopTenCategories($sType,$aConfig=array()){
            $sType=strtolower($sType);
		$blStoreCat = substr($sType,0,16) == 'topstorecategory';
		if ($blStoreCat) {
			try {
				$aStoreData = MagnaConnector::gi()->submitRequestCached(array('ACTION' => 'HasStore'));
			} catch (MagnaException $e) {
				echo print_m($e->getErrorArray(), 'Error');
			}
			if(!$aStoreData['DATA']['Answer']=='True'){
				throw new Exception('noStore');
			}
		}
		$sTopTenCatSql = "
            SELECT DISTINCT ".$sType."
            FROM `magnalister_ebay_prepare` 
            WHERE ".$sType." <> 0 and mpID = '".$this->iMarketPlaceId."'
            GROUP BY ".$sType." 
            ORDER BY count( `".$sType."` ) DESC
            ".(
					(int)MLModul::gi()->getConfig('topten') != 0
					? "LIMIT ".(int)  MLModul::gi()->getConfig('topten')
					:""
			)
        ;
        $aTopTenCatSql = MLDatabase::getDbInstance()->fetchArray($sTopTenCatSql, true);
		$aTopTenCatIds = array();
		foreach ($aTopTenCatSql as $iCatId) {
			$aTopTenCatIds[$iCatId] = geteBayCategoryPath($iCatId,$blStoreCat);
			if (empty($aTopTenCatIds[$iCatId])) {
				unset($aTopTenCatIds[$iCatId]);
				MLDatabase::getDbInstance()->query("UPDATE `magnalister_ebay_prepare` set ".$sType."=0 where ".$sType."='".$iCatId."' AND mpID='".MLModul::gi()->getMarketPlaceId()."'");//better siteid instead mpid
			}
		}
        asort($aTopTenCatIds);
		return $aTopTenCatIds;
	}
	public function configCopy(){
		$sCopySql = "
			update `magnalister_ebay_prepare`
			set 
				topPrimaryCategory = primaryCategory,
				topSecondaryCategory = secondaryCategory,
				topStoreCategory = storeCategory,
				topStoreCategory2 = storeCategory2
			where 
				mpID = '".$this->iMarketPlaceId."'
		";
		MLDatabase::getDbInstance()->query($sCopySql);
	}
	public function configDelete($aDelete){
		foreach ($aDelete as $sKey => $aValue) {
			if(in_array($sKey, array('topPrimaryCategory', 'topSecondaryCategory', 'topStoreCategory', 'topStoreCategory2'))){
				$sIn = '(';
				foreach($aValue as $iValue){
					$sIn .= ((int)$iValue).', ';
				}
				$sIn = substr($sIn, 0, -2).')';
				$sQuery = "update `magnalister_ebay_prepare` set ".$sKey." = '' where ".$sKey." in ".$sIn;
				MLDatabase::getDbInstance()->query($sQuery);
			}
		}
	}


	public function renderConfigDelete($aDelete = array()) {
		ob_start();
		if(count($aDelete)>0){
			$this->configDelete($aDelete);
			?><p class="successBox"><?php echo ML_TOPTEN_DELETE_INFO ?></p><?php
		}
		$aCats = array();
		foreach(array(
			'topPrimaryCategory'	=> ML_EBAY_PRIMARY_CATEGORY, 
			'topSecondaryCategory'	=> ML_EBAY_SECONDARY_CATEGORY, 
			'topStoreCategory'		=> ML_EBAY_STORE_CATEGORY, 
			'topStoreCategory2'		=> ML_EBAY_SECONDARY_STORE_CATEGORY
		) as $sType => $sName){
			try{
				$aCats[$sName] = array(
					'type' => $sType,
					'data' => $this->getTopTenCategories($sType)
				);
			}catch(Exception $oEx){
				//do nothing
			}
		}
		?>
                        <form method="post" action="<?php echo MLHttp::gi()->getCurrentUrl(array('what' => 'topTenConfig', 'kind' => 'ajax'))?>&<?php echo MLHttp::gi()->parseFormFieldName('tab')?>=delete">
                <?php foreach(MLHttp::gi()->getNeededFormFields() as $sName=>$sValue){?>
                                    <input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue?>" />
                <?php }?>
				<p><?php echo ML_TOPTEN_DELETE_DESC ?></p>
				<dl>
					<?php
						foreach($aCats as $sName => $aTopTenCatIds){
							?>
								<dt><?php echo $sName ?></dt>
								<dd>
                                    <select name="<?php echo MLHttp::gi()->parseFormFieldName("delete[{$aTopTenCatIds['type']}][]"); ?>" style="width:100%" multiple="multiple" size="5">
										<?php
											foreach ($aTopTenCatIds['data'] as $sKey => $sValue) {
												?><option value="<?php echo $sKey ?>"><?php echo $sValue ?></option>;<?php
											}
										?>
									</select>
								</dd>
							<?php
						}
					?>
				</dl>
				<button type="submit"><?php echo ML_TOPTEN_DELETE_HEAD ?></button>
			</form>
		<?php
		$sOut = ob_get_contents();
		ob_end_clean();
		return $sOut;
	}
}
