<?php
/**
 * User: martyn ling <mling@orthomeo.com>
 * Date: 04/12/13
 * Time: 18:22
 */

namespace Payum\YiiExtension\Model;

use Payum\Core\Model\Token;

class PaymentSecurityToken extends Token
{
    /**
     * @var \CActiveRecord
     */
    protected $activeRecord;

    public function __construct($scenario = 'insert', $tableName = '')
    {
        if ($scenario == 'insert') {
            $this->activeRecord = new TokenActiveRecord('insert', $tableName);
            $this->hash = $this->activeRecord->_hash;
        }
    }

    public function save()
    {
        $this->activeRecord->save();
    }

    public function delete()
    {
        $this->activeRecord->delete();
    }

    public static function findModelById($tableName, $id)
    {
        $token = new PaymentSecurityToken('update');
        $token->activeRecord = TokenActiveRecord::model($tableName)->findByPk($id);

        // Load the values into the token object from the activeRecord
        $token->hash = $token->activeRecord->_hash;
        $token->targetUrl = $token->activeRecord->_target_url;
        $token->afterUrl = $token->activeRecord->_after_url;
        $token->paymentName = $token->activeRecord->_payment_name;
        $token->details = $token->activeRecord->getDetailsIdentificator();
        return $token;
    }

    /**
     * {@inheritDoc}
     *
     * @return Identificator
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
         $this->activeRecord->_details = $this->details = $details;
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
        $this->hash = $this->activeRecord->_hash = $hash;
    }

    /**
     * {@inheritDoc}
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function setTargetUrl($targetUrl)
    {
        $this->targetUrl = $this->activeRecord->_target_url = $targetUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getAfterUrl()
    {
        return $this->afterUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function setAfterUrl($afterUrl)
    {
        $this->afterUrl = $this->activeRecord->_after_url = $afterUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentName()
    {
        return $this->paymentName;
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentName($paymentName)
    {
        $this->paymentName = $this->activeRecord->_payment_name = $paymentName;
    }
}
