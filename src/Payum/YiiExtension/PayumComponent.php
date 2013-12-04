<?php
namespace Payum\YiiExtension;

use Payum\Core\Exception\RuntimeException;
use Payum\Core\Extension\StorageExtension;
use Payum\Core\PaymentInterface;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Registry\SimpleRegistry;
use Payum\Core\Request\BinaryMaskStatusRequest;
use Payum\Core\Request\RedirectUrlInteractiveRequest;
use Payum\Core\Request\SecuredCaptureRequest;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Payum\Core\Security\PlainHttpRequestVerifier;
use Payum\Core\Storage\StorageInterface;

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
