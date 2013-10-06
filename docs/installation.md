# Installation

The preferred way to install the extension is using [composer](http://getcomposer.org/).
Add next lines to your _composer.json_ file:

```json
{
    "required": {
        "payum/payum-yii-extension": "0.6.*@dev",
        "payum/paypal-express-checkout-nvp": "0.6.*@dev"
    }
}
```

Then run composer update:

```bash
php composer.phar update payum/payum-yii-extension payum/paypal-express-checkout-nvp
```

Now you have all required code downloaded.
Next step would be to configure composer autoloader.
You have to register it inside _www/index.php_ and _app/yiic.php_ to make both cli and web working.

```php
<?php
//app/yiic.php

//put it at the beginning of the file
require_once(dirname(__FILE__).'/../vendor/autoload.php');

// ...
```

```php
<?php
//www/index.php

//put it at the beginning of the file
require_once(dirname(__FILE__).'/../vendor/autoload.php');

// ...
```

Yii use aliasing system so we have to register alias for payum extension namespace:

```php
<?php
//app/config/main.php

Yii::setPathOfAlias(
    'Payum.YiiExtension',
    dirname(__FILE__).'/../../vendor/payum/payum-yii-extension/src/Payum/YiiExtension'
);
```

Now you are ready to [get it started](get-it-started.md).

Back to [index](index.md).