<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;

$request = Request::createFromGlobals();
$router = new Routeria;
$router->get('/', function() { echo 'Hello World';});
$router->route($request->getPathInfo(), $request->getMethod());