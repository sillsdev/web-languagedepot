<?php

ini_set('xdebug.show_exception_trace', 0);

$apiPath = realpath(__DIR__ . '/../../src/api');
$testPath = realpath(__DIR__);
define('API_PATH', $apiPath);
define('API_TEST_PATH', $testPath);

require_once API_PATH . '/../vendor/autoload.php';
