<?php

$rootPath = realpath(__DIR__ . '/../..') . DIRECTORY_SEPARATOR;

if (! defined('SRC_PATH')) {
    define('SRC_PATH', $rootPath . 'src/');
}

if (!defined('TEST_PATH')) {
    define('TEST_PATH', $rootPath . 'tests/php/');
}

require_once (SRC_PATH . '/Config.php');

require_once SRC_PATH . '/vendor/autoload.php';

require_once TEST_PATH . 'TestEnvironment.php';