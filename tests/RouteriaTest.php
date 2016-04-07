<?php

namespace Routeria\Test;
use Routeria\Routeria;

class RouteriaTest extends \PHPUnit_Framework_TestCase
{
	private $routeria;

	public function setUp()
	{
		$this->routeria = new Routeria;
	}

	public function testOutputHello()
	{
		$this->routeria->get('/hello',function() {echo "Hello World";});
		$this->expectOutputString('Hello World');
		$this->routeria->route('/hello');
	}

	public function providerTestAllHTTPMethods()
	{
		return array(
			array('GET','Getting information...'),
			array('POST','Sending information...'),
			array('PUT','Putting information...'),
			array('DELETE', 'Deleting information...'),
			array('CUSTOM','Custom HTTP Method')
			);
	}

	/**
	 * @dataProvider providerTestAllHTTPMethods
	 */
	public function testAllHTTPMethods($method, $output)
	{
		$this->routeria->add('/',function() use ($output) {echo $output;}, $method);
		$this->expectOutputString($output);
		$this->routeria->route('/');
	}

	public function providerTestCallbackRoute()
	{
		return array(
			array(function($name, $id) {echo $name . ' ' . $id;}, 'ADMIN 123'),
			array(function($name, $id) {echo $id . ' ' . $name;}, '123 ADMIN'),
			array(function($id, $name) {echo $id . ' ' . $name;}, '123 ADMIN'),
			array(function($id, $name) {echo $name . ' ' . $id;}, 'ADMIN 123'),
			);
	}

	/**
	 * @dataProvider providerTestCallbackRoute
	 */
	public function testCallbackRoute($callback, $expected)
	{
		$this->routeria->get('/posts/{name:alpha}/{id:int}', $callback);
		$this->expectOutputString($expected);
		$this->routeria->route('/posts/ADMIN/123');
	}

	public function testConvertArgs()
	{
		$this->routeria->get('/posts/{name:alpha}/{id:int}', function($name, $id) { echo $name . ' ' . $id;})
						->convert(function($name) {return ucwords(str_replace('-', ' ', $name));})
						->convert(function($id) {return $id + 1000;});
		$this->expectOutputString('Hello World 1001');
		$this->routeria->route('/posts/hello-world/1');
	}

	public function providerTestRegisterInvalidPath()
	{
		return array(
			array('invalid'),
			array('/invalid path'),
			array('//invalid-path')
			);
	}

	/**
	 * @dataProvider providerTestRegisterInvalidPath
	 * @expectedException Routeria\Exception\InvalidPathException
	 */
	public function testRouteInvalidPath($invalid_path)
	{
		$this->routeria->get($invalid_path, function() {;});
	}

	/**
	 * @expectedException Routeria\Exception\ResourceNotFoundException
	 */
	public function testRouteUnregisteredPath()
	{
		$this->routeria->route('/unregistered');
	}

	public function testControllerRoute()
	{
		$this->routeria->get('/user/{name:alpha}/{id:int}','Routeria\Test\Helper\User::greet');
		$this->expectOutputString('Hello, admin!. Your ID is 1');
		$this->routeria->route('/user/admin/1');
	}
}