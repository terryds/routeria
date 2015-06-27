<?php

namespace Routeria\Tests;

use Routeria\Dispatcher;
use Routeria\RouteCollection;
use Routeria\Router;
use Routeria\Route;
use Symfony\Component\HttpFoundation\Request;
use Routeria\Dispatch\ControllerDispatch;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$routesGET = array(
			'/' => function() {echo 'Hello World';},
			'/post/{id:int}' => function($router) {echo 'get post by id: '.$router->getParam('id');},
			'/post/{name:alpha}' => function($router) {echo 'get post by name: '.$router->getParam('name');},
			'/user/{id:int}' => function($router) {echo 'get user by id: '.$router->getParam('id');},
			'/user/{name:alpha}' => function($router) {echo 'get user by name: '.$router->getParam('name');}
			);

		$routesPOST = array(
			'/post/{id:int}' => function($router) {echo 'sending post id data: '.$router->getParam('id');},
			'/post/{name:alpha}' => function($router) {echo 'sending post name data: '.$router->getParam('name');},
			'/user/{id:int}' => function($router) {echo 'sending user id data: '.$router->getParam('id');},
			'/user/{name:alpha}' => function($router) {echo 'sending user name data: '.$router->getParam('name');}
			);


		$routeCollection = new RouteCollection();
		foreach ($routesGET as $pattern => $callback) {
			$routeCollection->add($pattern, $callback);
		}
		foreach ($routesPOST as $pattern => $callback) {
			$routeCollection->add($pattern, $callback, 'POST');
		}
		$this->collection = $routeCollection;
		$this->router = new Router($this->collection);
		$this->dispatcher = new Dispatcher($this->router);
	}

	/**
	 * @dataProvider providerTestDispatchWillCallTheCallback
	 */
	public function testDispatchWillCallTheCallback($path, $httpMethod, $message)
	{
		$request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
							->disableOriginalConstructor()
							->getMock();
		$request->expects($this->once())
				->method('getPathInfo')
				->will($this->returnValue($path));
		$request->expects($this->once())
				->method('getMethod')
				->will($this->returnValue($httpMethod));
		$this->expectOutputString($message);
		$this->dispatcher->dispatch($request);
	}

	public function providerTestDispatchWillCallTheCallback()
	{
		return array(
				array('/post/12','GET','get post by id: 12'),
				array('/post/lorem-ipsum','GET','get post by name: lorem-ipsum'),
				array('/user/15','GET','get user by id: 15'),
				array('/user/terry','GET','get user by name: terry'),
				array('/post/12','POST','sending post id data: 12'),
				array('/post/lorem-ipsum','POST','sending post name data: lorem-ipsum'),
				array('/user/15','POST','sending user id data: 15'),
				array('/user/terry','POST','sending user name data: terry'),
			);
	}

	public function testControllerDispatch()
	{
		$controller = new \Routeria\TestHelper\FakeController;
		$this->collection->addRoute(new Route('/testController/{id:int}', new ControllerDispatch($controller, 'fakeMethod', 'id')));
		$request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
							->disableOriginalConstructor()
							->getMock();
		$request->expects($this->once())
				->method('getPathInfo')
				->will($this->returnValue('/testController/55'));
		$request->expects($this->once())
				->method('getMethod')
				->will($this->returnValue('GET'));
		$this->expectOutputString('Hello user id: 55');
		$this->dispatcher->dispatch($request);
	}
}