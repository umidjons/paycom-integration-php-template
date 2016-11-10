# Paycom integration template

## Installation via Composer

Change current directory to your project folder and install package:
```
cd my-shop-project
composer require paycom/integration-template
```

From now you can use classes from package as following:
```php
<?php
// File `my-shop-project/api/index.php`
require_once '../vendor/autoload.php';

use Paycom\Application;

// load configuration
$paycomConfig = require_once 'paycom.config.php';

$application = new Application($paycomConfig);
$application->run();
```

Make copy of `paycom.config.sample.php` as `paycom.config.php` and set your real settings there.

Assuming your domain as `https://myshop.uz`,
now you can set entry point to handle requests from Paycom as `https://myshop.uz/api/index.php`.
