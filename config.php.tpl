<?php

$config = array(
	'debug'          => false,
	'api_base_url'   => '',
	'api_user_token' => '',
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