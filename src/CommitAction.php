<?php

class CommitAction
{
	public $action;
	public $id;
	public $link;
	public $message;

	public function __construct($action, $id, $link, $author, $message = '')
	{
		$this->action  = $action;
		$this->id      = $id;
		$this->link    = $link;
		$this->author  = $author;
		$this->message = $message;
	}
}