<?php
/**
 * this is the file that should be called by the bitbucket post-commit hook
 * it expects a payload of json data
 */

require_once dirname(__FILE__) . '/bootstrap.php';

try {
	// check if ip is whitelisted or we won't process request
	if (!HttpUtils::ipIsInRange($_SERVER['REMOTE_ADDR'], $config['ip_whitelist'])) {
		throw new Exception(sprintf('IP %s is not whitelisted', $_SERVER['REMOTE_ADDR']), 403);
	}

	$input = file_get_contents('php://input');

	// parse incoming data
	try {
		$parser = new BitBucketPostParser();
		$parser->parse($input);
	}
	// if we can't parse it, we'll send bad request to bitbucket
	catch (Exception $e) {
		throw new Exception($e->getMessage(), 400);
	}

	// import to active collab
	$connector = new ActiveCollabConnector($config);
	$connector->import($parser);
}
catch (Exception $e) {
	error_log($e->getMessage());
	$statusCode = $e->getCode() > 0 ? $e->getCode() : 500;
	HttpUtils::sendHeader($statusCode);
}