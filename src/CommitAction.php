<?php

class CommitAction
{
	public $action;
	public $id;
	public $link;
	public $message;
	public $author;
	public $repo;

	/**
	 * Detect if this commit is an actual change
	 * Currently detects if it's a branch merge but we can add other checks here
	 * if necessary
	 * @return boolean
	 */
	public function isActualChange()
	{
		$isChange = true;
		// check if it's a merge
		if (strpos($this->message, 'Merge branch') === 0) {
			$isChange = false;
		}
		return $isChange;
	}

	/**
	 * Get commit message in html
	 * @return string
	 */
	public function getHtmlMessage() {
		return nl2br($this->message);
	}
}