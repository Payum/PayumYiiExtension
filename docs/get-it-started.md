# Get it started

1. Unzip yii archive and put the payum extension to extensions folder.

2. Edit the extension in `config/main.php`:

```php
<?php
// config/main.php

use Buzz\Client\Curl;
use Payum\Extension\StorageExtension;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory;
use Payum\Storage\FilesystemStorage;

return array(
    'components' => array(
        'payum' => array(
            'class' => 'Payum\YiiExtension\PayumComponent',
            'token_storage' => new FilesystemStorage(__DIR__.'/../../data', 'Payum\Model\Token'),
            'payments' => array(
                'paypal' => PaymentFactory::create(new Api(new Curl(), array(
                    'username' => 'REPLACE WITH YOURS',
                    'password' => 'REPLACE WITH YOURS',
                    'signature' => 'REPLACE WITH YOURS',
                    'sandbox' => true
                )))
            ),
            'storages' => array(
                'paypal' => array(
                    'Payum\Paypal\ExpressCheckout\Model\PaymentDetails' => new FilesystemStorage(
                        __DIR__.'/../../data',
                        'Payum\Paypal\ExpressCheckout\Model\PaymentDetails'
                    ),
                )
            )
        ),
    ),
);
```

 _**Note**: Here we use paypal as example. You can configure any other payment similar way._
 
4. Использование:

```php
<?php
 
use Buzz\Client\Curl;
use Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
 
class PaymentController extends Controller {
 
    public function actionIndex() {
 
        $payum = $this->getPayum();
 
        $tokenStorage = $payum->getTokenStorage();
        $storage = $payum->getStorage();
 
        $paymentDetails = $storage->createModel();
        $paymentDetails['PAYMENTREQUEST_0_CURRENCYCODE'] = 'USD';
        $paymentDetails['PAYMENTREQUEST_0_AMT'] = 1.23;
        $storage->updateModel($paymentDetails);
 
        $doneToken = $tokenStorage->createModel();
        $doneToken->setPaymentName('paypal');
        $doneToken->setDetails($storage->getIdentificator($paymentDetails));
 
        $doneToken->setTargetUrl($this->createAbsoluteUrl('payment/done', array('payum_token' => $doneToken->getHash())));
        $tokenStorage->updateModel($doneToken);
 
        $captureToken = $tokenStorage->createModel();
        $captureToken->setPaymentName('paypal');
        $captureToken->setDetails($storage->getIdentificator($paymentDetails));
        $captureToken->setTargetUrl($this->createAbsoluteUrl('payment/capture', array('payum_token' => $captureToken->getHash())));
        $captureToken->setAfterUrl($doneToken->getTargetUrl());
        $tokenStorage->updateModel($captureToken);
 
        $paymentDetails['RETURNURL'] = $captureToken->getTargetUrl();
        $paymentDetails['CANCELURL'] = $captureToken->getTargetUrl();
        $storage->updateModel($paymentDetails);
 
        $this->redirect($captureToken->getTargetUrl());
    }
 
    public function actionCapture() {
        $this->getPayum()->captureRequest();
    }
 
    public function actionDone() {
        
        $result = $this->getPayum()->donePayment();
        echo $result;
    }
 
    private function getPayum() {
        $payum = Yii::app()->payum;
        $detailsClass = 'PaymentDetailsModel';
        $paymentFactory = PaymentFactory::create(new Api(new Curl, $payum->payments_details['paypal']));
        $payum->initRegistry($detailsClass, $paymentFactory, 'paypal');
 
        return $payum;
    }
 
}
```