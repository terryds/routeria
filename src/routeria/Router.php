<?php

namespace Routeria;

use Symfony\Component\HttpFoundation\Request;

class Router implements RouterInterface
{
	private $collection;
	private $params = array();
	private $callback;

	public function __construct(RouteCollectionInterface $collection)
	{
		$this->collection = $collection;
	}

	public function route(Request $request)
	{
		$path = $request->getPathInfo();
		$httpMethod = strtoupper($request->getMethod());
		$routes = $this->collection;
		foreach ($routes as $route)
		{
			if (preg_match($route->getPattern(), $path, $matches) && in_array($httpMethod, $route->getHttpMethod() ))
			{
				unset($matches[0]);
				if($converters = $route->getConverters()) {
					foreach ($converters as $param => $converter) {
						if (array_key_exists($param, $matches)) {
							$matches[$param] = $converter($matches[$param]);
						}				
					}
				}
				$this->callback = $route->getCallback();
				$this->params = $matches;
				return TRUE;
			}
		}
		return FALSE;
	}

	public function getParam($key)
	{
		if (!is_string($key)) {
			throw new \InvalidArgumentException(sprintf('$key must be string, given : %s', gettype($key)));
		}
		return (isset($this->params[$key])) ? $this->params[$key] : null;
	}

	public function setParam($key, $value)
	{
		$this->params[$key] = $value;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function setCallback($callback)
	{
		if (!is_callable($callback)) {
			throw new InvalidArgumentException(sprintf('$callback must be callable, given: %s', gettype($callback)));
		}
		$this->callback = $callback;
	}

	public function getCallback()
	{
		return $this->callback;
	}
}