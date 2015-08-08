<?php

namespace Routeria;

use Regex\Translator;
use Regex\TranslatorInterface;

class Route implements RouteInterface
{
	const VALID_PATTERN = '#^/(?:([^/\s]+/)*[^/\s]+)?$#';

	private $pattern;
	private $httpMethods;
	private $ion;

	private $controller;
	private $action;
	private $callback;

	private $converters;


	public function __construct($pattern, $route, $httpMethods = 'GET')
	{
		// Validate $pattern and set the pattern
		if (!$this->isPatternValid($pattern))
		{
			throw new \InvalidArgumentException(sprintf('Invalid pattern format. Valid ones should match regex: #^/(?:(\w+/)*\w+)?#'));
		}
		$this->pattern = $this->compile($pattern);

		// Validate $route and set the callback or route
		if ($route instanceOf Closure) {
			$this->setCallback($route);
		}
		elseif (is_string($route)) {
			list($controller, $action) = $parts = explode('::', $route);
			if (count($parts) != 2) {
				throw new \InvalidArgumentException(sprintf('The format of controller route is controller::action, given: %s', $route));
			}
			$this->setControllerRoute($controller, $action);
		}
		else {
			throw new \InvalidArgumentException(sprintf('The type of argument two must be either a closure or a string with format controller::action, given: %s',gettype($route)));
		}

		// Validate $httpMethod and assign it
		if (!is_string($httpMethod) && !is_array($httpMethod))
		{
			throw new \InvalidArgumentException(sprintf('Invalid type for $method, string or array expected, given : %s', gettype($method)));
		}
		if (is_array($httpMethods))
		{
			foreach ($httpMethods as $k => $method)
			{
				$httpMethods[$k] = strtoupper($method);
			}
		}
		else
		{
			$httpMethods = strtoupper($httpMethod);
		}
		$this->httpMethods = (array)$httpMethods;
	}

	protected function setCallback($callback)
	{
		if (!is_object($callback) && !method_exists($callback, '__invoke'))
		{
			throw new \InvalidArgumentException('Invalid type for $callback, closure or invokable object expected');
		}
		else 
		{
			$this->callback = $callback;
		}
	}

	protected function setControllerRoute($controller, $action)
	{
		if (!class_exists($controller) || !method_exists($controller, $action)) {
			throw new \InvalidArgumentException(sprintf('The class %s or the method %s doesnot exist', $controller, $action));
		}
		$this->controller = $controller;
		$this->action = $action;
	}

	protected function isPatternValid(&$pattern)
	{
		if (!is_string($pattern))
		{
			throw new \InvalidArgumentException(sprintf('Invalid type for $pattern; string expected, given : %s', gettype($pattern)));
		}
		if (strlen($pattern) > 1) {
			$pattern = rtrim($pattern, '/');
		}
		if (preg_match(self::VALID_PATTERN, $pattern)) {
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	protected function compile($pattern)
	{
		$regex = '#/{(?:([a-zA-Z][a-zA-Z0-9_-]*):)?([^}]+)}#';
		$compiled = preg_replace_callback(
			$regex,
			function ($matches) use ($translator) {
				if (!empty($matches[1])) {
					return '/(?P<'.$matches[1].'>'.Translator::translate($matches[2]).')';	
				}
				else {
					return '/(' . Translator::translate($matches[2]).')';	
				}						
			},
			$pattern
		);

		$compiled = '#^'.$compiled.'$#';
		return $compiled;
	}

	public function convert($name, $converter)
	{
		if (!is_string($name)) {
			throw new \InvalidArgumentException(sprintf('The parameter to convert must be string, given : %s', gettype($name)));
		}
		if (!is_object($converter) && !method_exists($converter, '__invoke')) {
			throw new \InvalidArgumentException(sprintf('Clousure or invokable object expected, given :%s', gettype($converter)));
		}

		$this->converters[$name] = $converter;
	}

	public function getPattern()
	{
		return $this->pattern;
	}

	public function getCallback()
	{
		if (isset($this->callback))
		{
			return $this->callback;
		}
		else
		{
			return function($dispatcher) {
				$dispatcher->dispatchController($this->controller, $this->action);
			};
		}
	}

	public function getHttpMethods()
	{
		return $this->httpMethods;
	}

	public function getConverters()
	{
		return $this->converters;
	}
}