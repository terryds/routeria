<?php

namespace Routeria\Test\Helper;

class User
{
	public function greet($id, $name) {
		echo 'Hello, ' . $name . '!. Your ID is ' . $id;
	}
}