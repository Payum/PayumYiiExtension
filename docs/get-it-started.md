1. Распаковать архив, положить папку PayumYii в папку extensions
 
2. Исправить config/main.php, добавть payum в components :

```
'components' => array(
        'payum' => array(
            'class' => 'PayumYiiComponent',
            'storage_path' => '/path/to/storage',
            'payments_details' => array (
                'paypal' => array(
                    'username' => 'PAYPAL_USERNAME',
                    'password' => 'PAYPAL_PASSWORD',
                    'signature' => 'PAYPAL_SIGN',
                    'sandbox' => true
                )
            )
        ),
)
```
 
3. в components создать файл PaymentDetailsModel.php :

```
<?php
 
use Payum\Paypal\ExpressCheckout\Nvp\Model\PaymentDetails;
 
 
class PaymentDetailsModel extends PaymentDetails
{
    protected $id;
}
 
4. Использование:
 
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