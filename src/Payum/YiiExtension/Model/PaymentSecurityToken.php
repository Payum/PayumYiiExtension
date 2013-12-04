<?php
/**
 * This is the model class for table $tableName, which is being used for token storage for Payum payments
 *
 * The following are the available columns in table $tableName:
 * @property integer $id
 * @property string $payment_name
 * @property string $details
 * @property string $after_url
 * @property string $target_url
 * @property string $hash
 */

namespace Payum\YiiExtension\Model;

use Payum\Security\TokenInterface;
use Payum\Exception\InvalidArgumentException;


class PaymentSecurityToken extends \CActiveRecord implements TokenInterface
{
    private static $_tableName;

    /**
     * Constructs a model corresponding to table $tableName
     * The table must have the columns identified above in the
     * comments for this class.
     *
     * @param string $scenario
     * @param $tableName
     * @throws \Payum\Exception\InvalidArgumentException
     */
    public function __construct($scenario = 'insert', $tableName = '')
    {
        if ($scenario == 'insert' && $tableName == '') {
            throw new InvalidArgumentException(
                'Table name must be supplied when creating a new PaymentSecurityToken'
            );
        }
        if ($tableName !== '') {
            self::$_tableName = $tableName;
        }
        parent::__construct($scenario);
        if ($scenario == 'insert') {
            $this->hash = Random::generateToken();
        }
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return self::$_tableName;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $tableName table corresponding to the model
     * @param string $className active record class name.
     * @return Payment the static model class
     * @throws \Payum\Exception\InvalidArgumentException
     */
    public static function model($tableName, $className=__CLASS__)
    {
        if ($tableName == '') {
            throw new InvalidArgumentException(
                'Table name must be supplied when trying to find a PaymentSecurityToken'
            );
        }
        self::$_tableName = $tableName;
        return parent::model($className);
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
    function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * {@inheritDoc}
     */
    function getHash()
    {
        return $this->hash;
    }

    /**
     * {@inheritDoc}
     */
    function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * {@inheritDoc}
     */
    function getTargetUrl()
    {
        return $this->target_url;
    }

    /**
     * {@inheritDoc}
     */
    function setTargetUrl($targetUrl)
    {
        $this->target_url = $targetUrl;
    }

    /**
     * {@inheritDoc}
     */
    function getAfterUrl()
    {
        return $this->after_url;
    }

    /**
     * {@inheritDoc}
     */
    function setAfterUrl($afterUrl)
    {
        $this->after_url = $afterUrl;
    }

    /**
     * {@inheritDoc}
     */
    function getPaymentName()
    {
        return $this->payment_name;
    }

    /**
     * {@inheritDoc}
     */
    function setPaymentName($paymentName)
    {
        $this->payment_name = $paymentName;
    }
}
