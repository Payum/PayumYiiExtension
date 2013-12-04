<?php
/**
 * This is the model class for table $tableName, which is being used for token storage for Payum payments
 *
 * The following are the available columns in table $tableName:
 * @property string $_hash
 * @property string $_payment_name
 * @property string $_details
 * @property string $_after_url
 * @property string $_target_url
 *
 * Underscores are used because usually these would be private and to prevent
 * the ActiveRecord getters and setters clashing with the required
 * getters and setters from TokenInterface
 */

namespace Payum\YiiExtension\Model;

use Payum\Core\Security\TokenInterface;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Security\Util\Random;
use Payum\Core\Model\Identificator;

class TokenActiveRecord extends \CActiveRecord
{
    private static $_tableName;

    /**
     * Constructs a model corresponding to table $tableName
     * The table must have the columns identified above in the
     * comments for this class.
     *
     * @param string $scenario
     * @param $tableName
     * @throws \Payum\Core\Exception\InvalidArgumentException
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
            $this->_hash = Random::generateToken();
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
     * @throws \Payum\Core\Exception\InvalidArgumentException
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
     * @return Identificator
     */
    public function getDetails()
    {
        $parts = explode("#", $this->_details);
        return new Identificator($parts[1], $parts[0]);
    }

    /**
     * {@inheritDoc}
     *
     */
    function setDetails($details)
    {
        $this->_details = $details;
    }

    /**
     * {@inheritDoc}
     */
    function getHash()
    {
        return $this->_hash;
    }

    /**
     * {@inheritDoc}
     */
    function setHash($hash)
    {
        $this->_hash = $hash;
    }

    /**
     * {@inheritDoc}
     */
    function getTargetUrl()
    {
        return $this->_target_url;
    }

    /**
     * {@inheritDoc}
     */
    function setTargetUrl($targetUrl)
    {
        $this->_target_url = $targetUrl;
    }

    /**
     * {@inheritDoc}
     */
    function getAfterUrl()
    {
        return $this->_after_url;
    }

    /**
     * {@inheritDoc}
     */
    function setAfterUrl($afterUrl)
    {
        $this->_after_url = $afterUrl;
    }

    /**
     * {@inheritDoc}
     */
    function getPaymentName()
    {
        return $this->_payment_name;
    }

    /**
     * {@inheritDoc}
     */
    function setPaymentName($paymentName)
    {
        $this->_payment_name = $paymentName;
    }

    /**
     * @return \Payum\Core\Model\Identificator
     */
    public function getDetailsIdentificator()
    {
        $parts = explode("#", $this->_details);
        if (count($parts) == 2) {
            return new Identificator($parts[1], $parts[0]);
        }
        return $this->_details;
    }
}
