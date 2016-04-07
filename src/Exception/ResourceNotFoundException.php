<?php

namespace Routeria\Exception;

class ResourceNotFoundException extends \Exception {
	public function __construct($path, $code = 104)
	{
		$message = 'Resource not found for "' . $path . '". Please register it first';
		parent::__construct($message, $code); 
	}
}