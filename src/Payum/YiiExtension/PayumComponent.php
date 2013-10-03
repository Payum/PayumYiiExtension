<?php
namespace Payum\YiiExtension;

use Payum\Exception\RuntimeException;
use Payum\Registry\RegistryInterface;
use Payum\Request\BinaryMaskStatusRequest;
use Payum\Request\RedirectUrlInteractiveRequest;
use Payum\Request\SecuredCaptureRequest;
use Payum\Security\HttpRequestVerifierInterface;
use Payum\Security\PlainHttpRequestVerifier;
use Payum\Storage\StorageInterface;

class PayumComponent extends CApplicationComponent
{
    /**
     * @var  RegistryInterface
     */
    public $registry;

    /**
     * @var StorageInterface
     */
    public $tokenStorage;

    /**
     * @var HttpRequestVerifierInterface
     */
    protected $httpRequestVerifier;

    public function init()
    {
        if (false == $this->registry instanceof RegistryInterface) {
            throw new RuntimeException(sprintf(
                'Registry must be instance of RegistryInterface interface but it is %s',
                is_object($this->registry) ? get_class($this->registry) : gettype($this->registry)
            ));
        }

        if (false == $this->tokenStorage instanceof StorageInterface) {
            throw new RuntimeException(sprintf(
                'Token storage must be instance of StorageInterface interface but it is %s',
                is_object($this->registry) ? get_class($this->registry) : gettype($this->registry)
            ));
        }

        $this->httpRequestVerifier = new PlainHttpRequestVerifier($this->tokenStorage);
    }

    public function captureController()
    {
        $token = $this->httpRequestVerifier->verify($_REQUEST);
        $payment = $this->registry->getPayment($token->getPaymentName());

        $payment->execute($status = new BinaryMaskStatusRequest($token));
        if (false == $status->isNew()) {
            header('HTTP/1.1 400 Bad Request', true, 400);
            exit;
        }

        if ($interactiveRequest = $payment->execute(new SecuredCaptureRequest($token), true)) {
            if ($interactiveRequest instanceof RedirectUrlInteractiveRequest) {
                Yii::app()->request->redirect($interactiveRequest->getUrl());
                Yii::app()->end();
            }

            throw new \LogicException('Unsupported interactive request', null, $interactiveRequest);
        }

        $this->httpRequestVerifier->invalidate($token);
        
        Yii::app()->request->redirect($token->getAfterUrl());
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
        return $this->httRequestVerifier;
    }

    /**
     * @return RegistryInterface
     */
    public function getRegistry() {
        return $this->registry;
    }
}
