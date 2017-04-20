<?php

$rootPath = realpath(__DIR__ . '/../..') . DIRECTORY_SEPARATOR;

if (! defined('API_PATH')) {
    define('API_PATH', $rootPath . 'src/api/');
}

if (!defined('API_TEST_PATH')) {
    define('API_TEST_PATH', $rootPath . 'tests/api/');
}

require_once API_PATH . '/../vendor/autoload.php';

require_once API_TEST_PATH . '/ApiTestEnvironment.php';