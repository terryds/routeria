<?php

namespace Routeria;

class CallbackRoute extends Route
{
	private $callback;

	public function __construct($pattern, $callback, $http_method)
	{
		// Validate the callback type
		if (!is_callable($callback)) {
			throw new \InvalidArgumentException('Argument $callback must be callable');
		}

		// Validate the pattern. The method is inherited from abstract Route class
		$this->validatePattern($pattern);

		// Setting the values, and compiling pattern, also make http methods in an array
		$this->pattern = $this->compile($pattern);
		$this->callback = $callback;
		$this->http_method = (array) $http_method;
	}

	public function run(array $args)
	{
		// Get the reflection of the anonymous function to get all its parameters
		$reflection = new \ReflectionFunction($this->callback);
		$params = $reflection->getParameters();

		// Getting all the required parameters for callback to work in an array
		$required_params = array();
		foreach ($params as $k => $param) {
			$required_params[$k] = $param->name;
		}

		// Flip the array so the required parameters are the indexes of array
		// Initiating the parsed and missing arguments arrays
		$required_params = array_flip($required_params);
		$parsed_args = array();
		$missing_args = array();

		// Loop through the required params (Remember that the keys are the params)
		// If the key is also in the arguments (caught in the URIParser) get it, otherwise throw it in the missing argument
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

		// Alright, call the function with the parsed arguments!
		call_user_func_array($this->callback, $parsed_args);
	}
}