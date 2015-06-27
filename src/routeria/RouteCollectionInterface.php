<?php
namespace Routeria;

interface RouteCollectionInterface extends \Countable, \IteratorAggregate
{
	public function addRoute(RouteInterface $route);
	public function addRoutes(array $routes);
	public function remove($path, $httpMethod);
	public function get($path, $httpMethod);
	public function exists($path, $httpMethod);
	public function clear();
	public function toArray();
}