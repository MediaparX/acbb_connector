<?php
/**
 * include required files
 * @todo autoload if we end up having more classes
 */

error_reporting(E_ALL);
ini_set('display_errors', 'on');

define('BASE_PATH', dirname(__FILE__));

require_once BASE_PATH . '/config.php';
require_once BASE_PATH . '/src/BitBucketPostParser.php';
require_once BASE_PATH . '/src/ActiveCollabConnector.php';
require_once BASE_PATH . '/src/CommitAction.php';