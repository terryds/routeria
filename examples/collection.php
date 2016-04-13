<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;
use Routeria\RouteCollection;
use Routeria\ControllerRoute;
use Routeria\RouteProviderInterface;

class Blog {
	public function index() {echo 'index';}
	public function showPost() {echo 'post 1';}
	public function showPage() {echo 'page 1';}
}

class BlogCollection implements RouteProviderInterface {
	public function register(RouteCollection $collection) {
		$blogRoutes = array(
			'index' => new ControllerRoute('/','Blog::index','GET'),
			'post' => new ControllerRoute('/{id:int}/{title:alnum}','Blog::showPost','GET'),
			'page' => new ControllerRoute('/page/{title:alpha}','Blog::showPage','GET')
			);

		$collection->addRoutes($blogRoutes);
	}
}

$request = Request::createFromGlobals();
$router = new Routeria;

$collection = new BlogCollection;
$router->register($collection);
$router->route($request->getPathInfo(), $request->getMethod());