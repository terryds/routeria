<?php

namespace Routeria\Dispatch;
use Routeria\RouterInterface;

abstract class Dispatch implements DispatchInterface
{
	abstract public function dispatch(RouterInterface $router);
	public function __invoke($router)
	{
		$this->dispatch($router);
	}
}