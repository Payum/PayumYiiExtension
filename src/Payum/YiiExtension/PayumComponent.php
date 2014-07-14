<?php
namespace Payum\YiiExtension;

use Payum\Core\PaymentInterface;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Registry\SimpleRegistry;
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
        $this->registry = new SimpleRegistry($this->payments, $this->storages, null);

        $this->httpRequestVerifier = new PlainHttpRequestVerifier($this->tokenStorage);
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
