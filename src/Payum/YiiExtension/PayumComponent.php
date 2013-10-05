<?php
namespace Payum\YiiExtension;

use Payum\Exception\RuntimeException;
use Payum\Extension\StorageExtension;
use Payum\PaymentInterface;
use Payum\Registry\RegistryInterface;
use Payum\Registry\SimpleRegistry;
use Payum\Request\BinaryMaskStatusRequest;
use Payum\Request\RedirectUrlInteractiveRequest;
use Payum\Request\SecuredCaptureRequest;
use Payum\Security\HttpRequestVerifierInterface;
use Payum\Security\PlainHttpRequestVerifier;
use Payum\Storage\StorageInterface;

class PayumComponent extends \CApplicationComponent
{
    /**
     * @var PaymentInterface[]
     */
    public $payments;

    /**
     * @var array
     */
    public $storages;

    /**
     * @var StorageInterface
     */
    public $tokenStorage;

    /**
     * @var HttpRequestVerifierInterface
     */
    protected $httpRequestVerifier;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    public function init()
    {
        $this->registry = new SimpleRegistry($this->payments, $this->storages, null, null);
        $this->httpRequestVerifier = new PlainHttpRequestVerifier($this->tokenStorage);

        foreach ($this->registry->getPayments() as $name => $payment) {
            foreach ($this->registry->getStorages($name) as $storage) {
                $payment->addExtension(new StorageExtension($storage));
            }
        }
    }

    public function captureController()
    {
        $token = $this->httpRequestVerifier->verify(\Yii::app()->request->getRequest());
        $payment = $this->registry->getPayment($token->getPaymentName());

        $payment->execute($status = new BinaryMaskStatusRequest($token));
        if (false == $status->isNew()) {
            header('HTTP/1.1 400 Bad Request', true, 400);
            exit;
        }

        if ($interactiveRequest = $payment->execute(new SecuredCaptureRequest($token), true)) {
            if ($interactiveRequest instanceof RedirectUrlInteractiveRequest) {
                \Yii::app()->request->redirect($interactiveRequest->getUrl());
                \Yii::app()->end();
            }

            throw new \LogicException('Unsupported interactive request', null, $interactiveRequest);
        }

        $this->httpRequestVerifier->invalidate($token);
        
        \Yii::app()->request->redirect($token->getAfterUrl());
    }

    /**
     * @return StorageInterface
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @return HttpRequestVerifierInterface
     */
    public function getHttpRequestVerifier()
    {
        return $this->httpRequestVerifier;
    }

    /**
     * @return RegistryInterface
     */
    public function getRegistry()
    {
        return $this->registry;
    }
}
