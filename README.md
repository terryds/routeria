#Routeria Router

A simple fast yet powerful PHP router

##Why Routeria Router?
Because, it's designed to be simple, flexible, and also extensible.  
It follows the Dependency Inversion Principles.

##Installation

To install Routeria, you just need to add this to your `composer.json`:

```
	"require": {
		"terrydjony\routeria": "1.*@dev"
	}
```

##Usage

The installed Routeria and all of the components is in the `vendor` folder.  
In order to use it, you just need to require the autoload.  
And, you need to load the namespace using `use` keyword.  

```php
require_once __DIR__ . '/vendor/autoload.php';
```


###Configuration (.htaccess)

Before using Routeria, you need to turn your rewrite engine on and add rules so any requests to non-existing directory or filename will be rewritten to index.php.
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

###Simple Callback Routing

For a simple callback route, you just need to use Route, Router and Dispatcher classes like below.  
The Request component of Symfony HttpFoundation is required to tell the request path to the router.
```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Route;
use Routeria\Router;
use Routeria\Dispatcher;

$request = Request::createFromFlobals();
$router = new Router;
$router->add(new Route('/', function() { echo 'Hello World'; }));
$dispatcher = new Dispatcher($router);
$dispatcher->dispatch($request);
```

###Using Named Parameters

```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Route;
use Routeria\Router;
use Routeria\Dispatcher;

$request = Request::createFromFlobals();
$router = new Router;
$router->add(new Route('/user/{id:int}', function($router) {
                        echo 'Hello User with ID: ' . $router->getParam('id'); 
                    })
            );
$dispatcher = new Dispatcher($router);
$dispatcher->dispatch($request);
```

If you go to the relative path `/user/1`, it will yell `Hello User with ID: 1`

###Routing with specific HTTP Method

As default, the HTTP Method is 'GET'.  
But, if you want to use other HTTP method, you can specify it as the third argument of `Route` class.
```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Route;
use Routeria\Router;
use Routeria\Dispatcher;

$request = Request::createFromFlobals();
$router = new Router;
$router->add(new Route('/user/{id:int}', function($router) {
                        echo 'Sending user data.. ID: ' . $router->getParam('id'); 
                    }, 'POST')
            );
$dispatcher = new Dispatcher($router);
$dispatcher->dispatch($request);
```

If the HTTP Method is `POST` and the path is `user/5` then, it will output `Sending user data.. ID: 5`.

###Dispatching Controller

If you want to dispatch a controller via router, you can use `ControllerDispatch` class.  
The `ControllerDispatch` class expects 3 parameters, which are the controller which can be an object **or** a controller name  for a static call, the method name, and the **optional** parameter which is an **array** of arguments.  
Notice that the argument is the named parameter.  
Just pass it as the second argument of `Route` class, and _voila!_ It will call the controller and dispatch the method with given parameters.

```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Route;
use Routeria\Router;
use Routeria\Dispatcher;
use Routeria\Dispatch\ControllerDispatch;

$request = Request::createFromFlobals();
$router = new Router;
$router->add(new Route('/user/{id:int}',
             new ControllerDispatch( new UserController, 'getByID', array('id'))
            );
$dispatcher = new Dispatcher($router);
$dispatcher->dispatch($request);
```

If the request path is `/user/3`, it will call the `UserController` and dispatch `getByID(3)`

###Dispatching Controller with Dependencies

You can use `inject($dependency)` method in the `ControllerDispatch` to inject a dependency, and `injectDependencies(array $dependencies)` to inject an array of dependencies.

For PHP 5.4, you can:
```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Route;
use Routeria\Router;
use Routeria\Dispatcher;
use Routeria\Dispatch\ControllerDispatch;

$request = Request::createFromFlobals();
$router = new Router;
$router->add(new Route(
                '/user/{id:int}',
                (new ControllerDispatch( new UserController, 'getByID', array('id')))
                ->inject(new UserMapper());
             )
            );
$dispatcher = new Dispatcher($router);
$dispatcher->dispatch($request);
```

But, with PHP 5.3, you must specify the controller dispatch before.
```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Route;
use Routeria\Router;
use Routeria\Dispatcher;
use Routeria\Dispatch\ControllerDispatch;

$request = Request::createFromFlobals();
$router = new Router;
$controllerDispatch = new ControllerDispatch(new UserController, 'getByID', array('id'));
$controllerDispatch->inject(new UserMapper());
$router->add(new Route('user/{id:int}', $controllerDispatch));
$dispatcher = new Dispatcher($router);
$dispatcher->dispatch($request);
```

###Modify the parameters before using it

If you want to modify a param by a function, you can use `convert($name, $converter)` method on Route class **or** after adding a `Route` on `RouteCollection` class

```php
use Routeria\RouteCollection;
use Routeria\Route;

$collection = new RouteCollection;
$collection->add(new Route('/threads/{title:alpha}', function() {}))					           ->convert('title',function($param) {
	return str_replace('-', ' ', $param);
});
```

With this convertion, the dashes will be converted to space before parameter `title` is fetched.

###Using custom route collection
If you want to use custom route collection, such as this `BlogCollection`.  
You must make it extend the `CustomCollection` class.
```php
use Routeria\Route;
use Routeria\CustomCollection;

class BlogCollection extends CustomCollection
{
   public function initialize()
   {
       $this->add(new Route('/', ControllerDispatch('BlogController','listAll');
       $this->add(new Route('/{id:int}', ControllerDispatch('BlogController','listByID', 'id');
        $this->add(new Route('/{title:alpha}', ControllerDispatch('BlogController','listByTitle', 'title');
   }
}
```

Then, just inject it in the `Router` class.
```php
use Routeria\Router;
$router = new Router(new BlogCollection);
```

##Contribute to this library

Since this is my first project and I'm still learning, please contribute to this project by forking it, make good commits and then perform a pull request.