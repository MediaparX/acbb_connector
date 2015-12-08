<?php
/**
 * this is the file that should be called by the bitbucket post-commit hook
 * it expects a payload of json data
 */

require_once dirname(__FILE__) . '/bootstrap.php';

try {
	$input = file_get_contents('php://input');

	// parse incoming data
	$parser = new BitBucketPostParser();
	$parser->parse($input);

	// import to active collab
	$connector = new ActiveCollabConnector($config);
	$connector->import($parser);
}
catch (Exception $e) {
	// @todo log errors
	die($e->getMessage());
}