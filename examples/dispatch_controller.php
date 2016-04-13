<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;

class User {
	public function getInfo($id, $name) {
		echo 'Hello User ' . $name . ' ID: ' . $id;
	}
}

$request = Request::createFromGlobals();
$router = new Routeria;
$router->get('/user/{name:alpha}/{id:int}', 'User::getInfo');
$router->route($request->getPathInfo(), $request->getMethod());