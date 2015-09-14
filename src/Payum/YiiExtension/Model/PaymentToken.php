<?php

namespace Payum\YiiExtension\Model;

use Payum\Core\Security\TokenInterface;
use Payum\Core\Security\Util\Random;

/**
 * This is the model class for table "payum_payment_token".
 *
 * The followings are the available columns in table 'payum_payment_token':
 *
 * @property string $hash
 * @property string $payment_name
 * @property string $after_url
 * @property string $target_url
 * @property integer $details_id
 *
 * The followings are the available model relations:
 * @property PayumPaymentDetails $details
 */
class PaymentToken extends \CActiveRecord implements TokenInterface
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->hash = Random::generateToken();
    }

    /**
     * {@inheritDoc}
     */
    public function primaryKey()
    {
        return $this->getHash();
    }

    /**
     * {@inheritDoc}
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * {@inheritDoc}
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * {@inheritDoc}
     */
    public function getTargetUrl()
    {
        return $this->target_url;
    }

    /**
     * {@inheritDoc}
     */
    public function setTargetUrl($targetUrl)
    {
        $this->target_url = $targetUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getAfterUrl()
    {
        return $this->after_url;
    }

    /**
     * {@inheritDoc}
     */
    public function setAfterUrl($afterUrl)
    {
        return $this->after_url = $afterUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentName()
    {
        return $this->payment_name;
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentName($paymentName)
    {
        $this->payment_name = $paymentName;
    }

    /**
     * {@inheritDoc}
     */
    public function tableName()
    {
        return 'payum_payment_token';
    }

    /**
     * {@inheritDoc}
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * {@inheritDoc}
     */
    public function setDetails($details)
    {
        $this->details_id = $details->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return array(
            array('hash, payment_name', 'required'),
            array('details_id', 'numerical', 'integerOnly' => true),
            array('hash, payment_name', 'length', 'max' => 255),
            array('after_url, target_url', 'safe'),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function relations()
    {
        return array(
            'details' => array(self::BELONGS_TO, 'Payum\YiiExtension\Model\PaymentDetails', 'details_id'),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'hash' => 'Hash',
            'payment_name' => 'Payment Name',
            'after_url' => 'After Url',
            'target_url' => 'Target Url',
            'details_id' => 'Details',
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
