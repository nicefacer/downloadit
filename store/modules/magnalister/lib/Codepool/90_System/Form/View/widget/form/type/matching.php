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
class_exists('ML', false) or die(); 
/**
 * @param array $aField array(
 *      ...,
 *      'i18n' => array(
 *          ...,
 *          'matching' => array(
 *              'titlesrc' => (string),
 *              'titledst' => (string)
 *          )
 *      )
 *      'valuessrc' => array (
 *          (string) $key => array(
 *              (string)'i18n' => (string),
 *              'required' => (bool)
 *          ),
 *          ...
 *      )
 * )
 */
?>
<table style="width:100%;">
    <?php if (!empty($aField['i18n']['matching']['titlesrc']) || !empty($aField['i18n']['matching']['titledst'])) { ?>
    <thead>
        <th><?php echo $aField['i18n']['matching']['titlesrc']; ?></th>
        <th><?php echo $aField['i18n']['matching']['titledst']; ?></th>
    </thead>
    <?php } ?>
    <tbody>
        <?php foreach ($aField['valuessrc'] as $sKey => $aValue) { ?>
            <tr>
                <td><?php echo $aValue['i18n'] ?></td>
                <td>
                    <?php 
                        $sValue = isset($aField['value'][$sKey]) ? $aField['value'][$sKey] : current($aField['valuesdst']);
                        $aSelect = array(
                            'name' => $aField['name'].'['.$sKey.']',
                            'type' => 'select',
                            'i18n' => array(),
                            'values' => $aField['valuesdst'],
                            'value' => $sValue
                        );

						if (isset($aField['error'][$sKey])) {
							$aSelect['cssclass'] = 'error';
						}

                        if (!isset($aValue['required']) || $aValue['required'] === false) {
							// Changed because in previous implementation array keys are recreated.
							$aNewArray = array(MLI18n::gi()->get('form_type_matching_optional'));
							foreach ($aSelect['values'] as $sSelectKey => $sSelectValue) {
								$aNewArray[$sSelectKey] = $sSelectValue;
							}
							$aSelect['values'] = $aNewArray;
                        }

                        if (isset($aField['addonempty']) && $aField['addonempty'] === true && count($aField['valuesdst']) == 0) {
                            $aSelect['values'][$sKey] = $aValue['i18n'];
                            $aSelect['value'] = $aSelect['value'] != '' ? $aSelect['value'] : $sKey;
                        }
                        
                        if (!isset($aField['value'][$sKey]) && isset($aField['automatch']) && $aField['automatch'] === true
                                && count($aField['valuesdst']) > 0) {
                            $aSelect['value'] = null;
                            foreach ($aField['valuesdst'] as $sDstKey => $sDstValue) {
                                if ($aValue['i18n'] == $sDstValue) {
                                    $aSelect['value'] = $sDstKey;
                                    break;
                                }
                            }
                        }

                        $this->includeType($aSelect);
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
