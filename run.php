<?php

// @todo
// - change assigned user on-solved
// - add config.php for all variable stuff
// - handling for project name
// - add "bot" user
// - add error logging

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once 'config.php';
require_once 'src/BitbucketPostParser.php';
require_once 'src/ActiveCollabConnector.php';

try {

	$input = file_get_contents('data.json');

	// parse incoming data
	$parser = new BitbucketPostParser();
	$parser->parse($input);

	$connector = new ActiveCollabConnector($config);
	$connector->import($parser);
}
catch (Exception $e) {
	die($e->getMessage());
}