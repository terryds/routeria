<?php

class Task
{
	private $callback;
	private $controller;
	private $action;
	private $args;

	public function __construct($callback, $args)
	{
		$this->callback = $callback;
		$this->args = $args;
	}

	public function run(Dispatcher $dispatcher)
	{
		$this->callback($dispatcher, $this->args);
	}

	public function getArgument($name)
	{
		return $this->args[$name];
	}
}