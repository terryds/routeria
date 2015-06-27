<?php
namespace Routeria;

class Dispatcher implements DispatcherInterface
{
	private $router;

	public function __construct(RouterInterface $router)
	{
		$this->router = $router;
	}

	public function dispatch()
	{
		$callback = $this->router->getCallback();
		$callback($this->router);
	}
}