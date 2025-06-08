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
 * (c) 2011 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class MagnaCompatibleErrorView extends ML_Core_Controller_Abstract{
	private $errorLog = array();
	
	private $settings = array();
	private $sort = array();
	private $currentPage = 1;
	private $pages = 1;
	protected $mpID = 0;
	protected $marketplace = '';
	
	protected $request = array();	
        protected $numberofitems = 0 ;
        protected $offset = 0 ;
        protected $aParameters = array('controller');
        
	public function __construct($settings = array()) {
		parent::__construct();
                global $_MagnaSession;
		
		$this->settings = array_merge(array(
			'maxTitleChars' => 90,
			'itemLimit'     => 50,
			'hasImport' => false,
			'hasOrigin' => false,
		), $settings);
		$this->mpID = $_MagnaSession['mpID'];
		$this->marketplace = $_MagnaSession['currentPlatform'];

		$this->request = MLRequest::gi()->data();

		/* Delete selected Error Messages*/
		if (isset($this->request['action'])) {
			if ($this->request['action'] == 'deleteall') {
				MLDatabase::getDbInstance()->delete(TABLE_MAGNA_COMPAT_ERRORLOG, array(
					'mpID' => $this->mpID
				));
			} else if (($this->request['action'] == 'delete') && isset($this->request['errIDs'])) {
				foreach ($this->request['errIDs'] as $errID) {
					if (ctype_digit($errID)) {
						MLDatabase::getDbInstance()->delete(TABLE_MAGNA_COMPAT_ERRORLOG, array(
							'id' => (int)$errID
						));
					}
				}
			}
		}
		
		$this->importErrorLog();
		
		if (isset($this->request['sorting'])) {
			$sorting = $this->request['sorting'];
		} else {
			$sorting = 'blabla'; // fallback for default
		}

		switch ($sorting) {
			case 'errormessage':
				$this->sort['order'] = 'errormessage';
				$this->sort['type']  = 'ASC';
				break;
			case 'errormessage-desc':
				$this->sort['order'] = 'errormessage';
				$this->sort['type']  = 'DESC';
				break;
			case 'dateadded':
				$this->sort['order'] = 'dateadded';
				$this->sort['type']  = 'ASC';
				break;
			case 'dateadded-desc':
			default:
				$this->sort['order'] = 'dateadded';
				$this->sort['type']  = 'DESC';
				break;
		}

		$this->numberofitems = (int)MLDatabase::getDbInstance()->fetchOne('
			SELECT DISTINCT count(id) FROM '.TABLE_MAGNA_COMPAT_ERRORLOG.' WHERE mpID='.$this->mpID.'
		');
		$this->pages = ceil($this->numberofitems / $this->settings['itemLimit']);
		$this->currentPage = 1;

		if (isset($this->request['page']) && ctype_digit($this->request['page']) && (1 <= (int)$this->request['page']) && ((int)$this->request['page'] <= $this->pages)) {
			$this->currentPage = (int)$this->request['page'];
		}

		$this->offset = ($this->currentPage - 1) * $this->settings['itemLimit'];

                $max = MLDatabase::getDbInstance()->fetchOne("SELECT max(id) FROM " . TABLE_MAGNA_AMAZON_ERRORLOG);
                $max = (!is_numeric($max) ||$max < 0) ? 0 : $max ;
		$this->errorLog = MLDatabase::getDbInstance()->fetchArray(
                "SELECT * FROM (
                    SELECT al.id, al.origin, al.dateadded, al.errormessage, al.additionaldata
                    FROM ".TABLE_MAGNA_COMPAT_ERRORLOG." al
                    WHERE al.mpID='" . MLRequest::gi()->get('mp') . "'

                    UNION all
                    SELECT (ml.id + $max) as id, '--' as origin, ml.dateadded, ml.errormessage, ml.data as additionaldata              
                    FROM magnalister_errorlog ml
                    WHERE ml.mpID='" . MLRequest::gi()->get('mp') . "'
                ) AS T	    
		  GROUP BY T.id
		  ORDER BY `".$this->sort['order']."` ".$this->sort['type']." 
		     LIMIT ".$this->offset.','.$this->settings['itemLimit']."
		");
		if (!empty($this->errorLog)) {
			foreach ($this->errorLog as &$item) {
				$item['errormessage'] = fixHTMLUTF8Entities($item['errormessage']);
				$item['additionaldata'] = @unserialize($item['additionaldata']);
			}
		}
	}
	
        protected function getTotalPage() {
            return ceil($this->numberofitems / $this->settings['itemLimit']);
        }
        
        protected function getCurrentPage() {
            return $this->currentPage;
        } 
        
        public function getData(){
            return $this->errorLog;
        }

        
        public function getNumberOfItems(){
            return $this->numberofitems;
        }
        
        public function getOffset(){
            return $this->offset;
        }
	private function processErrorAdditonalData($data) {
		if (isset($data['MOrderID'])) {
			$o = MLDatabase::getDbInstance()->fetchOne('
				SELECT data FROM '.TABLE_MAGNA_ORDERS.'
				 WHERE special=\''.MLDatabase::getDbInstance()->escape($data['MOrderID']).'\'
			');
			if ($o === false) return;
			$o = @unserialize($o);
			if (!is_array($o)) {
				$o = array();
			}
			$o['ML_ERROR_LABEL'] = 'ML_GENERIC_ERROR_ORDERSYNC_FAILED';
			#echo print_m($o);
			$o = serialize($o);
			MLDatabase::getDbInstance()->update(TABLE_MAGNA_ORDERS, array('data' => $o), array('special' => $data['MOrderID']));
		}
	}
	
	protected function importErrorLog() {
		if (!$this->settings['hasImport']) {
			return;
		}
		$begin = MLDatabase::getDbInstance()->fetchOne('
		    SELECT dateadded FROM '.TABLE_MAGNA_COMPAT_ERRORLOG.'
		     WHERE mpID = '.$this->mpID.'
		  ORDER BY dateadded DESC
		     LIMIT 1
		');
                $beginConfig = MLModul::gi()->getConfig('errorlog.lastdate');
                if($beginConfig != null){
                    $begin = $beginConfig;
                }
		if ($begin === false) {
			$begin = time() - 60 * 60 * 24 * 12;
		} else {
			$begin = strtotime($begin.' +0000') + 1;
		}
		$begin = gmdate('Y-m-d H:i:s', max($begin, time() - 60 * 60 * 24 * 12));
		#$begin = '2013-01-01 00:00:00';
		
		$request = array(
			'ACTION' => 'GetErrorLogForDateRange',
			'BEGIN' => $begin,
			'OFFSET' => array (
				'COUNT' => 1000,
				'START' => 0
			),
		);
		#echo print_m($request, '$request');
		try {
			$result = MagnaConnector::gi()->submitRequest($request);
		} catch (MagnaException $e) {
			$result['DATA'] = array();
		}
		#echo print_m($result, '$result');
		#return;
		$newbegin = '';
		if (array_key_exists('DATA', $result) && !empty($result['DATA'])) {
			foreach ($result['DATA'] as $item) {
				$this->processErrorAdditonalData($item['ErrorData']);
				$data = array (
					'mpID' => $item['MpId'],
					'origin' => isset($item['Origin']) ? $item['Origin'] : '',
					'dateadded' => $item['DateAdded'],
					'errormessage' => $item['ErrorMessage'],
					'additionaldata' => serialize($item['ErrorData']),
				);
				if ($begin < $item['DateAdded']) {
					$begin = $item['DateAdded'];
				}
				if (!MLDatabase::getDbInstance()->recordExists(TABLE_MAGNA_COMPAT_ERRORLOG, $data)) {
					MLDatabase::getDbInstance()->insert(TABLE_MAGNA_COMPAT_ERRORLOG, $data);
				}
			}
			$newbegin = $item['DateAdded'];
		}
		if (!empty($newbegin)) {
			MLModul::gi()->setConfig('errorlog.lastdate', $begin);
		}
	}
	
	private function sortByType($type) {
		return '
			<div class="ml-plist">
				<a class="noButton ml-right arrowAsc" href="'.$this->getCurrentUrl(array('sorting' => $type.'')).'" title="'.$this->__('ML_LABEL_SORT_ASCENDING').'">'.$this->__('ML_LABEL_SORT_ASCENDING').'</a>
				<a class="noButton ml-right arrowDesc" href="'.$this->getCurrentUrl( array('sorting' => $type.'-desc')).'" title="'.$this->__('ML_LABEL_SORT_DESCENDING').'">'.$this->__('ML_LABEL_SORT_DESCENDING').'</a>
			</div>';
	}

	public function renderActionBox() {
		$left = '<input type="button" class="ml-button ml-js-deleteBtn" value="'.$this->__('ML_BUTTON_LABEL_DELETE').'" name="delete"/>';
		$right = '<input type="button" class="ml-button ml-js-deleteBtn" value="'.$this->__('ML_BUTTON_LABEL_DELETE_ENTIRE_PROTOCOL').'" id="errorLogDelete" name="deleteall"/>';

		ob_start();?>
<script type="text/javascript">/*<![CDATA[*/
jqml(document).ready(function() {
	jqml('.ml-js-deleteBtn').click(function() {
		var btnAction = jqml(this).attr('name');
		
		if ((btnAction == 'deleteall')
			&& confirm(unescape(<?php echo "'".  html_entity_decode($this->__('ML_GENERIC_CONFIRM_DELETE_ENTIRE_ERROR_PROTOCOL'))."'"; ?>))
		) {
			jqml('#action').val(btnAction);
			jqml(this).parents('form').submit();
			
		} else if ((jqml('#errorlog input[type="checkbox"]:checked').length > 0)
			&& confirm(unescape(<?php echo "'".html_entity_decode($this->__('ML_GENERIC_DELETE_ERROR_MESSAGES'))."'"; ?>))
		) {
			jqml('#action').val(btnAction);
			jqml(this).parents('form').submit();
		}
	});
});
/*]]>*/</script>
<?php // Durch aufrufen der Seite wird automatisch ein Aktualisierungsauftrag gestartet
		$js = ob_get_contents();	
		ob_end_clean();

		return '
			<input type="hidden" id="action" name="'.MLHttp::gi()->parseFormFieldName('action').'" value="">
			<input type="hidden" name="'.MLHttp::gi()->parseFormFieldName('timestamp').'" value="'.time().'">
			<table class="actions">
				<thead><tr><th>'.$this->__('ML_LABEL_ACTIONS').'</th></tr></thead>
				<tbody><tr><td>
					<table><tbody><tr>
						<td class="firstChild">'.$left.'</td>
						<td class="lastChild">'.$right.'</td>
					</tr></tbody></table>
				</td></tr></tbody>
			</table>
			'.$js;
	}
	
	private function renderAdditionalData($data) {
		if (empty($data)) {
			return '&nbsp;';
		}
		$html = '<table class="nostyle addData fullWidth"><tbody>';
		foreach ($data as $key => $item) {
			$html .= '
				<tr>
					<th>'.str_replace(' ', '&nbsp;', $key).'</th>
					<td>'.$item.'</td>
				</tr>';
		}
		return $html.'</tbody></table>';
	}
	
	protected function additionalDataHandler($data) {
		if (!is_array($data) || empty($data)) {
			return '&nbsp;&nbsp;&mdash;';
		}
		$fData = array();
		if (array_key_exists('SKU', $data) && !empty($data['SKU'])) {
			$fData['SKU'] = htmlspecialchars($data['SKU']);
			
			MLProduct::factory()->createModelProductByMarketplaceSku($data['SKU']);
			$title = '';
			try {
				$title = MLProduct::factory()->set('marketplacesku', $data['SKU'])->getName();
			} catch (Exception $e) {}
			if (!empty($title)) {
				$fData[$this->__('ML_LABEL_SHOP_TITLE')] = $title;
			}
		} else {
			$fData = $data;
		}
		return $this->renderAdditionalData($fData);
	}
	
	protected function processErrorMessage($item) {
		$ret = array (
			'long' => $item['errormessage'],
			'short' => '',
		);
		if (preg_match('/^constant\(([A-Z_]*)\)$/', $item['errormessage'], $match)) {
			if (defined($match[1])) {
				$ret['long'] = constant($match[1]);
			} else {
				$ret['long'] = $match[1];
			}
		} else {
			try {
				$jsonerror = @json_decode($ret['long'], true);
			} catch (Exception $e) {
				$jsonerror = false;
			}
			if (is_array($jsonerror)) {
				if (isset($jsonerror['MissingFields'])) {
					$ret['long'] = $this->__('ML_LABEL_MISSING_DATA').': '.implode(', ', $jsonerror['MissingFields']);
				}
			}
		}
		$ret['short'] = (
			(strlen($ret['long']) > $this->settings['maxTitleChars'] + 2) ? 
				(substr($ret['long'], 0, $this->settings['maxTitleChars']).'&hellip;') : 
				$ret['long']
		);
		return $ret;
	}

	public function renderView() {
		$html = '';
		if (empty($this->errorLog)) {
			return '<table class="magnaframe"><tbody><tr><td>'.$this->__('ML_GENERIC_NO_ERRORS_YET').'</td></tr></tbody></table>';
		}

		$tmpURL = array();
		if (isset($this->request['sorting'])) {
			$tmpURL['sorting'] = $this->request['sorting'];
		}

		$html .= '
			<form action="'.$this->getCurrentUrl().'" method="POST">';
				foreach (MLHttp::gi()->getNeededFormFields() as $sKey => $sValue) {
					$html .= '
					<input type="hidden" name="'.$sKey.'" value="'.$sValue.'" />';
				}
					
                $html .= $this->includeViewBuffered('widget_listings_misc_pagination');

                $html .= '				
				<table class="datagrid ml-plist-old-fix" id="errorlog">
					<thead><tr>
						<td class="nowrap" style="width: 5px;"><input type="checkbox" id="selectAll"/><label for="selectAll">'.$this->__('ML_LABEL_CHOICE').'</label></td>
						<td class="nowrap">'.$this->__('ML_AMAZON_LABEL_ADDITIONAL_DATA').'</td>
						<td>'.$this->__('ML_GENERIC_ERROR_MESSAGES').'&nbsp;'.$this->sortByType('errormessage').'</td>
						'.($this->settings['hasOrigin'] ? '<td>'.$this->__('ML_GENERIC_LABEL_ORIGIN').'</td>' : '').'
						<td>'.$this->__('ML_GENERIC_COMMISSIONDATE').'&nbsp;'.$this->sortByType('dateadded').'</td>
					</tr></thead>
					<tbody>';
		$oddEven = false;
		foreach ($this->errorLog as $item) {
			$dateadded = strtotime($item['dateadded']);
			$hdate = date("d.m.Y", $dateadded).' &nbsp;&nbsp;<span class="small">'.date("H:i", $dateadded).'</span>';
			$message = $this->processErrorMessage($item);
			$html .= '
						<tr class="'.(($oddEven = !$oddEven) ? 'odd' : 'even').'">
							<td><input type="checkbox" name="'.MLHttp::gi()->parseFormFieldName('errIDs[]').'" value="'.$item['id'].'"></td>
							<td class="nopadding" style="width: 1px">'.$this->additionalDataHandler($item['additionaldata']).'</td>
							<td class="errormessage">'.$message['short'].'<span>'.$message['long'].'</span></td>
							'.($this->settings['hasOrigin'] ? '<td>'.$item['origin'].'</td>' : '').'
							<td>'.$hdate.'</td>
						</tr>';
		}
		$html .= '
					</tbody>
				</table>
				<div id="errordetails" class="dialog2" title="'.$this->__('ML_GENERIC_ERROR_DETAILS').'"></div>';
		ob_start(); ?>
<script type="text/javascript">/*<![CDATA[*/
	jqml(document).ready(function() {
		jqml('table#errorlog tbody td.errormessage').click(function() {
			jqml('#errordetails').html(jqml('span', this).html()).jDialog();
		});
		
		jqml('#selectAll').click(function() {
			state = jqml(this).attr('checked');
			jqml('#errorlog input[type="checkbox"]:not([disabled])').each(function() {
				jqml(this).attr('checked', state);
			});
		});
	});
	/*]]>*/</script>
<?php
		$html .= ob_get_contents();
		ob_end_clean();
		$html .= $this->renderActionBox().'
			</form>';
		return $html;
	}
}
