<?php
require_once 'bootstrap.php';

use Routeria\RouteCollection;
use Routeria\Route;

class RouteCollectionTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->collection = new RouteCollection;
	}

	public function tearDown()
	{
		$this->collection = null;
	}

	public function testAddMethodWillReturnRouteObject()
	{
		$route = $this->collection->add('/',function() {});
		$this->assertInstanceOf('Routeria\Route', $route);
	}

	public function testAddRouteToProperty()
	{
		$route = $this->collection->add('/',function() {});
		$this->assertAttributeContains($route, 'routes',$this->collection);
	}

	public function testAddArrayOfRoutesToProperty()
	{
		$routes = array(
							new Route('/',function() {}),
							new Route('/post/{id:int}', function() {}),
							new Route('/{post-title:alnum}', function() {}),
							new Route('/user/{name:alnum}',function() {})
						);
		$this->collection->addRoutes($routes);
		foreach ($routes as $route) {
			$this->assertAttributeContains($route, 'routes',$this->collection);
		}
		$routeCollection = clone $this->collection;
		return $routeCollection;
	}

	/**
	 * @depends testAddArrayOfRoutesToProperty
	 */
	
	public function testRemoveARoute($routeCollection)
	{
		$route = new Route('/user/{id:int}', function() {});
		$routeCollection->addRoute($route);
		$routeCollection->remove('/user/123123');
		$this->assertAttributeNotContains($route, 'routes', $routeCollection);
	}

	/**
	 * @depends testAddArrayOfRoutesToProperty
	 */	
	public function testGetARouteBySpecifiedPaths($routeCollection)
	{
		$route = $routeCollection->get('/post/1232');
		$this->assertEquals(new Route('/post/{id:int}', function() {}), $route);
	}

	/**
	 * @depends testAddArrayOfRoutesToProperty
	 */
	public function testIfARouteExists($routeCollection)
	{
		$exists = $routeCollection->exists('/user/andi');
		$this->assertTrue($exists);
	}

	/**
	 * @depends testAddArrayOfRoutesToProperty
	 */
	public function testClearRoutes($routeCollection)
	{
		$routeCollection->clear();
		$this->assertAttributeEmpty('routes', $routeCollection);
	}
}