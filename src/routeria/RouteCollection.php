<?php
namespace Routeria;

class RouteCollection implements RouteCollectionInterface
{
	private $routes = array();

	public function add(RouteInterface $route)
	{
		$this->routes[] = $route;
		return $route;
	}

	public function addRoutes(array $routes)
	{
		foreach ($routes as $route)
		{
			$this->add($route);
		}
	}

	/**
	 * Remove the responsible route for the given path from $routes property
	 * @param  string $path       The path to seek for the responsible route
	 * @param  string $httpMethod The HTTP method to match with the route
	 * @return bool             Return true if succeed removing a route responsible for the given path, false otherwise
	 */
	public function remove($path, $httpMethod='GET')
	{
		if (!is_string($path) && !is_string($httpMethod)) {
			throw new \InvalidArgumentException(sprintf('Both parameters must be a valid string type, given $path: %s, $httpMethod: %s', gettype($path), gettype($httpMethod)));
		}
		foreach ($this as $k => $route) {
			if (preg_match($route->getPattern(), $path) && in_array($httpMethod, $route->getHttpMethod())) {
				unset($this->routes[$k]);
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Get the responsible route for the given path
	 * @param  string $path       The path to seek for the responsible route
	 * @param  string $httpMethod HTTP method to match with the route
	 * @return mixed             Return the route object if there is a match, or null otherwise
	 */
	public function get($path, $httpMethod='GET')
	{
		if (!is_string($path) && !is_string($httpMethod)) {
			throw new \InvalidArgumentException(sprintf('Both parameters must be a valid string type, given $path: %s, $httpMethod: %s', gettype($path), gettype($httpMethod)));
		}
		foreach ($this as $k => $route) {
			if (preg_match($route->getPattern(), $path) && in_array($httpMethod, $route->getHttpMethod())) {
				return $route;
			}
		}
		return null;
	}

	public function exists($path, $httpMethod='GET')
	{
		if (!is_string($path) && !is_string($httpMethod)) {
			throw new \InvalidArgumentException(sprintf('Both parameters must be a valid string type, given $path: %s, $httpMethod: %s', gettype($path), gettype($httpMethod)));
		}
		$routes = $this->routes;
		foreach ($routes as $k => $route) {
			if (preg_match($route->getPattern(), $path) && in_array($httpMethod, $route->getHttpMethod())) {
				return TRUE;
			}
		}
		return FALSE;
	}

	public function clear()
	{
		$this->routes = array();
	}

	public function count()
	{
		return count($this->routes);
	}

	public function getIterator()
	{
		$func = function($a, $b) {
			return substr_count($b->getPattern(), '/') - substr_count($a->getPattern(), '/');
		};
		usort($this->routes, $func);
		return new \ArrayIterator($this->routes);
	}

	public function toArray()
	{
		$routes = array();
		foreach ($this->routes as $route) {
			foreach ($route->getHttpMethod() as $httpMethod) {
				$routes[$route->getPattern()][$httpMethod] = $route->getCallback();
			}			
		}
		return $routes;
	}

	public function getRoutes()
	{
		return $this->routes;
	}
}