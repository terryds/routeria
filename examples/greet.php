<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;

$request = Request::createFromGlobals();
$router = new Routeria;
$callback = function($fname, $lname) {
	echo "Hello $fname $lname. Nice to meet ya!";
};
$router->get('/greet/{fname:alpha}/{lname:alpha}', $callback);
$router->route($request->getPathInfo(), $request->getMethod());