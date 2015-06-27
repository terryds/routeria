<?php

namespace Routeria;

use Regex\Translator;
use Regex\TranslatorInterface;

class Route implements RouteInterface
{
	private $pattern;
	private $callback;
	private $converters;
	private $translator;
	private $httpMethod;

	public function __construct($pattern, $callback, $httpMethod = 'GET', $translator = null)
	{
		if (!is_object($callback) || !method_exists($callback, '__invoke'))
		{
			throw new \InvalidArgumentException(sprintf('The parameter $callback must be a closure or invokable object, given a  %s', gettype($callback)));
		}
		if (isset($translator) && $translator instanceOf TranslatorInterface)
		{
			$this->setTranslator($translator);
		}
		else
		{
			$this->setTranslator(new Translator);
		}
		$this->setPattern($pattern);
		$this->setHttpMethod($httpMethod);
		$this->setCallback($callback);
	}

	protected function setTranslator(TranslatorInterface $translator)
	{
		$this->translator = $translator;
	}

	protected function setPattern($pattern)
	{
		if ($this->isPatternValid($pattern))
		{
			$this->pattern = $this->compile($pattern);
		}
		else
		{
			throw new \InvalidArgumentException(sprintf('Invalid pattern format. Valid ones should match regex: #^/(?:(\w+/)*\w+)?#'));
		}
		
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
		if (preg_match('#^/(?:([^/\s]+/)*[^/\s]+)?$#', $pattern)) {
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
		$translator = $this->translator;
		$compiled = preg_replace_callback(
			$regex,
			function ($matches) use ($translator) {
				if (!empty($matches[1])) {
					return '/(?P<'.$matches[1].'>'.$translator->translate($matches[2]).')';	
				}
				else {
					return '/(' . $translator->translate($matches[2]).')';	
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

	protected function setCallback($callback)
	{
		if (!is_object($callback) && !method_exists($callback, '__invoke'))
		{
			throw new \InvalidArgumentException('Invalid type for $callback, closure or invokable object expected');
		}
		if (method_exists($callback, 'toClosure'))
		{
			$this->callback = $callback->toClosure();
		}
		else 
		{
			$this->callback = $callback;
		}
	}

	protected function setHttpMethod($httpMethod)
	{
		if (!is_string($httpMethod) && !is_array($httpMethod))
		{
			throw new \InvalidArgumentException(sprintf('Invalid type for $method, string or array expected, given : %s', gettype($method)));
		}
		if (is_array($httpMethod))
		{
			foreach ($httpMethod as $k => $method)
			{
				$httpMethod[$k] = strtoupper($method);
			}
		}
		else
		{
			$httpMethod = strtoupper($httpMethod);
		}
		$this->httpMethod = (array)$httpMethod;
	}

	public function getPattern()
	{
		return $this->pattern;
	}

	public function getCallback()
	{
		return $this->callback;
	}

	public function getHttpMethod()
	{
		return $this->httpMethod;
	}

	public function getConverters()
	{
		return $this->converters;
	}
}