<?php
/**
 *  2007-2024 PayPal
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2024 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Braintree\Transaction;

use Braintree\Instance;

if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Amex Express Checkout card details from a transaction
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

/**
 * creates an instance of AmexExpressCheckoutCardDetails
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property string $cardType
 * @property string $bin
 * @property string $cardMemberExpiryDate
 * @property string $cardMemberNumber
 * @property string $cardType
 * @property string $sourceDescription
 * @property string $token
 * @property string $imageUrl
 * @property string $expirationMonth
 * @property string $expirationYear
 *
 * @uses Instance inherits methods
 */
class AmexExpressCheckoutCardDetails extends Instance
{
    protected $_attributes = [];

    /**
     * @ignore
     */
    public function __construct($attributes)
    {
        parent::__construct($attributes);
    }
}
class_alias('Braintree\Transaction\AmexExpressCheckoutCardDetails', 'Braintree_Transaction_AmexExpressCheckoutCardDetails');
