<?php

namespace Routeria;

class ControllerRoute extends Route
{
	private $controller;
	private $method;

	public function __construct($pattern, $route, $http_method, $ioc=null)
	{
		// Validate the pattern
		$this->validatePattern($pattern);
		// Split the controller and the method from the string 'Controller::method'
		list($controller, $method) = explode('::', $route);

		// Throw RuntimeException if controller or the method are not found
		if (!class_exists($controller)) {
			throw new \RuntimeException("Controller $controller not found");
		}
		if (!method_exists($controller, $method)) {
			throw new \RuntimeException("Method $method not found in $controller class");
		}

		// Set the controller, compiled pattern, method, and http method
		$this->controller = ($ioc instanceOf ION\Container) ? $ion->make($controller) : new $controller;
		$this->pattern = $this->compile($pattern);
		$this->method = $method;
		$this->http_method = (array)$http_method;
	}

	public function run(array $args)
	{
		// Get the reflection over the method to get all the parameters
		$reflection = new \ReflectionMethod($this->controller, $this->method);
		$params = $reflection->getParameters();

		// Initiating the required params array
		// Looping through the reflectionparameters to get the name if it is required (not optional)
		$required_params = array();
		foreach ($params as $k => $param) {
			if ($param->isOptional()) {
				continue;
			}
			$required_params[$k] = $param->name;
		}

		// Flipping the required params array so the keys are now the required parameter names
		// Initiating parsed and missing args arrays
		$required_params = array_flip($required_params);
		$parsed_args = array();
		$missing_args = array();

		// Looping through the required parameters array
		// If the key(required parameter name) also exists in the arguments, catch it in parsed args. Otherwise, toss it to missing args
		foreach ($required_params as $k => $v) {
			if (isset($args[$k])) {
				$parsed_args[$k] = $args[$k];
				continue;
			}
			$missing_args[] = $k;
		}

		// If there are any missing arguments, throw a Runtime Exception
		if ($missing_args) {
			throw new \RuntimeException('Missing parameters: '.implode(', ', $diff));
		}

		// Yuhuu.. Call the controller method with the parsed arguments passed!
		call_user_func_array(array($this->controller, $this->method), $parsed_args);
	}
}