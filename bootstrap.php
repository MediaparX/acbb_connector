<?php
/**
 * include required files
 * @todo autoload if we end up having more classes
 */

error_reporting(E_ALL);
ini_set('display_errors', 'on');

define('BASE_PATH', dirname(__FILE__));

// define error logging
ini_set('log_errors', 'on');
ini_set('error_log', BASE_PATH . '/error_log');

require_once BASE_PATH . '/config.php';
require_once BASE_PATH . '/src/BitBucketPostParser.php';
require_once BASE_PATH . '/src/ActiveCollabConnector.php';
require_once BASE_PATH . '/src/CommitAction.php';
require_once BASE_PATH . '/src/HttpUtils.php';