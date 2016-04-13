<?php

namespace Routeria;

class Routeria
{
	private $collection;
	private $ioc;
	private $parser;

	private $begin;

	public function __construct($ioc=null)
	{
		$this->collection = new RouteCollection;
		$this->parser = new URIRouteParser($this->collection);
		if ($ioc !== null && $ioc instanceOf ION\Container)
		{
			$this->ioc = $ioc;
		}
	}

	public function register(RouteProviderInterface $provider)
	{
		$this->collection->register($provider);
	}

	public function with($namespace, $controller, array $options) {
		foreach ($options as $request => $method) {
			$http_method = 'GET';
			if (strpos($request, ' ') !== FALSE) {
				list($sub, $http_method) = explode(' ', $request);
			}
			$pattern = $namespace . '/' . $sub;
			$route = ucfirst($controller) . '::' . $method;
			$this->add($pattern, $route, $http_method);
		}
	}

	public function add($pattern, $route, $http_method)
	{
		if (is_string($route)) {
			$route = new ControllerRoute($pattern, $route, strtoupper($http_method));
			$this->collection->add($route);
		}
		elseif (is_callable($route)) {
			$route = new CallbackRoute($pattern, $route, strtoupper($http_method));
			$this->collection->add($route);
		}
		return $route;
	}

	public function get($pattern, $route)
	{
		if (is_string($route)) {
			$route = new ControllerRoute($pattern, $route, 'GET');
			$this->collection->add($route);
		}
		elseif (is_callable($route)) {
			$route = new CallbackRoute($pattern, $route, 'GET');
			$this->collection->add($route);
		}	
		return $route;	
	}

	public function post($pattern, $route)
	{
		if (is_string($route)) {
			$route = new ControllerRoute($pattern, $route, 'POST');
			$this->collection->add($route);
		}
		elseif (is_callable($route)) {
			$route = new CallbackRoute($pattern, $route, 'POST');
			$this->collection->add($route);
		}
		return $route;
	}

	public function put($pattern, $route)
	{
		if (is_string($route)) {
			$route = new ControllerRoute($pattern, $route, 'PUT');
			$this->collection->add($route);
		}
		elseif (is_callable($route)) {
			$route = new CallbackRoute($pattern, $route, 'PUT');
			$this->collection->add($route);
		}
		return $route;
	}

	public function delete($pattern, $route)
	{
		if (is_string($route)) {
			$route = new ControllerRoute($pattern, $route, 'DELETE');
			$this->collection->add($route);
		}
		elseif (is_callable($route)) {
			$route = new CallbackRoute($pattern, $route, 'DELETE');
			$this->collection->add($route);
		}
		return $route;
	}

	public function route($request, $method='GET')
	{
		$route = $this->parser->parse($request, $method);
		$route->run($this->parser->getParams());
	}
}