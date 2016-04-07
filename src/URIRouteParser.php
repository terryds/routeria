<?php

namespace Routeria;
use Routeria\Exception\ResourceNotFoundException;

class URIRouteParser
{
	private $collection;
	private $route;
	private $matches;

	public function __construct(RouteCollection $collection)
	{
		$this->collection = $collection;
	}

	public function parse($request_path, $http_method = 'GET')
	{
		$matches = array();
		foreach ($this->collection as $route)
		{
			extract($route->getInfo(), EXTR_PREFIX_ALL, 'route');
			if (preg_match($route_pattern, $request_path, $matches) && in_array($http_method, $route_http_method))
			{
				break;
			}
		}

		if (!$matches)
		{
			throw new ResourceNotFoundException($request_path);
		}
		unset($matches[0]);
		$this->route = $route;
		$converters = $this->route->getConverters();
		if ($converters)
		{
			foreach ($converters as $converter)
			{
				$reflection = new \ReflectionFunction($converter);
				$params = $reflection->getParameters();
				if(count($params) > 1)
				{
					throw new \Exception('Only one parameter is required. If you want to convert more, use more callbacks');
				}
				$param = $params[0];
				$param = $param->name;
				foreach ($matches as $k => $v)
				{
					if ($k === $param)
					{
						$matches[$k] = $converter($matches[$k]);
					}
				}
			}
		}
		$this->matches = $matches;
		return $route;
	}

	public function getRoute()
	{
		return $this->route;
	}

	public function get($identifier)
	{
		return $this->matches[$identifier];
	}

	public function getParams()
	{
		return $this->matches;
	}
}