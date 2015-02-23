<?php
namespace Payum\YiiExtension;

\Yii::import('Payum\YiiExtension\TokenFactory', true);

use Payum\Core\Bridge\PlainPhp\Security\HttpRequestVerifier;
use Payum\Core\PaymentInterface;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Registry\SimpleRegistry;
use Payum\Core\Security\GenericTokenFactory;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
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
     * @var GenericTokenFactoryInterface
     */
    public $tokenFactory;

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
        $this->registry = new SimpleRegistry($this->payments, $this->storages, array());

        $this->httpRequestVerifier = new HttpRequestVerifier($this->tokenStorage);
        $this->tokenFactory = new GenericTokenFactory(new TokenFactory($this->tokenStorage, $this->registry), array(
            'capture' => 'payment/capture',
            'notify' => 'payment/notify',
            'authorize' => 'payment/authorize',
            'refund' => 'payment/refund'
        ));
    }

    /**
     * @return StorageInterface
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @return GenericTokenFactoryInterface
     */
    public function getTokenFactory()
    {
        return $this->tokenFactory;
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
