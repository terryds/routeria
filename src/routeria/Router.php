<?php

namespace Routeria;

class Router implements RouterInterface
{
	private $routes = array();

	private $ioc;

	public function __construct(RouteProviderInterface $provider)
	{
		$provider->register($this);
	}

	public function setIOC($ioc)
	{
		if ($ioc instanceOf Ion\Container)
		{
		 	$this->ion = $ioc;
		}
		else
		{
			throw new \InvalidArgumentException('The inversion of control class must be an instance of Ion\Container');
		}
	}

	public function add($pattern, $route, $method)
	{
		$this->routes[] = new Route($pattern, $route, $method);
	}

	public function get($pattern, $route)
	{
		$this->routes[] = new Route($pattern, $route, 'GET');
	}

	public function post($pattern, $route)
	{
		$this->routes[] = new Route($pattern, $route, 'POST');
	}

	public function put($pattern, $route)
	{
		$this->routes[] = new Route($pattern, $route, 'PUT');
	}

	public function delete($pattern, $route)
	{
		$this->routes[] = new Route($pattern, $route, 'DELETE');
	}

	public function getRoute($path, $httpMethod = 'GET')
	{
		$routes = $this->routes;
		foreach ($routes as $route)
		{
			if (preg_match($route->getPattern(), $path, $matches) && in_array($httpMethod, $route->getHttpMethods() ))
			{
				unset($matches[0]);
				if($converters = $route->getConverters()) {
					foreach ($converters as $param => $converter) {
						if (array_key_exists($param, $matches)) {
							$matches[$param] = $converter($matches[$param]);
						}				
					}
				}
				return new Task($route->getCallback(), $matches);
			}
		}
		return FALSE;
	}
}