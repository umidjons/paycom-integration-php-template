# NOTE

Please, use the following up to date repo instead https://github.com/PaycomUZ/paycom-integration-php-template

# Paycom integration template

## Prerequisites

- `PHP 5.4` or greater
- [`PDO`](http://php.net/manual/en/book.pdo.php) extension
- [`Composer`](https://getcomposer.org/download/) dependency manager

## Installation via Composer

Change current directory to your project folder and install package:
```
cd my-shop-project
composer create-project paycom/integration-template
```

## Installation via Git
```
git clone https://github.com/umidjons/paycom-integration-php-template.git
cd paycom-integration-php-template
composer dumpautoload
```

From now you can use classes from package as following:
```php
<?php
// File `my-shop-project/api/index.php`
require_once 'vendor/autoload.php';

use Paycom\Application;

// load configuration
$paycomConfig = require_once 'paycom.config.php';

$application = new Application($paycomConfig);
$application->run();
```

Make copy of `paycom.config.sample.php` as `paycom.config.php` and set your real settings there.

Assuming your domain as `https://myshop.uz`,
now you can set entry point URL to handle requests from Paycom as `https://myshop.uz/api/index.php`.
