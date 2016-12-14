<?php
// Enable to debug
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once 'vendor/autoload.php';
require_once 'functions.php';

use Paycom\Application;

// load configuration
$paycomConfig = require_once 'paycom.config.php';

$application = new Application($paycomConfig);
$application->run();
