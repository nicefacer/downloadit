<?php
/**
 * @var ML_Shop_Model_Order_Abstract $oOrder
 */
$oI18n = MLI18n::gi();
?><table><?php
    $oOrder  = !isset($oOrder) && isset($o_order) ? $o_order:$oOrder;
    foreach ($oOrder->get('data') as $sKey => $mValue) {
        $aPrefixes = array("_platformName_" => $oOrder->get('platform'));
        $sTitle = $oI18n->get($sKey, $aPrefixes);
        $sInfo = '';
        $sDate = null;
        if (in_array($sKey, array('MOrderID', 'MPreviousOrderID', 'MPreviousOrderIDS'))) {
            if ($sKey == 'MPreviousOrderIDS' && !MLSetting::gi()->get('blDebug')) {
                continue;
            } elseif ($sKey == 'MPreviousOrderID') {
                if (is_array($mValue)) {
                    $sDate = $mValue['date'];
                    $mValue = $mValue['id'];
                }
            } elseif ($sKey == 'MOrderID') {
                $aOrderData = $oOrder->get('orderdata');
                $sDate = isset($aOrderData['Order']['DatePurchased'])?$aOrderData['Order']['DatePurchased']:'--';
            }
        }
        if (is_array($mValue)) {
            $sInfo .='<ul>';
            foreach ($mValue as $sValueKey => $sValue) {
                $sInfo .='<li>' .(is_numeric($sValueKey) ? '' : $sValueKey.': '). $oI18n->get($sValue, $aPrefixes) . '</li>';
            }
            $sInfo .='</ul>';
        } else {
            $sInfo .= '&nbsp;' .$oI18n->get($mValue, $aPrefixes). (isset($sDate) ? "&nbsp;({$sDate})" : '');;
        }
        ?>
        <tr>
            <th><?php
                echo $sTitle;
                ?><th>
            <th>:</th>
            <td><?php
                echo $sInfo;
                ?></td>
        </tr>
        <?php
    }
    ?></table>
