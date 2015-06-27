<?php
namespace Routeria;
use Symfony\Component\HttpFoundation\Request;

class Dispatcher implements DispatcherInterface
{
	private $router;

	public function __construct(RouterInterface $router)
	{
		$this->router = $router;
	}

	public function dispatch(Request $request)
	{
		$this->router->route($request);
		$callback = $this->router->getCallback();
		$callback($this->router);
	}
}