<?php

$rootPath = realpath(__DIR__ . '/../..') . DIRECTORY_SEPARATOR;

if (! defined('SRC_PATH')) {
    define('SRC_PATH', $rootPath . 'src/');
}

if (!defined('TEST_PATH')) {
    define('TEST_PATH', $rootPath . 'test/php/');
}

if (!defined('API_TEST_PATH')) {
    define('API_TEST_PATH', $rootPath . 'test/php/api/');
}

require_once (SRC_PATH . '/Config.php');

require_once SRC_PATH . '/vendor/autoload.php';

require_once TEST_PATH . 'TestEnvironment.php';

require_once API_TEST_PATH . 'ApiTestEnvironment.php';
