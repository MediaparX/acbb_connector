<?php

class ActivecollabConnector
{
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
	public function getApiUrl() 
	{
		return sprintf('%s?auth_api_token=%s', 
			$this->getConfig('api_base_url'),
			$this->getConfig('api_user_token'));
	}

	/**
	 * @param BitbucketPostParser $parser
	 */
	public function import(BitbucketPostParser $parser) 
	{
		$matches = $parser->getCommits();
		foreach ($matches as $commit) {
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
	}

	/**
	 * @param CommitAction $commit
	 */
	public function solveTask(CommitAction $commit)
	{
		$url = sprintf('%s&path_info=projects/%s/tasks/%d/edit',
			$this->getApiUrl(),
			$this->getProject($commit->repo),
			$commit->id
			);
		$data = array(
			'submitted'      => 'submitted',
			'task[label_id]' => $this->getConfig('label_id_map')['solved'],
			);

		$message = sprintf('Fixed by %s in <a href="%s">BitBucket commit</a>', 
			$commit->author,
			$commit->link);

		$this->post($url, $data);
	}

	/**
	 * @param CommitAction $commit
	 */
	public function referenceTask(CommitAction $commit)
	{
		$message = sprintf('%s<p>by %s in <a href="%s">BitBucket commit</a></p>',
			$commit->message,
			$commit->author,
			$commit->link
		);
		$this->commentTask($commit->id, $this->getProject($commit->repo), $message);
	}

	/**
	 * @param integer $taskId
	 * @param string  $project
	 * @param string  $message
	 */
	public function commentTask($taskId, $project, $message)
	{
		$url = sprintf('%s&path_info=projects/%s/tasks/%d/comments/add',
			$this->getApiUrl(),
			$project,
			$taskId
			);
		$data = array(
			'submitted'     => 'submitted',
			'comment[body]' => $message,
			);
		$this->post($url, $data);
	}

	/**
	 * @param  string $key
	 * @return array
	 */
	private function getConfig($key) 
	{
		return $this->config[$key];
	}

	/**
	 * @param  string $repo
	 * @return string
	 */
	private function getProject($repo) 
	{
		return $this->config['repo_project_map'][$repo];
	}

	/**
	 * @param  string $label
	 * @return integer
	 */
	private function getLabelId($label) 
	{
		return $this->config['label_id_map'][$label];
	}

	/**
	 * @param  string $url
	 * @param  array $data
	 * @return mixed Response
	 */
	private function post($url, array $data) 
	{
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_POST => count($data),
			CURLOPT_POSTFIELDS => http_build_query($data),
			// CURLOPT_VERBOSE => 1,
			// CURLOPT_HEADER => 1,
			// CURLOPT_RETURNTRANSFER => 1,
			));
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
}