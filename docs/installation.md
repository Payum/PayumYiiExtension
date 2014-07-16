# Installation

The preferred way to install the extension is using [composer](http://getcomposer.org/).
Create a folder named `PayumYiiExtension` in the extensions directory of your Yii project
and then create a _composer.json_ file with the following content:

```json
{
    "required": {
        "payum/payum-yii-extension": "@stable",
        "payum/paypal-express-checkout-nvp": "@stable"
    }
}
```

Then run composer update:

```bash
php composer.phar update payum/payum-yii-extension payum/paypal-express-checkout-nvp
```

Now you have all required code downloaded.
Next step would be to configure composer autoloader.
You have to register it inside _config/main.php_ or _config/console.php_ for the web and cli 
versions, respectively.

```php
<?php

//put it at the beginning of the file
Yii::setPathOfAlias('Payum', dirname(__FILE__).'/../extensions/PayumYiiExtension/vendor');
Yii::setPathOfAlias('Payum.YiiExtension', Yii::getPathOfAlias('Payum').'/payum/payum-yii-extension/src/Payum/YiiExtension');
Yii::import('Payum.autoload', true);

use Buzz\Client\Curl;
use Payum\Core\Storage\FilesystemStorage;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory;

// ...
```

Now you are ready to [get it started](get-it-started.md).

Back to [index](index.md).