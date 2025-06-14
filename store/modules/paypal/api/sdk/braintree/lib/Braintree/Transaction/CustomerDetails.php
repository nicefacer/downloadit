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
 * Customer details from a transaction
 * Creates an instance of customer details as returned from a transaction
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property string $company
 * @property string $email
 * @property string $fax
 * @property string $firstName
 * @property string $id
 * @property string $lastName
 * @property string $phone
 * @property string $website
 */
class CustomerDetails extends Instance
{
}
class_alias('Braintree\Transaction\CustomerDetails', 'Braintree_Transaction_CustomerDetails');
