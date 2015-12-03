<?php

class BitbucketPostParser 
{
	/**
	 * @var array
	 */
	private $commits;

	/**
	 * @throw  Exception when data cannot be parsed or nothin relevant is found
	 * @param  string $input
	 * @return void
	 */
	public function parse($input) 
	{
		$data = json_decode($input);
		if ($data === false || !isset($data->push) || !isset($data->push->changes)) {
			throw new Exception('Could not parse data');
		}

		foreach ($data->push->changes as $change) {
			foreach ($change->commits as $commit) {
				$matched = preg_match('/(fixes|refs|solves) #([\d]{1,8})/', $commit->message, $matches);
				if ($matched && count($matches) >= 2) {
					$ca = new CommitAction();

					$ca->action  = $matches[1];
					$ca->id      = $matches[2];
					$ca->link    = $commit->links->html->href;
					$ca->author  = $commit->author->user->display_name;
					$ca->repo    = $data->repository->name;
					$ca->message = str_replace($matches[0], '', $commit->message);
					
					$this->commits[] = $ca;
				}
			}
		}

		if (!count($this->commits) >= 1) {
			throw new Exception('Found no data to parse');
		}
	}

	/**
	 * @return array
	 */
	public function getCommits() 
	{
		return $this->commits;
	}
}