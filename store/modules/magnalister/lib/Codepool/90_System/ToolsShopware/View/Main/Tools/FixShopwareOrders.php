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

$sSql = "
    SELECT o.id FROM ".Shopware()->Models()->getClassMetadata('Shopware\Models\Order\Order')->getTableName()." o
    LEFT JOIN ".Shopware()->Models()->getClassMetadata('Shopware\Models\Order\Billing')->getTableName()." b on o.id=b.orderid
    WHERE 
	b.userid is null and
	o.remote_addr = '5.9.57.141' 
";
// $sSql = "SELECT * from magnalister_orders where mpid= 17753";
$aSqlResult = MLDatabase::getDbInstance()->fetchArray($sSql, true);
echo '<a href="'.$this->getCurrentUrl(array('execute'=>true)).'">Count: '.count($aSqlResult).'</a><br />';
if ($this->getRequest('execute')) {
    echo 'Delete: ';
    foreach ($aSqlResult as $iId) {
        echo $iId.', ';
        Shopware()->Db()->query("DELETE FROM s_order where id = " . $iId);
        Shopware()->Db()->query("DELETE FROM s_order_details where orderId = " . $iId);
        Shopware()->Db()->query("DELETE FROM s_order_shippingaddress where orderId = " . $iId);
        Shopware()->Db()->query("DELETE FROM s_order_billingaddress where orderId = " . $iId);
    }
}