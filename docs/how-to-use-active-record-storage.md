# How to use active record storage

## Configuration

_**Note**: We assume you already [install the extension](installation.md) correctly._

In the _app/config/main.php_ you have to configure payum extensions.
In general you define model storages and payments.
Your configuration may look like this:

```php
<?php
// config/main.php

use Buzz\Client\Curl;
use Payum\Extension\StorageExtension;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory;

return array(
    'controllerMap'=>array(
        'payment'=>array(
            'class'=>'Payum\YiiExtension\PaymentController',
        ),
    ),
    'components' => array(
        'payum' => array(
            'class' => '\Payum\YiiExtension\PayumComponent',
            'tokenStorage' => new Payum\YiiExtension\Storage\ActiveRecordStorage(
                'TOKEN_TABLE_NAME', 'Payum\YiiExtension\Model\PaymentSecurityToken'
            ),
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
                    'Payum\Paypal\ExpressCheckout\Nvp\Model\PaymentDetails' => new Payum\YiiExtension\Storage\ActiveRecordStorage(
                        'PAYMENT_DETAILS_TABLE_NAME',
                        'Payum\YiiExtension\Model\PaymentDetailsActiveRecordWrapper'
                    ),
                )
            )
        ),
    ),
);
```
You will need to create database tables for token storage and payment details storage.

The token storage table can be created in a MySQL database with the following SQL:

```
--
-- Table structure for table `TOKEN_TABLE_NAME`
--

CREATE TABLE IF NOT EXISTS `TOKEN_TABLE_NAME` (
  `_hash` varchar(255) NOT NULL DEFAULT '',
  `_payment_name` varchar(255) DEFAULT NULL,
  `_details` varchar(255) DEFAULT NULL,
  `_after_url` varchar(255) DEFAULT NULL,
  `_target_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

The payment details table can be created as follows:
```
--
-- Table structure for table `PAYMENT_DETAILS_TABLE_NAME`
--

CREATE TABLE IF NOT EXISTS `PAYMENT_DETAILS_TABLE_NAME` (
  `_id` int(11) NOT NULL AUTO_INCREMENT,
  `_details` text,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
```
The same table can be used to store payment details for multiple payment methods (e.g. Paypal, AuthorizeNet, etc.)
The payment details are serialized and stored in _details.

_**Note**: We use paypal as an example. You may configure any other payment you want._
 
## Usage
(The following code is unchanged from using FileSystemStorage - implementing ActiveRecordStorage
doesn't require changes to your code).
Here is the class you want start with.
The prepare action create payment details model.
Fill it with amount and currency and creates some tokens.
At the end it redirects you to payum capture controller which does all the job.
The done action is your landing action after the capture is finished.
Here you want to check the status and do your business actions depending on it.

```php
<?php
//app/controllers/PaypalController.php

class PaypalController extends CController
{
    public function actionPrepare()
    {
        $paymentName = 'paypal';

        $payum = $this->getPayum();

        $tokenStorage = $payum->getTokenStorage();
        $paymentDetailsStorage = $payum->getRegistry()->getStorageForClass(
            'Payum\Paypal\ExpressCheckout\Nvp\Model\PaymentDetails',
            $paymentName
        );

        $paymentDetails = $paymentDetailsStorage->createModel();
        $paymentDetails['PAYMENTREQUEST_0_CURRENCYCODE'] = 'USD';
        $paymentDetails['PAYMENTREQUEST_0_AMT'] = 1.23;
        $paymentDetailsStorage->updateModel($paymentDetails);

        $doneToken = $tokenStorage->createModel();
        $doneToken->setPaymentName($paymentName);
        $doneToken->setDetails($paymentDetailsStorage->getIdentificator($paymentDetails));
        $doneToken->setTargetUrl(
            $this->createAbsoluteUrl('paypal/done', array('payum_token' => $doneToken->getHash()))
        );
        $tokenStorage->updateModel($doneToken);

        $captureToken = $tokenStorage->createModel();
        $captureToken->setPaymentName('paypal');
        $captureToken->setDetails($paymentDetailsStorage->getIdentificator($paymentDetails));
        $captureToken->setTargetUrl(
            $this->createAbsoluteUrl('payment/capture', array('payum_token' => $captureToken->getHash()))
        );
        $captureToken->setAfterUrl($doneToken->getTargetUrl());
        $tokenStorage->updateModel($captureToken);

        $paymentDetails['RETURNURL'] = $captureToken->getTargetUrl();
        $paymentDetails['CANCELURL'] = $captureToken->getTargetUrl();
        $paymentDetailsStorage->updateModel($paymentDetails);

        $this->redirect($captureToken->getTargetUrl());
    }

    public function actionDone()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $payment = $this->getPayum()->getRegistry()->getPayment($token->getPaymentName());

        $payment->execute($status = new \Payum\Request\BinaryMaskStatusRequest($token));

        $content = '';
        if ($status->isSuccess()) {
            $content .= '<h3>Payment status is success.</h3>';
        } else {
            $content .= '<h3>Payment status IS NOT success.</h3>';
        }

        $content .= '<br /><br />'.json_encode(iterator_to_array($status->getModel()), JSON_PRETTY_PRINT);

        echo '<pre>',$content,'</pre>';
        exit;
    }

    /**
     * @return \Payum\YiiExtension\PayumComponent
     */
    private function getPayum()
    {
        return Yii::app()->payum;
    }
}
```

Back to [index](index.md).