<?php

$config = array(
	'debug'          => false,
	'api_base_url'   => '',
	'api_user_token' => '',
	'ip_whitelist'   => array(
		// these are the ips bitbucket says to whitelist on the edit-webhook page
		'131.103.20.160/27',
		'165.254.145.0/26',
		'104.192.143.0/24'
		),
	'label_id_map'   => array(
		'solved' => 1,
		),
	'repo_project_map' => array(
		),
	'texts' => array(
		'reference_task_msg' => '%1$s<p>by %2$s in this <a href="%3$s">BitBucket commit</a></p>',
		'solve_task_msg'     => '<p>%1$s</p><ul><li>reassigned to %2$s</li><li>fixed by %3$s in this <a href="%4$s">BitBucket commit</a></li></ul>',
		),
	);

return $config;