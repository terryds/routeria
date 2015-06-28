<?php
namespace Routeria;

use Symfony\Component\HttpFoundation\Request;

interface RouterInterface
{
	public function add(RouteInterface $router);
	public function route(Request $request);
	public function setCallback($callback);
	public function getCallback();
	public function setParam($key, $value);
	public function getParam($key);
	public function getParams();
}