<?php
require_once MLFilesystem::getOldLibPath('php' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'TopTen.php');

class AmazonTopTen extends TopTen {

	/**
	 *
	 * @param string $sType  topMainCategory || topProductType || topBrowseNode 
	 * @return array (key=>value)
	 * @throws Exception 
	 */
	public function getTopTenCategories($sField, $aConfig=array()) {
//            debugBacktrace();
		$sParent = isset($aConfig[0])?$aConfig[0]:'';
//		$sParentParent = isset($aConfig[1])?$aConfig[1]:'';
		switch ($sField) {
			case 'topMainCategory':{
				$sWhere = "1 = 1";
				$sUnion = null;
				break;
			}
			case 'topProductType':{
				$sWhere = "topMainCategory = '".$sParent."'";
				$sUnion = null;
				break;
			}
			case 'topBrowseNode':{
				$sField = 'topBrowseNode1';
				$sWhere = "topMainCategory = '".$sParent."'";
				$sUnion = 'topBrowseNode2';
				break;
			}
			
		}
		if ($sUnion === null) {
			$sSql = "
				select ".$sField." 
				from magnalister_amazon_prepare
				where ".$sWhere."
				and  mpID = '".$this->iMarketPlaceId."'
				and ".$sField." <> '0'
				group by ".$sField." 
				order by count(*) desc
			";
		}else{
			// if performance problems in this query, get all data and prepare with php
			$sSql="
				select m.".$sField." from
				(
					(
						select f.".$sField."
						from magnalister_amazon_prepare f 
						where ".$sWhere." and mpID = '".$this->iMarketPlaceId."' and ".$sField." <> '0' 
					)
					UNION ALL
					(
						select u.".$sUnion."
						from magnalister_amazon_prepare u 
						where ".$sWhere." and mpID = '".$this->iMarketPlaceId."' and ".$sUnion." <> '0'
					)
				) m
				group by m.".$sField."
				order by count(m.".$sField.") desc
			";
		}
		$aTopTen = MLDatabase::getDbInstance()->fetchArray($sSql, true);
		$aOut = array();
		try {
			switch ($sField) {
				case 'topMainCategory':{
					$aCategories = MLModul::gi()->getMainCategories();
					break;
				}
				case 'topProductType':{
					$aCategories = MLModul::gi()->getProductTypesAndAttributes($sParent);
					$aCategories = $aCategories['ProductTypes'];
					break;
				}
				case 'topBrowseNode1':{
					$aCategories = MLModul::gi()->getBrowseNodes($sParent);
					break;
				}
			}
			foreach($aTopTen as $sCurrent){
				if(array_key_exists($sCurrent, $aCategories)) {
					$aOut[$sCurrent] = $aCategories[$sCurrent];
				}else{
					MLDatabase::getDbInstance()->query("UPDATE magnalister_amazon_prepare set ".$sField." = 0 where ".$sField." = '".$sCurrent."'");//no mpid
					if($sUnion !== null){
						MLDatabase::getDbInstance()->query("UPDATE magnalister_amazon_prepare set ".$sUnion." = 0 where ".$sUnion." = '".$sCurrent."'");//no mpid
					}
				}
			}
		} catch (MagnaException $e) {
			echo print_m($e->getErrorArray(), 'Error: '.$e->getMessage(), true);
		}
                asort($aOut);
		return $aOut;
	}

	public function renderConfigDelete($aDelete = array()) {
		global $_url;
		ob_start();
		if (count($aDelete)>0 ) {
			$this->configDelete($aDelete);
			?><p class="successBox"><?php echo ML_TOPTEN_DELETE_INFO ?></p><?php
		}
		?>
                        <form method="post" action="<?php echo MLHttp::gi()->getCurrentUrl(array('what' => 'topTenConfig', 'kind' => 'ajax','ajax'=>'true','tab'=>'delete'))?>">
                                <?php foreach(MLHttp::gi()->getNeededFormFields() as $sName=>$sValue){?>
                                    <input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue?>" />
                                <?php }?>
				<select name="<?php echo MLHttp::gi()->parseFormFieldName('delete[]'); ?>" style="width:100%" multiple="multiple" size="15">
					<?php foreach($this->getTopTenCategories('topMainCategory') as $sMainKey=>$sMainValue){ ?>
						<option title="<?php echo ML_AMAZON_CATEGORY ?>" value="main:<?php echo $sMainKey ?>"><?php echo $sMainValue ?></option>
						<?php foreach($this->getTopTenCategories('topProductType', array($sMainKey)) as $sTypeKey => $sTypeValue){ ?>
							<option title="<?php echo ML_AMAZON_PRODUCTGROUP ?>" value="type:<?php echo $sTypeKey ?>">&nbsp;&nbsp;<?php echo $sTypeValue ?></option>
							<?php foreach($this->getTopTenCategories('topBrowseNode', array($sTypeKey, $sMainKey)) as $sBrowseKey => $sBrowseValue){ ?>
								<option title="<?php echo ML_AMAZON_LABEL_APPLY_BROWSENODES ?>" value="browse:<?php echo $sBrowseKey ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $sBrowseValue ?></option>
							<?php } ?>
						<?php } ?>
					<?php } ?>
				</select>
				<button type="submit"><?php echo ML_TOPTEN_DELETE_HEAD ?></button>
			</form>
		<?php
		$sOut = ob_get_contents();
		ob_end_clean();
		return $sOut;
	}
    public function configCopy() {
        $oList = MLDatabase::factory('amazon_prepare')->getList();
        $oList->getQueryObject()->where("PrepareType = 'apply'");
        foreach ($oList->getList() as $oModel) {
            $aCategory = $oModel->get('ApplyData');
            $oModel->set('TopMainCategory', $oModel->get('MainCategory'))
                    ->set('TopProductType', $aCategory['ProductType']);
            if (isset($aCategory['BrowseNodes']) && is_array($aCategory['BrowseNodes'])) {
                $oModel->set('TopBrowseNode1', $aCategory['BrowseNodes'][0]);
                if (count($aCategory['BrowseNodes']) > 1) {
                    $oModel->set('TopBrowseNode2', $aCategory['BrowseNodes'][1]);
                }
            }
            $oModel->save();
        }
    }

    public function configDelete($aDelete) {
        $oModel = MLDatabase::factory('amazon_prepare');
        foreach ($aDelete as $sValue) {
            $aCurrent = explode(':', $sValue);
            switch ($aCurrent[0]) {
                case 'main': {
                        $oModel->resetTopTen('TopMainCategory', $aCurrent[1]);
                    }
                case 'type': {
                        $oModel->resetTopTen('TopProductType', $aCurrent[1]);
                    }
                case 'browse': {
                        $oModel->resetTopTen('TopBrowseNode1', $aCurrent[1]);
                        $oModel->resetTopTen('TopBrowseNode2', $aCurrent[1]);
                    }
            }
        }
    }

}
