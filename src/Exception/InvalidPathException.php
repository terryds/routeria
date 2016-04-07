<?php

namespace Routeria\Exception;

class InvalidPathException extends \Exception {
	public function __construct($path, $code = 100)
	{
		$message = 'Invalid path for routing "' . $path . '". Valid pattern must match regex #^/(?:([^/\s]+/)*[^/\s]+)?$#';
		parent::__construct($message, $code); 
	}
}