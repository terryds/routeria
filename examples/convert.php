<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;

$request = Request::createFromGlobals();
$router = new Routeria;
$router->get('/posts/{title:alpha}', function($title) { echo '<h1>'.$title.'</h1>';})
		->convert(function($title) {
			return ucwords(str_replace('-', ' ', $title));
		});

$router->route($request->getPathInfo(), $request->getMethod());