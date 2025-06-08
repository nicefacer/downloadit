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

MLFilesystem::gi()->loadClass('Form_Helper_Model_Table_PrepareData_Abstract');
class ML_Hitmeister_Helper_Model_Table_Hitmeister_PrepareData extends ML_Form_Helper_Model_Table_PrepareData_Abstract {
    
	public $aErrors = array();
	public $bIsSinglePrepare;
    
    public $itemsPerPage;
    public $productChunks;
    public $totalPages;
    public $currentPage;
    public $currentChunk;

	public function getPrepareTableProductsIdField() {
        return 'products_id';    
    }
    
    protected function productMatchField(&$aField) {
        if (isset($_POST['matching_nextpage'])) {
			$this->currentPage = $_POST['matching_nextpage'];
		} else {
			$this->currentPage = 1;
		}
        
        foreach ($this->oSelectList->getList() as $product) {
            $aField['products'][] = $this->getProductInfoById($product->pID);
        }
        
        $this->itemsPerPage = MLModul::gi()->getConfig('itemsperpage');
		$this->productChunks = array_chunk($aField['products'], $this->itemsPerPage);
		$this->totalPages = count($this->productChunks);
		$this->currentChunk = $this->productChunks[$this->currentPage - 1];
    }

    protected function variationGroups_ValueField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        $aField['optional'] = array('active' => true);

        if (!isset($aField['value']) || $aField['value'] === '') {
            $this->aErrors[] = 'hitmeister_prepareform_category';
        }
    }
	
	protected function products_idField(&$aField) {
        $aField['value'] = $this->oProduct->get('id');
    }

    protected function priceField(&$aField) {
		$aField['value'] = $this->getFirstValue($aField);
		$aField['issingleview'] = $this->bIsSinglePrepare;
		if ($this->bIsSinglePrepare === true && isset($aField['value']) === false) {
            $aField['value'] = $this->oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
		} elseif ($this->bIsSinglePrepare === false) {
			$aField['value'] = 0;
		}
	}

    public function getProductInfoById($iProductId)
    {
        /* @var $oP ML_Shop_Model_Product_Abstract */
        $oP = MLProduct::factory()->set('id', $iProductId)->load();

        $oProduct = array(
            'Id'			=> $iProductId,
            'Model'			=> $oP->getSKU(),
            'Title'			=> $oP->getName(),
            'Description'	=> $oP->getDescription(),
            'Images'		=> $oP->getImages(),
            'Price'			=> $oP->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject(), true, true),
            'Manufacturer'	=> $oP->getManufacturer(),
            'EAN'			=> $oP->getModulField('general.ean', true),
//				'ShippingTime'	=> $p['ShippingTime'],
//				'Condition'		=> $p['ConditionType'],
//				'Comment'		=> $p['Comment'],
//				'Country'		=> $p['Location'],
        );

        if (empty($oProduct['EAN']) === false) {
            $aSearchResult = $this->searchOnHitmeister($oProduct['EAN'], 'EAN');
        }

        if ($aSearchResult === false) {
            $aSearchResult = $this->searchOnHitmeister($oProduct['EAN'], 'Title');
        }

        if ($aSearchResult !== false) {
            $oProduct['Results'] = $aSearchResult;
        }

        return $oProduct;
    }
    
    public function searchOnHitmeister($sSearch = '', $sSearchBy = 'EAN') {
		try {
			$aData = MagnaConnector::gi()->submitRequest(array(
				'ACTION' => 'GetItemsFromMarketplace',
				'DATA' => array(
					$sSearchBy => $sSearch
				)
			));
		} catch (MagnaException $e) {
			$aData = array(
				'DATA' => false
			);
		}
        
        if (!is_array($aData) || !isset($aData['DATA']) || empty($aData['DATA'])) {
			return false;
		}

		return $aData['DATA'];
    }
	
	protected function titleField(&$aField) {        
        $aField['value'] = $this->getFirstValue($aField);
        if (isset($this->oProduct)) {
            if (((isset($aField['value']) === false) || empty($aField['value'])) || $this->bIsSinglePrepare === false) {
                $aField['value'] = $this->oProduct->getName();
            }
        }
    }
    
	protected function subtitleField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        if (isset($this->oProduct)) {
            if (((isset($aField['value']) === false) || empty($aField['value'])) || $this->bIsSinglePrepare === false) {
                $aField['value'] = $this->oProduct->getShortDescription();
            }
        }
    }
	
	protected function descriptionField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        if (isset($this->oProduct)) {
            if (((isset($aField['value']) === false) || empty($aField['value'])) || $this->bIsSinglePrepare === false) {
                $aField['value'] = $this->oProduct->getDescription();
            }
        }
    }
    
    protected function imagesField(&$aField) {
		$aField['value'] = $this->getFirstValue($aField);
		$aField['values'] = array();
		$aIds = array();
		if (isset($this->oProduct)) {
            $aImages = $this->oProduct->getImages();
            
			foreach ($aImages as $sImagePath) {
				$sId = $this->substringAferLast('\\', $sImagePath);
				if (isset($sId) === false || strpos($sId, '/') !== false) {
					$sId = $this->substringAferLast('/', $sImagePath);
				}
				
				try {
					$aUrl = MLImage::gi()->resizeImage($sImagePath, 'products', 60, 60);
					$aField['values'][$sId] = array(
						'height' => '60',
						'width' => '60',
						'alt' => $sId,
						'url' => $aUrl['url'],
					);
					$aIds[] = $sId;
				} catch(Exception $ex) {
					// Happens if image doesn't exist.
				}
			}
		}
		
		if (isset($aField['value']) && $this->bIsSinglePrepare === true) {
			if (in_array('false', $aField['value']) === true) {
				array_shift($aField['value']);
			}
		} else {
			$aField['value'] = $aIds;
		}
    }
    
    protected function itemConditionField(&$aField) {
		$aField['value'] = $this->getFirstValue($aField);
	}
    
    protected function shippingTimeField(&$aField) {
		$aField['value'] = $this->getFirstValue($aField);
	}
    
    protected function itemCountryField(&$aField) {
		$aField['value'] = $this->getFirstValue($aField);
	}
    
    protected function commentField(&$aField) {
		$aField['value'] = $this->getFirstValue($aField);
	}
    
	private function substringAferLast($sNeedle, $sString) {
		if (!is_bool($this->strrevpos($sString, $sNeedle))) {
			return substr($sString, $this->strrevpos($sString, $sNeedle) + strlen($sNeedle));
		}
	}
	
	private function strrevpos($instr, $needle) {
		$rev_pos = strpos (strrev($instr), strrev($needle));
		if ($rev_pos === false) {
			return false;
		} else {
			return strlen($instr) - $rev_pos - strlen($needle);
		}
	}
}