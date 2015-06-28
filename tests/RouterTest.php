<?php
namespace Routeria\Tests;

use Routeria\Router;
use Routeria\RouteCollection;
use Routeria\Route;
use Symfony\Component\HttpFoundation\Request;

class RouterTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$routesGET = array(
			'/' => function() {echo 'Hello World';},
			'/post/{id:int}' => function() {echo 'get post by id';},
			'/post/{name:alpha}' => function() {echo 'get post by name';},
			'/user/{id:int}' => function() {echo 'get user by id';},
			'/user/{name:alpha}' => function() {echo 'get user by name';}
			);

		$routesPOST = array(
			'/post/{name:alpha}' => function() {echo 'sending post name data';},
			'/user/{id:int}' => function() {echo 'sending user id data';},
			'/user/{name:alpha}' => function() {echo 'sending user name data';}
			);


		$routeCollection = new RouteCollection();
		foreach ($routesGET as $pattern => $callback) {
			$routeCollection->add(new Route($pattern, $callback));
		}
		foreach ($routesPOST as $pattern => $callback) {
			$routeCollection->add(new Route($pattern, $callback, 'POST'));
		}
		$this->collection = $routeCollection;
		$this->router = new Router($routeCollection);
	}

	/**
	 * @dataProvider providerRealURLPath
	 */
	public function testSucceedRouteARequest($path, $httpMethod)
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
		$result = $this->router->route($request);
		$this->assertTrue($result);
	}

	public function providerRealURLPath()
	{
		return array(
			array('/','GET'),
			array('/post/tsunami-in-a-country','GET'),
			array('/user/332','GET'),
			array('/user/6532','POST')
			);
	}

	/**
	 * @dataProvider providerTestGetParamFromURL
	 */
	public function testGetParamFromURL($url, $httpMethod, $name, $param)
	{
		$request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
							->disableOriginalConstructor()
							->getMock();
		$request->expects($this->once())
				->method('getPathInfo')
				->will($this->returnValue($url));
		$request->expects($this->once())
				->method('getMethod')
				->will($this->returnValue($httpMethod));
		if ($this->router->route($request))
		{
			$this->assertContains($param, $this->router->getParams());
			$this->assertEquals($param, $this->router->getParam($name));
		}
		else
		{
			throw new Exception('No Resources Found');
		}
	}

	public function providerTestGetParamFromURL()
	{
		return array(
				array('/post/15', 'GET','id','15'),
				array('/user/terry-djony', 'GET','name', 'terry-djony'),
				array('/post/lorem-ipsum','POST','name', 'lorem-ipsum')
			);
	}

	public function testRouteConvertWillModifyParamBeforeFetched()
	{
		$request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
							->disableOriginalConstructor()
							->getMock();
		$request->expects($this->once())
				->method('getPathInfo')
				->will($this->returnValue('/threads/lorem-ipsum-dolor-sit-amet'));
		$request->expects($this->once())
				->method('getMethod')
				->will($this->returnValue('GET'));
		$this->collection->add(new Route('/threads/{title:alpha}', function() {}))
						->convert('title',function($param) {
									return str_replace('-', ' ', $param);
									}
								);
		if ($this->router->route($request))
		{
			$this->assertEquals('lorem ipsum dolor sit amet', $this->router->getParam('title'));
		}
		else
		{
			throw new Exception('No Resources Found');
		}
	}
}