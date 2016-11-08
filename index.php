<?php
require_once 'vendor/autoload.php';

use Paycom\Application;

// load configuration
$paycomConfig = require_once 'paycom.config.php';

$application = new Application($paycomConfig);
$application->run();
