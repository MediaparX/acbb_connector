<?php

class ActiveCollabConnector
{
	/**
	 * @const integer
	 */
	const COMMENT_EXISTS = 1001;

	/**
	 * @var array
	 */
	private $config;

	/**
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config  = $config;
	}

	/**
	 * @return string
	 */
	public function getApiUrl($path)
	{
		return sprintf('%s?auth_api_token=%s&path_info=%s', 
			$this->getConfig('api_base_url'),
			$this->getConfig('api_user_token'),
			$path
			);
	}

	/**
	 * @param BitbucketPostParser $parser
	 */
	public function import(BitBucketPostParser $parser) 
	{
		$matches = $parser->getCommits();
		foreach ($matches as $commit) {
			// debug
			if ($this->getConfig('debug') === true) {
				var_dump($commit);
			}

			// handle commit action
			try {
				switch ($commit->action) {
					case 'fixes':
					case 'solves':
						$this->solveTask($commit);
						break;

					case 'refs':
						$this->referenceTask($commit);
						break;
				}
			}
			catch (Exception $e) {
				if ($e->getCode() == self::COMMENT_EXISTS) {
					if ($this->getConfig('debug') === true) {
						print 'comment already exists<br>';
					}
					continue;
				}
			}

		}
	}

	/**
	 * @param CommitAction $commit
	 */
	public function solveTask(CommitAction $commit)
	{
		// build urls
		$taskUrl = $this->getApiUrl(sprintf('projects/%s/tasks/%d',
			$this->getConfig($commit->repo, 'repo_project_map'),
			$commit->id
			));
		$taskUpdateUrl = $taskUrl . '/edit';

		// get existing task
		$task = $this->get($taskUrl);

		$data = array(
			'submitted'         => 'submitted',
			'task[label_id]'    => $this->getConfig('solved', 'label_id_map'),
			// reassign task to user who delegated it
			'task[assignee_id]' => $task->delegated_by->id,
			);

		$message = sprintf($this->getConfig('solve_task_msg', 'texts'), 
			$commit->message,
			$commit->author,
			$task->delegated_by->name,
			$commit->link
			);

		// add comment and update task
		$this->commentTask($commit->id, $this->getConfig($commit->repo, 'repo_project_map'), $message);
		$this->post($taskUpdateUrl, $data);
	}

	/**
	 * @param CommitAction $commit
	 */
	public function referenceTask(CommitAction $commit)
	{
		$message = sprintf($this->getConfig('reference_task_msg', 'texts'),
			$commit->message,
			$commit->author,
			$commit->link
		);
		$this->commentTask($commit->id, $this->getConfig($commit->repo, 'repo_project_map'), $message);
	}

	/**
	 * @throws Exception When comment already exists
	 * @param integer $taskId
	 * @param string  $project
	 * @param string  $message
	 */
	public function commentTask($taskId, $project, $message)
	{
		// build urls
		$url = $this->getApiUrl(sprintf('projects/%s/tasks/%d/comments',
			$project,
			$taskId
			));
		$addUrl = $url . '/add';

		// get pre-existing comments for this task
		$comments = $this->get($url);
		// if we already commented the same comment on this task, abort
		// this occurs e.g. when merging and pushing branches
		foreach($comments as $comment) {
			if ($comment->body == $message) {
				throw new Exception('Comment already exists for this task', self::COMMENT_EXISTS);
			}
		}

		// post data to api
		$data = array(
			'submitted'     => 'submitted',
			'comment[body]' => $message,
			);
		$this->post($addUrl, $data);

		if ($this->getConfig('debug') === true) {
			print $message . '<br>';
		}
	}

	/**
	 * @param  string $key
	 * @param  string $parent
	 * @return array
	 */
	private function getConfig($key, $parent = null) 
	{
		if (!is_null($parent)) {
			return $this->config[$parent][$key];	
		}
		else {
			return $this->config[$key];
		}
	}

	/**
	 * @param  string $url
	 * @param  array $data
	 * @return mixed Response
	 */
	public function post($url, array $data) 
	{
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_POST => count($data),
			CURLOPT_POSTFIELDS => http_build_query($data),
			// CURLOPT_VERBOSE => 1,
			// CURLOPT_HEADER => 1,
			CURLOPT_RETURNTRANSFER => 1,
			));
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	/**
	 * @throws Exception when API returns invalid response
	 * @param  string $url
	 * @return mixed  Parsed response (from json)
	 */
	public function get($url) 
	{
		// make sure we get json
		$url .= '&format=json';

		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => 1,
			));
		$response = curl_exec($ch);
		curl_close($ch);
		if ($response === false) {
			throw new Exception('Invalid response from API');
		}

		$parsed = json_decode($response);
		if ($parsed === false) {
			throw new Exception('Invalid response from API: could not parse JSON');
		}
		return $parsed;
	}
}