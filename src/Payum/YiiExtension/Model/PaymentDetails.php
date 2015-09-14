<?php

namespace Payum\YiiExtension\Model;

use Payum\Core\Model\PaymentInterface;

/**
 * This is the model class for table "payum_payment_details".
 *
 * The followings are the available columns in table 'payum_payment_details':
 *
 * @property integer $id
 * @property string $number
 * @property string $description
 * @property string $client_email
 * @property string $client_id
 * @property string $currency_code
 * @property integer $total_amount
 * @property integer $currency_digits_after_decimal_point
 *
 * The followings are the available model relations:
 * @property PayumPaymentToken[] $payumPaymentTokens
 */
class PaymentDetails extends \CActiveRecord implements PaymentInterface
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->setDetails(array());
        $this->currency_digits_after_decimal_point = 2;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientEmail()
    {
        return $this->client_email;
    }

    /**
     * @param string $clientEmail
     */
    public function setClientEmail($clientEmail)
    {
        $this->client_email = $clientEmail;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->client_id = $clientId;
    }

    /**
     * {@inheritDoc}
     */
    public function getTotalAmount()
    {
        return $this->total_amount;
    }

    /**
     * @param int $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->total_amount = $totalAmount;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * @param string $currencyCode
     * @param int    $digitsAfterDecimalPoint
     */
    public function setCurrencyCode($currencyCode, $digitsAfterDecimalPoint = 2)
    {
        $this->currency_code = $currencyCode;
        $this->currency_digits_after_decimal_point = $digitsAfterDecimalPoint;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyDigitsAfterDecimalPoint()
    {
        return $this->currency_digits_after_decimal_point;
    }

    /**
     * {@inheritDoc}
     */
    public function getDetails()
    {
        if (null !== $this->details) {
            return unserialize($this->details);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param array|\Traversable $details
     */
    public function setDetails($details)
    {
        if ($details instanceof \Traversable) {
            $details = iterator_to_array($details);
        }

        $this->details = serialize($details);
    }

    /**
     * {@inheritDoc}
     */
    public function tableName()
    {
        return 'payum_payment_details';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return array(
            array('number', 'required'),
            array('total_amount, currency_digits_after_decimal_point', 'numerical', 'integerOnly' => true),
            array('number, client_email, client_id, currency_code', 'length', 'max' => 255),
            array('description', 'safe'),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function relations()
    {
        return array(
            'payumPaymentTokens' => array(self::HAS_MANY, 'PayumPaymentToken', 'details_id'),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'number' => 'Number',
            'description' => 'Description',
            'client_email' => 'Client Email',
            'client_id' => 'Client',
            'currency_code' => 'Currency Code',
            'total_amount' => 'Total Amount',
            'currency_digits_after_decimal_point' => 'Currency Digits After Decimal Point',
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
