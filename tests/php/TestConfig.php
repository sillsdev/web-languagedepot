<?php

ini_set('xdebug.show_exception_trace', 0);

$testPath = realpath(__DIR__);
define('TEST_PATH', $testPath);
define('SRC_PATH', realpath($testPath . '/../../src'));

require_once (SRC_PATH . '/Config.php');

require_once SRC_PATH . '/vendor/autoload.php';
