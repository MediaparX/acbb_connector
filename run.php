<?php

// @todo
// - change assigned user on-solved
// - add error logging
// - get data from php input
// - add IP whitelisting

error_reporting(E_ALL);
ini_set('display_errors', 'on');

define('BASE_PATH', dirname(__FILE__));

require_once BASE_PATH . '/config.php';
require_once BASE_PATH . '/src/BitbucketPostParser.php';
require_once BASE_PATH . '/src/ActiveCollabConnector.php';
require_once BASE_PATH . '/src/CommitAction.php';

try {
	//$input = file_get_contents('data.json');
	$input = file_get_contents('php://input');

	// parse incoming data
	$parser = new BitBucketPostParser();
	$parser->parse($input);

	$connector = new ActiveCollabConnector($config);
	$connector->import($parser);
}
catch (Exception $e) {
	die($e->getMessage());
}