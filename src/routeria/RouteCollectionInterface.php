<?php
namespace Routeria;

interface RouteCollectionInterface extends \Countable, \IteratorAggregate
{
	public function add(RouteInterface $route);
	public function addRoutes(array $routes);
	public function remove($path, $httpMethod);
	public function get($path, $httpMethod);
	public function exists($path, $httpMethod);
	public function clear();
	public function toArray();
}