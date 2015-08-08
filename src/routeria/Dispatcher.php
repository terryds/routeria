<?php
namespace Routeria;

class Dispatcher
{
	public function dispatch(Task $task)
	{
		$task->run($this);
	}
}