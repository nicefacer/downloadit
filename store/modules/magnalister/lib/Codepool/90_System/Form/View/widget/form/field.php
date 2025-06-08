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
?>
<?php
    if(!isset($aField['id'])){
        return;
    }
    if(isset($aField['type'])){
        if (
                (
                    isset($aField[$aField['type']]) 
                    && !isset($aField[$aField['type']]['field']['type'])
                )
                ||
                $aField['type']==='hidden'
        ) {
            $blDisplay=false;
        } else {
            $blDisplay=true;
        }
    }else{
        $blDisplay=false;
    }
?>
<tr class="js-field <?php echo $sClass.(isset($aField['classes']) ? ' '.implode(' ', $aField['classes']) : ''); ?>"<?php echo $blDisplay?'':' style="display:none"' ?>>
    <th>
        <label for="<?php echo $aField['id'] ?>"><?php echo $aField['i18n']['label'] ?></label>
		<?php if (isset($aField['requiredField']) === true && $aField['requiredField'] === true) { ?>
			<span>•</span>
		<?php } ?>
    </th>
    <td class="mlhelp ml-js-noBlockUi">
        <?php if (isset($aField['i18n']['help'])) {?>
            <a data-ml-modal="#modal-<?php echo str_replace('.', '\\.', $aField['id']); ?>">
                &nbsp;
            </a>
            <div class="ml-modal dialog2" id="modal-<?php echo $aField['id'] ?>" title="<?php echo $aField['i18n']['label'] ;?>">
                <?php echo $aField['i18n']['help']; ?>
            </div>
        <?php } ?>
    </td>
    <td class="input">
        <?php 
            if (array_key_exists('debug', $aField) && $aField['debug']) {
                new dBug($aField, '', true);
            }
            $this->includeType($aField);
        ?>
    </td>
    <td class="info">
        <?php
        if (isset($aField['hint']['template'])) {
            $this->includeView('widget_form_hint_'.$aField['hint']['template'],array('aField'=>$aField));
        }
        ?>
    </td>
</tr>