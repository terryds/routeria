<?php

namespace Routeria;
use Routeria\Translator;
use Routeria\Exception\InvalidPathException;

abstract class Route implements RouteInterface
{
	const VALID_PATTERN = '#^/(?:([^/\s\#]+/)*[^/\s\#]+)?$#';

	protected $converters = array();
	protected $http_method;
	protected $pattern;

	protected function validatePattern($pattern)
	{
		// Cut away the right blackslash, validate the pattern using constant or throw InvalidPathException
		if (strlen($pattern) > 1)
		{
			$pattern = rtrim($pattern, '/');
		}
		if (preg_match(self::VALID_PATTERN, $pattern))
		{
			return TRUE;
		}
		else
		{
			throw new InvalidPathException($pattern);
		}
	}

	protected function compile($pattern)
	{
		// Compile the pattern by translating it to a valid regex expression
		$regex = '#/{(?:([a-zA-Z][a-zA-Z0-9_-]*):)?([^}]+)}#';
		$compiled = preg_replace_callback(
			$regex,
			function ($matches) {
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

	public function convert($function)
	{
		// Validate the callable and set the converter
		if (!is_callable($function))
		{
			throw new \InvalidArgumentException(sprintf('Clousure or invokable object expected, given :%s', gettype($converter)));
		}
		$this->converters[] = $function;
		return $this;
	}

	/**
	 * Return an array with pattern and http method of the route
	 * @return array The pattern and http method of the route
	 */
	public function getInfo()
	{
		return array('pattern'=> $this->pattern, 'http_method'=> $this->http_method);
	}

	/**
	 * Get all the converter functions in an array
	 * @return array Array of converter functions
	 */
	public function getConverters()
	{
		return $this->converters;
	}

	abstract public function run(array $params);
}