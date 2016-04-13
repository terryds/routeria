<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;

$request = Request::createFromGlobals();
$router = new Routeria;
$router->get('/', function() { echo 'HTTP METHOD : GET';});
$router->post('/', function() { echo 'HTTP METHOD : POST';});
$router->put('/', function() { echo 'HTTP METHOD : PUT';});
$router->delete('/', function() { echo 'HTTP METHOD : DELETE';});
$router->add('/', function() { echo 'HTTP METHOD : CUSTOM';}, 'CUSTOM');
$router->route($request->getPathInfo(), $request->getMethod());