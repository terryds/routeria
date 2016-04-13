<?php

namespace Routeria;

class RouteCollection implements \IteratorAggregate
{
	private $routes = array();

	public function add(RouteInterface $route)
	{
		$info = $route->getInfo();
		$this->routes[$info['pattern']] = $route;
	}

	public function addRoutes(array $routes)
	{
		foreach ($routes as $route) {
			$this->add($route);
		}
	}

	public function merge($routes)
	{
		$this->routes = array_merge($this->routes, $routes);
	}

	public function register(RouteProviderInterface $provider)
	{
		$provider->register($this);
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->routes);
	}

	public function __toString()
	{
		$data = array();
		foreach ($routes as $route) {
			$data[] = $route->getInfo();
		}
		return implode("\n", $data);
	}
}