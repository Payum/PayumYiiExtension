# Get it started

We assume you already [install the extension](installation.md) correctly. Here we continue our journey.

## Configuration

First add some models:

```php
<?php
//app/models/PaymentDetails.php

class PaymentDetails  extends \Payum\Core\Model\ArrayObject
{
    protected $id;
}
```

The other one is `PaymentSecurityToken`.
We will use it to secure our payment operations:

```php
<?php
//app/models/PaymentSecurityToken.php

class PaymentSecurityToken  extends \Payum\Core\Model\Token
{
}
```

_**Note**: We provide Doctrine ORM\MognoODM mapping for the ArrayObject models too.

In the _config/main.php_ or _config/console.php_ (web or cli app) you have to configure payum extensions.
In general you define model storages and payments.
Your configuration may look like this:

```php
<?php
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
            'tokenStorage' => new FilesystemStorage(__DIR__.'/../data', 'PaymentSecurityToken', 'hash'),
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
                'PaymentDetails' => new FilesystemStorage(__DIR__.'/../data', 'PaymentDetails', 'id'),
            )
        ),
    ),
);
```

_**Note**: We use paypal as an example. You may configure any other payment you may want._
 
## Usage

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
        $paymentName = 'paypal_ec';

        $payum = $this->getPayum();

        $storage = $payum->getRegistry()->getStorage(
            'PaymentDetails',
            $paymentName
        );

        $details = $storage->create();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = 'USD';
        $details['PAYMENTREQUEST_0_AMT'] = 1.23;
        $storage->update($details);
        
        $captureToken = $payum->getTokenFactory()->createCaptureToken($paymentName, $details, 'paypal/done');

        $this->redirect($captureToken->getTargetUrl());
    }

    public function actionDone()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $payment = $this->getPayum()->getRegistry()->getPayment($token->getPaymentName());

        $status = new \Payum\Core\Request\GetHumanStatus($token);
        $payment->execute($status);

        echo CHtml::tag('h3', array(), 'Payment status is ' . $status->getStatus());
        echo CHtml::tag('pre', array(), json_encode(iterator_to_array($status->getModel()), JSON_PRETTY_PRINT));
        Yii::app()->end();
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
