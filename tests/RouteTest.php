<?php

use Routeria\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @param  string $string     The path string to translate to regex
	 * @param  string $translated The translated path as regex
	 * @dataProvider providerTestTranslatedPattern
	 */
	public function testTranslatedPattern($string, $translated)
	{
		$callback = function() {};
		$route = new Route($string, $callback);
		$this->assertEquals($translated, $route->getPattern());
	}

	public function providerTestTranslatedPattern()
	{
		return array(
			array('/','#^/$#'),
			array('/{int}','#^/([0-9]+)$#'),
			array('/posts/{id:int}','#^/posts/(?P<id>[0-9]+)$#'),
			array('/{post-title:alnum}','#^/(?P<post-title>[a-zA-Z0-9_-]+)$#'),
			array('/trailing/slash/is/okay/', '#^/trailing/slash/is/okay$#'),
			array('/multiple/trailing/slash/will/be/trimmed/////////', '#^/multiple/trailing/slash/will/be/trimmed$#')
			);
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @dataProvider providerTestInvalidPathFormatWillThrowException
	 */
	public function testInvalidPathFormatWillThrowException($invalidPath)
	{
		$callback = function() {};
		$route = new Route($invalidPath, $callback);
	}

	public function providerTestInvalidPathFormatWillThrowException()
	{
		return array(
			array('post/title'),
			array('/test//double'),
			);
	}

	/**
	 * @param  string $pattern The valid pattern for url route
	 * @dataProvider providerTestValidPattern
	 */
	public function testValidPattern($pattern)
	{
		$route = new Route('/', function() {});
		$bool = $this->invokeMethod($route, 'isPatternValid', array(&$pattern));
		$this->assertTrue($bool);
	}

	public function providerTestValidPattern()
	{
		return array(
			array('/'),
			array('/test'),
			array('/trailing/slash/'),
			array('/[]/;,.*&^%324/|*&~@!'),
			array('/multiple/trailing/slash/will/be/trimmed/////////'),
			array('/test/double//')
			);
	}

	public function invokeMethod(&$object, $methodName, array $parameters = array())
	{
		$reflection = new \ReflectionClass(get_class($object));
		$method = $reflection->getMethod($methodName);
		$method->setAccessible(true);

		return $method->invokeArgs($object, $parameters);
	}
}