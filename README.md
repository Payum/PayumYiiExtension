# Payum Yii Extension

The extension integrates [payum](http://payum.org/doc#Payum) into [yii](http://www.yiiframework.com/) framework.
It already supports [+35 payments](https://github.com/Payum/Core/blob/master/Resources/docs/supported-payments.md).
Provide nice configuration layer, secured capture controller, storages and lots of other features.

## Resources

* [Documentation](http://payum.org/doc/0.15#PayumYiiExtension)
* [Forum](http://www.yiiframework.com/forum/index.php/topic/48571-payum-payment-extension/)
* [Sandbox](https://github.com/makasim/PayumYiiExtensionSandbox)
* [Questions](http://stackoverflow.com/questions/tagged/payum)
* [Issue Tracker](https://github.com/Payum/PayumYiiExtension/issues)

## DB storage
```php
// app/config/main.php
use Payum\Core\Storage\FilesystemStorage;

$paypalExpressCheckoutPaymentFactory = new \Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory();

return array(
    'controllerMap'=>array(
        'payment'=>array(
            'class'=>'Payum\YiiExtension\PaymentController',
        ),
    ),
    'components' => array(
        'payum' => array(
            'class' => '\Payum\YiiExtension\PayumComponent',
            'tokenStorage' => new Payum\YiiExtension\Model\CActiveRecordStorage('Payum\YiiExtension\Model\PaymentToken'),'PaymentSecurityToken', 'hash'),
            'payments' => array(
                // you can add other payments here.
                'paypal_ec' => $paypalExpressCheckoutPaymentFactory->create(array(
                    'username' => 'EDIT ME',
                    'password' => 'EDIT ME',
                    'signature' => 'EDIT ME',
                    'sandbox' => true
                )),
            ),
            'storages' => array(
                'Payum\YiiExtension\Model\PaymentDetails' => new Payum\YiiExtension\Model\CActiveRecordStorage('Payum\YiiExtension\Model\PaymentDetails'),
            )
        ),
    ),
);
```

```php
<?php
//app/controllers/PaypalController.php

class PaypalController extends CController
{
    public function actionPrepare()
    {
        $paymentName = 'paypal_ec';

        $payum = $this->getPayum();

        $storage = $payum->getRegistry()->getStorage(
            'Payum\YiiExtension\Model\PaymentDetails',
            $paymentName
        );
    }
}
```

## Contributing

PayumYiiExtension is an open source, community-driven project. Pull requests are very welcome.

## Like it? Spread the world!

Star it on [github](https://github.com/Payum/PayumYiiExtension) or [packagist](https://packagist.org/packages/payum/payum-yii-extension).
You may also drop a message on Twitter.

## License

PayumYiiExtension is released under the [MIT License](LICENSE).
