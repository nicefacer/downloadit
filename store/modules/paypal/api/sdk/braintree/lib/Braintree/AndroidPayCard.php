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

namespace Braintree;

if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Braintree AndroidPayCard module
 * Creates and manages Braintree Android Pay cards
 *
 * <b>== More information ==</b>
 *
 * See {@link https://developers.braintreepayments.com/javascript+php}<br />
 *
 * @category   Resources
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property string $bin
 * @property string $cardType
 * @property string $createdAt
 * @property string $customerId
 * @property string $default
 * @property string $expirationMonth
 * @property string $expirationYear
 * @property string $googleTransactionId
 * @property string $imageUrl
 * @property string $last4
 * @property string $sourceCardLast4
 * @property string $sourceCardType
 * @property string $sourceDescription
 * @property string $token
 * @property string $updatedAt
 * @property string $virtualCardLast4
 * @property string $virtualCardType
 */
class AndroidPayCard extends Base
{
    /* instance methods */

    /**
     * returns false if default is null or false
     *
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     *  factory method: returns an instance of AndroidPayCard
     *  to the requesting method, with populated properties
     *
     * @ignore
     *
     * @return AndroidPayCard
     */
    public static function factory($attributes)
    {
        $defaultAttributes = [
            'expirationMonth' => '',
            'expirationYear' => '',
            'last4' => $attributes['virtualCardLast4'],
            'cardType' => $attributes['virtualCardType'],
        ];

        $instance = new self();
        $instance->_initialize(array_merge($defaultAttributes, $attributes));

        return $instance;
    }

    /**
     * sets instance properties from an array of values
     *
     * @param array $androidPayCardAttribs array of Android Pay card properties
     *
     * @return void
     */
    protected function _initialize($androidPayCardAttribs)
    {
        // set the attributes
        $this->_attributes = $androidPayCardAttribs;

        $subscriptionArray = [];
        if (isset($androidPayCardAttribs['subscriptions'])) {
            foreach ($androidPayCardAttribs['subscriptions'] as $subscription) {
                $subscriptionArray[] = Subscription::factory($subscription);
            }
        }

        $this->_set('subscriptions', $subscriptionArray);
    }
}
class_alias('Braintree\AndroidPayCard', 'Braintree_AndroidPayCard');
