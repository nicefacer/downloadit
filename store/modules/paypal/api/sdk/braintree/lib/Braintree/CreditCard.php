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
 * Braintree CreditCard module
 * Creates and manages Braintree CreditCards
 *
 * <b>== More information ==</b>
 *
 * For more detailed information on CreditCards, see {@link http://www.braintreepayments.com/gateway/credit-card-api http://www.braintreepaymentsolutions.com/gateway/credit-card-api}<br />
 * For more detailed information on CreditCard verifications, see {@link http://www.braintreepayments.com/gateway/credit-card-verification-api http://www.braintreepaymentsolutions.com/gateway/credit-card-verification-api}
 *
 * @category   Resources
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property string $billingAddress
 * @property string $bin
 * @property string $cardType
 * @property string $cardholderName
 * @property string $createdAt
 * @property string $customerId
 * @property string $expirationDate
 * @property string $expirationMonth
 * @property string $expirationYear
 * @property string $imageUrl
 * @property string $last4
 * @property string $maskedNumber
 * @property string $token
 * @property string $updatedAt
 */
class CreditCard extends Base
{
    // Card Type
    const AMEX = 'American Express';
    const CARTE_BLANCHE = 'Carte Blanche';
    const CHINA_UNION_PAY = 'China UnionPay';
    const DINERS_CLUB_INTERNATIONAL = 'Diners Club';
    const DISCOVER = 'Discover';
    const JCB = 'JCB';
    const LASER = 'Laser';
    const MAESTRO = 'Maestro';
    const MASTER_CARD = 'MasterCard';
    const SOLO = 'Solo';
    const SWITCH_TYPE = 'Switch';
    const VISA = 'Visa';
    const UNKNOWN = 'Unknown';

    // Credit card origination location
    const INTERNATIONAL = 'international';
    const US = 'us';

    const PREPAID_YES = 'Yes';
    const PREPAID_NO = 'No';
    const PREPAID_UNKNOWN = 'Unknown';

    const PAYROLL_YES = 'Yes';
    const PAYROLL_NO = 'No';
    const PAYROLL_UNKNOWN = 'Unknown';

    const HEALTHCARE_YES = 'Yes';
    const HEALTHCARE_NO = 'No';
    const HEALTHCARE_UNKNOWN = 'Unknown';

    const DURBIN_REGULATED_YES = 'Yes';
    const DURBIN_REGULATED_NO = 'No';
    const DURBIN_REGULATED_UNKNOWN = 'Unknown';

    const DEBIT_YES = 'Yes';
    const DEBIT_NO = 'No';
    const DEBIT_UNKNOWN = 'Unknown';

    const COMMERCIAL_YES = 'Yes';
    const COMMERCIAL_NO = 'No';
    const COMMERCIAL_UNKNOWN = 'Unknown';

    const COUNTRY_OF_ISSUANCE_UNKNOWN = 'Unknown';
    const ISSUING_BANK_UNKNOWN = 'Unknown';

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
     * checks whether the card is expired based on the current date
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expired;
    }

    /**
     * checks whether the card is associated with venmo sdk
     *
     * @return bool
     */
    public function isVenmoSdk()
    {
        return $this->venmoSdk;
    }

    /**
     * sets instance properties from an array of values
     *
     * @param array $creditCardAttribs array of creditcard data
     *
     * @return void
     */
    protected function _initialize($creditCardAttribs)
    {
        // set the attributes
        $this->_attributes = $creditCardAttribs;

        // map each address into its own object
        $billingAddress = isset($creditCardAttribs['billingAddress']) ?
            Address::factory($creditCardAttribs['billingAddress']) :
            null;

        $subscriptionArray = [];
        if (isset($creditCardAttribs['subscriptions'])) {
            foreach ($creditCardAttribs['subscriptions'] as $subscription) {
                $subscriptionArray[] = Subscription::factory($subscription);
            }
        }

        $this->_set('subscriptions', $subscriptionArray);
        $this->_set('billingAddress', $billingAddress);
        $this->_set('expirationDate', $this->expirationMonth . '/' . $this->expirationYear);
        $this->_set('maskedNumber', $this->bin . '******' . $this->last4);

        if (isset($creditCardAttribs['verifications']) && count($creditCardAttribs['verifications']) > 0) {
            $verifications = $creditCardAttribs['verifications'];
            usort($verifications, [$this, '_compareCreatedAtOnVerifications']);

            $this->_set('verification', CreditCardVerification::factory($verifications[0]));
        }
    }

    private function _compareCreatedAtOnVerifications($verificationAttrib1, $verificationAttrib2)
    {
        return ($verificationAttrib2['createdAt'] < $verificationAttrib1['createdAt']) ? -1 : 1;
    }

    /**
     * returns false if comparing object is not a CreditCard,
     * or is a CreditCard with a different id
     *
     * @param object $otherCreditCard customer to compare against
     *
     * @return bool
     */
    public function isEqual($otherCreditCard)
    {
        return !($otherCreditCard instanceof self) ? false : $this->token === $otherCreditCard->token;
    }

    /**
     * create a printable representation of the object as:
     * ClassName[property=value, property=value]
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . '[' .
                Util::attributesToString($this->_attributes) . ']';
    }

    /**
     *  factory method: returns an instance of CreditCard
     *  to the requesting method, with populated properties
     *
     * @ignore
     *
     * @return CreditCard
     */
    public static function factory($attributes)
    {
        $defaultAttributes = [
            'bin' => '',
            'expirationMonth' => '',
            'expirationYear' => '',
            'last4' => '',
        ];

        $instance = new self();
        $instance->_initialize(array_merge($defaultAttributes, $attributes));

        return $instance;
    }

    // static methods redirecting to gateway

    public static function create($attribs)
    {
        return Configuration::gateway()->creditCard()->create($attribs);
    }

    public static function createNoValidate($attribs)
    {
        return Configuration::gateway()->creditCard()->createNoValidate($attribs);
    }

    public static function createFromTransparentRedirect($queryString)
    {
        return Configuration::gateway()->creditCard()->createFromTransparentRedirect($queryString);
    }

    public static function createCreditCardUrl()
    {
        return Configuration::gateway()->creditCard()->createCreditCardUrl();
    }

    public static function expired()
    {
        return Configuration::gateway()->creditCard()->expired();
    }

    public static function fetchExpired($ids)
    {
        return Configuration::gateway()->creditCard()->fetchExpired($ids);
    }

    public static function expiringBetween($startDate, $endDate)
    {
        return Configuration::gateway()->creditCard()->expiringBetween($startDate, $endDate);
    }

    public static function fetchExpiring($startDate, $endDate, $ids)
    {
        return Configuration::gateway()->creditCard()->fetchExpiring($startDate, $endDate, $ids);
    }

    public static function find($token)
    {
        return Configuration::gateway()->creditCard()->find($token);
    }

    public static function fromNonce($nonce)
    {
        return Configuration::gateway()->creditCard()->fromNonce($nonce);
    }

    public static function credit($token, $transactionAttribs)
    {
        return Configuration::gateway()->creditCard()->credit($token, $transactionAttribs);
    }

    public static function creditNoValidate($token, $transactionAttribs)
    {
        return Configuration::gateway()->creditCard()->creditNoValidate($token, $transactionAttribs);
    }

    public static function sale($token, $transactionAttribs)
    {
        return Configuration::gateway()->creditCard()->sale($token, $transactionAttribs);
    }

    public static function saleNoValidate($token, $transactionAttribs)
    {
        return Configuration::gateway()->creditCard()->saleNoValidate($token, $transactionAttribs);
    }

    public static function update($token, $attributes)
    {
        return Configuration::gateway()->creditCard()->update($token, $attributes);
    }

    public static function updateNoValidate($token, $attributes)
    {
        return Configuration::gateway()->creditCard()->updateNoValidate($token, $attributes);
    }

    public static function updateCreditCardUrl()
    {
        return Configuration::gateway()->creditCard()->updateCreditCardUrl();
    }

    public static function updateFromTransparentRedirect($queryString)
    {
        return Configuration::gateway()->creditCard()->updateFromTransparentRedirect($queryString);
    }

    public static function delete($token)
    {
        return Configuration::gateway()->creditCard()->delete($token);
    }

    /** @return array */
    public static function allCardTypes()
    {
        return [
            CreditCard::AMEX,
            CreditCard::CARTE_BLANCHE,
            CreditCard::CHINA_UNION_PAY,
            CreditCard::DINERS_CLUB_INTERNATIONAL,
            CreditCard::DISCOVER,
            CreditCard::JCB,
            CreditCard::LASER,
            CreditCard::MAESTRO,
            CreditCard::MASTER_CARD,
            CreditCard::SOLO,
            CreditCard::SWITCH_TYPE,
            CreditCard::VISA,
            CreditCard::UNKNOWN,
        ];
    }
}
class_alias('Braintree\CreditCard', 'Braintree_CreditCard');
